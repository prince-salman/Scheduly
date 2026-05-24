@extends('layouts.app')
@section('title', 'Analytics & Report')

@push('styles')
<style>
    /* --- Page header --- */
    .analytics-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .analytics-header h1 {
        font-size: 22px;
        font-weight: 800;
        color: #1c1b20;
    }

    .export-actions {
        display: flex;
        gap: 8px;
    }

    /* ghost buttons for exports */
    .btn-ghost {
        padding: 9px 18px;
        border-radius: 12px;
        border: 1.5px solid #cac4d3;
        background: #ffffff;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: #797582;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }

    .btn-ghost:hover {
        border-color: #6351a7;
        color: #6351a7;
        background: #f3eeff;
    }

    .btn-ghost:active { transform: scale(0.98); }

    /* --- Stat cards --- */
    .analytics-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 22px 24px;
        box-shadow: 0 8px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0eaf8;
        position: relative;
        overflow: hidden;
    }

    /* corner accent */
    .stat-card::after {
        content: '';
        position: absolute;
        bottom: -12px;
        right: -12px;
        width: 64px;
        height: 64px;
        border-radius: 50%;
        opacity: 0.06;
    }

    .stat-card.s1::after { background: #6351a7; }
    .stat-card.s2::after { background: #006a61; }
    .stat-card.s3::after { background: #6a5f00; }
    .stat-card.s4::after { background: #6351a7; }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #797582;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 30px;
        font-weight: 800;
        color: #1c1b20;
        line-height: 1.1;
        margin-bottom: 5px;
    }

    .stat-value .unit {
        font-size: 15px;
        font-weight: 600;
        color: #797582;
    }

    .stat-trend {
        font-size: 12px;
        color: #006a61;
        font-weight: 600;
    }

    .stat-link {
        font-size: 12px;
        color: #6351a7;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .stat-link:hover { text-decoration: underline; }

    /* --- Charts row --- */
    .charts-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 24px;
    }

    .chart-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 22px 24px;
        box-shadow: 0 8px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0eaf8;
    }

    .chart-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .chart-card-title {
        font-size: 15px;
        font-weight: 800;
        color: #1c1b20;
    }

    .chart-period-select {
        padding: 5px 10px;
        border-radius: 8px;
        border: 1px solid #cac4d3;
        font-family: inherit;
        font-size: 12px;
        color: #797582;
        background: #fdf7ff;
        outline: none;
        cursor: pointer;
    }

    .chart-period-select:focus { border-color: #6351a7; }

    .chart-canvas-wrap {
        position: relative;
        height: 200px;
    }

    /* --- Table --- */
    .table-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 22px 0;
        box-shadow: 0 8px 20px rgba(181, 162, 255, 0.15);
        border: 1px solid #f0eaf8;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .table-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px 16px;
        border-bottom: 1px solid #f0eaf8;
    }

    .table-card-title {
        font-size: 15px;
        font-weight: 800;
        color: #1c1b20;
    }

    table.report-table {
        width: 100%;
        border-collapse: collapse;
    }

    table.report-table thead th {
        font-size: 11px;
        font-weight: 700;
        color: #797582;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 24px;
        text-align: left;
        background: #fdf7ff;
        border-bottom: 1px solid #f0eaf8;
    }

    table.report-table tbody tr {
        transition: background 0.15s;
    }

    table.report-table tbody tr:hover {
        background: #faf8ff;
    }

    table.report-table tbody td {
        padding: 13px 24px;
        font-size: 13px;
        color: #1c1b20;
        border-bottom: 1px solid #f6f1ff;
        vertical-align: middle;
    }

    table.report-table tbody tr:last-child td {
        border-bottom: none;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 600;
    }

    .chip-design    { background: #ede9ff; color: #6351a7; }
    .chip-dev       { background: #d0f5f3; color: #006a61; }
    .chip-planning  { background: #fff4cc; color: #6a5f00; }
    .chip-personal  { background: #fde8e8; color: #ba1a1a; }
    .chip-meeting   { background: #e8f0fe; color: #1a6ef7; }

    .badge-done {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 100px;
        background: #d0f5f3;
        color: #006a61;
        font-size: 11px;
        font-weight: 700;
    }

    .badge-progress {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 100px;
        background: #ede9ff;
        color: #6351a7;
        font-size: 11px;
        font-weight: 700;
    }

    /* --- Pagination --- */
    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        margin-top: 4px;
    }

    .pagination-info {
        font-size: 13px;
        color: #797582;
    }

    .pagination-btns {
        display: flex;
        gap: 4px;
    }

    .page-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1.5px solid #cac4d3;
        background: #ffffff;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: #797582;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }

    .page-btn:hover, .page-btn.active {
        border-color: #6351a7;
        color: #6351a7;
        background: #f3eeff;
    }

    .page-btn.active {
        background: #6351a7;
        color: #fff;
    }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="analytics-header">
    <h1>📊 Analytics & Laporan</h1>
    <div class="export-actions">
        <button class="btn-ghost">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            Export PDF
        </button>
        <button class="btn-ghost">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <path d="M3 9h18M3 15h18M9 3v18"/>
            </svg>
            Export Excel
        </button>
    </div>
</div>

{{-- 4 stat cards --}}
<div class="analytics-stats">
    <div class="stat-card s1">
        <div class="stat-label">Total Task Selesai</div>
        <div class="stat-value">47</div>
        <div class="stat-trend">↑ +8 minggu ini</div>
    </div>
    <div class="stat-card s2">
        <div class="stat-label">Total Jam Fokus</div>
        <div class="stat-value">32.5 <span class="unit">Jam</span></div>
        <div class="stat-trend">↑ +4.5h minggu ini</div>
    </div>
    <div class="stat-card s3">
        <div class="stat-label">Produktivitas Harian</div>
        <div class="stat-value">78 <span class="unit">%</span></div>
        <div class="stat-trend">↑ +3% vs minggu lalu</div>
    </div>
    <div class="stat-card s4">
        <div class="stat-label">Laporan Mingguan</div>
        <div class="stat-value" style="font-size:20px;padding-top:4px">Minggu 21</div>
        <a class="stat-link" href="#">Lihat Semua →</a>
    </div>
</div>

{{-- Charts row --}}
<div class="charts-row">

    {{-- Bar chart: Produktivitas Harian --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="chart-card-title">📈 Produktivitas Harian</span>
            <select class="chart-period-select">
                <option>7 Hari Terakhir</option>
                <option>30 Hari</option>
            </select>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="productivityChart"></canvas>
        </div>
    </div>

    {{-- Line chart: Jam Fokus --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="chart-card-title">⏱ Jam Fokus per Hari</span>
            <select class="chart-period-select">
                <option>7 Hari Terakhir</option>
                <option>30 Hari</option>
            </select>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="focusChart"></canvas>
        </div>
    </div>

</div>

{{-- Task completion table --}}
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">📋 Riwayat Penyelesaian Task</span>
        <span style="font-size:13px;color:#797582">Menampilkan 6 dari 47 task</span>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>Nama Task</th>
                <th>Kategori</th>
                <th>Deadline</th>
                <th>Waktu Selesai</th>
                <th>Durasi Fokus</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight:600">Define brand anchors & JSON structure</td>
                <td><span class="chip chip-planning">Planning</span></td>
                <td>23 Mei 2026</td>
                <td>24 Mei, 09:42</td>
                <td>2j 15m</td>
                <td><span class="badge-done"><i data-lucide="check-circle" class="icon-sm"></i> Selesai</span></td>
            </tr>
            <tr>
                <td style="font-weight:600">Riset referensi UI pattern library</td>
                <td><span class="chip chip-design">Design</span></td>
                <td>22 Mei 2026</td>
                <td>22 Mei, 14:10</td>
                <td>45m</td>
                <td><span class="badge-done"><i data-lucide="check-circle" class="icon-sm"></i> Selesai</span></td>
            </tr>
            <tr>
                <td style="font-weight:600">Setup routing & middleware Laravel</td>
                <td><span class="chip chip-dev">Development</span></td>
                <td>21 Mei 2026</td>
                <td>21 Mei, 11:30</td>
                <td>1j 50m</td>
                <td><span class="badge-done"><i data-lucide="check-circle" class="icon-sm"></i> Selesai</span></td>
            </tr>
            <tr>
                <td style="font-weight:600">Onboarding meeting tim baru</td>
                <td><span class="chip chip-meeting">Meeting</span></td>
                <td>20 Mei 2026</td>
                <td>20 Mei, 10:00</td>
                <td>1j 00m</td>
                <td><span class="badge-done"><i data-lucide="check-circle" class="icon-sm"></i> Selesai</span></td>
            </tr>
            <tr>
                <td style="font-weight:600">Implement navigation shell logic</td>
                <td><span class="chip chip-dev">Development</span></td>
                <td>25 Mei 2026</td>
                <td>—</td>
                <td>1j 24m (aktif)</td>
                <td><span class="badge-progress">🔄 In Progress</span></td>
            </tr>
            <tr>
                <td style="font-weight:600">Tulis jurnal produktivitas mingguan</td>
                <td><span class="chip chip-personal">Personal</span></td>
                <td>24 Mei 2026</td>
                <td>24 Mei, 20:15</td>
                <td>30m</td>
                <td><span class="badge-done"><i data-lucide="check-circle" class="icon-sm"></i> Selesai</span></td>
            </tr>
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="pagination-bar" style="margin-top:16px">
        <span class="pagination-info">Halaman 1 dari 8</span>
        <div class="pagination-btns">
            <button class="page-btn">‹</button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn" style="letter-spacing:1px">…</button>
            <button class="page-btn">8</button>
            <button class="page-btn">›</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // common chart defaults matching design tokens
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#797582';

    const gridColor = 'rgba(202, 196, 211, 0.25)';

    // --- Bar chart: Produktivitas Harian ---
    const prodCtx = document.getElementById('productivityChart').getContext('2d');
    new Chart(prodCtx, {
        type: 'bar',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [{
                label: 'Produktivitas (%)',
                data: [72, 85, 68, 91, 78, 60, 75],
                backgroundColor: [
                    '#ede9ff', '#ede9ff', '#ede9ff', '#6351a7', '#ede9ff', '#ede9ff', '#6351a7'
                ],
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: '#5240a0',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y}%`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: gridColor },
                    ticks: {
                        callback: v => v + '%',
                        stepSize: 25
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // --- Line chart: Jam Fokus per Hari ---
    const focusCtx = document.getElementById('focusChart').getContext('2d');
    new Chart(focusCtx, {
        type: 'line',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [{
                label: 'Jam Fokus',
                data: [3.5, 5.0, 4.0, 6.0, 4.5, 2.5, 4.5],
                borderColor: '#006a61',
                backgroundColor: 'rgba(0, 106, 97, 0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#006a61',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} Jam`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 8,
                    grid: { color: gridColor },
                    ticks: {
                        callback: v => v + 'j',
                        stepSize: 2
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
