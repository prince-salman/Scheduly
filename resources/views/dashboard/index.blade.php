@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>

    .dashboard-topbar{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:16px;
    margin-bottom:28px;
}

.logout-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:12px 18px;
    border:none;
    border-radius:14px;
    background:#ba1a1a;
    color:#fff;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    transition:.2s;
    box-shadow:0 8px 20px rgba(186,26,26,.18);
}

.logout-btn:hover{
    background:#981414;
    transform:translateY(-1px);
}

.logout-form{
    margin:0;
}
    .dashboard-greeting { margin-bottom:28px; }
    .dashboard-greeting h1 { font-size:24px; font-weight:800; color:#1c1b20; margin-bottom:4px; }
    .dashboard-greeting p  { font-size:14px; color:#797582; }

    .stat-cards { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
    .stat-card { background:#fff; border-radius:24px; padding:22px 24px; box-shadow:0 8px 20px rgba(181,162,255,.15); border:1px solid #f0eaf8; position:relative; overflow:hidden; }
    .stat-card::before { content:''; position:absolute; top:0; right:0; width:60px; height:60px; border-radius:0 24px 0 60px; opacity:.08; }
    .stat-card.card-primary::before  { background:#6351a7; }
    .stat-card.card-secondary::before { background:#006a61; }
    .stat-card.card-tertiary::before  { background:#6a5f00; }
    .stat-card.card-focus::before     { background:#6351a7; }
    .stat-card-label { font-size:12px; font-weight:600; color:#797582; text-transform:uppercase; letter-spacing:.6px; margin-bottom:10px; }
    .stat-card-value { font-size:32px; font-weight:800; color:#1c1b20; line-height:1.1; margin-bottom:6px; }
    .stat-card-value span.unit { font-size:16px; font-weight:600; color:#797582; }
    .stat-card-sub { font-size:12px; color:#797582; }
    .stat-card-sub.positive { color:#006a61; }
    .stat-card-sub.warning  { color:#6a5f00; }

    .dashboard-grid  { display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; }
    .dashboard-left  { display:flex; flex-direction:column; gap:20px; }
    .dashboard-right { display:flex; flex-direction:column; gap:20px; }

    .section-card { background:#fff; border-radius:24px; padding:24px; box-shadow:0 8px 20px rgba(181,162,255,.15); border:1px solid #f0eaf8; }
    .section-card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
    .section-card-title  { font-size:16px; font-weight:700; color:#1c1b20; }
    .section-card-link   { font-size:13px; font-weight:600; color:#6351a7; text-decoration:none; }
    .section-card-link:hover { text-decoration:underline; }

    .task-list { display:flex; flex-direction:column; gap:10px; }
    .task-item { display:flex; align-items:center; gap:14px; padding:12px 16px; border-radius:14px; background:#fdf7ff; border:1px solid #ede9ff; transition:background .15s; }
    .task-item:hover { background:#f3eeff; }

    .task-checkbox { width:22px; height:22px; border-radius:50%; border:2px solid #cac4d3; flex-shrink:0; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:border-color .15s,background .15s; }
    .task-checkbox:hover { border-color:#6351a7; }
    .task-checkbox.checked { background:#006a61; border-color:#006a61; }
    .task-checkbox.checked::after { content:''; width:6px; height:10px; border:2px solid #fff; border-top:none; border-left:none; transform:rotate(45deg) translateY(-1px); }

    .task-info { flex:1; min-width:0; }
    .task-name { font-size:14px; font-weight:600; color:#1c1b20; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:3px; }
    .task-name.done { text-decoration:line-through; color:#797582; }
    .task-deadline { font-size:12px; color:#797582; }
    .task-deadline.overdue { color:#ba1a1a; }

    .chip { display:inline-flex; align-items:center; padding:3px 10px; border-radius:100px; font-size:11px; font-weight:600; white-space:nowrap; }
    .chip-design   { background:#ede9ff; color:#6351a7; }
    .chip-dev      { background:#d0f5f3; color:#006a61; }
    .chip-planning { background:#fff4cc; color:#6a5f00; }
    .chip-personal { background:#fde8e8; color:#ba1a1a; }
    .chip-meeting  { background:#e8f0fe; color:#1a6ef7; }

    .activity-list { display:flex; flex-direction:column; }
    .activity-item { display:flex; gap:12px; padding:12px 0; border-bottom:1px solid #f0eaf8; align-items:flex-start; }
    .activity-item:last-child { border-bottom:none; }
    .activity-icon { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
    .activity-icon.done     { background:#d0f5f3; }
    .activity-icon.created  { background:#ede9ff; }
    .activity-icon.deadline { background:#fde8e8; }
    .activity-text { flex:1; }
    .activity-text strong { font-size:13px; font-weight:600; color:#1c1b20; display:block; margin-bottom:2px; }
    .activity-text span   { font-size:12px; color:#797582; }
    .activity-time { font-size:11px; color:#aaa; white-space:nowrap; padding-top:2px; }

    .focus-block { text-align:center; padding:8px 0; }
    .focus-big-number { font-size:42px; font-weight:800; color:#6351a7; line-height:1; margin-bottom:4px; }
    .focus-sub { font-size:13px; color:#797582; margin-bottom:14px; }
    .progress-bar-wrap { background:#ede9ff; border-radius:100px; height:12px; overflow:hidden; margin-bottom:6px; }
    .progress-bar-fill { height:100%; border-radius:100px; background:#006a61; transition:width .4s ease; }
    .progress-label { display:flex; justify-content:space-between; font-size:11px; color:#797582; }

    .cal-nav { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
    .cal-nav-btn { background:none; border:none; cursor:pointer; color:#6351a7; font-size:18px; padding:2px 6px; border-radius:6px; transition:background .15s; }
    .cal-nav-btn:hover { background:#f3eeff; }
    .cal-month-label { font-size:14px; font-weight:700; color:#1c1b20; }
    .cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; text-align:center; }
    .cal-day-label { font-size:11px; font-weight:700; color:#797582; padding:4px 0; }
    .cal-day { font-size:13px; font-weight:500; color:#1c1b20; padding:6px 4px; border-radius:8px; cursor:pointer; transition:background .1s; }
    .cal-day:hover { background:#f3eeff; color:#6351a7; }
    .cal-day.other-month { color:#cac4d3; }
    .cal-day.today { background:#6351a7; color:#fff; font-weight:700; }
    .cal-day.has-event::after { content:''; display:block; width:4px; height:4px; background:#006a61; border-radius:50%; margin:2px auto 0; }
    .cal-day.today.has-event::after { background:#b5a2ff; }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .stat-cards { grid-template-columns: repeat(2, 1fr); }
        .dashboard-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .stat-cards { grid-template-columns: 1fr; }
        .dashboard-topbar { flex-direction: column; align-items: stretch; }
        .dashboard-greeting h1 { font-size: 20px; }
    }
</style>
@endpush

@section('content')

@php
    $hour     = now()->hour;
    $greeting = $hour < 12 ? 'pagi' : ($hour < 17 ? 'siang' : 'malam');
    $focusH   = round($focusToday / 60, 1);
    $focusGoal = 360; // 6 hours
    $focusPct = min(100, round(($focusToday / $focusGoal) * 100));
@endphp

<div class="dashboard-topbar">

    <div class="dashboard-greeting">
        <h1>Selamat {{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
        <p>
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            · Berikut ringkasan produktivitas kamu hari ini.
        </p>
    </div>

    <form method="POST"
          action="{{ route('logout') }}"
          class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">
            🚪 Logout
        </button>
    </form>

</div>

{{-- Stat cards --}}
<div class="stat-cards">
    <div class="stat-card card-primary">
        <div class="stat-card-label">Tasks Hari Ini</div>
        <div class="stat-card-value">{{ $tasksToday }}</div>
        <div class="stat-card-sub positive">🗓 Due today</div>
    </div>
    <div class="stat-card card-secondary">
        <div class="stat-card-label">Selesai Hari Ini</div>
        <div class="stat-card-value">{{ $tasksDone }}</div>
        <div class="stat-card-sub {{ $tasksDone > 0 ? 'positive' : 'warning' }}">
            {{ $tasksDone > 0 ? '✓ Bagus!' : '⚡ Ayo mulai!' }}
        </div>
    </div>
    <div class="stat-card card-tertiary">
        <div class="stat-card-label">Streak Hari Ini</div>
        <div class="stat-card-value">{{ $streak }}</div>
        <div class="stat-card-sub positive">🔥 Hari berturut-turut</div>
    </div>
    <div class="stat-card card-focus">
        <div class="stat-card-label">Total Jam Fokus</div>
        <div class="stat-card-value">{{ $focusH }} <span class="unit">Jam</span></div>
        <div class="stat-card-sub">🎯 Target: 6 Jam</div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-left">

        {{-- Quick task list --}}
        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">📋 Tasks Hari Ini</span>
                <a href="{{ route('tasks.board') }}" class="section-card-link">Lihat semua →</a>
            </div>
            <div class="task-list">
                @forelse($columns['todo']->take(3)->merge($columns['in_progress']->take(2)) as $task)
                @php
                    $catClass = match(strtolower($task->category ?? '')) {
                        'design'      => 'chip-design',
                        'development' => 'chip-dev',
                        'planning'    => 'chip-planning',
                        'personal'    => 'chip-personal',
                        'meeting'     => 'chip-meeting',
                        default       => '',
                    };
                    $overdue = $task->due_date && $task->due_date->isPast();
                @endphp
                <div class="task-item">
                    <div class="task-checkbox {{ $task->status === 'done' ? 'checked' : '' }}"
                         data-task-id="{{ $task->id }}"></div>
                    <div class="task-info">
                        <div class="task-name {{ $task->status === 'done' ? 'done' : '' }}">{{ $task->title }}</div>
                        @if($task->due_date)
                        <div class="task-deadline {{ $overdue ? 'overdue' : '' }}">
                            📅 {{ $task->due_date->locale('id')->isoFormat('D MMM, HH:mm') }}
                        </div>
                        @endif
                    </div>
                    @if($task->category)
                        <span class="chip {{ $catClass }}">{{ $task->category }}</span>
                    @endif
                </div>
                @empty
                <p style="text-align:center;padding:20px 0;color:#797582;font-size:14px">
                    🎉 Tidak ada task untuk hari ini!
                </p>
                @endforelse
            </div>
        </div>

        {{-- Recent activity (from notifications) --}}
        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">🕐 Aktivitas Terbaru</span>
            </div>
            <div class="activity-list">
                @php
                    $recent = \App\Models\Notification::forUser(auth()->id())->with('task')->latest()->take(4)->get();
                @endphp
                @forelse($recent as $notif)
                @php
                    $iconClass = match($notif->type) {
                        'alarm'    => 'done',
                        'deadline' => 'deadline',
                        default    => 'created',
                    };
                    $icon = match($notif->type) {
                        'alarm'    => '✅',
                        'deadline' => '⚠️',
                        default    => '✨',
                    };
                @endphp
                <div class="activity-item">
                    <div class="activity-icon {{ $iconClass }}">{{ $icon }}</div>
                    <div class="activity-text">
                        <strong>{{ $notif->title }}</strong>
                        <span>{{ $notif->description }}</span>
                    </div>
                    <div class="activity-time">{{ $notif->created_at->diffForHumans() }}</div>
                </div>
                @empty
                <p style="text-align:center;padding:20px 0;color:#797582;font-size:14px">Belum ada aktivitas.</p>
                @endforelse
            </div>
        </div>

    </div>

    <div class="dashboard-right">

        {{-- Focus progress --}}
        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">⏱ Fokus Hari Ini</span>
            </div>
            <div class="focus-block">
                <div class="focus-big-number">{{ $focusH }}</div>
                <div class="focus-sub">dari 6 Jam target harian</div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width:{{ $focusPct }}%"></div>
                </div>
                <div class="progress-label">
                    <span>0 Jam</span>
                    <span>6 Jam</span>
                </div>
            </div>
        </div>

        {{-- Mini calendar (static render for current month) --}}
        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">📅 Kalender</span>
                <a href="{{ route('calendar.index') }}" class="section-card-link">Buka →</a>
            </div>
            @php
                $calNow      = now();
                $firstDay    = $calNow->copy()->startOfMonth();
                $daysInMonth = $calNow->daysInMonth;
                // 0=Sun…6=Sat, we want Sun first so padding = dayOfWeek
                $startPad    = $firstDay->dayOfWeek;

                // days with tasks due this month
                $taskDays = \App\Models\Task::forUser(auth()->id())
                    ->whereYear('due_date', $calNow->year)
                    ->whereMonth('due_date', $calNow->month)
                    ->pluck('due_date')
                    ->map(fn($d) => $d->day)
                    ->unique()
                    ->toArray();
            @endphp

            <div class="cal-nav">
                <button class="cal-nav-btn">‹</button>
                <span class="cal-month-label">{{ $calNow->locale('id')->isoFormat('MMMM YYYY') }}</span>
                <button class="cal-nav-btn">›</button>
            </div>

            <div class="cal-grid">
                @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
                    <div class="cal-day-label">{{ $d }}</div>
                @endforeach

                {{-- leading blanks --}}
                @for($i = 0; $i < $startPad; $i++)
                    <div class="cal-day other-month">{{ $firstDay->copy()->subDays($startPad - $i)->day }}</div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $cls  = 'cal-day';
                        if ($day === $calNow->day) $cls .= ' today';
                        if (in_array($day, $taskDays)) $cls .= ' has-event';
                    @endphp
                    <div class="{{ $cls }}">{{ $day }}</div>
                @endfor
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
// checkbox quick-toggle (visual only — full update via task board)
document.querySelectorAll('.task-checkbox').forEach(cb => {
    cb.addEventListener('click', function() {
        this.classList.toggle('checked');
        const name = this.closest('.task-item').querySelector('.task-name');
        if (name) name.classList.toggle('done');
    });
});
</script>
@endpush
