<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{


    public function index(Request $request)
    {
        $userId = Auth::id();
        $days   = (int) $request->get('days', 7);

        // ── Stat cards ────────────────────────────────────────────

        $totalDone      = Task::forUser($userId)->done()->count();
        $doneThisPeriod = Task::forUser($userId)->done()
            ->where('updated_at', '>=', now()->subDays($days))
            ->count();

        $totalFocusMinutes = (int) Task::forUser($userId)->sum('focus_minutes');
        $focusThisPeriod   = (int) Task::forUser($userId)
            ->where('updated_at', '>=', now()->subDays($days))
            ->sum('focus_minutes');

        // Format: jam jika >= 60, menit jika < 60
        $focusTotalLabel  = self::formatFocus($totalFocusMinutes);
        $focusPeriodLabel = self::formatFocus($focusThisPeriod);

        $createdInPeriod = Task::forUser($userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
        $doneInPeriod = Task::forUser($userId)->done()
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
        $productivityPct = $createdInPeriod > 0
            ? min(100, (int) round(($doneInPeriod / $createdInPeriod) * 100))
            : 0;

        $weekNumber = now()->isoWeek();

        // ── Daily productivity chart ───────────────────────────────
        $productivityChart = collect(range($days - 1, 0))->map(function (int $daysAgo) use ($userId) {
            $date = now()->subDays($daysAgo)->toDateString();
            $done = Task::forUser($userId)
                ->done()
                ->whereDate('updated_at', $date)
                ->count();
            return [
                'label' => now()->subDays($daysAgo)->locale('id')->isoFormat('D MMM'),
                'value' => $done,
            ];
        });

        // ── Focus chart (nilai = menit integer) ───────────────────
        $focusChart = collect(range($days - 1, 0))->map(function (int $daysAgo) use ($userId) {
            $date    = now()->subDays($daysAgo)->toDateString();
            $minutes = (int) Task::forUser($userId)
                ->whereDate('updated_at', $date)
                ->sum('focus_minutes');
            return [
                'label' => now()->subDays($daysAgo)->locale('id')->isoFormat('D MMM'),
                'value' => $minutes,
            ];
        });

        // ── Task history table ─────────────────────────────────────
        $taskHistory = Task::forUser($userId)
            ->whereIn('status', ['done', 'in_progress'])
            ->latest('updated_at')
            ->paginate(6);

           
        return view('analytics.index', compact(
            'totalDone', 'doneThisPeriod',
            'totalFocusMinutes', 'focusThisPeriod',
            'focusTotalLabel', 'focusPeriodLabel',
            'productivityPct', 'weekNumber',
            'productivityChart', 'focusChart',
            'taskHistory', 'days',
        ));
    }

    // helper: kembalikan ['value' => ..., 'unit' => ...]
    private static function formatFocus(int $minutes): array
    {
        if ($minutes >= 60) {
            $h = floor($minutes / 60);
            $m = $minutes % 60;
            return ['value' => $h, 'unit' => $m > 0 ? "Jam {$m} mnt" : 'Jam'];
        }
        return ['value' => $minutes, 'unit' => 'mnt'];
    }
}