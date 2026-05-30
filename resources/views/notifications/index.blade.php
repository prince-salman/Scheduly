@extends('layouts.app')
@section('title', 'Notifikasi')

@push('styles')
<style>
    .notif-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
    .notif-header h1 { font-size:22px; font-weight:800; color:#1c1b20; }
    .btn-mark-all { padding:9px 18px; border-radius:12px; border:1.5px solid #cac4d3; background:none; font-family:inherit; font-size:13px; font-weight:600; color:#6351a7; cursor:pointer; transition:background .15s,border-color .15s; }
    .btn-mark-all:hover { background:#f3eeff; border-color:#6351a7; }

    .filter-chips { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:22px; }
    .filter-chip { padding:7px 16px; border-radius:100px; border:1.5px solid #cac4d3; background:#fff; font-family:inherit; font-size:13px; font-weight:600; color:#797582; cursor:pointer; transition:background .15s,border-color .15s,color .15s; text-decoration:none; }
    .filter-chip:hover { border-color:#6351a7; color:#6351a7; }
    .filter-chip.active { background:#6351a7; border-color:#6351a7; color:#fff; }

    .notif-list { background:#fff; border-radius:24px; box-shadow:0 8px 20px rgba(181,162,255,.15); border:1px solid #f0eaf8; overflow:hidden; }

    .notif-item { display:flex; align-items:flex-start; gap:14px; padding:16px 20px; border-bottom:1px solid #f6f1ff; position:relative; transition:background .15s; cursor:pointer; }
    .notif-item:last-child { border-bottom:none; }
    .notif-item:hover { background:#faf8ff; }
    .notif-item.unread { background:#fdf7ff; }
    .notif-item.unread:hover { background:#f6f0ff; }

    .notif-icon { width:42px; height:42px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
    .notif-icon.deadline { background:#fde8e8; }
    .notif-icon.reminder { background:#ede9ff; }
    .notif-icon.alarm    { background:#d0f5f3; }
    .notif-icon.system   { background:#f0f0f0; }

    .notif-body { flex:1; min-width:0; }
    .notif-title { font-size:14px; font-weight:700; color:#1c1b20; margin-bottom:3px; }
    .notif-desc  { font-size:13px; color:#797582; line-height:1.45; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    .notif-meta { display:flex; flex-direction:column; align-items:flex-end; gap:8px; flex-shrink:0; padding-top:2px; }
    .notif-time { font-size:11px; color:#aaa; white-space:nowrap; }
    .unread-dot { width:8px; height:8px; border-radius:50%; background:#6351a7; }

    .notif-date-section { padding:10px 20px 6px; font-size:11px; font-weight:700; color:#797582; text-transform:uppercase; letter-spacing:.7px; background:#fdf7ff; border-bottom:1px solid #f6f1ff; }

    .notif-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:60px 20px; text-align:center; }
    .notif-empty-icon { font-size:56px; margin-bottom:16px; opacity:.5; }
    .notif-empty h3 { font-size:18px; font-weight:700; color:#1c1b20; margin-bottom:6px; }
    .notif-empty p  { font-size:14px; color:#797582; max-width:280px; }
</style>
@endpush

@section('content')

<div class="notif-header">
    <h1>🔔 Notifikasi
        @if($unreadCount > 0)
            <span style="font-size:13px;font-weight:600;color:#6351a7;background:#ede9ff;padding:3px 10px;border-radius:20px;margin-left:6px">{{ $unreadCount }}</span>
        @endif
    </h1>
    <form method="POST" action="{{ route('notifications.read.all') }}">
        @csrf @method('PATCH')
        <button type="submit" class="btn-mark-all">✓ Tandai Semua Dibaca</button>
    </form>
</div>

{{-- Filter chips --}}
<div class="filter-chips">
    <a href="{{ route('notifications.index') }}"
       class="filter-chip {{ !request('type') ? 'active' : '' }}">Semua</a>
    <a href="{{ route('notifications.index', ['type' => 'deadline']) }}"
       class="filter-chip {{ request('type') === 'deadline' ? 'active' : '' }}">⚠ Deadline</a>
    <a href="{{ route('notifications.index', ['type' => 'reminder']) }}"
       class="filter-chip {{ request('type') === 'reminder' ? 'active' : '' }}">🔔 Reminder</a>
    <a href="{{ route('notifications.index', ['type' => 'alarm']) }}"
       class="filter-chip {{ request('type') === 'alarm' ? 'active' : '' }}">⏱ Alarm</a>
</div>

<div class="notif-list">

    @forelse($grouped as $section => $items)

        <div class="notif-date-section">{{ $section }}</div>

        @foreach($items as $notif)
        @php
            $icon = match($notif->type) {
                'deadline' => '⚠️',
                'reminder' => '🔔',
                'alarm'    => '⏱',
                default    => '📢',
            };
        @endphp
        <div class="notif-item {{ $notif->is_read ? '' : 'unread' }}"
             data-id="{{ $notif->id }}"
             onclick="markRead(this, {{ $notif->id }})">
            <div class="notif-icon {{ $notif->type }}">{{ $icon }}</div>
            <div class="notif-body">
                <div class="notif-title">{{ $notif->title }}</div>
                <div class="notif-desc">{{ $notif->description }}</div>
            </div>
            <div class="notif-meta">
                <span class="notif-time">{{ $notif->created_at->diffForHumans() }}</span>
                @if(!$notif->is_read)
                    <span class="unread-dot" id="dot-{{ $notif->id }}"></span>
                @endif
            </div>
        </div>
        @endforeach

    @empty
        <div class="notif-empty">
            <div class="notif-empty-icon">🎉</div>
            <h3>Semua sudah dibaca!</h3>
            <p>Kamu sudah membaca semua notifikasi. Mantap!</p>
        </div>
    @endforelse

</div>

{{-- pagination --}}
@if($notifications->hasPages())
<div style="margin-top:16px;display:flex;justify-content:flex-end">
    {{ $notifications->withQueryString()->links() }}
</div>
@endif

@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

async function markRead(el, id) {
    // remove unread style immediately (optimistic UI)
    el.classList.remove('unread');
    const dot = document.getElementById('dot-' + id);
    if (dot) dot.remove();

    await fetch(`/notifications/${id}/read`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
}
</script>
@endpush