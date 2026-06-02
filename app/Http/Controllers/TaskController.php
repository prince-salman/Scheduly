<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TimerSession;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function board()
    {
        $userId = Auth::id();

        $columns = [
            'todo'        => Task::forUser($userId)->todo()->orderByRaw("FIELD(priority,'high','medium','low')")->get(),
            'in_progress' => Task::forUser($userId)->inProgress()->orderBy('updated_at', 'desc')->get(),
            'done'        => Task::forUser($userId)->done()->latest('updated_at')->take(10)->get(),
        ];

        return view('tasks.board', compact('columns'));
    }

    // ── Store ────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'category'         => 'nullable|string|max:100',
            'priority'         => 'required|in:low,medium,high',
            'status'           => 'sometimes|in:todo,in_progress,done',
            'due_date'         => 'nullable|date',
            'reminder_at'      => 'nullable|date',
            'subtasks'         => 'nullable|array',
            'subtasks.*.title' => 'required_with:subtasks|string|max:255',
            'subtasks.*.done'  => 'nullable',
        ]);

        $subtasks = collect($validated['subtasks'] ?? [])
            ->map(fn($s) => [
                'title' => trim($s['title'] ?? ''),
                'done'  => filter_var($s['done'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ])
            ->filter(fn($s) => $s['title'] !== '')
            ->values()
            ->toArray();

        $task = Task::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category'    => $validated['category']    ?? null,
            'priority'    => $validated['priority'],
            'status'      => $validated['status']      ?? 'todo',
            'due_date'    => $validated['due_date']     ?? null,
            'reminder_at' => $validated['reminder_at'] ?? null,
            'subtasks'    => count($subtasks) ? $subtasks : null,
        ]);

        if ($task->reminder_at) {
            Notification::create([
                'user_id'     => Auth::id(),
                'task_id'     => $task->id,
                'type'        => 'reminder',
                'title'       => 'Reminder',
                'description' => "{$task->title} dijadwalkan pada " . $task->reminder_at->format('H:i'),
            ]);
        }

        return response()->json(['task' => $this->serializeTask($task->fresh())], 201);
    }

    // ── Update ───────────────────────────────────────────────────────

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'description'      => 'nullable|string',
            'category'         => 'nullable|string|max:100',
            'priority'         => 'sometimes|in:low,medium,high',
            'status'           => 'sometimes|in:todo,in_progress,done',
            'due_date'         => 'nullable|date',
            'reminder_at'      => 'nullable|date',
            'subtasks'         => 'nullable|array',
            'subtasks.*.title' => 'sometimes|string|max:255',
            'subtasks.*.done'  => 'nullable',
        ]);

        if (array_key_exists('subtasks', $validated)) {
            $validated['subtasks'] = collect($validated['subtasks'])
                ->map(fn($s) => [
                    'title' => trim($s['title'] ?? ''),
                    'done'  => filter_var($s['done'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ])
                ->filter(fn($s) => $s['title'] !== '')
                ->values()
                ->toArray();

            if (empty($validated['subtasks'])) {
                $validated['subtasks'] = null;
            }
        }

        if (isset($validated['status'])
            && $validated['status'] !== 'in_progress'
            && $task->timer_started_at) {
           $elapsedSeconds = (int) $task->timer_started_at->diffInSeconds(now());
            $this->stopActiveSession($task, $elapsedSeconds);
            $validated['focus_minutes'] = $task->focus_minutes + (int) floor($elapsedSeconds / 60);
            $validated['timer_started_at'] = null;
        }

        $task->update($validated);

        if (($validated['status'] ?? null) === 'done') {
            Notification::create([
                'user_id'     => Auth::id(),
                'task_id'     => $task->id,
                'type'        => 'alarm',
                'title'       => 'Task Selesai',
                'description' => "Kamu telah menyelesaikan: {$task->title}",
            ]);
        }

        return response()->json(['success' => true, 'task' => $this->serializeTask($task->fresh())]);
    }

    // ── Destroy ──────────────────────────────────────────────────────

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Task deleted']);
        }

        return back()->with('success', 'Task dihapus.');
    }

    // ── Move (drag & drop) ───────────────────────────────────────────

    public function moveStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate(['status' => 'required|in:todo,in_progress,done']);

        $data = ['status' => $request->status];

        if ($request->status !== 'in_progress' && $task->timer_started_at) {
            $elapsed = (int) $task->timer_started_at->diffInMinutes(now());
            $this->stopActiveSession($task, $elapsed);
            $data['focus_minutes']    = $task->focus_minutes + $elapsed;
            $data['timer_started_at'] = null;
        }

        $task->update($data);

        return response()->json(['task' => $this->serializeTask($task->fresh())]);
    }

    // ── Timer: start ─────────────────────────────────────────────────

    public function startTimer(Task $task)
    {
        $this->authorize('update', $task);

        if (! $task->timer_started_at) {
            $now = now();

            $task->update([
                'status'           => 'in_progress',
                'timer_started_at' => $now,
            ]);

            TimerSession::create([
                'task_id'    => $task->id,
                'user_id'    => Auth::id(),
                'started_at' => $now,
            ]);
        }

        $task->refresh();

        return response()->json([
            'timer_started_at' => $task->timer_started_at->toISOString(),
            'focus_minutes'    => (int) $task->focus_minutes,
        ]);
    }

    // ── Timer: stop ──────────────────────────────────────────────────
public function stopTimer(Task $task)
{
    $this->authorize('update', $task);

    if ($task->timer_started_at) {
        $elapsedSeconds = (int) $task->timer_started_at->diffInSeconds(now());
        $elapsedMinutes = (int) floor($elapsedSeconds / 60);

        $this->stopActiveSession($task, $elapsedSeconds); // kirim detik

        $task->update([
            'focus_minutes'    => $task->focus_minutes + $elapsedMinutes,
             'total_seconds' => (int) TimerSession::where('task_id', $task->id)  // ← tambah
                        ->whereNotNull('stopped_at')
                        ->sum('duration_seconds'),
            'timer_started_at' => null,
        ]);

        Notification::create([
            'user_id'     => Auth::id(),
            'task_id'     => $task->id,
            'type'        => 'alarm',
            'title'       => 'Alarm Timer',
            'description' => "Sesi fokus pada {$task->title} selesai!",
        ]);
    }

    $task->refresh();
    return response()->json(['focus_minutes' => (int) $task->focus_minutes]);
}

    // ── Timer: history ───────────────────────────────────────────────

    public function timerHistory(Task $task)
    {
        $this->authorize('update', $task);

        $sessions = TimerSession::where('task_id', $task->id)
            ->whereNotNull('stopped_at')
            ->orderByDesc('started_at')
            ->get()
            ->map(fn($s) => [
                'id'               => $s->id,
                'started_at'       => $s->started_at->toISOString(),
                'stopped_at'       => $s->stopped_at->toISOString(),
                'duration_minutes' => $s->duration_minutes,
                'duration_seconds' => $s->duration_seconds ?? ($s->duration_minutes * 60), // ← tambah
                'date_label'       => $s->started_at->locale('id')->isoFormat('dddd, D MMM YYYY'),
                'time_label'       => $s->started_at->format('H:i') . ' – ' . $s->stopped_at->format('H:i'),
                'formatted'        => $s->formattedDuration(),
            ]);

        return response()->json([
            'task_title'    => $task->title,
            'total_minutes' => (int) $task->focus_minutes,
            'sessions'      => $sessions,
        ]);
    }

    // ── Toggle subtask ───────────────────────────────────────────────

    public function toggleSubtask(Task $task, int $index)
    {
        $this->authorize('update', $task);

        $subtasks = is_array($task->subtasks) ? $task->subtasks : [];

        if (! isset($subtasks[$index])) {
            return response()->json(['error' => 'Subtask not found'], 404);
        }

        $subtasks[$index]['done'] = ! ($subtasks[$index]['done'] ?? false);
        $task->update(['subtasks' => array_values($subtasks)]);

        $task->refresh();
        $fresh    = $task->subtasks ?? [];
        $total    = count($fresh);
        $done     = collect($fresh)->where('done', true)->count();

        return response()->json([
            'progress' => $total > 0 ? round(($done / $total) * 100) : 0,
            'subtasks' => $fresh,
        ]);
    }

    // ── Trash ────────────────────────────────────────────────────────

    public function trash()
    {
        $tasks = Task::onlyTrashed()->where('user_id', Auth::id())->latest()->get();
        return view('tasks.trash', compact('tasks'));
    }

    public function restore(int $id)
    {
        $task = Task::withTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $task->restore();
        return response()->json(['success' => true]);
    }

    // ── Show ─────────────────────────────────────────────────────────

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return response()->json($this->serializeTask($task));
    }

    // ── Export PDF ───────────────────────────────────────────────────

    public function exportPdf()
    {
        $tasks = Task::forUser(Auth::id())->latest()->get();
        $pdf   = Pdf::loadView('pdf.tasks', compact('tasks'));
        return $pdf->download('task-report.pdf');
    }

    // ── Export CSV ───────────────────────────────────────────────────

    public function exportCsv()
    {
        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['ID','Title','Description','Category','Priority','Status','Due Date','Focus Minutes','Created At'], ';');

            Task::forUser(Auth::id())->latest()->get()->each(function ($task) use ($file) {
                fputcsv($file, [
                    $task->id, $task->title, $task->description,
                    $task->category, $task->priority, $task->status,
                    $task->due_date, $task->focus_minutes, $task->created_at,
                ], ';');
            });

            fclose($file);
        }, 'task-report.csv');
    }

    // ── Private helpers ──────────────────────────────────────────────

    /** Close the open TimerSession when timer is stopped */
    private function stopActiveSession(Task $task, int $elapsedSeconds): void
{
    $session = TimerSession::where('task_id', $task->id)
        ->whereNull('stopped_at')
        ->latest('started_at')
        ->first();

    if ($session) {
        $session->update([
            'stopped_at'       => now(),
            'duration_seconds' => $elapsedSeconds,                    // kolom baru
            'duration_minutes' => (int) floor($elapsedSeconds / 60),  // tetap ada untuk kompatibilitas
        ]);
    }
}
    /** Serialize task model to array for JS */
    private function serializeTask(Task $task): array
    {
        $subtasks = is_array($task->subtasks) ? $task->subtasks : [];

        return [
            'id'               => (int)    $task->id,
            'title'            => (string) $task->title,
            'description'      => (string) ($task->description ?? ''),
            'category'         => (string) ($task->category ?? ''),
            'priority'         => (string) ($task->priority ?? ''),
            'status'           => (string) ($task->status ?? 'todo'),
            'due_date'         => $task->due_date    ? $task->due_date->format('Y-m-d')         : null,
            'reminder_at'      => $task->reminder_at ? $task->reminder_at->format('Y-m-d\TH:i') : null,
            'subtasks'         => array_values($subtasks),
            'total_minutes' => (int) TimerSession::where('task_id', $task->id)
                            ->whereNotNull('stopped_at')
                            ->sum('duration_minutes'),
            'total_seconds' => (int) TimerSession::where('task_id', $task->id)
                            ->whereNotNull('stopped_at')
                            ->sum('duration_seconds'),
            'timer_started_at' => $task->timer_started_at ? $task->timer_started_at->toISOString() : null,
        ];
    }

    
}