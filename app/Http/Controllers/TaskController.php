<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Barryvdh\DomPDF\Facade\Pdf;

class TaskController extends Controller
{
    use AuthorizesRequests;
    public function board()
    {
        $userId = Auth::id();

        $columns = [
            'todo' => Task::forUser($userId)
                ->todo()
                ->latest()
                ->get(),

            'in_progress' => Task::forUser($userId)
                ->inProgress()
                ->latest()
                ->get(),

            'done' => Task::forUser($userId)
                ->done()
                ->latest()
                ->get(),
        ];

        return view('tasks.board', compact('columns'));
    }


    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return response()->json($task);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'nullable|in:todo,in_progress,done',
            'due_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
            'subtasks' => 'nullable|array',
        ]);

        $task = Task::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'] ?? 'todo',
            'due_date' => $validated['due_date'] ?? null,
            'reminder_at' => $validated['reminder_at'] ?? null,
            'subtasks' => $validated['subtasks'] ?? [],
        ]);

        if ($task->reminder_at) {
            Notification::create([
                'user_id' => Auth::id(),
                'task_id' => $task->id,
                'type' => 'reminder',
                'title' => 'Reminder Task',
                'description' => $task->title,
            ]);
        }

        return response()->json([
            'success' => true,
            'task' => $task
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,in_progress,done',
            'due_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
            'subtasks' => 'nullable|array',
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'task' => $task->fresh()
        ]);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function moveStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'status' => 'required|in:todo,in_progress,done'
        ]);

        $task->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function startTimer(Task $task)
    {
        $this->authorize('update', $task);

        if (!$task->timer_started_at) {

            $task->update([
                'status' => 'in_progress',
                'timer_started_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'started_at' => $task->timer_started_at,
        ]);
    }

    public function stopTimer(Task $task)
    {
        $this->authorize('update', $task);

        if ($task->timer_started_at) {

            $minutes = now()
                ->diffInMinutes($task->timer_started_at);

            $task->update([
                'focus_minutes' =>
                    $task->focus_minutes + $minutes,

                'timer_started_at' => null
            ]);

            Notification::create([
                'user_id' => Auth::id(),
                'task_id' => $task->id,
                'type' => 'alarm',
                'title' => 'Focus Session',
                'description' =>
                    "Sesi fokus {$minutes} menit selesai",
            ]);
        }

        return response()->json([
            'success' => true,
            'focus_minutes' => $task->focus_minutes
        ]);
    }

    public function toggleSubtask(Task $task, $index)
    {
        $this->authorize('update', $task);

        $subtasks = $task->subtasks ?? [];

        if (isset($subtasks[$index])) {

            $subtasks[$index]['done']
                = !$subtasks[$index]['done'];

            $task->update([
                'subtasks' => $subtasks
            ]);
        }

        return response()->json([
            'progress' => $task->fresh()->progress
        ]);
    }

    public function trash()
    {
        $tasks = Task::onlyTrashed()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('tasks.trash', compact('tasks'));
    }

    public function restore($id)
    {
        $task = Task::withTrashed()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $task->restore();

        return response()->json([
            'success' => true
        ]);
    }

    public function exportPdf()
{
    $tasks = Task::latest()->get();

    $pdf = Pdf::loadView('pdf.tasks', compact('tasks'));

    return $pdf->download('task-report.pdf');
}

public function exportCsv()
{
    $filename = 'tasks.csv';

    return response()->streamDownload(function () {

        $file = fopen('php://output', 'w');

        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($file, [
            'ID',
            'Title',
            'Category',
            'Priority',
            'Status',
            'Due Date',
            'Focus Minutes'
        ], ';');

        foreach (Task::latest()->get() as $task) {
            fputcsv($file, [
                $task->id,
                $task->title,
                $task->category,
                $task->priority,
                $task->status,
                $task->due_date,
                $task->focus_minutes,
            ], ';');
        }

        fclose($file);

    }, $filename);
}
}