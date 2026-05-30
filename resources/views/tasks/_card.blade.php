{{-- Reusable task card partial --}}
@php
    $catClass = match(strtolower($task->category ?? '')) {
        'design'      => 'chip-design',
        'development' => 'chip-dev',
        'planning'    => 'chip-planning',
        'personal'    => 'chip-personal',
        'meeting'     => 'chip-meeting',
        default       => 'chip-design',
    };
    $focusH = floor($task->focus_minutes / 60);
    $focusM = $task->focus_minutes % 60;
    $focusLabel = $focusH > 0 ? "{$focusH}j {$focusM}m" : "{$focusM}m";
@endphp

<div class="task-card"
     draggable="true"
     data-task-id="{{ $task->id }}"
     ondragstart="handleDragStart(event)">

    {{-- category + priority chips --}}
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

    {{-- progress bar (only when there are subtasks) --}}
    @if($task->subtask_count > 0)
        <div class="card-progress-label">
            <span>Progress</span>
            <span>{{ $task->progress }}%</span>
        </div>
        <div class="card-progress-wrap">
            <div class="card-progress-fill" style="width:{{ $task->progress }}%"></div>
        </div>
    @endif

    <div class="card-meta">
        @if($task->status === 'done')
            {{-- done: subtask count + logged time --}}
            <span class="card-sub green">
                ✓ {{ $task->done_subtask_count }} / {{ $task->subtask_count ?: 1 }} subtasks
            </span>
            @if($task->focus_minutes > 0)
                <span class="card-sub">{{ $focusLabel }} logged</span>
            @endif

        @elseif($task->status === 'in_progress' && $task->timer_started_at)
            {{-- live timer --}}
            <div class="timer-badge">
                <button class="timer-stop-btn" title="Stop timer"></button>
                <span data-timer-start="{{ $task->timer_started_at->toISOString() }}"
                      data-timer-base="{{ $task->focus_minutes }}">
                    {{-- initial value rendered server-side --}}
                    @php
                        $elapsed = $task->focus_minutes * 60 + now()->diffInSeconds($task->timer_started_at);
                        $th = str_pad(floor($elapsed/3600),2,'0',STR_PAD_LEFT);
                        $tm = str_pad(floor(($elapsed%3600)/60),2,'0',STR_PAD_LEFT);
                        $ts = str_pad($elapsed%60,2,'0',STR_PAD_LEFT);
                    @endphp
                    {{ $th }}:{{ $tm }}:{{ $ts }}
                </span>
            </div>

        @else
            {{-- todo / in_progress without active timer --}}
            @if($task->due_date)
                <span class="card-sub {{ $task->due_date->isPast() && $task->status !== 'done' ? 'green' : '' }}">
                    📅 {{ $task->due_date->format('d M Y') }}
                </span>
            @elseif($task->subtask_count > 0)
                <span class="card-sub">{{ $task->done_subtask_count }} / {{ $task->subtask_count }} subtasks</span>
            @else
                <span class="card-sub"></span>
            @endif

            @if($task->status !== 'done')
                <button class="btn-start-timer">▶ Start timer</button>
            @endif
        @endif

        {{-- user avatar --}}
        <div class="avatar me">{{ auth()->user()->initials }}</div>
    </div>

</div>