{{-- File: resources/views/tasks/_card.blade.php --}}
@php
    $catClass = match(strtolower($task->category ?? '')) {
        'design'      => 'chip-design',
        'development' => 'chip-dev',
        'planning'    => 'chip-planning',
        'personal'    => 'chip-personal',
        'meeting'     => 'chip-meeting',
        default       => 'chip-design',
    };

    $subtasks   = is_array($task->subtasks) ? $task->subtasks : [];
    $totalSub   = count($subtasks);
    $doneSub    = collect($subtasks)->where('done', true)->count();
    $progress   = $totalSub > 0 ? round(($doneSub / $totalSub) * 100) : 0;

    $taskData = [
        'id'               => $task->id,
        'title'            => $task->title,
        'description'      => $task->description,
        'category'         => $task->category,
        'priority'         => $task->priority,
        'status'           => $task->status,
        'due_date'         => $task->due_date?->format('Y-m-d'),
        'reminder_at'      => $task->reminder_at?->format('Y-m-d\TH:i'),
        'subtasks'         => $subtasks,
        'focus_minutes'    => (int) ($task->focus_minutes ?? 0),
        'total_seconds'    => (int) \App\Models\TimerSession::where('task_id', $task->id)  // ← tambah
                            ->whereNotNull('stopped_at')
                            ->sum('duration_seconds'),
        'timer_started_at' => $task->timer_started_at?->toISOString(),
    ];
@endphp

<div class="task-card"
     draggable="true"
     data-task-id="{{ $task->id }}"
     data-task='@json($taskData)'
     ondragstart="handleDragStart(event)">

    {{-- action buttons --}}
    <div class="card-actions">
        {{-- Progress --}}
        <button class="card-action-btn" title="Lihat Progres"
                onclick="event.stopPropagation(); openProgressModal(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
        </button>

        {{-- Timer history --}}
        <button class="card-action-btn" title="Riwayat Timer"
                onclick="event.stopPropagation(); openTimerHistoryModal(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </button>

        {{-- Edit --}}
        <button class="card-action-btn" title="Edit Task"
                onclick="event.stopPropagation(); openEditModal(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
        </button>

        {{-- Delete --}}
        <button class="card-action-btn danger" title="Hapus Task"
                onclick="event.stopPropagation(); deleteTask({{ $task->id }})">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14H6L5 6"/>
                <path d="M10 11v6"/><path d="M14 11v6"/>
                <path d="M9 6V4h6v2"/>
            </svg>
        </button>
    </div>

    {{-- chips --}}
    <div class="card-chips">
        @if($task->category)
            <span class="chip {{ $catClass }}">{{ $task->category }}</span>
        @endif
        @if($task->priority)
            <span class="chip chip-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
        @endif
    </div>

    {{-- title --}}
    <div class="card-title {{ $task->status === 'done' ? 'strikethrough' : '' }}">
        {{ $task->title }}
    </div>

    {{-- progress bar --}}
    @if($totalSub > 0)
        <div class="card-progress-label">
            <span>Progress</span>
            <span class="card-progress-pct">{{ $progress }}%</span>
        </div>
        <div class="card-progress-wrap">
            <div class="card-progress-fill" style="width:{{ $progress }}%"></div>
        </div>
    @endif


    {{-- meta row --}}
    <div class="card-meta">

        @if($task->status === 'done')
            <span class="card-sub green">✓ {{ $doneSub }}/{{ $totalSub ?: 1 }} subtasks</span>

        @elseif($task->status === 'in_progress' && $task->timer_started_at)
            {{-- Active timer badge --}}
            <div class="timer-badge">
                <button class="timer-stop-btn" title="Stop timer"></button>
                <span class="timer-display"
                      data-timer-start="{{ $task->timer_started_at->toISOString() }}"
                      data-timer-base-seconds="{{ $taskData['total_seconds'] }}">
                    @php
                        $base    = (int)($task->focus_minutes ?? 0) * 60;
                        $elapsed = $base + now()->diffInSeconds($task->timer_started_at);
                        $th = str_pad(floor($elapsed / 3600), 2, '0', STR_PAD_LEFT);
                        $tm = str_pad(floor(($elapsed % 3600) / 60), 2, '0', STR_PAD_LEFT);
                        $ts = str_pad($elapsed % 60, 2, '0', STR_PAD_LEFT);
                    @endphp
                    {{ $th }}:{{ $tm }}:{{ $ts }}
                </span>
            </div>

        @else
            @if($task->due_date)
                <span class="card-sub">📅 {{ $task->due_date->format('d M Y') }}</span>
            @elseif($totalSub > 0)
                <span class="card-sub">{{ $doneSub }}/{{ $totalSub }} subtasks</span>
            @else
                <span class="card-sub"></span>
            @endif

            @if($task->status !== 'done')
                <button class="btn-start-timer">▶ Start timer</button>
            @endif
        @endif

        <div class="avatar me">{{ auth()->user()->initials }}</div>
    </div>
</div>