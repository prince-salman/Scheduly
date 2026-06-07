@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

    * { box-sizing: border-box; }

    .um-wrap { font-family: 'Instrument Sans', sans-serif; color: #18171c; padding: 0; }

    /* Header */
    .um-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 32px; gap: 16px; }
    .um-header-left h1 { font-size: 26px; font-weight: 700; letter-spacing: -0.6px; color: #18171c; margin: 0 0 4px; line-height: 1.2; }
    .um-header-left p { font-size: 13.5px; color: #78757f; margin: 0; font-weight: 400; }
    .um-btn-add {
        display: inline-flex; align-items: center; gap: 7px;
        background: #18171c; color: #fff; font-family: 'Instrument Sans', sans-serif;
        font-size: 13.5px; font-weight: 600; padding: 10px 18px; border-radius: 10px;
        border: none; cursor: pointer; text-decoration: none; white-space: nowrap;
        transition: background 0.15s, transform 0.1s; letter-spacing: -0.1px;
    }
    .um-btn-add:hover { background: #2e2d35; }
    .um-btn-add:active { transform: scale(0.98); }
    .um-btn-add svg { width: 14px; height: 14px; stroke: #fff; stroke-width: 2.5; }

    /* Stats */
    .um-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
    .um-stat-card {
        background: #fff; border: 1px solid #eceaf1; border-radius: 14px;
        padding: 16px 18px; display: flex; align-items: center; gap: 12px; transition: border-color 0.15s;
    }
    .um-stat-card:hover { border-color: #c5bfda; }
    .um-stat-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 17px; }
    .um-stat-icon.all      { background: #f1eeff; }
    .um-stat-icon.active   { background: #e3faf3; }
    .um-stat-icon.pending  { background: #fef9e7; }
    .um-stat-icon.rejected { background: #fef0f0; }
    .um-stat-num { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; color: #18171c; line-height: 1; margin-bottom: 2px; }
    .um-stat-label { font-size: 12px; color: #9895a2; font-weight: 500; text-transform: uppercase; letter-spacing: 0.4px; }

    /* Toolbar */
    .um-toolbar { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
    .um-tabs { display: flex; background: #f5f3fa; border-radius: 10px; padding: 3px; gap: 2px; }
    .um-tab {
        font-family: 'Instrument Sans', sans-serif; font-size: 13px; font-weight: 600;
        padding: 6px 14px; border-radius: 7px; border: none; background: transparent;
        color: #78757f; cursor: pointer; text-decoration: none;
        transition: background 0.15s, color 0.15s;
        display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;
    }
    .um-tab.active { background: #fff; color: #18171c; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    .um-tab:hover:not(.active) { color: #18171c; }
    .um-tab-count { font-size: 11px; font-weight: 700; padding: 1px 6px; border-radius: 20px; background: #ece9f8; color: #5a4d9e; line-height: 1.4; }

    .um-search-wrap { position: relative; margin-left: auto; }
    .um-search-wrap svg { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; stroke: #9895a2; stroke-width: 2; fill: none; pointer-events: none; }
    .um-search {
        font-family: 'Instrument Sans', sans-serif; font-size: 13.5px;
        padding: 8px 14px 8px 34px; border: 1px solid #e0dcea; border-radius: 10px;
        background: #fff; color: #18171c; outline: none; width: 240px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .um-search:focus { border-color: #8b7fd4; box-shadow: 0 0 0 3px rgba(139,127,212,.12); }
    .um-search::placeholder { color: #b8b4c4; }

    /* Table */
    .um-table-wrap { background: #fff; border: 1px solid #eceaf1; border-radius: 16px; overflow: hidden; }
    .um-table { width: 100%; border-collapse: collapse; }
    .um-table thead tr { border-bottom: 1px solid #f0edf8; background: #faf9fd; }
    .um-table th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: #9895a2; padding: 13px 20px; text-align: left; white-space: nowrap; }
    .um-table td { padding: 13px 20px; border-bottom: 1px solid #f7f5fb; vertical-align: middle; font-size: 13.5px; }
    .um-table tbody tr:last-child td { border-bottom: none; }
    .um-table tbody tr { transition: background 0.1s; }
    .um-table tbody tr:hover td { background: #faf9fd; }

    .um-user-cell { display: flex; align-items: center; gap: 11px; }
    .um-avatar { width: 34px; height: 34px; border-radius: 50%; background: #ece9f8; color: #5a4d9e; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; letter-spacing: 0.3px; }
    .um-user-name { font-size: 13.5px; font-weight: 600; color: #18171c; line-height: 1.3; }
    .um-user-email { font-size: 12px; color: #9895a2; margin-top: 1px; font-family: 'DM Mono', monospace; }

    .um-role { font-size: 11.5px; font-weight: 600; padding: 3px 9px; border-radius: 6px; display: inline-block; letter-spacing: 0.1px; }
    .um-role-admin  { background: #ece9f8; color: #5a4d9e; }
    .um-role-user   { background: #e3faf3; color: #0a6647; }
    .um-role-member { background: #e3faf3; color: #0a6647; }

    .um-status { font-size: 11.5px; font-weight: 600; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px; letter-spacing: 0.1px; }
    .um-status::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
    .um-status-active   { background: #e3faf3; color: #0a6647; }
    .um-status-pending  { background: #fef9e7; color: #7a6500; }
    .um-status-rejected { background: #fef0f0; color: #b91c1c; }

    .um-date { font-family: 'DM Mono', monospace; font-size: 12px; color: #9895a2; }

    /* Actions */
    .um-actions { display: flex; align-items: center; gap: 6px; }
    .um-act-view {
        font-family: 'Instrument Sans', sans-serif; font-size: 12.5px; font-weight: 600;
        padding: 5px 12px; border-radius: 7px; border: 1px solid #e0dcea;
        background: transparent; color: #4a4460; cursor: pointer; text-decoration: none;
        display: inline-flex; align-items: center; gap: 5px;
        transition: background 0.12s, border-color 0.12s; white-space: nowrap;
    }
    .um-act-view:hover { background: #f5f3fa; border-color: #c5bfda; }
    .um-act-view svg { width: 13px; height: 13px; stroke: currentColor; stroke-width: 2; fill: none; }

    .um-more-wrap { position: relative; }
    .um-more-btn {
        width: 30px; height: 30px; border-radius: 7px; border: 1px solid #e0dcea;
        background: transparent; color: #78757f; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.12s; font-size: 15px; line-height: 1;
        letter-spacing: 1px; font-weight: 700;
    }
    .um-more-btn:hover { background: #f5f3fa; border-color: #c5bfda; }

    .um-dropdown {
        position: absolute; right: 0; top: calc(100% + 6px);
        background: #fff; border: 1px solid #e0dcea; border-radius: 12px;
        box-shadow: 0 8px 28px rgba(24,23,28,.12); min-width: 172px;
        z-index: 100; display: none; overflow: hidden; padding: 4px;
    }
    .um-dropdown.open { display: block; }
    .um-dropdown-item {
        display: flex; align-items: center; gap: 8px; width: 100%; text-align: left;
        padding: 8px 12px; font-family: 'Instrument Sans', sans-serif;
        font-size: 13px; font-weight: 500; border: none; background: transparent;
        cursor: pointer; color: #18171c; text-decoration: none;
        border-radius: 8px; transition: background 0.1s; white-space: nowrap;
    }
    .um-dropdown-item:hover { background: #f5f3fa; }
    .um-dropdown-item svg { width: 14px; height: 14px; stroke: currentColor; stroke-width: 2; fill: none; flex-shrink: 0; }
    .um-dropdown-item.is-danger { color: #b91c1c; }
    .um-dropdown-item.is-danger:hover { background: #fef0f0; }
    .um-dropdown-item.is-success { color: #0a6647; }
    .um-dropdown-item.is-success:hover { background: #e3faf3; }
    .um-dropdown-sep { height: 1px; background: #f0edf8; margin: 4px 0; }

    /* Empty */
    .um-empty { text-align: center; padding: 56px 20px; color: #9895a2; }
    .um-empty-icon { width: 48px; height: 48px; background: #f5f3fa; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
    .um-empty-icon svg { width: 22px; height: 22px; stroke: #b8b4c4; stroke-width: 1.8; fill: none; }
    .um-empty p { font-size: 14px; margin: 0; }

    /* Pagination */
    .um-pagination { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid #f0edf8; }
    .um-pagination-info { font-size: 12.5px; color: #9895a2; }

    /* Modals */
    .um-modal-backdrop {
        position: fixed; inset: 0; background: rgba(18,17,22,.45); z-index: 300;
        display: flex; align-items: center; justify-content: center; padding: 20px;
        opacity: 0; pointer-events: none; transition: opacity 0.2s; backdrop-filter: blur(2px);
    }
    .um-modal-backdrop.open { opacity: 1; pointer-events: all; }
    .um-modal {
        background: #fff; border-radius: 18px; padding: 28px; width: 100%; max-width: 420px;
        box-shadow: 0 24px 60px rgba(0,0,0,.18); transform: translateY(8px); transition: transform 0.2s;
    }
    .um-modal-backdrop.open .um-modal { transform: translateY(0); }
    .um-modal-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
    .um-modal-icon.danger { background: #fef0f0; }
    .um-modal-icon.danger svg { stroke: #b91c1c; }
    .um-modal-icon svg { width: 22px; height: 22px; stroke-width: 2; fill: none; }
    .um-modal-title { font-size: 16px; font-weight: 700; color: #18171c; margin: 0 0 6px; letter-spacing: -0.3px; }
    .um-modal-sub { font-size: 13px; color: #78757f; margin: 0 0 20px; line-height: 1.5; }
    .um-modal-label { display: block; font-size: 12.5px; font-weight: 600; color: #18171c; margin-bottom: 6px; }
    .um-modal-textarea {
        width: 100%; height: 96px; border: 1px solid #e0dcea; border-radius: 10px;
        padding: 10px 13px; font-family: 'Instrument Sans', sans-serif; font-size: 13.5px;
        color: #18171c; resize: none; outline: none; background: #faf9fd;
        transition: border-color 0.15s, box-shadow 0.15s; line-height: 1.5;
    }
    .um-modal-textarea:focus { border-color: #8b7fd4; box-shadow: 0 0 0 3px rgba(139,127,212,.12); background: #fff; }
    .um-modal-textarea::placeholder { color: #b8b4c4; }
    .um-modal-footer { display: flex; gap: 8px; margin-top: 18px; justify-content: flex-end; }
    .um-modal-btn { font-family: 'Instrument Sans', sans-serif; font-size: 13.5px; font-weight: 600; padding: 9px 18px; border-radius: 9px; border: 1px solid transparent; cursor: pointer; transition: background 0.12s, transform 0.1s; }
    .um-modal-btn:active { transform: scale(0.98); }
    .um-modal-btn.cancel { background: #f5f3fa; color: #78757f; border-color: #e0dcea; }
    .um-modal-btn.cancel:hover { background: #eceaf1; }
    .um-modal-btn.confirm-danger { background: #b91c1c; color: #fff; border-color: #b91c1c; }
    .um-modal-btn.confirm-danger:hover { background: #991414; }

    @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    .um-flash { display: flex; align-items: center; gap: 10px; background: #e3faf3; border: 1px solid #a7f3d4; border-radius: 10px; padding: 11px 16px; font-size: 13.5px; color: #0a6647; font-weight: 500; margin-bottom: 20px; animation: slideDown 0.25s ease; }
    .um-flash svg { width: 16px; height: 16px; stroke: #0a6647; stroke-width: 2.5; fill: none; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="um-wrap">

    @if(session('success'))
    <div class="um-flash">
        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="um-header">
        <div class="um-header-left">
            <h1>Manajemen Pengguna</h1>
            <p>Kelola akun, peran, dan status pengguna platform.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="um-btn-add">
            <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah User
        </a>
    </div>

    {{-- Stats --}}
    <div class="um-stats">
        <div class="um-stat-card">
            <div class="um-stat-icon all">👥</div>
            <div><div class="um-stat-num">{{ $counts['all'] }}</div><div class="um-stat-label">Total</div></div>
        </div>
        <div class="um-stat-card">
            <div class="um-stat-icon active">✅</div>
            <div><div class="um-stat-num">{{ $counts['approved'] }}</div><div class="um-stat-label">Aktif</div></div>
        </div>
        <div class="um-stat-card">
            <div class="um-stat-icon pending">⏳</div>
            <div><div class="um-stat-num">{{ $counts['pending'] }}</div><div class="um-stat-label">Pending</div></div>
        </div>
        <div class="um-stat-card">
            <div class="um-stat-icon rejected">🚫</div>
            <div><div class="um-stat-num">{{ $counts['rejected'] }}</div><div class="um-stat-label">Ditolak</div></div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="um-toolbar">
        <div class="um-tabs">
            <a href="{{ route('admin.users.index') }}" class="um-tab {{ !request('status') ? 'active' : '' }}">
                Semua <span class="um-tab-count">{{ $counts['all'] }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'approved']) }}" class="um-tab {{ request('status') === 'approved' ? 'active' : '' }}">
                Aktif <span class="um-tab-count">{{ $counts['approved'] }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="um-tab {{ request('status') === 'pending' ? 'active' : '' }}">
                Pending <span class="um-tab-count">{{ $counts['pending'] }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'rejected']) }}" class="um-tab {{ request('status') === 'rejected' ? 'active' : '' }}">
                Ditolak <span class="um-tab-count">{{ $counts['rejected'] }}</span>
            </a>
        </div>
        <div class="um-search-wrap">
            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <form method="GET" action="{{ route('admin.users.index') }}" id="searchForm">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input class="um-search" type="text" name="search"
                       placeholder="Cari nama atau email…"
                       value="{{ request('search') }}"
                       oninput="clearTimeout(window._st);window._st=setTimeout(()=>document.getElementById('searchForm').submit(),350)">
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="um-table-wrap">
        <table class="um-table">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="um-user-cell">
                            <div class="um-avatar">{{ $user->initials }}</div>
                            <div>
                                <div class="um-user-name">{{ $user->name }}</div>
                                <div class="um-user-email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="um-role um-role-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                    <td>
                        @php
                            $sc = match($user->status) { 'approved' => 'active', 'pending' => 'pending', 'rejected' => 'rejected', default => 'pending' };
                            $sl = match($user->status) { 'approved' => 'Aktif', 'pending' => 'Pending', 'rejected' => 'Ditolak', default => $user->status };
                        @endphp
                        <span class="um-status um-status-{{ $sc }}">{{ $sl }}</span>
                    </td>
                    <td><span class="um-date">{{ $user->created_at->format('d M Y') }}</span></td>
                    <td>
                        <div class="um-actions">
                            <a href="{{ route('admin.users.detail', $user->id) }}" class="um-act-view">
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                Lihat
                            </a>
                            <div class="um-more-wrap">
                                <button class="um-more-btn" onclick="toggleDrop(this,event)" type="button">···</button>
                                <div class="um-dropdown">
                                    @if($user->status === 'pending')
                                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="role" value="{{ $user->role }}">
                                            <button type="submit" class="um-dropdown-item is-success">
                                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                                Setujui
                                            </button>
                                        </form>
                                        <div class="um-dropdown-sep"></div>
                                        <button type="button" class="um-dropdown-item is-danger"
                                            data-action="reject"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ addslashes($user->name) }}"
                                            data-url="{{ route('admin.users.reject', $user->id) }}">
                                            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                            Tolak
                                        </button>
                                    @elseif($user->status === 'approved')
                                        <button type="button" class="um-dropdown-item is-danger"
                                            data-action="deactivate"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ addslashes($user->name) }}"
                                            data-url="{{ route('admin.users.toggle', $user->id) }}">
                                            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                            Nonaktifkan
                                        </button>
                                    @else
                                        <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="um-dropdown-item is-success">
                                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                Aktifkan Kembali
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="um-empty">
                            <div class="um-empty-icon">
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            </div>
                            <p>Tidak ada pengguna ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
        <div class="um-pagination">
            <div class="um-pagination-info">
                @if($users->firstItem())
                    Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna
                @endif
            </div>
            <div>{{ $users->withQueryString()->links() }}</div>
        </div>
        @endif
    </div>

    {{-- Modal: Tolak --}}
    <div class="um-modal-backdrop" id="modal-reject">
        <div class="um-modal">
            <div class="um-modal-icon danger">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div class="um-modal-title">Tolak Pendaftaran</div>
            <p class="um-modal-sub" id="reject-sub"></p>
            <form method="POST" id="reject-form">
                @csrf @method('PATCH')
                <label class="um-modal-label">Alasan Penolakan</label>
                <textarea name="reason" class="um-modal-textarea" required placeholder="Jelaskan alasan penolakan kepada pengguna…"></textarea>
                <div class="um-modal-footer">
                    <button type="button" class="um-modal-btn cancel" onclick="closeModal('reject')">Batal</button>
                    <button type="submit" class="um-modal-btn confirm-danger">Tolak Akun</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Nonaktifkan --}}
    <div class="um-modal-backdrop" id="modal-deactivate">
        <div class="um-modal">
            <div class="um-modal-icon danger">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
            </div>
            <div class="um-modal-title">Nonaktifkan Akun</div>
            <p class="um-modal-sub" id="deactivate-sub"></p>
            <form method="POST" id="deactivate-form">
                @csrf @method('PATCH')
                <label class="um-modal-label">Alasan Penonaktifan</label>
                <textarea name="reason" class="um-modal-textarea" required placeholder="Jelaskan alasan penonaktifan akun ini…"></textarea>
                <div class="um-modal-footer">
                    <button type="button" class="um-modal-btn cancel" onclick="closeModal('deactivate')">Batal</button>
                    <button type="submit" class="um-modal-btn confirm-danger">Nonaktifkan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleDrop(btn, e) {
    e.stopPropagation();
    const menu = btn.nextElementSibling;
    document.querySelectorAll('.um-dropdown.open').forEach(m => { if (m !== menu) m.classList.remove('open'); });
    menu.classList.toggle('open');
}
document.addEventListener('click', () => {
    document.querySelectorAll('.um-dropdown.open').forEach(m => m.classList.remove('open'));
});

// Bind modal triggers via data attributes — no hardcoded URLs
document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;
    e.stopPropagation();

    const action = btn.dataset.action;
    const name   = btn.dataset.name;
    const url    = btn.dataset.url;

    document.querySelectorAll('.um-dropdown.open').forEach(m => m.classList.remove('open'));

    if (action === 'reject') {
        document.getElementById('reject-sub').textContent = 'Berikan alasan penolakan untuk akun ' + name + '.';
        document.getElementById('reject-form').action = url;
        document.querySelector('#reject-form textarea').value = '';
        document.getElementById('modal-reject').classList.add('open');
    } else if (action === 'deactivate') {
        document.getElementById('deactivate-sub').textContent = 'Masukkan alasan penonaktifan untuk akun ' + name + '.';
        document.getElementById('deactivate-form').action = url;
        document.querySelector('#deactivate-form textarea').value = '';
        document.getElementById('modal-deactivate').classList.add('open');
    }
});

function closeModal(type) {
    document.getElementById('modal-' + type).classList.remove('open');
}

document.querySelectorAll('.um-modal-backdrop').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.um-modal-backdrop.open').forEach(m => m.classList.remove('open'));
});
</script>
@endpush