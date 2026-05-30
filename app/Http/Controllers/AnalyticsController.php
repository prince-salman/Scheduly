<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $days   = (int) $request->get('days', 7); // 7 | 30

        // ── Stat cards ────────────────────────────────────────────

        $totalDone   = Task::forUser($userId)->done()->count();
        $donePrevPeriod = Task::forUser($userId)->done()
            ->where('updated_at', '<', now()->subDays($days))
            ->count();
        $doneThisPeriod = Task::forUser($userId)->done()
            ->where('updated_at', '>=', now()->subDays($days))
            ->count();

        $totalFocusMinutes = Task::forUser($userId)->sum('focus_minutes');
        $focusThisPeriod   = Task::forUser($userId)
            ->where('updated_at', '>=', now()->subDays($days))
            ->sum('focus_minutes');

        // Productivity = % of tasks done vs created in the period
        $createdInPeriod = Task::forUser($userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
        $doneInPeriod = Task::forUser($userId)->done()
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
        $productivityPct = $createdInPeriod > 0
            ? min(100, (int) round(($doneInPeriod / $createdInPeriod) * 100))
            : 0;

        // Current ISO week number
        $weekNumber = now()->isoWeek();

        // ── Daily productivity chart (last N days) ─────────────────

        $productivityChart = collect(range($days - 1, 0))->map(function (int $daysAgo) use ($userId) {
            $date  = now()->subDays($daysAgo)->toDateString();
            $label = now()->subDays($daysAgo)->locale('id')->isoFormat('ddd');

            $created = Task::forUser($userId)->whereDate('created_at', $date)->count();
            $done    = Task::forUser($userId)->done()->whereDate('updated_at', $date)->count();

            return [
                'label' => $label,
                'value' => $created > 0 ? min(100, (int) round(($done / $created) * 100)) : 0,
            ];
        });

        // ── Focus hours chart (last N days) ───────────────────────

        $focusChart = collect(range($days - 1, 0))->map(function (int $daysAgo) use ($userId) {
            $date  = now()->subDays($daysAgo)->toDateString();
            $label = now()->subDays($daysAgo)->locale('id')->isoFormat('ddd');

            $minutes = Task::forUser($userId)
                ->whereDate('updated_at', $date)
                ->sum('focus_minutes');

            return [
                'label' => $label,
                'value' => round($minutes / 60, 1),
            ];
        });

        // ── Task completion history table ─────────────────────────

        $taskHistory = Task::forUser($userId)
            ->whereIn('status', ['done', 'in_progress'])
            ->with('user')
            ->latest('updated_at')
            ->paginate(6);

        return view('analytics.index', compact(
            'totalDone',
            'doneThisPeriod',
            'totalFocusMinutes',
            'focusThisPeriod',
            'productivityPct',
            'weekNumber',
            'productivityChart',
            'focusChart',
            'taskHistory',
            'days',
        ));
    }
}