<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimerSession extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'started_at',
        'stopped_at',
        'duration_minutes',
        'duration_seconds'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Format duration: "1j 30m" atau "45m" */
   public function formattedDuration(): string
{
    $total = $this->duration_seconds > 0
        ? $this->duration_seconds
        : $this->duration_minutes * 60;

    $h = intdiv($total, 3600);
    $m = intdiv($total % 3600, 60);
    $s = $total % 60;

    if ($h > 0) return "{$h}j {$m}m {$s}d";
    if ($m > 0) return "{$m}m {$s}d";
    return "{$s}d";
}
}