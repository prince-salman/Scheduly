<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'color',      // primary | secondary | tertiary
        'starts_at',
        'ends_at',
        'task_id',    // optional link to a task
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeForWeek($query, \Carbon\Carbon $weekStart)
    {
        return $query->whereBetween('starts_at', [
            $weekStart->startOfWeek(\Carbon\Carbon::MONDAY),
            $weekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY),
        ]);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('starts_at', $year)
                     ->whereMonth('starts_at', $month);
    }

    // ── Helpers ────────────────────────────────────────────────────

    /** Pixel offset from top of hour grid (hour 08:00 = 0, each hour = 72px) */
    public function getTopPxAttribute(): int
    {
        $startHour = 8;
        $diffMinutes = ($this->starts_at->hour - $startHour) * 60 + $this->starts_at->minute;
        return (int) round($diffMinutes * (72 / 60));
    }

    /** Height in pixels based on duration */
    public function getHeightPxAttribute(): int
    {
        $durationMinutes = $this->starts_at->diffInMinutes($this->ends_at);
        return max((int) round($durationMinutes * (72 / 60)) - 4, 20);
    }
}