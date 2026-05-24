@extends('layouts.app')
@section('title', 'Kalender')

@push('styles')
<style>
    /* --- Page header --- */
    .cal-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .cal-page-header-left h1 {
        font-size: 22px;
        font-weight: 800;
        color: #1c1b20;
        margin-bottom: 2px;
    }

    .cal-page-header-left p {
        font-size: 14px;
        color: #797582;
    }

    .cal-view-toggles {
        display: flex;
        align-items: center;
        gap: 4px;
        background: #f3eeff;
        border-radius: 12px;
        padding: 4px;
    }

    .view-toggle-btn {
        padding: 8px 16px;
        border-radius: 9px;
        border: none;
        background: none;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: #797582;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
    }

    .view-toggle-btn.active {
        background: #ffffff;
        color: #6351a7;
        box-shadow: 0 2px 8px rgba(99, 81, 167, 0.15);
    }

    /* --- Layout: calendar + sidebar --- */
    .cal-layout {
        display: grid;
        grid-template-columns: 1fr 290px;
        gap: 20px;
        align-items: start;
    }

    .cal-main {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 8px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0eaf8;
        overflow: hidden;
    }

    /* --- Weekly grid header --- */
    .week-header {
        display: grid;
        grid-template-columns: 56px repeat(7, 1fr);
        border-bottom: 1px solid #f0eaf8;
    }

    .week-header-time { /* spacer for time column */ }

    .week-day-col {
        padding: 14px 8px 12px;
        text-align: center;
        border-left: 1px solid #f0eaf8;
    }

    .week-day-name {
        font-size: 11px;
        font-weight: 700;
        color: #797582;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .week-day-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 700;
        color: #1c1b20;
        margin: 0 auto;
    }

    .week-day-num.today {
        background: #6351a7;
        color: #fff;
    }

    /* --- Time grid body --- */
    .week-grid-body {
        display: grid;
        grid-template-columns: 56px repeat(7, 1fr);
        position: relative;
    }

    .time-col { display: flex; flex-direction: column; }

    .time-slot-label {
        height: 72px;
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
        padding: 6px 10px 0 0;
        font-size: 11px;
        font-weight: 500;
        color: #aaa;
    }

    .day-col-body {
        border-left: 1px solid #f0eaf8;
        position: relative;
        /* each hour row = 72px */
    }

    .hour-row {
        height: 72px;
        border-bottom: 1px solid #f8f4ff;
    }

    /* event cards positioned absolutely within day column */
    .cal-event {
        position: absolute;
        left: 4px;
        right: 4px;
        border-radius: 10px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        overflow: hidden;
        transition: box-shadow 0.15s, transform 0.1s;
        z-index: 2;
    }

    .cal-event:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        transform: translateY(-1px);
    }

    .cal-event.primary {
        background: #ede9ff;
        border-left: 3px solid #6351a7;
        color: #6351a7;
    }

    .cal-event.secondary {
        background: #d0f5f3;
        border-left: 3px solid #006a61;
        color: #006a61;
    }

    .cal-event.tertiary {
        background: #fff4cc;
        border-left: 3px solid #6a5f00;
        color: #6a5f00;
    }

    .cal-event-title {
        font-weight: 700;
        font-size: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cal-event-time {
        font-size: 10px;
        opacity: 0.75;
        margin-top: 2px;
    }

    /* time grid container needed for positioning */
    .day-col-inner {
        position: relative;
        height: 720px; /* 10 hours × 72px */
    }

    /* --- Right sidebar --- */
    .cal-sidebar {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .sidebar-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 22px 20px;
        box-shadow: 0 8px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0eaf8;
    }

    .sidebar-card-title {
        font-size: 15px;
        font-weight: 800;
        color: #1c1b20;
        margin-bottom: 14px;
    }

    /* today's task list in sidebar */
    .sidebar-task-list { display: flex; flex-direction: column; gap: 10px; }

    .sidebar-task-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .sidebar-task-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .sidebar-task-icon.purple { background: #ede9ff; }
    .sidebar-task-icon.teal   { background: #d0f5f3; }
    .sidebar-task-icon.yellow { background: #fff4cc; }

    .sidebar-task-text strong {
        font-size: 13px;
        font-weight: 600;
        color: #1c1b20;
        display: block;
        margin-bottom: 2px;
    }

    .sidebar-task-text span {
        font-size: 11px;
        color: #797582;
    }

    /* focus block in sidebar */
    .focus-num {
        font-size: 38px;
        font-weight: 800;
        color: #6351a7;
        text-align: center;
        line-height: 1;
        margin-bottom: 4px;
    }

    .focus-sub {
        text-align: center;
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
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        color: #797582;
    }

    /* add event button */
    .add-event-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #6351a7;
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 10px 18px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(99, 81, 167, 0.3);
        transition: background 0.2s, transform 0.1s;
        width: 100%;
        justify-content: center;
    }

    .add-event-btn:hover { background: #5240a0; }
    .add-event-btn:active { transform: scale(0.98); }

    /* --- Daily / Monthly views (hidden by default) --- */
    [data-view="daily"],
    [data-view="monthly"] {
        display: none;
    }

    [data-view="daily"].view-active,
    [data-view="monthly"].view-active {
        display: block;
    }

    [data-view="weekly"] {
        display: block;
    }

    [data-view="weekly"].view-hidden {
        display: none;
    }

    /* Daily view placeholder */
    .daily-view-placeholder {
        background: #ffffff;
        border-radius: 24px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 8px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0eaf8;
    }

    .daily-view-placeholder h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1c1b20;
        margin-bottom: 8px;
    }

    .daily-view-placeholder p { font-size: 14px; color: #797582; }

    /* Month grid placeholder */
    .monthly-grid {
        background: #ffffff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 8px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0eaf8;
    }

    .monthly-grid-head {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        text-align: center;
        margin-bottom: 8px;
    }

    .monthly-grid-head span {
        font-size: 11px;
        font-weight: 700;
        color: #797582;
        text-transform: uppercase;
        padding: 4px 0;
    }

    .monthly-grid-body {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }

    .month-cell {
        min-height: 80px;
        border: 1px solid #f0eaf8;
        border-radius: 10px;
        padding: 6px;
    }

    .month-cell-num {
        font-size: 12px;
        font-weight: 700;
        color: #797582;
        margin-bottom: 4px;
    }

    .month-cell.today .month-cell-num {
        background: #6351a7;
        color: #fff;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }

    .month-event-dot {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="cal-page-header">
    <div class="cal-page-header-left">
        <h1><i data-lucide="calendar" class="icon-sm"></i> Jadwal Mingguan</h1>
        <p>Mei 2026</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
        <div class="cal-view-toggles" id="viewToggles">
            <button class="view-toggle-btn" data-target="daily" onclick="switchView(this)">Daily</button>
            <button class="view-toggle-btn active" data-target="weekly" onclick="switchView(this)">Weekly</button>
            <button class="view-toggle-btn" data-target="monthly" onclick="switchView(this)">Monthly</button>
        </div>
    </div>
</div>

<div class="cal-layout">
    {{-- Main calendar area --}}
    <div>

        {{-- === WEEKLY VIEW === --}}
        <div data-view="weekly" id="view-weekly">
            <div class="cal-main">
                {{-- Day headers --}}
                <div class="week-header">
                    <div class="week-header-time"></div>
                    @php
                        // Week of May 19–25, 2026; today = Sunday 25
                        $days = [
                            ['name' => 'Sen', 'num' => 19],
                            ['name' => 'Sel', 'num' => 20],
                            ['name' => 'Rab', 'num' => 21],
                            ['name' => 'Kam', 'num' => 22],
                            ['name' => 'Jum', 'num' => 23],
                            ['name' => 'Sab', 'num' => 24],
                            ['name' => 'Min', 'num' => 25],
                        ];
                    @endphp
                    @foreach($days as $d)
                        <div class="week-day-col">
                            <div class="week-day-name">{{ $d['name'] }}</div>
                            <div class="week-day-num {{ $d['num'] === 25 ? 'today' : '' }}">{{ $d['num'] }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Grid body --}}
                <div class="week-grid-body">
                    {{-- Time labels column --}}
                    <div class="time-col">
                        @foreach(range(8, 17) as $hour)
                            <div class="time-slot-label">{{ sprintf('%02d:00', $hour) }}</div>
                        @endforeach
                    </div>

                    {{-- Day columns (Mon–Sun) --}}
                    @foreach($days as $idx => $d)
                        <div class="day-col-body">
                            {{-- Hour divider rows --}}
                            @foreach(range(8, 17) as $hr)
                                <div class="hour-row"></div>
                            @endforeach

                            {{-- Events: top offset = (hour - 8) * 72px, height = duration_hrs * 72px --}}
                            <div class="day-col-inner">
                                @if($idx === 0) {{-- Monday: Team Sync 09:00–10:30 --}}
                                    <div class="cal-event primary"
                                         style="top: {{ (9-8)*72 }}px; height: {{ 1.5*72 - 4 }}px">
                                        <div class="cal-event-title">Team Sync</div>
                                        <div class="cal-event-time">09:00 – 10:30</div>
                                    </div>
                                @endif
                                @if($idx === 2) {{-- Wednesday: Gym 11:00–12:00 --}}
                                    <div class="cal-event secondary"
                                         style="top: {{ (11-8)*72 }}px; height: {{ 1*72 - 4 }}px">
                                        <div class="cal-event-title">Gym Session</div>
                                        <div class="cal-event-time">11:00 – 12:00</div>
                                    </div>
                                @endif
                                @if($idx === 3) {{-- Thursday: Client Review 13:00–14:30 --}}
                                    <div class="cal-event tertiary"
                                         style="top: {{ (13-8)*72 }}px; height: {{ 1.5*72 - 4 }}px">
                                        <div class="cal-event-title">Client Review</div>
                                        <div class="cal-event-time">13:00 – 14:30</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- === DAILY VIEW (hidden by default) === --}}
        <div data-view="daily" id="view-daily">
            <div class="daily-view-placeholder">
                <div style="font-size:48px;margin-bottom:12px">📆</div>
                <h3>Daily View — Minggu, 25 Mei 2026</h3>
                <p>Tampilan harian akan ditampilkan di sini.</p>
            </div>
        </div>

        {{-- === MONTHLY VIEW (hidden by default) === --}}
        <div data-view="monthly" id="view-monthly">
            <div class="monthly-grid">
                <div class="monthly-grid-head">
                    @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $dl)
                        <span>{{ $dl }}</span>
                    @endforeach
                </div>
                <div class="monthly-grid-body">
                    {{-- leading blanks (May 2026 starts Friday) --}}
                    @for($i = 0; $i < 5; $i++) <div class="month-cell"></div> @endfor
                    @for($d = 1; $d <= 31; $d++)
                        <div class="month-cell {{ $d === 25 ? 'today' : '' }}">
                            <div class="month-cell-num">{{ $d }}</div>
                            @if($d === 19)
                                <div class="month-event-dot" style="background:#ede9ff;color:#6351a7">Team Sync</div>
                            @endif
                            @if($d === 21)
                                <div class="month-event-dot" style="background:#d0f5f3;color:#006a61">Gym</div>
                            @endif
                            @if($d === 22)
                                <div class="month-event-dot" style="background:#fff4cc;color:#6a5f00">Client Review</div>
                            @endif
                        </div>
                    @endfor
                    {{-- trailing --}}
                    <div class="month-cell"></div>
                    <div class="month-cell"></div>
                    <div class="month-cell"></div>
                    <div class="month-cell"></div>
                    <div class="month-cell"></div>
                    <div class="month-cell"></div>
                </div>
            </div>
        </div>

    </div>{{-- /.cal-main-col --}}

    {{-- === Right sidebar === --}}
    <div class="cal-sidebar">

        {{-- Add event --}}
        <button class="add-event-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Event
        </button>

        {{-- Today's tasks --}}
        <div class="sidebar-card">
            <div class="sidebar-card-title">📋 Kegiatan Hari Ini</div>
            <div class="sidebar-task-list">

                <div class="sidebar-task-item">
                    <div class="sidebar-task-icon purple">📋</div>
                    <div class="sidebar-task-text">
                        <strong>Buat Laporan Mingguan</strong>
                        <span><i data-lucide="clock" class="icon-sm"></i> Deadline 17:00</span>
                    </div>
                </div>

                <div class="sidebar-task-item">
                    <div class="sidebar-task-icon teal">🤝</div>
                    <div class="sidebar-task-text">
                        <strong>Meeting dengan Client</strong>
                        <span>14:00 – 15:00</span>
                    </div>
                </div>

                <div class="sidebar-task-item">
                    <div class="sidebar-task-icon yellow">🎯</div>
                    <div class="sidebar-task-text">
                        <strong>Review Design System</strong>
                        <span>Fleksibel</span>
                    </div>
                </div>

                <div class="sidebar-task-item">
                    <div class="sidebar-task-icon purple">💻</div>
                    <div class="sidebar-task-text">
                        <strong>Implement Nav Shell Logic</strong>
                        <span>In Progress · 65%</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- Focus progress --}}
        <div class="sidebar-card">
            <div class="sidebar-card-title">⏱ Fokus Hari Ini</div>
            <div class="focus-num">4.5</div>
            <div class="focus-sub">Jam dari target 6 Jam</div>
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" style="width: 75%"></div>
            </div>
            <div class="progress-label">
                <span>0 Jam</span>
                <span>6 Jam</span>
            </div>
        </div>

    </div>{{-- /.cal-sidebar --}}
</div>

@endsection

@push('scripts')
<script>
    // simple view switcher — daily / weekly / monthly
    function switchView(btn) {
        // update active button
        document.querySelectorAll('#viewToggles .view-toggle-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const target = btn.dataset.target;
        const views = ['daily', 'weekly', 'monthly'];

        views.forEach(v => {
            const el = document.getElementById(`view-${v}`);
            if (!el) return;
            if (v === target) {
                el.removeAttribute('data-view-hidden');
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        });
    }
</script>
@endpush
