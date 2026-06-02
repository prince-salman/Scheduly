<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TimerSession;
use Illuminate\Support\Facades\Auth;

class TimeAllocationController extends Controller
{
  public function index()
{
    $userId = Auth::id();

    $sessions = TimerSession::with('task')
        ->where('user_id', $userId)
        ->whereNotNull('stopped_at')
        ->orderByDesc('started_at')
        ->get();

    // Gunakan detik sebagai basis, bukan menit
    $grandTotalSeconds = (int) $sessions->sum(fn($s) =>
        $s->duration_seconds > 0 ? $s->duration_seconds : $s->duration_minutes * 60
    );
    $totalSessionCount = $sessions->count();

    $taskSummaries = $sessions
        ->groupBy('task_id')
        ->map(function ($group) use ($grandTotalSeconds) {
            $task         = $group->first()->task;
            $totalSeconds = (int) $group->sum(fn($s) =>
                $s->duration_seconds > 0 ? $s->duration_seconds : $s->duration_minutes * 60
            );
            $pct = $grandTotalSeconds > 0
                ? round(($totalSeconds / $grandTotalSeconds) * 100, 1)
                : 0;

            return [
                'task_id'         => $task->id,
                'title'           => $task->title,
                'category'        => $task->category,
                'status'          => $task->status,
                'total_seconds'   => $totalSeconds,
                'formatted_total' => self::fmtSec($totalSeconds),
                'pct'             => $pct,
                'sessions'        => $group->map(fn($s) => [
                    'date'             => $s->started_at->locale('id')->isoFormat('D MMM YYYY'),
                    'time'             => $s->started_at->format('H:i') . '–' . $s->stopped_at->format('H:i'),
                    'duration_seconds' => $s->duration_seconds > 0
                                            ? $s->duration_seconds
                                            : $s->duration_minutes * 60,
                    'formatted'        => $s->formattedDuration(),
                ])->values()->toArray(),
            ];
        })
        ->sortByDesc('total_seconds')
        ->values();

    $grandTotalFormatted = self::fmtSec($grandTotalSeconds);
    $grandTotalHtml      = self::fmtSecHtml($grandTotalSeconds);

    return view('time-allocation.index', compact(
        'taskSummaries',
        'grandTotalFormatted',
        'grandTotalHtml',
        'totalSessionCount',
    ));
}

private static function fmtSec(int $seconds): string
{
    $h = intdiv($seconds, 3600);
    $m = intdiv($seconds % 3600, 60);
    $s = $seconds % 60;

    if ($h > 0) return "{$h}j {$m}m {$s}d";
    if ($m > 0) return "{$m}m {$s}d";
    return "{$s}d";
}

private static function fmtSecHtml(int $seconds): string
{
    $h = intdiv($seconds, 3600);
    $m = intdiv($seconds % 3600, 60);
    $s = $seconds % 60;
    $u = 'font-size:16px;font-weight:600;color:#797582';

    if ($h > 0) return "{$h}<span style='{$u}'>j</span> {$m}<span style='{$u}'>m</span> {$s}<span style='{$u}'>d</span>";
    if ($m > 0) return "{$m}<span style='{$u}'>m</span> {$s}<span style='{$u}'>d</span>";
    return "{$s}<span style='{$u}'>d</span>";
}
}