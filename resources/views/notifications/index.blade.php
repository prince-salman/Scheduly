@extends('layouts.app')
@section('title', 'Notifikasi')

@push('styles')
<style>
    /* --- Page header --- */
    .notif-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .notif-header h1 {
        font-size: 22px;
        font-weight: 800;
        color: #1c1b20;
    }

    .btn-mark-all {
        padding: 9px 18px;
        border-radius: 12px;
        border: 1.5px solid #cac4d3;
        background: none;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: #6351a7;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
    }

    .btn-mark-all:hover {
        background: #f3eeff;
        border-color: #6351a7;
    }

    /* --- Filter chips --- */
    .filter-chips {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }

    .filter-chip {
        padding: 7px 16px;
        border-radius: 100px;
        border: 1.5px solid #cac4d3;
        background: #ffffff;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: #797582;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }

    .filter-chip:hover {
        border-color: #6351a7;
        color: #6351a7;
    }

    .filter-chip.active {
        background: #6351a7;
        border-color: #6351a7;
        color: #ffffff;
    }

    /* --- Notifications list container --- */
    .notif-list {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 8px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0eaf8;
        overflow: hidden;
    }

    /* --- Single notification item --- */
    .notif-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 20px;
        border-bottom: 1px solid #f6f1ff;
        position: relative;
        transition: background 0.15s;
        cursor: pointer;
    }

    .notif-item:last-child { border-bottom: none; }

    .notif-item:hover { background: #faf8ff; }

    /* unread items get a subtle tint */
    .notif-item.unread {
        background: #fdf7ff;
    }

    .notif-item.unread:hover { background: #f6f0ff; }

    /* icon circle */
    .notif-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .notif-icon.deadline { background: #fde8e8; }
    .notif-icon.reminder { background: #ede9ff; }
    .notif-icon.alarm    { background: #d0f5f3; }
    .notif-icon.system   { background: #f0f0f0; }

    /* text block */
    .notif-body { flex: 1; min-width: 0; }

    .notif-title {
        font-size: 14px;
        font-weight: 700;
        color: #1c1b20;
        margin-bottom: 3px;
    }

    .notif-desc {
        font-size: 13px;
        color: #797582;
        line-height: 1.45;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* right side: time + unread dot */
    .notif-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        flex-shrink: 0;
        padding-top: 2px;
    }

    .notif-time {
        font-size: 11px;
        color: #aaa;
        white-space: nowrap;
    }

    .unread-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #6351a7;
    }

    /* --- Empty state --- */
    .notif-empty {
        display: none; /* shown via JS when all read */
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        text-align: center;
    }

    .notif-empty.visible { display: flex; }

    .notif-empty-icon {
        font-size: 56px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .notif-empty h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1c1b20;
        margin-bottom: 6px;
    }

    .notif-empty p {
        font-size: 14px;
        color: #797582;
        max-width: 280px;
    }

    /* section date label */
    .notif-date-section {
        padding: 10px 20px 6px;
        font-size: 11px;
        font-weight: 700;
        color: #797582;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        background: #fdf7ff;
        border-bottom: 1px solid #f6f1ff;
    }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="notif-header">
    <h1><i data-lucide="bell" class="icon-sm"></i> Notifikasi</h1>
    <button class="btn-mark-all" id="markAllBtn" onclick="markAllRead()">
        ✓ Tandai Semua Dibaca
    </button>
</div>

{{-- Filter chips --}}
<div class="filter-chips" id="filterChips">
    <button class="filter-chip active" data-filter="semua" onclick="filterNotif(this)">Semua</button>
    <button class="filter-chip" data-filter="deadline" onclick="filterNotif(this)"><i data-lucide="alert-triangle" class="icon-sm"></i> Deadline</button>
    <button class="filter-chip" data-filter="reminder" onclick="filterNotif(this)"><i data-lucide="bell" class="icon-sm"></i> Reminder</button>
    <button class="filter-chip" data-filter="alarm" onclick="filterNotif(this)"><i data-lucide="clock" class="icon-sm"></i> Alarm</button>
</div>

{{-- Notification list --}}
<div class="notif-list" id="notifList">

    {{-- Hari Ini --}}
    <div class="notif-date-section" data-section-label>Hari Ini</div>

    <div class="notif-item unread" data-type="deadline">
        <div class="notif-icon deadline"><i data-lucide="alert-triangle" class="icon-sm"></i></div>
        <div class="notif-body">
            <div class="notif-title">Deadline Task</div>
            <div class="notif-desc">Review weekly schedule and goals jatuh tempo hari ini pukul 17:00</div>
        </div>
        <div class="notif-meta">
            <span class="notif-time">5 mnt lalu</span>
            <span class="unread-dot"></span>
        </div>
    </div>

    <div class="notif-item unread" data-type="reminder">
        <div class="notif-icon reminder"><i data-lucide="bell" class="icon-sm"></i></div>
        <div class="notif-body">
            <div class="notif-title">Reminder</div>
            <div class="notif-desc">Meeting dengan Client dalam 30 menit — siapkan materi presentasi kamu</div>
        </div>
        <div class="notif-meta">
            <span class="notif-time">25 mnt lalu</span>
            <span class="unread-dot"></span>
        </div>
    </div>

    <div class="notif-item unread" data-type="alarm">
        <div class="notif-icon alarm"><i data-lucide="clock" class="icon-sm"></i></div>
        <div class="notif-body">
            <div class="notif-title">Alarm Timer</div>
            <div class="notif-desc">Sesi fokus 25 menit pada Buat Laporan Mingguan selesai!</div>
        </div>
        <div class="notif-meta">
            <span class="notif-time">1 jam lalu</span>
            <span class="unread-dot"></span>
        </div>
    </div>

    <div class="notif-item unread" data-type="reminder">
        <div class="notif-icon reminder"><i data-lucide="bell" class="icon-sm"></i></div>
        <div class="notif-body">
            <div class="notif-title">Reminder</div>
            <div class="notif-desc">Gym Session terjadwal pukul 11:00 — jangan lupa warm up!</div>
        </div>
        <div class="notif-meta">
            <span class="notif-time">2 jam lalu</span>
            <span class="unread-dot"></span>
        </div>
    </div>

    <div class="notif-item" data-type="alarm">
        <div class="notif-icon alarm"><i data-lucide="clock" class="icon-sm"></i></div>
        <div class="notif-body">
            <div class="notif-title">Alarm Timer</div>
            <div class="notif-desc">Sesi fokus 50 menit pada Review Design System selesai!</div>
        </div>
        <div class="notif-meta">
            <span class="notif-time">3 jam lalu</span>
        </div>
    </div>

    {{-- Kemarin --}}
    <div class="notif-date-section" data-section-label>Kemarin</div>

    <div class="notif-item" data-type="deadline">
        <div class="notif-icon deadline"><i data-lucide="alert-triangle" class="icon-sm"></i></div>
        <div class="notif-body">
            <div class="notif-title">Deadline Task</div>
            <div class="notif-desc">Create soft UI component library — deadline besok! Selesaikan hari ini.</div>
        </div>
        <div class="notif-meta">
            <span class="notif-time">Kemarin, 16:00</span>
        </div>
    </div>

    <div class="notif-item" data-type="reminder">
        <div class="notif-icon reminder"><i data-lucide="bell" class="icon-sm"></i></div>
        <div class="notif-body">
            <div class="notif-title">Reminder</div>
            <div class="notif-desc">Team Sync dimulai dalam 15 menit — link Zoom tersedia di kalender</div>
        </div>
        <div class="notif-meta">
            <span class="notif-time">Kemarin, 08:45</span>
        </div>
    </div>

    <div class="notif-item" data-type="alarm">
        <div class="notif-icon alarm"><i data-lucide="clock" class="icon-sm"></i></div>
        <div class="notif-body">
            <div class="notif-title">Alarm Timer</div>
            <div class="notif-desc">Kamu sudah bekerja 4 jam hari ini. Istirahat sebentar ya!</div>
        </div>
        <div class="notif-meta">
            <span class="notif-time">Kemarin, 13:00</span>
        </div>
    </div>

    <div class="notif-item" data-type="deadline">
        <div class="notif-icon deadline"><i data-lucide="alert-triangle" class="icon-sm"></i></div>
        <div class="notif-body">
            <div class="notif-title">Deadline Task Terlewat</div>
            <div class="notif-desc">Define brand anchors & JSON structure — sudah melewati deadline tapi sudah diselesaikan <i data-lucide="check-circle" class="icon-sm"></i></div>
        </div>
        <div class="notif-meta">
            <span class="notif-time">Kemarin, 09:00</span>
        </div>
    </div>

    {{-- Empty state (hidden by default) --}}
    <div class="notif-empty" id="emptyState">
        <div class="notif-empty-icon">🎉</div>
        <h3>Semua sudah dibaca!</h3>
        <p>Kamu sudah membaca semua notifikasi. Mantap!</p>
    </div>

</div>{{-- /.notif-list --}}

@endsection

@push('scripts')
<script>
    // --- Filter by type ---
    function filterNotif(btn) {
        document.querySelectorAll('#filterChips .filter-chip').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');

        const filter = btn.dataset.filter;
        const items = document.querySelectorAll('#notifList .notif-item');

        items.forEach(item => {
            if (filter === 'semua' || item.dataset.type === filter) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });

        // hide section labels if all items in section are hidden — simple approach
        checkEmptyState();
    }

    // --- Mark all read ---
    function markAllRead() {
        document.querySelectorAll('.notif-item.unread').forEach(item => {
            item.classList.remove('unread');
            const dot = item.querySelector('.unread-dot');
            if (dot) dot.remove();
        });
    }

    // click individual item to mark read
    document.querySelectorAll('.notif-item').forEach(item => {
        item.addEventListener('click', () => {
            item.classList.remove('unread');
            const dot = item.querySelector('.unread-dot');
            if (dot) dot.remove();
        });
    });

    function checkEmptyState() {
        const visible = [...document.querySelectorAll('#notifList .notif-item')].some(el => el.style.display !== 'none');
        const emptyEl = document.getElementById('emptyState');
        if (emptyEl) emptyEl.classList.toggle('visible', !visible);
    }
</script>
@endpush
