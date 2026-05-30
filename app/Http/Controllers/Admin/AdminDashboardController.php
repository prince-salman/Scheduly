<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();

        $lastWeekUsers = User::where(
            'created_at',
            '<',
            now()->subWeek()
        )->count();

        $userGrowthPct = $lastWeekUsers > 0
            ? round((($totalUsers - $lastWeekUsers) / $lastWeekUsers) * 100, 1)
            : 0;

        $tasksCompleted = Task::where('status', 'done')->count();

        $tasksCompletedPrev = Task::where('status', 'done')
            ->where('updated_at', '<', now()->subWeek())
            ->count();

        $tasksGrowthPct = $tasksCompletedPrev > 0
            ? round((($tasksCompleted - $tasksCompletedPrev) / $tasksCompletedPrev) * 100, 1)
            : 0;

        $pendingCount = User::where('status', 'pending')->count();

        $reportedCount = 3;

        $activityData = collect(range(13, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->toDateString();

            return [
                'label' => now()->subDays($daysAgo)->format('M d'),
                'value' => User::whereDate('last_login_at', $date)->count(),
            ];
        });

        $taskDistribution = Task::select(
                'category',
                DB::raw('count(*) as total')
            )
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                return [
                    'label' => $row->category,
                    'value' => $row->total,
                ];
            });

        $pendingUsers = User::where('status', 'pending')
            ->latest()
            ->take(4)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'userGrowthPct',
            'tasksCompleted',
            'tasksGrowthPct',
            'pendingCount',
            'reportedCount',
            'activityData',
            'taskDistribution',
            'pendingUsers'
        ));
    }
}