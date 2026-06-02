@extends('layouts.app')
@section('title', 'Alokasi Waktu')

@push('styles')
<style>
.alloc-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
.alloc-header h1 { font-size:22px; font-weight:800; color:#1c1b20; }
.alloc-header-meta { font-size:13px; color:#797582; }

.split-shell {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 20px;
    align-items: start;
}

.split-left {
    background: #fff;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(99,81,167,.1);
    border: 1px solid #f0eaf8;
}
.split-left-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px; border-bottom: 1px solid #f0eaf8;
}
.split-left-title { font-size: 15px; font-weight: 800; color: #1c1b20; }
.btn-go-board {
    display:flex; align-items:center; gap:6px;
    padding:7px 14px; border-radius:10px;
    background:#6351a7; color:#fff;
    font-size:12px; font-weight:700;
    border:none; cursor:pointer; text-decoration:none; transition:background .15s;
}
.btn-go-board:hover { background:#5240a0; }
.split-left-body { padding:20px 22px; }
.section-label { font-size:11px; font-weight:700; color:#797582; text-transform:uppercase; letter-spacing:.6px; margin-bottom:12px; }

.bubble-wrap {
    display: flex; flex-wrap: wrap; gap: 12px;
    align-items: center; justify-content: center;
    padding: 20px; background: #fdf7ff;
    border-radius: 20px; border: 1px solid #ede9ff;
    min-height: 120px; margin-bottom: 22px;
}
.bubble {
    border-radius: 50%; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    cursor: pointer; transition: transform .2s, box-shadow .2s;
    position: relative; flex-shrink: 0;
}
.bubble:hover  { transform: scale(1.08); box-shadow: 0 8px 24px rgba(0,0,0,.18); }
.bubble.active { box-shadow: 0 0 0 3px #fff, 0 0 0 5px #6351a7; }
.bubble-label  { font-size:10px; font-weight:700; color:#fff; text-align:center; padding:0 4px; line-height:1.3; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
.bubble-pct    { font-size:11px; font-weight:800; color:rgba(255,255,255,.9); margin-top:2px; }

.task-alloc-item {
    display:flex; align-items:center; gap:12px;
    padding:11px 14px; border-radius:14px;
    background:#fdf7ff; border:1px solid #ede9ff;
    margin-bottom:8px; cursor:pointer;
    transition:background .15s, border-color .15s;
}
.task-alloc-item:hover  { background:#f3eeff; border-color:#c4b8f0; }
.task-alloc-item.active { background:#ede9ff; border-color:#6351a7; }
.rank-badge { width:22px; height:22px; border-radius:50%; background:#ede9ff; color:#6351a7; font-size:10px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.task-alloc-item.active .rank-badge { background:#6351a7; color:#fff; }
.task-alloc-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.task-alloc-info { flex:1; min-width:0; }
.task-alloc-title { font-size:13px; font-weight:700; color:#1c1b20; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.task-alloc-meta  { font-size:11px; color:#797582; margin-top:2px; }
.task-alloc-bar-wrap { width:70px; }
.task-alloc-bar-bg   { background:#e8e0ff; border-radius:100px; height:5px; overflow:hidden; }
.task-alloc-bar-fill { height:100%; border-radius:100px; background:#6351a7; transition:width .4s; }
.task-alloc-time { font-size:12px; font-weight:700; color:#6351a7; min-width:44px; text-align:right; white-space:nowrap; }

.split-right { position:sticky; top:24px; display:flex; flex-direction:column; gap:14px; }
.panel-card { background:#fff; border-radius:20px; padding:18px 20px; box-shadow:0 8px 24px rgba(99,81,167,.1); border:1px solid #f0eaf8; }
.panel-card-title { font-size:12px; font-weight:700; color:#797582; text-transform:uppercase; letter-spacing:.5px; margin-bottom:14px; }

.total-stat { text-align:center; padding:6px 0; }
.total-stat-value { font-size:34px; font-weight:800; color:#6351a7; line-height:1; }
.total-stat-unit  { font-size:14px; font-weight:600; color:#797582; }
.total-stat-label { font-size:12px; color:#797582; margin-top:6px; }

.session-list { display:flex; flex-direction:column; gap:7px; max-height:340px; overflow-y:auto; }
.session-date-group { }
.session-date-header {
    display:flex; justify-content:space-between; align-items:center;
    padding:5px 8px; background:#f3eeff; border-radius:8px; margin-bottom:5px;
}
.session-date-header span { font-size:11px; font-weight:700; color:#6351a7; }
.session-item {
    display:flex; justify-content:space-between; align-items:center;
    padding:8px 10px; background:#fdf7ff; border:1px solid #ede9ff;
    border-radius:10px; margin-bottom:4px;
}
.session-item-time { font-size:11px; color:#797582; }
.session-item-dur  { font-size:13px; font-weight:800; color:#1c1b20; }

.panel-selected-title { font-size:13px; font-weight:800; color:#1c1b20; margin-bottom:4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.panel-selected-total { font-size:24px; font-weight:800; color:#6351a7; margin-bottom:14px; }
.no-hint { text-align:center; padding:20px; font-size:13px; color:#797582; }
</style>
@endpush

@section('content')

<div class="alloc-header">
    <h1>⏱ Alokasi Waktu</h1>
    <span class="alloc-header-meta">
        Total: <strong style="color:#6351a7">{{ $grandTotalFormatted }}</strong>
        dari <strong>{{ $taskSummaries->count() }}</strong> task
    </span>
</div>

<div class="split-shell">

    {{-- ══ LEFT ══ --}}
    <div class="split-left">
        <div class="split-left-header">
            <span class="split-left-title">📊 Distribusi Waktu per Task</span>
            <a href="{{ route('tasks.board') }}" class="btn-go-board">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px">
                    <rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/>
                    <rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/>
                </svg>
                Task Board
            </a>
        </div>

        <div class="split-left-body">

            {{-- Bubble chart --}}
            <div class="section-label">Proporsi Fokus</div>
            <div class="bubble-wrap" id="bubbleWrap">
                @forelse($taskSummaries as $i => $t)
                    @php
                        $size   = max(56, min(130, (int)($t['pct'] * 2.2 + 52)));
                        $colors = ['#6351a7','#006a61','#e05c2a','#1a6ef7','#9333ea','#0891b2','#d97706','#16a34a','#db2777','#7c3aed'];
                        $color  = $colors[$i % count($colors)];
                    @endphp
                    <div class="bubble"
                         id="bubble-{{ $t['task_id'] }}"
                         data-task-id="{{ $t['task_id'] }}"
                         style="width:{{ $size }}px;height:{{ $size }}px;background:{{ $color }}"
                         onclick="selectTask({{ $t['task_id'] }})">
                        <div class="bubble-label" style="max-width:{{ $size - 10 }}px">
                            {{ Str::limit($t['title'], 18) }}
                        </div>
                        <div class="bubble-pct">{{ $t['pct'] }}%</div>
                    </div>
                @empty
                    <div style="text-align:center;width:100%;padding:24px;font-size:13px;color:#797582">
                        📭 Belum ada sesi timer. Start timer di Task Board terlebih dahulu.
                    </div>
                @endforelse
            </div>

            {{-- Ranked task list --}}
            <div class="section-label">Urutan Task berdasarkan Waktu</div>
            @php
                $catColors = ['Design'=>'#6351a7','Development'=>'#006a61','Planning'=>'#d97706','Personal'=>'#ba1a1a','Meeting'=>'#1a6ef7'];
            @endphp

            @forelse($taskSummaries as $i => $t)
                @php
                    $dotColor = $catColors[$t['category']] ?? '#797582';
                @endphp
                <div class="task-alloc-item" id="item-{{ $t['task_id'] }}"
                     onclick="selectTask({{ $t['task_id'] }})">
                    <div class="rank-badge">{{ $i + 1 }}</div>
                    <div class="task-alloc-dot" style="background:{{ $dotColor }}"></div>
                    <div class="task-alloc-info">
                        <div class="task-alloc-title">{{ $t['title'] }}</div>
                        <div class="task-alloc-meta">
                            {{ $t['category'] ?? '—' }} &middot; {{ count($t['sessions']) }} sesi
                        </div>
                    </div>
                    <div class="task-alloc-bar-wrap">
                        <div class="task-alloc-bar-bg">
                            <div class="task-alloc-bar-fill" style="width:{{ $t['pct'] }}%"></div>
                        </div>
                    </div>
                    <div class="task-alloc-time">{{ $t['formatted_total'] }}</div>
                </div>
            @empty
                <div style="text-align:center;padding:32px;font-size:13px;color:#797582">
                    Belum ada data sesi fokus.
                </div>
            @endforelse

        </div>
    </div>

    {{-- ══ RIGHT ══ --}}
    <div class="split-right">

        {{-- Grand total card --}}
        <div class="panel-card">
            <div class="panel-card-title">Total Waktu Fokus</div>
            <div class="total-stat">
                <div class="total-stat-value">
                    {!! $grandTotalHtml !!}
                </div>
                <div class="total-stat-label">{{ $taskSummaries->count() }} task &middot; {{ $totalSessionCount }} sesi</div>
            </div>
        </div>

        {{-- Session detail panel --}}
        <div class="panel-card" id="sessionPanel">
            <div class="panel-card-title">Riwayat Sesi</div>
            <div class="no-hint">← Pilih task untuk melihat riwayat sesi</div>
        </div>

    </div>

</div>

<script>
const TASK_DATA = @json($taskSummaries);
let activeTaskId = null;

function fmtSec(totalSec) {
    const h = Math.floor(totalSec / 3600);
    const m = Math.floor((totalSec % 3600) / 60);
    const s = totalSec % 60;
    if (h > 0) return `${h}j ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}d`;
    if (m > 0) return `${m}m ${String(s).padStart(2,'0')}d`;
    return `${s}d`;
}

function selectTask(taskId) {
    document.querySelectorAll('.bubble').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.task-alloc-item').forEach(i => i.classList.remove('active'));

    const bubble = document.getElementById(`bubble-${taskId}`);
    const item   = document.getElementById(`item-${taskId}`);
    if (bubble) bubble.classList.add('active');
    if (item)   item.classList.add('active');

    activeTaskId = taskId;
    const task = TASK_DATA.find(t => t.task_id === taskId);
    if (!task) return;

    // Group sessions by date
    const byDate = {};
    task.sessions.forEach(s => {
        if (!byDate[s.date]) byDate[s.date] = [];
        byDate[s.date].push(s);
    });

    let sessionsHtml = '';
    if (task.sessions.length === 0) {
        sessionsHtml = '<div class="no-hint">Belum ada sesi tercatat.</div>';
    } else {
        Object.entries(byDate).forEach(([date, sessions]) => {
             const dayTotalSec = sessions.reduce((sum, s) => sum + (s.duration_seconds || 0), 0);
            sessionsHtml += `
            <div class="session-date-group">
                <div class="session-date-header">
                    <span>${date}</span>
                    <span>${fmtSec(dayTotalSec)}</span>
                </div>`;
            sessions.forEach(s => {
                sessionsHtml += `
                <div class="session-item">
                    <span class="session-item-time">${s.time}</span>
                    <span class="session-item-dur">${s.formatted}</span>
                </div>`;
            });
            sessionsHtml += '</div>';
        });
    }

    document.getElementById('sessionPanel').innerHTML = `
        <div class="panel-card-title">Riwayat Sesi</div>
        <div class="panel-selected-title">${escHtml(task.title)}</div>
        <div class="panel-selected-total">${task.formatted_total}</div>
        <div class="session-list">${sessionsHtml}</div>`;
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Auto-select first
if (TASK_DATA.length > 0) selectTask(TASK_DATA[0].task_id);
</script>
@endsection