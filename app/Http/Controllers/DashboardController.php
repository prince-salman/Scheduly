<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // ── Stats ─────────────────────────────────────────────────

        $tasksToday  = Task::forUser($userId)->whereDate('due_date', today())->count();
        $tasksDone   = Task::forUser($userId)->done()->whereDate('updated_at', today())->count();
        $focusToday  = Task::forUser($userId)->whereDate('updated_at', today())->sum('focus_minutes');

        // Streak: consecutive days with at least 1 completed task
        $streak = $this->calculateStreak($userId);

        // ── Task board preview ────────────────────────────────────

        $columns = [
            'todo'        => Task::forUser($userId)->todo()->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")->take(5)->get(),
            'in_progress' => Task::forUser($userId)->inProgress()->latest('updated_at')->take(3)->get(),
            'done'        => Task::forUser($userId)->done()->latest('updated_at')->take(3)->get(),
        ];

        // ── Unread notification count ─────────────────────────────

        $unreadCount = Notification::forUser($userId)->unread()->count();

        return view('dashboard.index', compact(
            'tasksToday',
            'tasksDone',
            'focusToday',
            'streak',
            'columns',
            'unreadCount',
        ));
    }

    // ── Private helpers ────────────────────────────────────────────

    private function calculateStreak(int $userId): int
    {
        $streak = 0;
        $date   = Carbon::today();

        while (true) {
            $hasDone = Task::forUser($userId)
                ->done()
                ->whereDate('updated_at', $date->toDateString())
                ->exists();

            if (!$hasDone) break;

            $streak++;
            $date->subDay();

            if ($streak >= 365) break; // safety cap
        }

        return $streak;
    }
}