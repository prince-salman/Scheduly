@extends('layouts.admin')
@section('title', 'Detail Pengguna - ' . $user->name)

@push('styles')
<style>
    /* ── back button ── */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #797582;
        text-decoration: none;
        margin-bottom: 20px;
        padding: 6px 0;
        transition: color 0.15s;
    }

    .back-link:hover { color: #6351a7; }

    .back-link svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2.5;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* ── profile card ── */
    .profile-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0ecf8;
        display: flex;
        align-items: center;
        gap: 24px;
        margin-bottom: 24px;
    }

    .profile-avatar-lg {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        color: #6351a7;
        background: linear-gradient(135deg, #ede9ff 0%, #d6c8ff 100%);
        flex-shrink: 0;
        letter-spacing: -1px;
        box-shadow: 0 4px 14px rgba(99, 81, 167, 0.2);
    }

    .profile-meta { flex: 1; }

    .profile-meta .full-name {
        font-size: 22px;
        font-weight: 800;
        color: #1c1b20;
        letter-spacing: -0.3px;
    }

    .profile-meta .email-addr {
        font-size: 14px;
        color: #797582;
        margin-top: 2px;
    }

    .profile-meta .member-since {
        font-size: 12px;
        color: #b0aac0;
        margin-top: 4px;
    }

    .status-chip-lg {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 5px 14px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
    }

    .status-chip-lg::before {
        content: '';
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .chip-active  { background: #d6faf5; color: #006a61; }
    .chip-pending { background: #fdf3b8; color: #6a5f00; }
    .chip-ditolak { background: #ffedea; color: #ba1a1a; }

    /* ── two column grid ── */
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .detail-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0ecf8;
    }

    .detail-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #6351a7;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-card-title svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .info-rows {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .info-row-label {
        font-size: 13px;
        font-weight: 500;
        color: #797582;
        flex-shrink: 0;
    }

    .info-row-value {
        font-size: 14px;
        font-weight: 600;
        color: #1c1b20;
        text-align: right;
    }

    /* ── activity stats ── */
    .activity-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .activity-stat-item {
        background: #fdf7ff;
        border-radius: 14px;
        padding: 14px 16px;
        text-align: center;
    }

    .activity-stat-item .val {
        font-size: 22px;
        font-weight: 800;
        color: #6351a7;
        letter-spacing: -0.5px;
    }

    .activity-stat-item .lbl {
        font-size: 12px;
        color: #797582;
        margin-top: 2px;
        font-weight: 500;
    }

    /* ── approval action card ── */
    .approval-action-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(181, 162, 255, 0.15);
        border: 2px solid #fdf3b8;
        margin-bottom: 24px;
    }

    .approval-action-card .aa-title {
        font-size: 16px;
        font-weight: 700;
        color: #1c1b20;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .approval-action-card .aa-sub {
        font-size: 13px;
        color: #797582;
        margin-bottom: 20px;
    }

    .role-row {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1c1b20;
        margin-bottom: 6px;
    }

    .role-select {
        font-size: 14px;
        font-family: inherit;
        padding: 10px 16px;
        border: 1.5px solid #cac4d3;
        border-radius: 12px;
        background: #fdf7ff;
        color: #1c1b20;
        outline: none;
        width: 240px;
        cursor: pointer;
        transition: border-color 0.15s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23797582' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
    }

    .role-select:focus { border-color: #6351a7; }

    .approval-btns {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .btn-approve-lg {
        font-size: 14px;
        font-weight: 700;
        padding: 11px 28px;
        border-radius: 14px;
        border: none;
        background: #006a61;
        color: #fff;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s, transform 0.1s;
        box-shadow: 0 4px 14px rgba(0, 106, 97, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-approve-lg:hover { background: #005850; }
    .btn-approve-lg:active { transform: scale(0.98); }

    .btn-reject-lg {
        font-size: 14px;
        font-weight: 700;
        padding: 11px 28px;
        border-radius: 14px;
        border: 2px solid #ba1a1a;
        background: transparent;
        color: #ba1a1a;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s, transform 0.1s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-reject-lg:hover { background: #ffedea; }
    .btn-reject-lg:active { transform: scale(0.98); }

    /* rejection reason textarea (hidden by default) */
    .rejection-form {
        margin-top: 20px;
        display: none;
    }

    .rejection-form.show { display: block; }

    .rejection-textarea {
        width: 100%;
        height: 100px;
        border: 1.5px solid #ba1a1a;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 14px;
        font-family: inherit;
        color: #1c1b20;
        resize: none;
        outline: none;
        background: #fff8f8;
        transition: border-color 0.15s;
        margin-top: 6px;
    }

    .rejection-textarea:focus { border-color: #8c0e0e; }

    .rejection-submit {
        margin-top: 12px;
        font-size: 14px;
        font-weight: 700;
        padding: 10px 24px;
        border-radius: 12px;
        border: none;
        background: #ba1a1a;
        color: #fff;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s, transform 0.1s;
    }

    .rejection-submit:hover { background: #a01515; }
    .rejection-submit:active { transform: scale(0.98); }

    /* ── tasks table ── */
    .tasks-section {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0ecf8;
        overflow: hidden;
    }

    .tasks-section-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f0ecf8;
    }

    .tasks-section-header h3 {
        font-size: 15px;
        font-weight: 700;
        color: #1c1b20;
    }

    .tasks-section-header p {
        font-size: 13px;
        color: #797582;
        margin-top: 2px;
    }

    .tasks-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tasks-table thead tr {
        background: #fdf7ff;
        border-bottom: 1px solid #f0ecf8;
    }

    .tasks-table th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #797582;
        padding: 12px 20px;
        text-align: left;
    }

    .tasks-table td {
        padding: 13px 20px;
        border-bottom: 1px solid #f9f5ff;
        font-size: 14px;
        color: #1c1b20;
        vertical-align: middle;
    }

    .tasks-table tbody tr:last-child td { border-bottom: none; }
    .tasks-table tbody tr:hover td { background: #fdf7ff; }

    .task-name-cell {
        font-weight: 600;
        color: #1c1b20;
    }

    .task-priority {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .prio-high   { background: #ffedea; color: #ba1a1a; }
    .prio-medium { background: #fdf3b8; color: #6a5f00; }
    .prio-low    { background: #d6faf5; color: #006a61; }

    .task-status-chip {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .ts-done       { background: #d6faf5; color: #006a61; }
    .ts-inprogress { background: #ede9ff; color: #6351a7; }
    .ts-todo       { background: #f4f4f4; color: #797582; }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .profile-card { flex-direction: column; text-align: center; }
        .profile-meta .status-chip-lg { margin: 10px auto; }
        .activity-stats { grid-template-columns: 1fr; }
        .approval-btns { flex-direction: column; align-items: stretch; }
        .btn-approve-lg, .btn-reject-lg { justify-content: center; width: 100%; }
        .role-select { width: 100%; }
        .tasks-section { overflow-x: auto; }
        .tasks-table { min-width: 600px; }
    }
</style>
@endpush

@section('content')

{{-- Back link --}}
<a href="{{ route('admin.users.index') }}" class="back-link">
    <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    Manajemen Pengguna
</a>

{{-- ── Profile card ── --}}
<div class="profile-card">
    {{-- fallback menggunakan inisial nama jika foto kosong --}}
    <div class="profile-avatar-lg">
        {{ strtoupper(substr($user->name, 0, 2)) }}
    </div>

    <div class="profile-meta">
        <div class="full-name">{{ $user->name }}</div>
        <div class="email-addr">{{ $user->email }}</div>
        
        {{-- status chip dinamis sesuai status model --}}
        @if($user->status === 'active')
            <div class="status-chip-lg chip-active">Active</div>
        @elseif($user->status === 'pending')
            <div class="status-chip-lg chip-pending">Pending</div>
        @elseif($user->status === 'rejected')
            <div class="status-chip-lg chip-ditolak">Ditolak</div>
        @endif

        <div class="member-since">Member sejak {{ $user->created_at->translatedFormat('d F Y') }}</div>
    </div>
</div>

{{-- ── Two column detail grid ── --}}
<div class="detail-grid">

    {{-- LEFT: User Info --}}
    <div class="detail-card">
        <div class="detail-card-title">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Informasi Pengguna
        </div>
        <div class="info-rows">
            <div class="info-row">
                <span class="info-row-label">Nama Lengkap</span>
                <span class="info-row-value">{{ $user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">Email</span>
                <span class="info-row-value">{{ $user->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">Role</span>
                <span class="info-row-value" style="text-transform: capitalize;">{{ $user->role ?? 'Member' }}</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">Bergabung</span>
                <span class="info-row-value">{{ $user->created_at->translatedFormat('d F Y') }}</span>
            </div>
            @if($user->isRejected())
            <div class="info-row">
                <span class="info-row-label" style="color: #ba1a1a;">Alasan Penolakan</span>
                <span class="info-row-value" style="color: #ba1a1a;">{{ $user->reason }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: Activity Summary --}}
    <div class="detail-card">
        <div class="detail-card-title">
            <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Ringkasan Aktivitas
        </div>
        <div class="activity-stats">
            <div class="activity-stat-item">
                <div class="val">{{ $user->tasks->count() }}</div>
                <div class="lbl">Tasks Dibuat</div>
            </div>
            <div class="activity-stat-item">
                <div class="val">{{ $user->tasks->where('status', 'done')->count() }}</div>
                <div class="lbl">Tasks Selesai</div>
            </div>
            <div class="activity-stat-item">
                {{-- Menghitung total jam fokus dari akumulasi focus_minutes di database --}}
                <div class="val">{{ round($user->tasks->sum('focus_minutes') / 60, 1) }} jam</div>
                <div class="lbl">Jam Fokus</div>
            </div>
            <div class="activity-stat-item">
                <div class="val">—</div>
                <div class="lbl">Last Login</div>
            </div>
        </div>
    </div>

</div>

{{-- ── Pending Approval Actions (Hanya muncul jika status akun pending) ── --}}
@if($user->status === 'pending')
<div class="approval-action-card">
    <div class="aa-title">
         Akun Menunggu Persetujuan
    </div>
    <p class="aa-sub">Tinjau informasi pengguna di atas, pilih role yang sesuai, lalu setujui atau tolak akun ini.</p>

    <div class="role-row">
        <label class="form-label">Pilih Role</label>
        <select class="role-select" id="roleSelect" onchange="syncRole()">
            <option value="user">User / Member</option>
            <option value="admin">Super Admin</option>
        </select>
    </div>

    <div class="approval-btns">
        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" style="margin:0">
            @csrf
            @method('PATCH')
            <input type="hidden" name="role" id="roleInputHidden" value="user">
            <button type="submit" class="btn-approve-lg">
                Setujui Akun
            </button>
        </form>

        <button class="btn-reject-lg" id="toggleRejectBtn" onclick="toggleRejectForm()">
            Tolak Pengajuan
        </button>
    </div>

    {{-- Rejection reason form --}}
    <div class="rejection-form" id="rejectionForm">
        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <label class="form-label" style="color:#ba1a1a">Alasan Penolakan</label>
            <textarea
                class="rejection-textarea"
                name="reason"
                placeholder="Jelaskan alasan penolakan akun ini..."
                required
            ></textarea>
            <div>
                <button type="submit" class="rejection-submit">Kirim & Tolak Akun</button>
                <button type="button" onclick="toggleRejectForm()" style="font-size:13px;font-weight:600;color:#797582;background:none;border:none;cursor:pointer;padding:10px 16px;font-family:inherit">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ── Recent Tasks by this user ── --}}
<div class="tasks-section">
    <div class="tasks-section-header">
        <h3>Task Terbaru</h3>
        <p>5 task terakhir yang dibuat atau dikerjakan oleh pengguna ini.</p>
    </div>
    <table class="tasks-table">
        <thead>
            <tr>
                <th>Judul Task</th>
                <th>Kategori</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            {{-- Mengambil 5 task terbaru milik user secara real --}}
            @forelse($user->tasks()->latest()->take(5)->get() as $task)
            <tr>
                <td class="task-name-cell">{{ $task->title }}</td>
                <td>{{ $task->category ?? 'General' }}</td>
                <td>
                    @if($task->priority === 'high')
                        <span class="task-priority prio-high">High</span>
                    @elseif($task->priority === 'medium')
                        <span class="task-priority prio-medium">Medium</span>
                    @else
                        <span class="task-priority prio-low">Low</span>
                    @endif
                </td>
                <td>
                    @if($task->status === 'done')
                        <span class="task-status-chip ts-done">Selesai</span>
                    @elseif($task->status === 'in_progress')
                        <span class="task-status-chip ts-inprogress">In Progress</span>
                    @else
                        <span class="task-status-chip ts-todo">To Do</span>
                    @endif
                </td>
                <td>{{ $task->due_date ? $task->due_date->translatedFormat('d M Y') : '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #797582; padding: 20px;">
                    Belum ada task yang dibuat oleh pengguna ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
function toggleRejectForm() {
    const form = document.getElementById('rejectionForm');
    form.classList.toggle('show');
}

function syncRole() {
    const roleVal = document.getElementById('roleSelect').value;
    document.getElementById('roleInputHidden').value = roleVal;
}

// Set value default saat halaman dimuat pertama kali
document.addEventListener('DOMContentLoaded', function() {
    syncRole();
});
</script>
@endpush