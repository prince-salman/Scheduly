@extends('layouts.admin')
@section('title', 'Admin Overview')

@push('styles')
<style>
    .admin-page-header { margin-bottom: 28px; }
    .admin-page-header h1 { font-size: 26px; font-weight: 800; color: #1c1b20; letter-spacing: -0.4px; }
    .admin-page-header p  { font-size: 14px; color: #797582; margin-top: 4px; }

    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px; }

    .stat-card {
        background: #fff; border-radius: 24px; padding: 22px 24px;
        box-shadow: 0 4px 20px rgba(181,162,255,.15); border: 1px solid #f0ecf8;
        display: flex; flex-direction: column; gap: 12px;
    }
    .stat-card-top { display: flex; align-items: flex-start; justify-content: space-between; }
    .stat-card-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
    }
    .stat-card-icon svg { width: 22px; height: 22px; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; fill: none; }
    .icon-primary   { background: #ede9ff; } .icon-primary   svg { stroke: #6351a7; }
    .icon-secondary { background: #d6faf5; } .icon-secondary svg { stroke: #006a61; }
    .icon-tertiary  { background: #fdf3b8; } .icon-tertiary  svg { stroke: #6a5f00; }
    .icon-error     { background: #ffedea; } .icon-error     svg { stroke: #ba1a1a; }
    .stat-value  { font-size: 28px; font-weight: 800; color: #1c1b20; letter-spacing: -0.5px; line-height: 1; }
    .stat-label  { font-size: 13px; font-weight: 500; color: #797582; margin-top: 2px; }
    .stat-badge  { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; padding: 3px 8px; border-radius: 20px; margin-top: 4px; width: fit-content; }
    .badge-up   { background: #d6faf5; color: #006a61; }
    .badge-warn { background: #fdf3b8; color: #6a5f00; }
    .badge-down { background: #ffedea; color: #ba1a1a; }

    .charts-row { display: grid; grid-template-columns: 1fr 380px; gap: 20px; margin-bottom: 28px; }
    .chart-card {
        background: #fff; border-radius: 24px; padding: 24px;
        box-shadow: 0 4px 20px rgba(181,162,255,.15); border: 1px solid #f0ecf8;
    }
    .chart-card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
    .chart-card-title  { font-size: 16px; font-weight: 700; color: #1c1b20; }
    .chart-canvas-wrap { position: relative; height: 220px; }

    .donut-canvas-wrap { position: relative; height: 200px; margin-bottom: 16px; }
    .donut-legend { display: flex; flex-direction: column; gap: 10px; }
    .legend-item { display: flex; align-items: center; justify-content: space-between; font-size: 13px; }
    .legend-left { display: flex; align-items: center; gap: 8px; color: #1c1b20; font-weight: 500; }
    .legend-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .legend-pct  { font-weight: 700; color: #797582; }

    .approvals-section {
        background: #fff; border-radius: 24px; padding: 24px;
        box-shadow: 0 4px 20px rgba(181,162,255,.15); border: 1px solid #f0ecf8;
        margin-bottom: 32px;
    }
    .section-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
    .section-header-left h2 { font-size: 17px; font-weight: 700; color: #1c1b20; }
    .section-header-left p  { font-size: 13px; color: #797582; margin-top: 3px; }
    .view-all-link {
        font-size: 13px; font-weight: 600; color: #6351a7; text-decoration: none;
        padding: 6px 14px; border: 1px solid #cac4d3; border-radius: 10px;
        transition: background 0.15s; white-space: nowrap;
    }
    .view-all-link:hover { background: #f3eeff; }

    .approval-item { display: flex; align-items: center; gap: 16px; padding: 16px 0; border-bottom: 1px solid #f0ecf8; }
    .approval-item:last-child { border-bottom: none; padding-bottom: 0; }

    .user-avatar {
        width: 42px; height: 42px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; font-weight: 700; color: #6351a7; background: #ede9ff; flex-shrink: 0;
    }
    .user-info { flex: 1; min-width: 0; }
    .user-name  { font-size: 14px; font-weight: 700; color: #1c1b20; }
    .user-email { font-size: 12px; color: #797582; margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .approval-meta { font-size: 12px; color: #797582; font-weight: 500; min-width: 80px; text-align: right; flex-shrink: 0; }
    .approval-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

    .btn-approve {
        font-size: 13px; font-weight: 600; padding: 7px 16px; border-radius: 10px;
        border: none; cursor: pointer; background: #006a61; color: #fff;
        transition: background 0.15s; font-family: inherit;
    }
    .btn-approve:hover { background: #005850; }
    .btn-reject {
        font-size: 13px; font-weight: 600; padding: 7px 16px; border-radius: 10px;
        border: 1.5px solid #cac4d3; cursor: pointer; background: transparent; color: #ba1a1a;
        transition: background 0.15s; font-family: inherit;
    }
    .btn-reject:hover { background: #ffedea; border-color: #ba1a1a; }

    /* reject modal */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 200;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none; transition: opacity 0.2s;
    }
    .modal-overlay.open { opacity: 1; pointer-events: all; }
    .modal-box {
        background: #fff; border-radius: 20px; padding: 28px; width: 440px; max-width: 94vw;
        box-shadow: 0 12px 40px rgba(0,0,0,.18);
        transform: translateY(10px) scale(.98); transition: transform 0.2s;
    }
    .modal-overlay.open .modal-box { transform: translateY(0) scale(1); }
    .modal-title { font-size: 17px; font-weight: 700; color: #1c1b20; margin-bottom: 6px; }
    .modal-sub   { font-size: 13px; color: #797582; margin-bottom: 18px; }
    .modal-label { display: block; font-size: 13px; font-weight: 600; color: #1c1b20; margin-bottom: 6px; }
    .modal-textarea {
        width: 100%; height: 100px; border: 1.5px solid #cac4d3; border-radius: 12px;
        padding: 10px 14px; font-size: 14px; font-family: inherit; color: #1c1b20;
        resize: none; outline: none; background: #fdf7ff; transition: border-color 0.15s;
    }
    .modal-textarea:focus { border-color: #6351a7; }
    .modal-actions { display: flex; gap: 10px; margin-top: 18px; justify-content: flex-end; }
    .modal-cancel  { font-size: 14px; font-weight: 600; padding: 9px 20px; border-radius: 12px; border: 1.5px solid #cac4d3; background: transparent; color: #797582; cursor: pointer; font-family: inherit; }
    .modal-confirm { font-size: 14px; font-weight: 600; padding: 9px 20px; border-radius: 12px; border: none; background: #ba1a1a; color: #fff; cursor: pointer; font-family: inherit; }
    .modal-confirm:hover { background: #a01515; }

    .page-footer { text-align: center; padding: 20px 0 4px; font-size: 12px; color: #797582; border-top: 1px solid #f0ecf8; }
    .page-footer a { color: #6351a7; text-decoration: none; font-weight: 500; }
    .footer-links { margin-top: 6px; display: flex; justify-content: center; gap: 20px; }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-row { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .stat-grid { grid-template-columns: 1fr; }
        .approval-item { flex-direction: column; align-items: flex-start; gap: 10px; }
        .approval-actions { width: 100%; justify-content: flex-start; }
        .approval-meta { text-align: left; }
    }
</style>
@endpush

@section('content')

<div class="admin-page-header">
    <h1>Admin Overview</h1>
    <p>Monitor platform health, user growth, and pending actions.</p>
</div>

{{-- ── Stat Cards ── --}}
<div class="stat-grid">

    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <div class="stat-value">{{ number_format($totalUsers) }}</div>
                <div class="stat-label">Total Users</div>
                <div class="stat-badge badge-up">
                    ↑ +{{ $userGrowthPct }}% this week
                </div>
            </div>
            <div class="stat-card-icon icon-primary">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <div class="stat-value">{{ number_format($tasksCompleted) }}</div>
                <div class="stat-label">Tasks Completed</div>
                <div class="stat-badge badge-up">
                    ↑ +{{ $tasksGrowthPct }}% this week
                </div>
            </div>
            <div class="stat-card-icon icon-secondary">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-label">Pending Approvals</div>
                @if($pendingCount > 0)
                    <div class="stat-badge badge-warn">⚠ Requires attention</div>
                @else
                    <div class="stat-badge badge-up">✓ All clear</div>
                @endif
            </div>
            <div class="stat-card-icon icon-tertiary">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <div class="stat-value">{{ $reportedCount }}</div>
                <div class="stat-label">Reported Issues</div>
                <div class="stat-badge badge-down">↓ -2 from yesterday</div>
            </div>
            <div class="stat-card-icon icon-error">
                <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
        </div>
    </div>

</div>

{{-- ── Charts Row ── --}}
<div class="charts-row">

    {{-- Platform Activity bar chart --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="chart-card-title">Platform Activity</span>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="activityChart"></canvas>
        </div>
    </div>

    {{-- Task Distribution donut --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="chart-card-title">Task Distribution</span>
        </div>
        <div>
            <div class="donut-canvas-wrap">
                <canvas id="distributionChart"></canvas>
            </div>
            <div class="donut-legend">
                @php
                    $donutColors = ['#6351a7','#006a61','#c1b137','#cac4d3','#ba1a1a'];
                    $totalTasks = $taskDistribution->sum('value') ?: 1;
                @endphp
                @foreach($taskDistribution->take(4) as $i => $item)
                    <div class="legend-item">
                        <div class="legend-left">
                            <span class="legend-dot" style="background:{{ $donutColors[$i] ?? '#cac4d3' }}"></span>
                            {{ $item['label'] }}
                        </div>
                        <span class="legend-pct">{{ round(($item['value'] / $totalTasks) * 100) }}%</span>
                    </div>
                @endforeach
                @if($taskDistribution->isEmpty())
                    <p style="font-size:13px;color:#797582;text-align:center">Belum ada data task.</p>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ── Pending Approvals ── --}}
<div class="approvals-section">
    <div class="section-header">
        <div class="section-header-left">
            <h2>Pending User Approvals</h2>
            <p>New accounts waiting for admin review before activation.</p>
        </div>
        <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="view-all-link">View All</a>
    </div>

    @forelse($pendingUsers as $u)
        <div class="approval-item">
            <div class="user-avatar">{{ $u->initials }}</div>
            <div class="user-info">
                <div class="user-name">{{ $u->name }}</div>
                <div class="user-email">{{ $u->email }}</div>
            </div>
            <span class="approval-meta">{{ $u->created_at->diffForHumans() }}</span>
            <div class="approval-actions">
                <form action="{{ route('admin.users.approve', $u->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="role" value="admin">
                    <button type="submit" class="btn-approve">Setujui</button>
                </form>
                <button class="btn-reject" onclick="openRejectModal({{ $u->id }}, '{{ $u->name }}')">Tolak</button>
            </div>
        </div>
    @empty
        <p style="text-align:center;padding:28px 0;color:#797582;font-size:14px">
            🎉 Tidak ada akun yang menunggu persetujuan.
        </p>
    @endforelse
</div>

<footer class="page-footer">
    &copy; {{ date('Y') }} Scheduly. Crafted by <a href="https://portofolio-salman.netlify.app/#guestbook" target="_blank" rel="noopener" style="text-decoration:none;color:#6351a7;font-weight:600;">salman</a> & alfihra.
    <div class="footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Help Center</a>
    </div>
</footer>

{{-- Reject Modal --}}
<div class="modal-overlay" id="rejectModal">
    <div class="modal-box">
        <div class="modal-title">Tolak Pendaftaran</div>
        <p class="modal-sub" id="rejectModalSub">Berikan alasan penolakan.</p>
        <form action="" method="POST" id="rejectForm">
            @csrf @method('PATCH')
            <label class="modal-label">Alasan Penolakan</label>
            <textarea class="modal-textarea" name="reason" id="rejectReason"
                      placeholder="Contoh: Data tidak lengkap, email tidak valid, dll..."
                      required></textarea>
            <div class="modal-actions">
                <button type="button" class="modal-cancel" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="modal-confirm">Tolak Akun</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Reject modal
function openRejectModal(userId, userName) {
    document.getElementById('rejectModalSub').textContent = 'Berikan alasan penolakan untuk ' + userName + '.';
    document.getElementById('rejectForm').action = '/admin/users/' + userId + '/reject';
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectModal').classList.add('open');
}
function closeRejectModal() { document.getElementById('rejectModal').classList.remove('open'); }
document.getElementById('rejectModal').addEventListener('click', e => { if (e.target === document.getElementById('rejectModal')) closeRejectModal(); });

// Charts
document.addEventListener('DOMContentLoaded', function () {
    const actLabels = @json($activityData->pluck('label'));
    const actValues = @json($activityData->pluck('value'));

    const actCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(actCtx, {
        type: 'bar',
        data: {
            labels: actLabels,
            datasets: [{
                label: 'Active Users',
                data: actValues,
                backgroundColor: 'rgba(99,81,167,.18)',
                borderColor: '#6351a7',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: 'rgba(99,81,167,.32)',
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: "'Plus Jakarta Sans',sans-serif", size: 11 }, color: '#797582' } },
                y: { grid: { color: '#f0ecf8' }, ticks: { font: { family: "'Plus Jakarta Sans',sans-serif", size: 11 }, color: '#797582' } }
            }
        }
    });

    @if($taskDistribution->isNotEmpty())
    const distColors = ['#6351a7','#006a61','#c1b137','#cac4d3','#ba1a1a'];
    const distCtx = document.getElementById('distributionChart').getContext('2d');
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: @json($taskDistribution->pluck('label')),
            datasets: [{
                data: @json($taskDistribution->pluck('value')),
                backgroundColor: distColors,
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '68%',
            plugins: { legend: { display: false } }
        }
    });
    @endif
});
</script>
@endpush
