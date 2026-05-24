@extends('layouts.app')
@section('title', 'Admin Overview')

@push('styles')
<style>
    /* ── page header ── */
    .admin-page-header {
        margin-bottom: 28px;
    }

    .admin-page-header h1 {
        font-size: 26px;
        font-weight: 800;
        color: #1c1b20;
        letter-spacing: -0.4px;
        line-height: 1.2;
    }

    .admin-page-header p {
        font-size: 14px;
        color: #797582;
        margin-top: 4px;
        font-weight: 400;
    }

    /* ── stat cards ── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 22px 24px;
        box-shadow: 0 4px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0ecf8;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .stat-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-card-icon svg {
        width: 22px;
        height: 22px;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
        fill: none;
    }

    .icon-primary   { background: #ede9ff; }
    .icon-primary svg   { stroke: #6351a7; }

    .icon-secondary { background: #d6faf5; }
    .icon-secondary svg { stroke: #006a61; }

    .icon-tertiary  { background: #fdf3b8; }
    .icon-tertiary svg  { stroke: #6a5f00; }

    .icon-error     { background: #ffedea; }
    .icon-error svg     { stroke: #ba1a1a; }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #1c1b20;
        letter-spacing: -0.5px;
        line-height: 1;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 500;
        color: #797582;
        margin-top: 2px;
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 20px;
        margin-top: 4px;
        width: fit-content;
    }

    .badge-up   { background: #d6faf5; color: #006a61; }
    .badge-warn { background: #fdf3b8; color: #6a5f00; }
    .badge-down { background: #ffedea; color: #ba1a1a; }

    /* ── charts section ── */
    .charts-row {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 20px;
        margin-bottom: 28px;
    }

    .chart-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0ecf8;
    }

    .chart-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .chart-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1c1b20;
    }

    .chart-filter-select {
        font-size: 13px;
        font-weight: 500;
        color: #6351a7;
        border: 1px solid #cac4d3;
        border-radius: 10px;
        padding: 6px 12px;
        background: #fdf7ff;
        cursor: pointer;
        outline: none;
        font-family: inherit;
    }

    .chart-filter-select:focus {
        border-color: #6351a7;
    }

    .chart-canvas-wrap {
        position: relative;
        height: 220px;
    }

    /* donut chart + legend */
    .donut-wrap {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .donut-canvas-wrap {
        position: relative;
        height: 200px;
        margin-bottom: 16px;
    }

    .donut-legend {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
    }

    .legend-left {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #1c1b20;
        font-weight: 500;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .legend-pct {
        font-weight: 700;
        color: #797582;
        font-size: 13px;
    }

    /* ── approvals section ── */
    .approvals-section {
        background: #ffffff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0ecf8;
        margin-bottom: 32px;
    }

    .section-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .section-header-left h2 {
        font-size: 17px;
        font-weight: 700;
        color: #1c1b20;
    }

    .section-header-left p {
        font-size: 13px;
        color: #797582;
        margin-top: 3px;
    }

    .view-all-link {
        font-size: 13px;
        font-weight: 600;
        color: #6351a7;
        text-decoration: none;
        padding: 6px 14px;
        border: 1px solid #cac4d3;
        border-radius: 10px;
        transition: background 0.15s;
        white-space: nowrap;
    }

    .view-all-link:hover {
        background: #f3eeff;
    }

    .approval-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .approval-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid #f0ecf8;
    }

    .approval-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 700;
        color: #6351a7;
        background: #ede9ff;
        flex-shrink: 0;
        letter-spacing: -0.3px;
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-size: 14px;
        font-weight: 700;
        color: #1c1b20;
    }

    .user-email {
        font-size: 12px;
        color: #797582;
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .plan-chip {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 3px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .chip-enterprise { background: #ede9ff; color: #6351a7; }
    .chip-pro        { background: #d6faf5; color: #006a61; }
    .chip-basic      { background: #f4f4f4; color: #797582; }

    .approval-meta {
        font-size: 12px;
        color: #797582;
        font-weight: 500;
        min-width: 80px;
        text-align: right;
        flex-shrink: 0;
    }

    .approval-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .btn-approve {
        font-size: 13px;
        font-weight: 600;
        padding: 7px 16px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        background: #006a61;
        color: #fff;
        transition: background 0.15s, transform 0.1s;
        font-family: inherit;
    }

    .btn-approve:hover { background: #005850; }
    .btn-approve:active { transform: scale(0.98); }

    .btn-reject {
        font-size: 13px;
        font-weight: 600;
        padding: 7px 16px;
        border-radius: 10px;
        border: 1.5px solid #cac4d3;
        cursor: pointer;
        background: transparent;
        color: #ba1a1a;
        transition: background 0.15s, border-color 0.15s, transform 0.1s;
        font-family: inherit;
    }

    .btn-reject:hover {
        background: #ffedea;
        border-color: #ba1a1a;
    }

    .btn-reject:active { transform: scale(0.98); }

    /* ── rejection modal ── */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        z-index: 200;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
    }

    .modal-overlay.open {
        opacity: 1;
        pointer-events: all;
    }

    .modal-box {
        background: #ffffff;
        border-radius: 20px;
        padding: 28px;
        width: 440px;
        max-width: 94vw;
        box-shadow: 0 12px 40px rgba(0,0,0,0.18);
        transform: translateY(10px) scale(0.98);
        transition: transform 0.2s;
    }

    .modal-overlay.open .modal-box {
        transform: translateY(0) scale(1);
    }

    .modal-title {
        font-size: 17px;
        font-weight: 700;
        color: #1c1b20;
        margin-bottom: 6px;
    }

    .modal-sub {
        font-size: 13px;
        color: #797582;
        margin-bottom: 18px;
    }

    .modal-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1c1b20;
        margin-bottom: 6px;
    }

    .modal-textarea {
        width: 100%;
        height: 100px;
        border: 1.5px solid #cac4d3;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 14px;
        font-family: inherit;
        color: #1c1b20;
        resize: none;
        outline: none;
        transition: border-color 0.15s;
        background: #fdf7ff;
    }

    .modal-textarea:focus {
        border-color: #6351a7;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 18px;
        justify-content: flex-end;
    }

    .modal-cancel {
        font-size: 14px;
        font-weight: 600;
        padding: 9px 20px;
        border-radius: 12px;
        border: 1.5px solid #cac4d3;
        background: transparent;
        color: #797582;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s;
    }

    .modal-cancel:hover { background: #f5f5f5; }

    .modal-confirm {
        font-size: 14px;
        font-weight: 600;
        padding: 9px 20px;
        border-radius: 12px;
        border: none;
        background: #ba1a1a;
        color: #fff;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s, transform 0.1s;
    }

    .modal-confirm:hover { background: #a01515; }
    .modal-confirm:active { transform: scale(0.98); }

    /* ── page footer ── */
    .page-footer {
        text-align: center;
        padding: 20px 0 4px;
        font-size: 12px;
        color: #797582;
        border-top: 1px solid #f0ecf8;
    }

    .page-footer a {
        color: #6351a7;
        text-decoration: none;
        font-weight: 500;
    }

    .page-footer a:hover { text-decoration: underline; }

    .footer-links {
        margin-top: 6px;
        display: flex;
        justify-content: center;
        gap: 20px;
    }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="admin-page-header">
    <h1>Admin Overview</h1>
    <p>Monitor platform health, user growth, and pending actions.</p>
</div>

{{-- ── Stat Cards ── --}}
<div class="stat-grid">

    {{-- Total Users --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <div class="stat-value">12,405</div>
                <div class="stat-label">Total Users</div>
                <div class="stat-badge badge-up">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px"><polyline points="18 15 12 9 6 15"/></svg>
                    +12% this week
                </div>
            </div>
            <div class="stat-card-icon icon-primary">
                <svg viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Tasks Completed --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <div class="stat-value">84.2k</div>
                <div class="stat-label">Tasks Completed</div>
                <div class="stat-badge badge-up">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px"><polyline points="18 15 12 9 6 15"/></svg>
                    +5.4% this week
                </div>
            </div>
            <div class="stat-card-icon icon-secondary">
                <svg viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Pending Approvals --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <div class="stat-value">14</div>
                <div class="stat-label">Pending Approvals</div>
                <div class="stat-badge badge-warn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Requires attention
                </div>
            </div>
            <div class="stat-card-icon icon-tertiary">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Reported Issues --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <div class="stat-value">3</div>
                <div class="stat-label">Reported Issues</div>
                <div class="stat-badge badge-down">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px"><polyline points="18 9 12 15 6 9"/></svg>
                    -2 from yesterday
                </div>
            </div>
            <div class="stat-card-icon icon-error">
                <svg viewBox="0 0 24 24">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
        </div>
    </div>

</div>

{{-- ── Charts Row ── --}}
<div class="charts-row">

    {{-- Platform Activity (bar chart) --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="chart-card-title">Platform Activity</span>
            <select class="chart-filter-select" id="activityFilter">
                <option value="14">Last 14 Days</option>
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
            </select>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="activityChart"></canvas>
        </div>
    </div>

    {{-- Task Distribution (donut chart) --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="chart-card-title">Task Distribution</span>
        </div>
        <div class="donut-wrap">
            <div class="donut-canvas-wrap">
                <canvas id="distributionChart"></canvas>
            </div>
            <div class="donut-legend">
                <div class="legend-item">
                    <div class="legend-left">
                        <span class="legend-dot" style="background:#6351a7"></span>
                        Work &amp; Projects
                    </div>
                    <span class="legend-pct">45%</span>
                </div>
                <div class="legend-item">
                    <div class="legend-left">
                        <span class="legend-dot" style="background:#006a61"></span>
                        Personal Goals
                    </div>
                    <span class="legend-pct">30%</span>
                </div>
                <div class="legend-item">
                    <div class="legend-left">
                        <span class="legend-dot" style="background:#c1b137"></span>
                        Team Meetings
                    </div>
                    <span class="legend-pct">15%</span>
                </div>
                <div class="legend-item">
                    <div class="legend-left">
                        <span class="legend-dot" style="background:#cac4d3"></span>
                        Other
                    </div>
                    <span class="legend-pct">10%</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Pending User Approvals ── --}}
<div class="approvals-section">
    <div class="section-header">
        <div class="section-header-left">
            <h2>Pending User Approvals</h2>
            <p>New accounts waiting for admin review before activation.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="view-all-link">View All</a>
    </div>

    <div class="approval-list">

        {{-- User 1 --}}
        <div class="approval-item">
            <div class="user-avatar">JD</div>
            <div class="user-info">
                <div class="user-name">John Doe</div>
                <div class="user-email">john.doe@company.com</div>
            </div>
            <span class="plan-chip chip-enterprise">Enterprise Plan</span>
            <span class="approval-meta">2 hrs ago</span>
            <div class="approval-actions">
                <form action="{{ route('admin.users.approve', 1) }}" method="POST" style="margin:0">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-approve">Setujui</button>
                </form>
                <button class="btn-reject" onclick="openRejectModal(1, 'John Doe')">Tolak</button>
            </div>
        </div>

        {{-- User 2 --}}
        <div class="approval-item">
            <div class="user-avatar" style="background:#d6faf5;color:#006a61">MS</div>
            <div class="user-info">
                <div class="user-name">Michael Smith</div>
                <div class="user-email">m.smith@startup.io</div>
            </div>
            <span class="plan-chip chip-pro">Pro Plan</span>
            <span class="approval-meta">5 hrs ago</span>
            <div class="approval-actions">
                <form action="{{ route('admin.users.approve', 2) }}" method="POST" style="margin:0">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-approve">Setujui</button>
                </form>
                <button class="btn-reject" onclick="openRejectModal(2, 'Michael Smith')">Tolak</button>
            </div>
        </div>

        {{-- User 3 --}}
        <div class="approval-item">
            <div class="user-avatar" style="background:#fdf3b8;color:#6a5f00">AR</div>
            <div class="user-info">
                <div class="user-name">Aisha Rahman</div>
                <div class="user-email">aisha.r@freelance.net</div>
            </div>
            <span class="plan-chip chip-basic">Basic Plan</span>
            <span class="approval-meta">1 day ago</span>
            <div class="approval-actions">
                <form action="{{ route('admin.users.approve', 3) }}" method="POST" style="margin:0">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-approve">Setujui</button>
                </form>
                <button class="btn-reject" onclick="openRejectModal(3, 'Aisha Rahman')">Tolak</button>
            </div>
        </div>

        {{-- User 4 --}}
        <div class="approval-item">
            <div class="user-avatar" style="background:#f0ecf8;color:#6351a7">BS</div>
            <div class="user-info">
                <div class="user-name">Budi Santoso</div>
                <div class="user-email">budi.s@gmail.com</div>
            </div>
            <span class="plan-chip chip-basic">Basic Plan</span>
            <span class="approval-meta">2 days ago</span>
            <div class="approval-actions">
                <form action="{{ route('admin.users.approve', 4) }}" method="POST" style="margin:0">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-approve">Setujui</button>
                </form>
                <button class="btn-reject" onclick="openRejectModal(4, 'Budi Santoso')">Tolak</button>
            </div>
        </div>

    </div>
</div>

{{-- ── Footer ── --}}
<footer class="page-footer">
    &copy; 2024 Scheduly Productivity Platform. All rights reserved.
    <div class="footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Help Center</a>
    </div>
</footer>

{{-- ── Reject Modal ── --}}
<div class="modal-overlay" id="rejectModal">
    <div class="modal-box">
        <div class="modal-title">Tolak Pendaftaran</div>
        <p class="modal-sub" id="rejectModalSub">Berikan alasan penolakan untuk pengguna ini.</p>
        <form action="" method="POST" id="rejectForm">
            @csrf
            @method('PATCH')
            <label class="modal-label">Alasan Penolakan</label>
            <textarea
                class="modal-textarea"
                name="reason"
                id="rejectReason"
                placeholder="Contoh: Data tidak lengkap, email tidak valid, dll..."
                required
            ></textarea>
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
// charts are initialized here — activityChart and distributionChart
document.addEventListener('DOMContentLoaded', function () {

    // ── Bar chart: daily active users over 14 days ──
    const labels14 = ['May 10','May 11','May 12','May 13','May 14','May 15','May 16',
                       'May 17','May 18','May 19','May 20','May 21','May 22','May 23'];
    const data14   = [420, 610, 530, 740, 680, 590, 820, 760, 850, 720, 910, 870, 940, 1020];

    const activityCtx = document.getElementById('activityChart').getContext('2d');
    const activityChart = new Chart(activityCtx, {
        type: 'bar',
        data: {
            labels: labels14,
            datasets: [{
                label: 'Active Users',
                data: data14,
                backgroundColor: 'rgba(99, 81, 167, 0.18)',
                borderColor: '#6351a7',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: 'rgba(99, 81, 167, 0.32)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1c1b20',
                    titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '600' },
                    bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 13 },
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                        color: '#797582',
                        maxRotation: 0,
                        // show every other label so it doesn't get crowded
                        callback: function(val, idx) {
                            return idx % 2 === 0 ? this.getLabelForValue(val) : '';
                        }
                    }
                },
                y: {
                    grid: { color: '#f0ecf8' },
                    border: { dash: [4, 4] },
                    ticks: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                        color: '#797582',
                    }
                }
            }
        }
    });

    // ── Donut chart: task distribution ──
    const distCtx = document.getElementById('distributionChart').getContext('2d');
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Work & Projects', 'Personal Goals', 'Team Meetings', 'Other'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: ['#6351a7', '#006a61', '#c1b137', '#cac4d3'],
                hoverBackgroundColor: ['#5240a0', '#005850', '#a09530', '#b0aac0'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1c1b20',
                    titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
                    bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 13 },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => ` ${ctx.parsed}%`
                    }
                }
            }
        }
    });

    // ── Filter dropdown for activity chart ──
    document.getElementById('activityFilter').addEventListener('change', function () {
        // in real app we'd fetch new data here; for now just a stub
        const days = parseInt(this.value);
        console.log('Filter changed to', days, 'days');
    });
});

// ── Reject modal helpers ──
function openRejectModal(userId, userName) {
    document.getElementById('rejectModalSub').textContent =
        'Berikan alasan penolakan untuk ' + userName + '.';
    document.getElementById('rejectForm').action =
        '/admin/users/' + userId + '/reject';
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectModal').classList.add('open');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('open');
}

// close on overlay click
document.getElementById('rejectModal').addEventListener('click', function (e) {
    if (e.target === this) closeRejectModal();
});
</script>
@endpush
