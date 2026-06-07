@extends('layouts.app')
@section('title', 'Kalender')

@push('styles')
<style>
    .cal-page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .cal-page-header-left h1 { font-size:22px; font-weight:800; color:#1c1b20; margin-bottom:2px; }
    .cal-page-header-left p  { font-size:14px; color:#797582; }

    .cal-view-toggles { display:flex; align-items:center; gap:4px; background:#f3eeff; border-radius:12px; padding:4px; }
    .view-toggle-btn { padding:8px 16px; border-radius:9px; border:none; background:none; font-family:inherit; font-size:13px; font-weight:600; color:#797582; cursor:pointer; transition:background .15s,color .15s; }
    .view-toggle-btn.active { background:#fff; color:#6351a7; box-shadow:0 2px 8px rgba(99,81,167,.15); }

    .cal-layout { display:grid; grid-template-columns:1fr 290px; gap:20px; align-items:start; }
    .cal-main { background:#fff; border-radius:24px; box-shadow:0 8px 20px rgba(181,162,255,.15); border:1px solid #f0eaf8; overflow:hidden; }

    .week-header { display:grid; grid-template-columns:56px repeat(7,1fr); border-bottom:1px solid #f0eaf8; }
    .week-day-col { padding:14px 8px 12px; text-align:center; border-left:1px solid #f0eaf8; }
    .week-day-name { font-size:11px; font-weight:700; color:#797582; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
    .week-day-num { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:15px; font-weight:700; color:#1c1b20; margin:0 auto; }
    .week-day-num.today { background:#6351a7; color:#fff; }

    .week-grid-body { display:grid; grid-template-columns:56px repeat(7,1fr); position:relative; }
    .time-col { display:flex; flex-direction:column; }
    .time-slot-label { height:72px; display:flex; align-items:flex-start; justify-content:flex-end; padding:6px 10px 0 0; font-size:11px; font-weight:500; color:#aaa; }
    .day-col-body { border-left:1px solid #f0eaf8; position:relative; }
    .hour-row { height:72px; border-bottom:1px solid #f8f4ff; }
    .day-col-inner { position:relative; height:720px; }

    .cal-event { position:absolute; left:4px; right:4px; border-radius:10px; padding:6px 10px; font-size:12px; font-weight:600; cursor:pointer; overflow:hidden; transition:box-shadow .15s,transform .1s; z-index:2; }
    .cal-event:hover { box-shadow:0 4px 16px rgba(0,0,0,.15); transform:translateY(-1px); }
    .cal-event.primary   { background:#ede9ff; border-left:3px solid #6351a7; color:#6351a7; }
    .cal-event.secondary { background:#d0f5f3; border-left:3px solid #006a61; color:#006a61; }
    .cal-event.tertiary  { background:#fff4cc; border-left:3px solid #6a5f00; color:#6a5f00; }
    .cal-event-title { font-weight:700; font-size:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .cal-event-time  { font-size:10px; opacity:.75; margin-top:2px; }

    .cal-sidebar { display:flex; flex-direction:column; gap:16px; }
    .sidebar-card { background:#fff; border-radius:24px; padding:22px 20px; box-shadow:0 8px 20px rgba(181,162,255,.15); border:1px solid #f0eaf8; }
    .sidebar-card-title { font-size:15px; font-weight:800; color:#1c1b20; margin-bottom:14px; }

    .sidebar-task-list { display:flex; flex-direction:column; gap:10px; }
    .sidebar-task-item { display:flex; align-items:flex-start; gap:10px; }
    .sidebar-task-icon { width:32px; height:32px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
    .sidebar-task-icon.purple { background:#ede9ff; }
    .sidebar-task-icon.teal   { background:#d0f5f3; }
    .sidebar-task-icon.yellow { background:#fff4cc; }
    .sidebar-task-text strong { font-size:13px; font-weight:600; color:#1c1b20; display:block; margin-bottom:2px; }
    .sidebar-task-text span   { font-size:11px; color:#797582; }

    .focus-num { font-size:38px; font-weight:800; color:#6351a7; text-align:center; line-height:1; margin-bottom:4px; }
    .focus-sub { text-align:center; font-size:13px; color:#797582; margin-bottom:14px; }
    .progress-bar-wrap { background:#ede9ff; border-radius:100px; height:12px; overflow:hidden; margin-bottom:6px; }
    .progress-bar-fill { height:100%; border-radius:100px; background:#006a61; }
    .progress-label { display:flex; justify-content:space-between; font-size:11px; color:#797582; }

    .add-event-btn { display:flex; align-items:center; gap:6px; background:#6351a7; color:#fff; border:none; border-radius:14px; padding:10px 18px; font-family:inherit; font-size:13px; font-weight:700; cursor:pointer; box-shadow:0 4px 14px rgba(99,81,167,.3); transition:background .2s,transform .1s; width:100%; justify-content:center; }
    .add-event-btn:hover { background:#5240a0; }

    .week-nav { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid #f0eaf8; }
    .week-nav-btn { background:none; border:1.5px solid #cac4d3; border-radius:10px; padding:6px 12px; font-family:inherit; font-size:13px; font-weight:600; color:#797582; cursor:pointer; text-decoration:none; transition:border-color .15s,color .15s; }
    .week-nav-btn:hover { border-color:#6351a7; color:#6351a7; }

    .daily-view-placeholder { background:#fff; border-radius:24px; padding:40px; text-align:center; box-shadow:0 8px 20px rgba(181,162,255,.15); border:1px solid #f0eaf8; }
    .daily-view-placeholder h3 { font-size:18px; font-weight:700; color:#1c1b20; margin-bottom:8px; }
    .daily-view-placeholder p  { font-size:14px; color:#797582; }

    .monthly-grid { background:#fff; border-radius:24px; padding:24px; box-shadow:0 8px 20px rgba(181,162,255,.15); border:1px solid #f0eaf8; }
    .monthly-grid-head { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; text-align:center; margin-bottom:8px; }
    .monthly-grid-head span { font-size:11px; font-weight:700; color:#797582; text-transform:uppercase; padding:4px 0; }
    .monthly-grid-body { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; }
    .month-cell { min-height:80px; border:1px solid #f0eaf8; border-radius:10px; padding:6px; }
    .month-cell-num { font-size:12px; font-weight:700; color:#797582; margin-bottom:4px; }
    .month-cell.today .month-cell-num { background:#6351a7; color:#fff; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; }
    .month-event-dot { font-size:10px; padding:2px 6px; border-radius:4px; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    /* modal */
    .modal-overlay { position:fixed; inset:0; background:rgba(28,27,32,.5); backdrop-filter:blur(4px); z-index:200; display:none; align-items:center; justify-content:center; padding:20px; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:24px; width:100%; max-width:480px; box-shadow:0 24px 60px rgba(99,81,167,.25); animation:modalIn .2s ease; }
    @keyframes modalIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    .modal-header { display:flex; align-items:center; justify-content:space-between; padding:22px 24px 16px; border-bottom:1px solid #f0eaf8; }
    .modal-title  { font-size:17px; font-weight:800; color:#1c1b20; }
    .modal-close-btn { background:none; border:none; cursor:pointer; font-size:22px; color:#797582; padding:2px 6px; border-radius:8px; }
    .modal-close-btn:hover { background:#f3eeff; color:#6351a7; }
    .modal-body { padding:20px 24px; display:flex; flex-direction:column; gap:14px; }
    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-label { font-size:13px; font-weight:600; color:#1c1b20; }
    .form-input, .form-select { padding:10px 14px; border:1.5px solid #cac4d3; border-radius:12px; font-family:inherit; font-size:14px; color:#1c1b20; background:#fdf7ff; outline:none; width:100%; }
    .form-input:focus, .form-select:focus { border-color:#6351a7; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .modal-footer { display:flex; gap:10px; justify-content:flex-end; padding:16px 24px 22px; border-top:1px solid #f0eaf8; }
    .btn-cancel { padding:10px 20px; border-radius:12px; border:1.5px solid #cac4d3; background:none; font-family:inherit; font-size:14px; font-weight:600; color:#797582; cursor:pointer; }
    .btn-save   { padding:10px 24px; border-radius:12px; border:none; background:#6351a7; font-family:inherit; font-size:14px; font-weight:700; color:#fff; cursor:pointer; }
    .btn-save:hover { background:#5240a0; }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .cal-layout { grid-template-columns: 1fr; }
        .cal-sidebar { display: grid; grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 640px) {
        .cal-page-header { flex-direction: column; align-items: stretch; }
        .cal-view-toggles { justify-content: center; }
        .cal-sidebar { grid-template-columns: 1fr; }
        .week-nav { flex-direction: column; gap: 10px; text-align: center; }
        #view-weekly .cal-main { overflow-x: auto; }
        .week-header, .week-grid-body { min-width: 600px; }
        .monthly-grid { overflow-x: auto; padding: 16px; }
        .monthly-grid-head, .monthly-grid-body { min-width: 500px; }
    }
</style>
@endpush

@section('content')

@php
    $focusHours  = round($focusToday / 60, 1);
    $focusPct    = min(100, round(($focusToday / $focusGoal) * 100));
    $prevWeek    = $weekStart->copy()->subWeek()->toDateString();
    $nextWeek    = $weekStart->copy()->addWeek()->toDateString();
    $todayNum    = now()->day;
    $todayColIdx = now()->dayOfWeekIso - 1; // 0=Mon
@endphp

<div class="cal-page-header">
    <div class="cal-page-header-left">
        <h1>📅 Jadwal Mingguan</h1>
        <p>{{ $weekStart->locale('id')->isoFormat('MMMM YYYY') }}</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
        <div class="cal-view-toggles" id="viewToggles">
            <button class="view-toggle-btn" data-target="daily"   onclick="switchView(this)">Daily</button>
            <button class="view-toggle-btn active" data-target="weekly"  onclick="switchView(this)">Weekly</button>
            <button class="view-toggle-btn" data-target="monthly" onclick="switchView(this)">Monthly</button>
        </div>
    </div>
</div>

<div class="cal-layout">
    <div>

        {{-- WEEKLY VIEW --}}
        <div id="view-weekly">
            <div class="cal-main">

                {{-- week navigation --}}
                <div class="week-nav">
                    <a href="{{ route('calendar.index', ['week' => $prevWeek]) }}" class="week-nav-btn">‹ Prev</a>
                    <span style="font-size:14px;font-weight:600;color:#1c1b20">
                        {{ $weekStart->format('d M') }} – {{ $weekEnd->format('d M Y') }}
                    </span>
                    <a href="{{ route('calendar.index', ['week' => $nextWeek]) }}" class="week-nav-btn">Next ›</a>
                </div>

                {{-- day headers --}}
                <div class="week-header">
                    <div></div>
                    @foreach($days as $idx => $day)
                        <div class="week-day-col">
                            <div class="week-day-name">{{ $day->locale('id')->isoFormat('ddd') }}</div>
                            <div class="week-day-num {{ $day->isToday() ? 'today' : '' }}">{{ $day->day }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- time grid --}}
                <div class="week-grid-body">
                    <div class="time-col">
                        @foreach(range(8, 17) as $hour)
                            <div class="time-slot-label">{{ sprintf('%02d:00', $hour) }}</div>
                        @endforeach
                    </div>

                    @foreach($days as $idx => $day)
                        <div class="day-col-body">
                            @foreach(range(8, 17) as $hr)
                                <div class="hour-row"></div>
                            @endforeach
                            <div class="day-col-inner">
                                {{-- render events for this day --}}
                                @foreach($events->get($idx, collect()) as $event)
                                    <div class="cal-event {{ $event->color }}"
                                         style="top:{{ $event->top_px }}px; height:{{ $event->height_px }}px">
                                        <div class="cal-event-title">{{ $event->title }}</div>
                                        <div class="cal-event-time">
                                            {{ $event->starts_at->format('H:i') }} – {{ $event->ends_at->format('H:i') }}
                                        </div>
                                    </div>
                                @endforeach
                                {{-- tasks due on this day as small markers --}}
                                @foreach($tasksDueThisWeek->where('due_date', $day->toDateString()) as $t)
                                    <div class="cal-event tertiary"
                                         style="top:{{ (9-8)*72 }}px; height:32px; opacity:.8">
                                        <div class="cal-event-title">📋 {{ Str::limit($t->title, 22) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- DAILY VIEW --}}
        <div id="view-daily" style="display:none">
            <div class="daily-view-placeholder">
                <div style="font-size:48px;margin-bottom:12px">📆</div>
                <h3>Daily View — {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</h3>
                <p>Tampilan harian akan ditampilkan di sini.</p>
            </div>
        </div>

        {{-- MONTHLY VIEW --}}
        <div id="view-monthly" style="display:none">
            <div class="monthly-grid">
                <div class="monthly-grid-head">
                    @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $dl)
                        <span>{{ $dl }}</span>
                    @endforeach
                </div>
                <div class="monthly-grid-body">
                    @php
                        $firstOfMonth = \Carbon\Carbon::create($monthYear, $monthMonth, 1);
                        $pad          = $firstOfMonth->dayOfWeek; // 0=Sun
                        $daysInMonth  = $firstOfMonth->daysInMonth;
                    @endphp
                    {{-- leading blanks --}}
                    @for($i = 0; $i < $pad; $i++)
                        <div class="month-cell"></div>
                    @endfor
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php $isToday = ($d === now()->day && $monthMonth === now()->month && $monthYear === now()->year); @endphp
                        <div class="month-cell {{ $isToday ? 'today' : '' }}">
                            <div class="month-cell-num">{{ $d }}</div>
                            {{-- events on this day --}}
                            @foreach($monthEvents->get($d, collect()) as $ev)
                                @php
                                    $dotStyle = match($ev->color) {
                                        'primary'   => 'background:#ede9ff;color:#6351a7',
                                        'secondary' => 'background:#d0f5f3;color:#006a61',
                                        'tertiary'  => 'background:#fff4cc;color:#6a5f00',
                                        default     => 'background:#ede9ff;color:#6351a7',
                                    };
                                @endphp
                                <div class="month-event-dot" style="{{ $dotStyle }}">
                                    {{ Str::limit($ev->title, 12) }}
                                </div>
                            @endforeach
                            {{-- task due markers --}}
                            @foreach($monthTasksDue->get($d, collect()) as $t)
                                <div class="month-event-dot" style="background:#fff4cc;color:#6a5f00">
                                    📋 {{ Str::limit($t->title, 10) }}
                                </div>
                            @endforeach
                        </div>
                    @endfor
                </div>
            </div>
        </div>

    </div>

    {{-- Right sidebar --}}
    <div class="cal-sidebar">

        <button class="add-event-btn" onclick="openEventModal()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Event
        </button>

        {{-- today's tasks --}}
        <div class="sidebar-card">
            <div class="sidebar-card-title">📋 Kegiatan Hari Ini</div>
            <div class="sidebar-task-list">
                @forelse($todayTasks as $t)
                @php
                    $iconColors = ['purple','teal','yellow','purple'];
                    $icons = ['📋','🤝','🎯','💻'];
                    $i = $loop->index % 4;
                @endphp
                <div class="sidebar-task-item">
                    <div class="sidebar-task-icon {{ $iconColors[$i] }}">{{ $icons[$i] }}</div>
                    <div class="sidebar-task-text">
                        <strong>{{ Str::limit($t->title, 28) }}</strong>
                        <span>
                            @if($t->status === 'in_progress') In Progress · {{ $t->progress }}%
                            @elseif($t->due_date) Deadline {{ $t->due_date->format('H:i') }}
                            @else Fleksibel @endif
                        </span>
                    </div>
                </div>
                @empty
                <p style="font-size:13px;color:#797582;text-align:center;padding:8px 0">Tidak ada kegiatan hari ini.</p>
                @endforelse
            </div>
        </div>

        {{-- focus progress --}}
        <div class="sidebar-card">
            <div class="sidebar-card-title">⏱ Fokus Hari Ini</div>
            <div class="focus-num">{{ $focusHours }}</div>
            <div class="focus-sub">Jam dari target 6 Jam</div>
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" style="width:{{ $focusPct }}%"></div>
            </div>
            <div class="progress-label">
                <span>0 Jam</span>
                <span>6 Jam</span>
            </div>
        </div>

    </div>
</div>

{{-- Add Event Modal --}}
<div class="modal-overlay" id="eventModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">📅 Tambah Event</span>
            <button class="modal-close-btn" onclick="closeEventModal()">×</button>
        </div>
        <form id="eventForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Judul Event *</label>
                    <input type="text" name="title" class="form-input" placeholder="e.g. Team Meeting" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Mulai</label>
                        <input type="datetime-local" name="starts_at" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Selesai</label>
                        <input type="datetime-local" name="ends_at" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Warna</label>
                    <select name="color" class="form-select">
                        <option value="primary">Ungu (Primary)</option>
                        <option value="secondary">Teal (Secondary)</option>
                        <option value="tertiary">Kuning (Tertiary)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEventModal()">Batal</button>
                <button type="submit" class="btn-save">💾 Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// view switcher
function switchView(btn) {
    document.querySelectorAll('#viewToggles .view-toggle-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    ['daily','weekly','monthly'].forEach(v => {
        document.getElementById('view-' + v).style.display = (v === btn.dataset.target) ? 'block' : 'none';
    });
}

// event modal
const eventModal = document.getElementById('eventModal');
function openEventModal()  { eventModal.classList.add('open'); document.body.style.overflow = 'hidden'; }
function closeEventModal() { eventModal.classList.remove('open'); document.body.style.overflow = ''; }
eventModal.addEventListener('click', e => { if (e.target === eventModal) closeEventModal(); });

document.getElementById('eventForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const res  = await fetch('{{ route("calendar.events.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this),
    });
    if (res.ok) { closeEventModal(); location.reload(); }
});
</script>
@endpush