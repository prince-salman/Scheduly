@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .page-header h1 {
        font-size: 24px;
        font-weight: 800;
        color: #1c1b20;
        letter-spacing: -0.4px;
    }

    .page-header p {
        font-size: 13px;
        color: #797582;
        margin-top: 3px;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #6351a7;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        font-family: inherit;
        transition: background 0.15s, transform 0.1s;
        box-shadow: 0 4px 14px rgba(99, 81, 167, 0.28);
    }

    .btn-primary:hover { background: #5240a0; }
    .btn-primary:active { transform: scale(0.98); }

    /* ── filter tabs ── */
    .filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-tabs {
        display: flex;
        background: #ffffff;
        border-radius: 14px;
        padding: 4px;
        gap: 2px;
        border: 1px solid #cac4d3;
    }

    .filter-tab {
        font-size: 13px;
        font-weight: 600;
        padding: 7px 18px;
        border-radius: 10px;
        border: none;
        background: transparent;
        color: #797582;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s, color 0.15s;
    }

    .filter-tab.active {
        background: #6351a7;
        color: #fff;
    }

    .filter-tab:hover:not(.active) {
        background: #f3eeff;
        color: #6351a7;
    }

    /* ── search ── */
    .search-wrap {
        position: relative;
        margin-left: auto;
    }

    .search-wrap svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        stroke: #797582;
        stroke-width: 2;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
        pointer-events: none;
    }

    .search-input {
        font-size: 14px;
        font-family: inherit;
        padding: 9px 14px 9px 36px;
        border: 1.5px solid #cac4d3;
        border-radius: 12px;
        background: #ffffff;
        color: #1c1b20;
        outline: none;
        width: 260px;
        transition: border-color 0.15s;
    }

    .search-input:focus { border-color: #6351a7; }
    .search-input::placeholder { color: #b0aac0; }

    /* ── table card ── */
    .table-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 0;
        box-shadow: 0 4px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0ecf8;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .user-table {
        width: 100%;
        border-collapse: collapse;
    }

    .user-table thead tr {
        background: #fdf7ff;
        border-bottom: 1px solid #f0ecf8;
    }

    .user-table th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #797582;
        padding: 14px 20px;
        text-align: left;
        white-space: nowrap;
    }

    .user-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #f9f5ff;
        vertical-align: middle;
    }

    .user-table tbody tr:last-child td {
        border-bottom: none;
    }

    .user-table tbody tr:hover td {
        background: #fdf7ff;
    }

    /* ── user cell ── */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-sm {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .user-cell-info .name {
        font-size: 14px;
        font-weight: 600;
        color: #1c1b20;
    }

    .user-cell-info .handle {
        font-size: 12px;
        color: #797582;
        margin-top: 1px;
    }

    /* ── role badge ── */
    .role-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
    }

    .role-admin  { background: #ede9ff; color: #6351a7; }
    .role-member { background: #d6faf5; color: #006a61; }
    .role-viewer { background: #f4f4f4; color: #797582; }

    /* ── status chip ── */
    .status-chip {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .status-chip::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-active  { background: #d6faf5; color: #006a61; }
    .status-pending { background: #fdf3b8; color: #6a5f00; }
    .status-ditolak { background: #ffedea; color: #ba1a1a; }

    /* ── action buttons ── */
    .action-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .act-btn {
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1.5px solid #cac4d3;
        background: transparent;
        color: #1c1b20;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: background 0.15s, border-color 0.15s;
        white-space: nowrap;
    }

    .act-btn:hover {
        background: #f3eeff;
        border-color: #6351a7;
        color: #6351a7;
    }

    .act-btn-role:hover {
        background: #d6faf5;
        border-color: #006a61;
        color: #006a61;
    }

    /* dropdown menu */
    .act-dropdown {
        position: relative;
        display: inline-block;
    }

    .act-dropdown-btn {
        font-size: 12px;
        font-weight: 600;
        padding: 6px 10px;
        border-radius: 8px;
        border: 1.5px solid #cac4d3;
        background: transparent;
        color: #797582;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s;
        line-height: 1;
    }

    .act-dropdown-btn:hover {
        background: #f5f5f5;
    }

    .act-dropdown-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        background: #ffffff;
        border: 1px solid #cac4d3;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        min-width: 160px;
        z-index: 50;
        display: none;
        overflow: hidden;
    }

    .act-dropdown-menu.open { display: block; }

    .act-dropdown-menu button,
    .act-dropdown-menu a {
        display: block;
        width: 100%;
        text-align: left;
        padding: 10px 16px;
        font-size: 13px;
        font-family: inherit;
        font-weight: 500;
        border: none;
        background: transparent;
        cursor: pointer;
        color: #1c1b20;
        text-decoration: none;
        transition: background 0.12s;
    }

    .act-dropdown-menu button:hover,
    .act-dropdown-menu a:hover { background: #f3eeff; }

    .act-dropdown-menu .danger {
        color: #ba1a1a;
    }

    .act-dropdown-menu .danger:hover { background: #ffedea; }

    /* ── pagination ── */
    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        border-top: 1px solid #f0ecf8;
    }

    .pagination-info {
        font-size: 13px;
        color: #797582;
    }

    .pagination-controls {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .page-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1.5px solid #cac4d3;
        background: transparent;
        font-size: 13px;
        font-weight: 600;
        color: #797582;
        cursor: pointer;
        font-family: inherit;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s, border-color 0.15s;
        text-decoration: none;
    }

    .page-btn:hover { background: #f3eeff; border-color: #6351a7; color: #6351a7; }
    .page-btn.current { background: #6351a7; border-color: #6351a7; color: #fff; }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="page-header">
    <div>
        <h1>Manajemen Pengguna</h1>
        <p>Kelola akun, peran, dan status pengguna platform.</p>
    </div>
    <a href="#" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah User
    </a>
</div>

{{-- Filter bar + search --}}
<div class="filter-bar">
    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">Semua</button>
        <button class="filter-tab" data-filter="active">Active</button>
        <button class="filter-tab" data-filter="pending">Pending</button>
        <button class="filter-tab" data-filter="ditolak">Ditolak</button>
    </div>
    <div class="search-wrap">
        <svg viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input class="search-input" type="text" placeholder="Cari nama atau email..." id="userSearch">
    </div>
</div>

{{-- User table --}}
<div class="table-card">
    <table class="user-table" id="userTable">
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

            {{-- 1 --}}
            <tr data-status="active">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background:#ede9ff;color:#6351a7">AD</div>
                        <div class="user-cell-info">
                            <div class="name">Ahmad Darmawan</div>
                            <div class="handle">@ahmaddarmawan</div>
                        </div>
                    </div>
                </td>
                <td>ahmad.d@company.id</td>
                <td><span class="role-badge role-admin">Admin</span></td>
                <td><span class="status-chip status-active">Active</span></td>
                <td>12 Jan 2024</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.users.detail', 1) }}" class="act-btn"><i data-lucide="eye" class="icon-sm"></i> Lihat</a>
                        <button class="act-btn act-btn-role"><i data-lucide="settings" class="icon-sm"></i> Ubah Role</button>
                        <div class="act-dropdown">
                            <button class="act-dropdown-btn" onclick="toggleDropdown(this)">···</button>
                            <div class="act-dropdown-menu">
                                <button class="danger">🚫 Nonaktifkan</button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            {{-- 2 --}}
            <tr data-status="active">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background:#d6faf5;color:#006a61">SR</div>
                        <div class="user-cell-info">
                            <div class="name">Sarah Reynolds</div>
                            <div class="handle">@sarahrey</div>
                        </div>
                    </div>
                </td>
                <td>sarah.r@startup.io</td>
                <td><span class="role-badge role-member">Member</span></td>
                <td><span class="status-chip status-active">Active</span></td>
                <td>18 Jan 2024</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.users.detail', 2) }}" class="act-btn"><i data-lucide="eye" class="icon-sm"></i> Lihat</a>
                        <button class="act-btn act-btn-role"><i data-lucide="settings" class="icon-sm"></i> Ubah Role</button>
                        <div class="act-dropdown">
                            <button class="act-dropdown-btn" onclick="toggleDropdown(this)">···</button>
                            <div class="act-dropdown-menu">
                                <button class="danger">🚫 Nonaktifkan</button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            {{-- 3 --}}
            <tr data-status="pending">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background:#fdf3b8;color:#6a5f00">JD</div>
                        <div class="user-cell-info">
                            <div class="name">John Doe</div>
                            <div class="handle">@johndoe</div>
                        </div>
                    </div>
                </td>
                <td>john.doe@company.com</td>
                <td><span class="role-badge role-member">Member</span></td>
                <td><span class="status-chip status-pending">Pending</span></td>
                <td>22 May 2024</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.users.detail', 3) }}" class="act-btn"><i data-lucide="eye" class="icon-sm"></i> Lihat</a>
                        <button class="act-btn act-btn-role"><i data-lucide="settings" class="icon-sm"></i> Ubah Role</button>
                        <div class="act-dropdown">
                            <button class="act-dropdown-btn" onclick="toggleDropdown(this)">···</button>
                            <div class="act-dropdown-menu">
                                <button class="danger"><i data-lucide="x-circle" class="icon-sm"></i> Tolak</button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            {{-- 4 --}}
            <tr data-status="active">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background:#ede9ff;color:#6351a7">RW</div>
                        <div class="user-cell-info">
                            <div class="name">Rina Wahyuni</div>
                            <div class="handle">@rinawahyuni</div>
                        </div>
                    </div>
                </td>
                <td>rina.w@gmail.com</td>
                <td><span class="role-badge role-member">Member</span></td>
                <td><span class="status-chip status-active">Active</span></td>
                <td>3 Feb 2024</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.users.detail', 4) }}" class="act-btn"><i data-lucide="eye" class="icon-sm"></i> Lihat</a>
                        <button class="act-btn act-btn-role"><i data-lucide="settings" class="icon-sm"></i> Ubah Role</button>
                        <div class="act-dropdown">
                            <button class="act-dropdown-btn" onclick="toggleDropdown(this)">···</button>
                            <div class="act-dropdown-menu">
                                <button class="danger">🚫 Nonaktifkan</button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            {{-- 5 --}}
            <tr data-status="ditolak">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background:#ffedea;color:#ba1a1a">BK</div>
                        <div class="user-cell-info">
                            <div class="name">Bambang Kurniawan</div>
                            <div class="handle">@bambangk</div>
                        </div>
                    </div>
                </td>
                <td>bambang.k@mail.co.id</td>
                <td><span class="role-badge role-viewer">Viewer</span></td>
                <td><span class="status-chip status-ditolak">Ditolak</span></td>
                <td>10 Mar 2024</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.users.detail', 5) }}" class="act-btn"><i data-lucide="eye" class="icon-sm"></i> Lihat</a>
                        <button class="act-btn act-btn-role"><i data-lucide="settings" class="icon-sm"></i> Ubah Role</button>
                        <div class="act-dropdown">
                            <button class="act-dropdown-btn" onclick="toggleDropdown(this)">···</button>
                            <div class="act-dropdown-menu">
                                <button><i data-lucide="check-circle" class="icon-sm"></i> Aktifkan</button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            {{-- 6 --}}
            <tr data-status="pending">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background:#fdf3b8;color:#6a5f00">MS</div>
                        <div class="user-cell-info">
                            <div class="name">Michael Smith</div>
                            <div class="handle">@msmith</div>
                        </div>
                    </div>
                </td>
                <td>m.smith@startup.io</td>
                <td><span class="role-badge role-member">Member</span></td>
                <td><span class="status-chip status-pending">Pending</span></td>
                <td>23 May 2024</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.users.detail', 6) }}" class="act-btn"><i data-lucide="eye" class="icon-sm"></i> Lihat</a>
                        <button class="act-btn act-btn-role"><i data-lucide="settings" class="icon-sm"></i> Ubah Role</button>
                        <div class="act-dropdown">
                            <button class="act-dropdown-btn" onclick="toggleDropdown(this)">···</button>
                            <div class="act-dropdown-menu">
                                <button class="danger"><i data-lucide="x-circle" class="icon-sm"></i> Tolak</button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            {{-- 7 --}}
            <tr data-status="active">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background:#d6faf5;color:#006a61">DH</div>
                        <div class="user-cell-info">
                            <div class="name">Diana Hartono</div>
                            <div class="handle">@dianahartono</div>
                        </div>
                    </div>
                </td>
                <td>diana.h@enterprise.com</td>
                <td><span class="role-badge role-admin">Admin</span></td>
                <td><span class="status-chip status-active">Active</span></td>
                <td>5 Dec 2023</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.users.detail', 7) }}" class="act-btn"><i data-lucide="eye" class="icon-sm"></i> Lihat</a>
                        <button class="act-btn act-btn-role"><i data-lucide="settings" class="icon-sm"></i> Ubah Role</button>
                        <div class="act-dropdown">
                            <button class="act-dropdown-btn" onclick="toggleDropdown(this)">···</button>
                            <div class="act-dropdown-menu">
                                <button class="danger">🚫 Nonaktifkan</button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            {{-- 8 --}}
            <tr data-status="active">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background:#ede9ff;color:#6351a7">PL</div>
                        <div class="user-cell-info">
                            <div class="name">Pedro Lima</div>
                            <div class="handle">@pedrolima</div>
                        </div>
                    </div>
                </td>
                <td>pedro.l@freelance.net</td>
                <td><span class="role-badge role-viewer">Viewer</span></td>
                <td><span class="status-chip status-active">Active</span></td>
                <td>14 Apr 2024</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.users.detail', 8) }}" class="act-btn"><i data-lucide="eye" class="icon-sm"></i> Lihat</a>
                        <button class="act-btn act-btn-role"><i data-lucide="settings" class="icon-sm"></i> Ubah Role</button>
                        <div class="act-dropdown">
                            <button class="act-dropdown-btn" onclick="toggleDropdown(this)">···</button>
                            <div class="act-dropdown-menu">
                                <button class="danger">🚫 Nonaktifkan</button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            {{-- 9 --}}
            <tr data-status="pending">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background:#fdf3b8;color:#6a5f00">AR</div>
                        <div class="user-cell-info">
                            <div class="name">Aisha Rahman</div>
                            <div class="handle">@aisharahman</div>
                        </div>
                    </div>
                </td>
                <td>aisha.r@freelance.net</td>
                <td><span class="role-badge role-member">Member</span></td>
                <td><span class="status-chip status-pending">Pending</span></td>
                <td>23 May 2024</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.users.detail', 9) }}" class="act-btn"><i data-lucide="eye" class="icon-sm"></i> Lihat</a>
                        <button class="act-btn act-btn-role"><i data-lucide="settings" class="icon-sm"></i> Ubah Role</button>
                        <div class="act-dropdown">
                            <button class="act-dropdown-btn" onclick="toggleDropdown(this)">···</button>
                            <div class="act-dropdown-menu">
                                <button class="danger"><i data-lucide="x-circle" class="icon-sm"></i> Tolak</button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            {{-- 10 --}}
            <tr data-status="ditolak">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background:#ffedea;color:#ba1a1a">FN</div>
                        <div class="user-cell-info">
                            <div class="name">Farid Naufal</div>
                            <div class="handle">@faridnaufal</div>
                        </div>
                    </div>
                </td>
                <td>farid.n@gmail.com</td>
                <td><span class="role-badge role-viewer">Viewer</span></td>
                <td><span class="status-chip status-ditolak">Ditolak</span></td>
                <td>2 May 2024</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.users.detail', 10) }}" class="act-btn"><i data-lucide="eye" class="icon-sm"></i> Lihat</a>
                        <button class="act-btn act-btn-role"><i data-lucide="settings" class="icon-sm"></i> Ubah Role</button>
                        <div class="act-dropdown">
                            <button class="act-dropdown-btn" onclick="toggleDropdown(this)">···</button>
                            <div class="act-dropdown-menu">
                                <button><i data-lucide="check-circle" class="icon-sm"></i> Aktifkan</button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="pagination-bar">
        <div class="pagination-info">Menampilkan 1–10 dari 47 pengguna</div>
        <div class="pagination-controls">
            <button class="page-btn" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <a href="#" class="page-btn current">1</a>
            <a href="#" class="page-btn">2</a>
            <a href="#" class="page-btn">3</a>
            <span style="color:#797582;font-size:13px;padding:0 4px">...</span>
            <a href="#" class="page-btn">5</a>
            <button class="page-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            document.querySelectorAll('#userTable tbody tr').forEach(row => {
                if (filter === 'all' || row.dataset.status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // search
    document.getElementById('userSearch').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#userTable tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    });

    // dropdown toggle
    function toggleDropdown(btn) {
        const menu = btn.nextElementSibling;
        // close all others first
        document.querySelectorAll('.act-dropdown-menu.open').forEach(m => {
            if (m !== menu) m.classList.remove('open');
        });
        menu.classList.toggle('open');
    }

    // close dropdowns on outside click
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.act-dropdown')) {
            document.querySelectorAll('.act-dropdown-menu.open').forEach(m => m.classList.remove('open'));
        }
    });
</script>
@endpush
