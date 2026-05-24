@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .dashboard-greeting {
        margin-bottom: 28px;
    }

    .dashboard-greeting h1 {
        font-size: 24px;
        font-weight: 800;
        color: #1c1b20;
        margin-bottom: 4px;
    }

    .dashboard-greeting p {
        font-size: 14px;
        color: #797582;
        font-weight: 400;
    }

    /* --- Stat cards --- */
    .stat-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 22px 24px;
        box-shadow: 0 8px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0eaf8;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 60px;
        height: 60px;
        border-radius: 0 24px 0 60px;
        opacity: 0.08;
    }

    .stat-card.card-primary::before  { background: #6351a7; }
    .stat-card.card-secondary::before { background: #006a61; }
    .stat-card.card-tertiary::before  { background: #6a5f00; }
    .stat-card.card-focus::before     { background: #6351a7; }

    .stat-card-label {
        font-size: 12px;
        font-weight: 600;
        color: #797582;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 10px;
    }

    .stat-card-value {
        font-size: 32px;
        font-weight: 800;
        color: #1c1b20;
        line-height: 1.1;
        margin-bottom: 6px;
    }

    .stat-card-value span.unit {
        font-size: 16px;
        font-weight: 600;
        color: #797582;
    }

    .stat-card-sub {
        font-size: 12px;
        color: #797582;
    }

    .stat-card-sub.positive { color: #006a61; }
    .stat-card-sub.warning  { color: #6a5f00; }

    /* --- Dashboard grid layout --- */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
        align-items: start;
    }

    .dashboard-left { display: flex; flex-direction: column; gap: 20px; }
    .dashboard-right { display: flex; flex-direction: column; gap: 20px; }

    /* --- Section cards --- */
    .section-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 8px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0eaf8;
    }

    .section-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .section-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1c1b20;
    }

    .section-card-link {
        font-size: 13px;
        font-weight: 600;
        color: #6351a7;
        text-decoration: none;
    }

    .section-card-link:hover { text-decoration: underline; }

    /* --- Quick task list --- */
    .task-list { display: flex; flex-direction: column; gap: 10px; }

    .task-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 16px;
        border-radius: 14px;
        background: #fdf7ff;
        border: 1px solid #ede9ff;
        transition: background 0.15s;
    }

    .task-item:hover { background: #f3eeff; }

    /* circular checkbox */
    .task-checkbox {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #cac4d3;
        flex-shrink: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: border-color 0.15s, background 0.15s;
    }

    .task-checkbox:hover {
        border-color: #6351a7;
    }

    .task-checkbox.checked {
        background: #006a61;
        border-color: #006a61;
    }

    .task-checkbox.checked::after {
        content: '';
        width: 6px;
        height: 10px;
        border: 2px solid #fff;
        border-top: none;
        border-left: none;
        transform: rotate(45deg) translateY(-1px);
    }

    .task-info { flex: 1; min-width: 0; }

    .task-name {
        font-size: 14px;
        font-weight: 600;
        color: #1c1b20;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 3px;
    }

    .task-name.done {
        text-decoration: line-through;
        color: #797582;
    }

    .task-deadline {
        font-size: 12px;
        color: #797582;
    }

    .task-deadline.overdue { color: #ba1a1a; }

    .chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .chip-design    { background: #ede9ff; color: #6351a7; }
    .chip-dev       { background: #d0f5f3; color: #006a61; }
    .chip-planning  { background: #fff4cc; color: #6a5f00; }
    .chip-personal  { background: #fde8e8; color: #ba1a1a; }
    .chip-meeting   { background: #e8f0fe; color: #1a6ef7; }

    /* --- Mini calendar --- */
    .mini-calendar {}

    .cal-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .cal-nav-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #6351a7;
        font-size: 18px;
        padding: 2px 6px;
        border-radius: 6px;
        transition: background 0.15s;
    }

    .cal-nav-btn:hover { background: #f3eeff; }

    .cal-month-label {
        font-size: 14px;
        font-weight: 700;
        color: #1c1b20;
    }

    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        text-align: center;
    }

    .cal-day-label {
        font-size: 11px;
        font-weight: 700;
        color: #797582;
        padding: 4px 0;
    }

    .cal-day {
        font-size: 13px;
        font-weight: 500;
        color: #1c1b20;
        padding: 6px 4px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.1s;
    }

    .cal-day:hover { background: #f3eeff; color: #6351a7; }

    .cal-day.other-month { color: #cac4d3; }

    .cal-day.today {
        background: #6351a7;
        color: #fff;
        font-weight: 700;
    }

    .cal-day.has-event::after {
        content: '';
        display: block;
        width: 4px;
        height: 4px;
        background: #006a61;
        border-radius: 50%;
        margin: 2px auto 0;
    }

    .cal-day.today.has-event::after { background: #b5a2ff; }

    /* --- Activity feed --- */
    .activity-list { display: flex; flex-direction: column; gap: 0; }

    .activity-item {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f0eaf8;
        align-items: flex-start;
    }

    .activity-item:last-child { border-bottom: none; }

    .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .activity-icon.done     { background: #d0f5f3; }
    .activity-icon.created  { background: #ede9ff; }
    .activity-icon.comment  { background: #e8f0fe; }
    .activity-icon.deadline { background: #fde8e8; }

    .activity-text {
        flex: 1;
    }

    .activity-text strong {
        font-size: 13px;
        font-weight: 600;
        color: #1c1b20;
        display: block;
        margin-bottom: 2px;
    }

    .activity-text span {
        font-size: 12px;
        color: #797582;
    }

    .activity-time {
        font-size: 11px;
        color: #aaa;
        white-space: nowrap;
        padding-top: 2px;
    }

    /* focus progress inside right card */
    .focus-block { text-align: center; padding: 8px 0; }

    .focus-big-number {
        font-size: 42px;
        font-weight: 800;
        color: #6351a7;
        line-height: 1;
        margin-bottom: 4px;
    }

    .focus-sub {
        font-size: 13px;
        color: #797582;
        margin-bottom: 14px;
    }

    .progress-bar-wrap {
        background: #ede9ff;
        border-radius: 100px;
        height: 12px;
        overflow: hidden;
        margin-bottom: 6px;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 100px;
        background: #006a61;
        transition: width 0.4s ease;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        color: #797582;
    }
</style>
@endpush

@section('content')

{{-- Greeting --}}
<div class="dashboard-greeting">
    <h1>Selamat pagi, Salman! <i data-lucide="hand" class="icon-sm"></i></h1>
    <p>Minggu, 25 Mei 2026 · Berikut ringkasan produktivitas kamu hari ini.</p>
</div>

{{-- 4 stat cards --}}
<div class="stat-cards">
    <div class="stat-card card-primary">
        <div class="stat-card-label">Tasks Hari Ini</div>
        <div class="stat-card-value">8</div>
        <div class="stat-card-sub positive">↑ +2 dari kemarin</div>
    </div>
    <div class="stat-card card-secondary">
        <div class="stat-card-label">Sedang Dikerjakan</div>
        <div class="stat-card-value">3</div>
        <div class="stat-card-sub warning">⚡ Butuh perhatian</div>
    </div>
    <div class="stat-card card-tertiary">
        <div class="stat-card-label">Selesai Minggu Ini</div>
        <div class="stat-card-value">12</div>
        <div class="stat-card-sub positive">↑ +5.4% minggu lalu</div>
    </div>
    <div class="stat-card card-focus">
        <div class="stat-card-label">Total Jam Fokus</div>
        <div class="stat-card-value">4.5 <span class="unit">Jam</span></div>
        <div class="stat-card-sub">🎯 Target: 6 Jam</div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-left">

        {{-- Quick Task List --}}
        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">📋 Tasks Hari Ini</span>
                <a href="{{ route('tasks.board') }}" class="section-card-link">Lihat semua →</a>
            </div>
            <div class="task-list">

                {{-- Task 1 --}}
                <div class="task-item">
                    <div class="task-checkbox"></div>
                    <div class="task-info">
                        <div class="task-name">Buat Laporan Mingguan</div>
                        <div class="task-deadline overdue"><i data-lucide="clock" class="icon-sm"></i> Deadline: Hari ini, 17:00</div>
                    </div>
                    <span class="chip chip-planning">Planning</span>
                </div>

                {{-- Task 2 --}}
                <div class="task-item">
                    <div class="task-checkbox"></div>
                    <div class="task-info">
                        <div class="task-name">Review Design System</div>
                        <div class="task-deadline"><i data-lucide="calendar" class="icon-sm"></i> Besok, 10:00</div>
                    </div>
                    <span class="chip chip-design">Design</span>
                </div>

                {{-- Task 3 --}}
                <div class="task-item">
                    <div class="task-checkbox"></div>
                    <div class="task-info">
                        <div class="task-name">Meeting dengan Client</div>
                        <div class="task-deadline"><i data-lucide="calendar" class="icon-sm"></i> Hari ini, 14:00</div>
                    </div>
                    <span class="chip chip-meeting">Meeting</span>
                </div>

                {{-- Task 4 --}}
                <div class="task-item">
                    <div class="task-checkbox"></div>
                    <div class="task-info">
                        <div class="task-name">Implement Navigation Shell</div>
                        <div class="task-deadline"><i data-lucide="calendar" class="icon-sm"></i> 26 Mei 2026</div>
                    </div>
                    <span class="chip chip-dev">Dev</span>
                </div>

                {{-- Task 5 — already done --}}
                <div class="task-item">
                    <div class="task-checkbox checked"></div>
                    <div class="task-info">
                        <div class="task-name done">Define Brand Anchors & JSON Structure</div>
                        <div class="task-deadline"><i data-lucide="check-circle" class="icon-sm"></i> Selesai tadi</div>
                    </div>
                    <span class="chip chip-planning">Planning</span>
                </div>

            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">🕐 Aktivitas Terbaru</span>
            </div>
            <div class="activity-list">

                <div class="activity-item">
                    <div class="activity-icon done"><i data-lucide="check-circle" class="icon-sm"></i></div>
                    <div class="activity-text">
                        <strong>Task diselesaikan</strong>
                        <span>Define Brand Anchors & JSON Structure ditandai selesai</span>
                    </div>
                    <div class="activity-time">10 mnt lalu</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon created">✨</div>
                    <div class="activity-text">
                        <strong>Task baru dibuat</strong>
                        <span>Implement Navigation Shell Logic ditambahkan ke In Progress</span>
                    </div>
                    <div class="activity-time">32 mnt lalu</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon deadline"><i data-lucide="alert-triangle" class="icon-sm"></i></div>
                    <div class="activity-text">
                        <strong>Deadline mendekat</strong>
                        <span>Buat Laporan Mingguan jatuh tempo hari ini pukul 17:00</span>
                    </div>
                    <div class="activity-time">1 jam lalu</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon comment">💬</div>
                    <div class="activity-text">
                        <strong>Sesi fokus selesai</strong>
                        <span>25 menit fokus pada Review Design System tercatat</span>
                    </div>
                    <div class="activity-time">2 jam lalu</div>
                </div>

            </div>
        </div>

    </div>{{-- /.dashboard-left --}}

    <div class="dashboard-right">

        {{-- Focus Progress --}}
        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">⏱ Fokus Hari Ini</span>
            </div>
            <div class="focus-block">
                <div class="focus-big-number">4.5</div>
                <div class="focus-sub">dari 6 Jam target harian</div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width: 75%"></div>
                </div>
                <div class="progress-label">
                    <span>0 Jam</span>
                    <span>6 Jam</span>
                </div>
            </div>
        </div>

        {{-- Mini Calendar --}}
        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title"><i data-lucide="calendar" class="icon-sm"></i> Kalender</span>
                <a href="{{ route('calendar') }}" class="section-card-link">Buka →</a>
            </div>

            <div class="cal-nav">
                <button class="cal-nav-btn">‹</button>
                <span class="cal-month-label">Mei 2026</span>
                <button class="cal-nav-btn">›</button>
            </div>

            <div class="cal-grid">
                {{-- Day labels --}}
                @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
                    <div class="cal-day-label">{{ $d }}</div>
                @endforeach

                {{-- Padding for first day (Fri = index 5, but May 2026 starts on Friday) --}}
                @for($i = 0; $i < 5; $i++)
                    <div class="cal-day other-month">{{ 26 + $i }}</div>
                @endfor

                {{-- Days 1–31 --}}
                @for($day = 1; $day <= 31; $day++)
                    @php
                        $isToday = ($day === 25);
                        $hasEvent = in_array($day, [5, 12, 19, 25, 28]);
                        $classes = 'cal-day';
                        if ($isToday) $classes .= ' today';
                        if ($hasEvent) $classes .= ' has-event';
                    @endphp
                    <div class="{{ $classes }}">{{ $day }}</div>
                @endfor

                {{-- trailing days --}}
                <div class="cal-day other-month">1</div>
                <div class="cal-day other-month">2</div>
                <div class="cal-day other-month">3</div>
                <div class="cal-day other-month">4</div>
                <div class="cal-day other-month">5</div>
                <div class="cal-day other-month">6</div>
            </div>
        </div>

    </div>{{-- /.dashboard-right --}}
</div>

@endsection

@push('scripts')
<script>
    // circular checkbox toggle — simple interaction
    document.querySelectorAll('.task-checkbox').forEach(cb => {
        cb.addEventListener('click', () => {
            cb.classList.toggle('checked');
            const nameEl = cb.closest('.task-item').querySelector('.task-name');
            if (nameEl) nameEl.classList.toggle('done');
        });
    });
</script>
@endpush
