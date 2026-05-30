<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Default to current week; allow ?week=YYYY-MM-DD to navigate
        $weekStart = $request->get('week')
            ? Carbon::parse($request->get('week'))->startOfWeek(Carbon::MONDAY)
            : now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Days of current week for the header
        $days = collect(range(0, 6))->map(fn($i) => $weekStart->copy()->addDays($i));

        // Events for the week
        $events = CalendarEvent::where('user_id', $userId)
            ->whereBetween('starts_at', [$weekStart, $weekEnd])
            ->get()
            ->groupBy(fn($e) => $e->starts_at->dayOfWeekIso - 1); // 0 = Monday

        // Tasks due this week (shown in sidebar / month dots)
        $tasksDueThisWeek = Task::forUser($userId)
            ->whereBetween('due_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('due_date')
            ->get();

        // Today's tasks for sidebar
        $todayTasks = Task::forUser($userId)
            ->where(function ($q) {
                $q->whereDate('due_date', today())
                  ->orWhere('status', 'in_progress');
            })
            ->orderBy('status')
            ->take(4)
            ->get();

        // Focus progress today
        $focusToday = Task::forUser($userId)
            ->whereDate('updated_at', today())
            ->sum('focus_minutes');
        $focusGoal  = 360; // 6 hours in minutes

        // Month grid data (for monthly view)
        $monthYear  = $weekStart->year;
        $monthMonth = $weekStart->month;

        $monthEvents = CalendarEvent::where('user_id', $userId)
            ->whereYear('starts_at', $monthYear)
            ->whereMonth('starts_at', $monthMonth)
            ->get()
            ->groupBy(fn($e) => $e->starts_at->day);

        $monthTasksDue = Task::forUser($userId)
            ->whereYear('due_date', $monthYear)
            ->whereMonth('due_date', $monthMonth)
            ->get()
            ->groupBy(fn($t) => $t->due_date->day);

        return view('calendar.index', compact(
            'weekStart',
            'weekEnd',
            'days',
            'events',
            'tasksDueThisWeek',
            'todayTasks',
            'focusToday',
            'focusGoal',
            'monthEvents',
            'monthTasksDue',
            'monthYear',
            'monthMonth',
        ));
    }

    // ── Store new event ────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'color'       => 'nullable|in:primary,secondary,tertiary',
            'starts_at'   => 'required|date',
            'ends_at'     => 'required|date|after:starts_at',
            'task_id'     => 'nullable|exists:tasks,id',
        ]);

        $event = CalendarEvent::create([
            ...$validated,
            'user_id' => Auth::id(),
            'color'   => $validated['color'] ?? 'primary',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['event' => $event], 201);
        }

        return back()->with('success', 'Event berhasil ditambahkan.');
    }

    // ── Delete event ───────────────────────────────────────────────

    public function destroy(CalendarEvent $event)
    {
        $this->authorize('delete', $event);
        $event->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Event deleted']);
        }

        return back()->with('success', 'Event dihapus.');
    }
}