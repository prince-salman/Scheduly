@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@push('styles')
<style>
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .page-header h1 { font-size: 24px; font-weight: 800; color: #1c1b20; letter-spacing: -0.4px; }
    .page-header p  { font-size: 13px; color: #797582; margin-top: 3px; }
    .btn-primary {
        display: inline-flex; align-items: center; gap: 6px;
        background: #6351a7; color: #fff; font-size: 14px; font-weight: 600;
        padding: 10px 20px; border-radius: 12px; border: none; cursor: pointer;
        text-decoration: none; font-family: inherit;
        box-shadow: 0 4px 14px rgba(99,81,167,.28); transition: background 0.15s;
    }
    .btn-primary:hover { background: #5240a0; }

    .filter-bar { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
    .filter-tabs { display: flex; background: #fff; border-radius: 14px; padding: 4px; gap: 2px; border: 1px solid #cac4d3; }
    .filter-tab {
        font-size: 13px; font-weight: 600; padding: 7px 16px; border-radius: 10px;
        border: none; background: transparent; color: #797582; cursor: pointer;
        font-family: inherit; transition: background 0.15s, color 0.15s; text-decoration: none;
    }
    .filter-tab.active { background: #6351a7; color: #fff; }
    .filter-tab:hover:not(.active) { background: #f3eeff; color: #6351a7; }

    .filter-tab sup {
        font-size: 10px; font-weight: 700; background: rgba(255,255,255,.3);
        padding: 1px 5px; border-radius: 20px; margin-left: 4px;
    }
    .filter-tab:not(.active) sup { background: #ede9ff; color: #6351a7; }

    .search-wrap { position: relative; margin-left: auto; }
    .search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: #797582; stroke-width: 2; fill: none; pointer-events: none; }
    .search-input {
        font-size: 14px; font-family: inherit; padding: 9px 14px 9px 36px;
        border: 1.5px solid #cac4d3; border-radius: 12px; background: #fff;
        color: #1c1b20; outline: none; width: 260px; transition: border-color 0.15s;
    }
    .search-input:focus { border-color: #6351a7; }
    .search-input::placeholder { color: #b0aac0; }

    .table-card { background: #fff; border-radius: 24px; box-shadow: 0 4px 20px rgba(181,162,255,.15); border: 1px solid #f0ecf8; overflow: hidden; margin-bottom: 24px; }
    .user-table { width: 100%; border-collapse: collapse; }
    .user-table thead tr { background: #fdf7ff; border-bottom: 1px solid #f0ecf8; }
    .user-table th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #797582; padding: 14px 20px; text-align: left; white-space: nowrap; }
    .user-table td { padding: 14px 20px; border-bottom: 1px solid #f9f5ff; vertical-align: middle; }
    .user-table tbody tr:last-child td { border-bottom: none; }
    .user-table tbody tr:hover td { background: #fdf7ff; }

    .user-cell { display: flex; align-items: center; gap: 12px; }
    .user-avatar-sm { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0; }
    .user-cell-info .name   { font-size: 14px; font-weight: 600; color: #1c1b20; }
    .user-cell-info .handle { font-size: 12px; color: #797582; margin-top: 1px; }

    .role-badge { font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 20px; display: inline-block; }
    .role-admin  { background: #ede9ff; color: #6351a7; }
    .role-member { background: #d6faf5; color: #006a61; }
    .role-viewer { background: #f4f4f4; color: #797582; }

    .status-chip { font-size: 11px; font-weight: 700; letter-spacing: 0.4px; text-transform: uppercase; padding: 4px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px; }
    .status-chip::before { content: ''; display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .status-active  { background: #d6faf5; color: #006a61; }
    .status-pending { background: #fdf3b8; color: #6a5f00; }
    .status-rejected{ background: #ffedea; color: #ba1a1a; }

    .action-group { display: flex; align-items: center; gap: 6px; }
    .act-btn {
        font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 8px;
        border: 1.5px solid #cac4d3; background: transparent; color: #1c1b20;
        cursor: pointer; font-family: inherit; text-decoration: none;
        display: inline-flex; align-items: center; gap: 4px;
        transition: background 0.15s, border-color 0.15s; white-space: nowrap;
    }
    .act-btn:hover { background: #f3eeff; border-color: #6351a7; color: #6351a7; }

    .act-dropdown { position: relative; display: inline-block; }
    .act-dropdown-btn { font-size: 12px; font-weight: 600; padding: 6px 10px; border-radius: 8px; border: 1.5px solid #cac4d3; background: transparent; color: #797582; cursor: pointer; font-family: inherit; transition: background 0.15s; line-height: 1; }
    .act-dropdown-btn:hover { background: #f5f5f5; }
    .act-dropdown-menu { position: absolute; right: 0; top: calc(100% + 6px); background: #fff; border: 1px solid #cac4d3; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.12); min-width: 160px; z-index: 50; display: none; overflow: hidden; }
    .act-dropdown-menu.open { display: block; }
    .act-dropdown-menu button, .act-dropdown-menu a { display: block; width: 100%; text-align: left; padding: 10px 16px; font-size: 13px; font-family: inherit; font-weight: 500; border: none; background: transparent; cursor: pointer; color: #1c1b20; text-decoration: none; transition: background 0.12s; }
    .act-dropdown-menu button:hover, .act-dropdown-menu a:hover { background: #f3eeff; }
    .act-dropdown-menu .danger { color: #ba1a1a; }
    .act-dropdown-menu .danger:hover { background: #ffedea; }

    .pagination-bar { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-top: 1px solid #f0ecf8; }
    .pagination-info { font-size: 13px; color: #797582; }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1>Manajemen Pengguna</h1>
        <p>Kelola akun, peran, dan status pengguna platform.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah User
    </a>
</div>

{{-- Filter tabs + search --}}
<div class="filter-bar">
    <div class="filter-tabs">
        <a href="{{ route('admin.users.index') }}"
           class="filter-tab {{ !request('status') ? 'active' : '' }}">
            Semua <sup>{{ $counts['all'] }}</sup>
        </a>
        <a href="{{ route('admin.users.index', ['status' => 'approved']) }}"
           class="filter-tab {{ request('status') === 'approved' ? 'active' : '' }}">
            Active <sup>{{ $counts['approved'] }}</sup>
        </a>
        <a href="{{ route('admin.users.index', ['status' => 'pending']) }}"
           class="filter-tab {{ request('status') === 'pending' ? 'active' : '' }}">
            Pending <sup>{{ $counts['pending'] }}</sup>
        </a>
        <a href="{{ route('admin.users.index', ['status' => 'rejected']) }}"
           class="filter-tab {{ request('status') === 'rejected' ? 'active' : '' }}">
            Ditolak <sup>{{ $counts['rejected'] }}</sup>
        </a>
    </div>

    <div class="search-wrap">
        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <form method="GET" action="{{ route('admin.users.index') }}" style="display:inline">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input class="search-input" type="text" name="search" placeholder="Cari nama atau email..."
                   value="{{ request('search') }}" oninput="this.form.submit()">
        </form>
    </div>
</div>

{{-- Table --}}
<div class="table-card">
    <table class="user-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
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
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background:#ede9ff;color:#6351a7">{{ $user->initials }}</div>
                        <div class="user-cell-info">
                            <div class="name">{{ $user->name }}</div>
                            <div class="handle">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="role-badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                </td>
                <td>
                    @php
                        $chipClass = match($user->status) {
                            'approved' => 'status-active',
                            'pending'  => 'status-pending',
                            'rejected' => 'status-rejected',
                            default    => 'status-pending',
                        };
                        $chipLabel = match($user->status) {
                            'approved' => 'Active',
                            'pending'  => 'Pending',
                            'rejected' => 'Ditolak',
                            default    => $user->status,
                        };
                    @endphp
                    <span class="status-chip {{ $chipClass }}">{{ $chipLabel }}</span>
                </td>
                <td>{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.users.detail', $user->id) }}" class="act-btn">👁 Lihat</a>

                        <div class="act-dropdown">
                            <button class="act-dropdown-btn" onclick="toggleDropdown(this)">···</button>
                            <div class="act-dropdown-menu">
                                @if($user->status === 'pending')
                                    <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="role" value="{{ $user->role }}">
                                        <button type="submit">✅ Setujui</button>
                                    </form>
                                    <button class="danger" onclick="openRejectModal({{ $user->id }}, '{{ $user->name }}')">❌ Tolak</button>
                                @elseif($user->status === 'approved')
                                    <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="danger">🚫 Nonaktifkan</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit">✅ Aktifkan</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:#797582;font-size:14px">
                    Tidak ada pengguna ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="pagination-bar">
        <div class="pagination-info">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna
        </div>
        <div>{{ $users->withQueryString()->links() }}</div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal-overlay" id="rejectModal" style="position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:200;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity 0.2s">
    <div style="background:#fff;border-radius:20px;padding:28px;width:440px;max-width:94vw;box-shadow:0 12px 40px rgba(0,0,0,.18)">
        <div style="font-size:17px;font-weight:700;color:#1c1b20;margin-bottom:6px">Tolak Pendaftaran</div>
        <p style="font-size:13px;color:#797582;margin-bottom:18px" id="rejectModalSub"></p>
        <form action="" method="POST" id="rejectForm">
            @csrf @method('PATCH')
            <label style="display:block;font-size:13px;font-weight:600;color:#1c1b20;margin-bottom:6px">Alasan Penolakan</label>
            <textarea name="reason" id="rejectReason" required
                style="width:100%;height:100px;border:1.5px solid #cac4d3;border-radius:12px;padding:10px 14px;font-size:14px;font-family:inherit;color:#1c1b20;resize:none;outline:none;background:#fdf7ff"
                placeholder="Jelaskan alasan penolakan..."></textarea>
            <div style="display:flex;gap:10px;margin-top:18px;justify-content:flex-end">
                <button type="button" onclick="closeRejectModal()"
                    style="font-size:14px;font-weight:600;padding:9px 20px;border-radius:12px;border:1.5px solid #cac4d3;background:transparent;color:#797582;cursor:pointer;font-family:inherit">Batal</button>
                <button type="submit"
                    style="font-size:14px;font-weight:600;padding:9px 20px;border-radius:12px;border:none;background:#ba1a1a;color:#fff;cursor:pointer;font-family:inherit">Tolak Akun</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleDropdown(btn) {
    const menu = btn.nextElementSibling;
    document.querySelectorAll('.act-dropdown-menu.open').forEach(m => { if (m !== menu) m.classList.remove('open'); });
    menu.classList.toggle('open');
}
document.addEventListener('click', e => {
    if (!e.target.closest('.act-dropdown')) document.querySelectorAll('.act-dropdown-menu.open').forEach(m => m.classList.remove('open'));
});

const rejectModal = document.getElementById('rejectModal');
function openRejectModal(userId, userName) {
    document.getElementById('rejectModalSub').textContent = 'Berikan alasan penolakan untuk ' + userName + '.';
    document.getElementById('rejectForm').action = '/admin/users/' + userId + '/reject';
    document.getElementById('rejectReason').value = '';
    rejectModal.style.opacity = '1'; rejectModal.style.pointerEvents = 'all';
}
function closeRejectModal() { rejectModal.style.opacity = '0'; rejectModal.style.pointerEvents = 'none'; }
rejectModal.addEventListener('click', e => { if (e.target === rejectModal) closeRejectModal(); });
</script>
@endpush