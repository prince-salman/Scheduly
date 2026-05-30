<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'due_date',
        'reminder_at',
        'focus_minutes',
        'timer_started_at',
        'subtasks',
    ];

    protected $casts = [
        'due_date'         => 'date',
        'reminder_at'      => 'datetime',
        'timer_started_at' => 'datetime',
        'subtasks'         => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeTodo($query)
    {
        return $query->where('status', 'todo');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today());
    }

    public function scopeOverdue($query)
    {
        return $query->whereDate('due_date', '<', today())
                     ->where('status', '!=', 'done');
    }

    // ── Helpers ────────────────────────────────────────────────────

    /** Returns live elapsed seconds if timer is running, else focus_minutes * 60 */
    public function getElapsedSecondsAttribute(): int
    {
        if ($this->timer_started_at) {
            return (int) now()->diffInSeconds($this->timer_started_at) + ($this->focus_minutes * 60);
        }
        return $this->focus_minutes * 60;
    }

    public function getSubtaskCountAttribute(): int
    {
        return count($this->subtasks ?? []);
    }

    public function getDoneSubtaskCountAttribute(): int
    {
        return collect($this->subtasks ?? [])->where('done', true)->count();
    }

    /** Progress percentage based on done subtasks */
    public function getProgressAttribute(): int
    {
        if (!$this->subtask_count) return $this->status === 'done' ? 100 : 0;
        return (int) round(($this->done_subtask_count / $this->subtask_count) * 100);
    }
}