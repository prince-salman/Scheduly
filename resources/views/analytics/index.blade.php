@extends('layouts.app')
@section('title', 'Analytics & Report')

@push('styles')
<style>
    .analytics-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .analytics-header h1 { font-size:22px; font-weight:800; color:#1c1b20; }
    .export-actions { display:flex; gap:8px; }
    .btn-ghost { padding:9px 18px; border-radius:12px; border:1.5px solid #cac4d3; background:#fff; font-family:inherit; font-size:13px; font-weight:600; color:#797582; cursor:pointer; display:flex; align-items:center; gap:6px; transition:background .15s,border-color .15s,color .15s; }
    .btn-ghost:hover { border-color:#6351a7; color:#6351a7; background:#f3eeff; }

    .analytics-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
    .stat-card { background:#fff; border-radius:24px; padding:22px 24px; box-shadow:0 8px 20px rgba(181,162,255,.15); border:1px solid #f0eaf8; position:relative; overflow:hidden; }
    .stat-card::after { content:''; position:absolute; bottom:-12px; right:-12px; width:64px; height:64px; border-radius:50%; opacity:.06; }
    .stat-card.s1::after { background:#6351a7; }
    .stat-card.s2::after { background:#006a61; }
    .stat-card.s3::after { background:#6a5f00; }
    .stat-card.s4::after { background:#6351a7; }
    .stat-label { font-size:12px; font-weight:600; color:#797582; text-transform:uppercase; letter-spacing:.6px; margin-bottom:10px; }
    .stat-value { font-size:30px; font-weight:800; color:#1c1b20; line-height:1.1; margin-bottom:5px; }
    .stat-value .unit { font-size:15px; font-weight:600; color:#797582; }
    .stat-trend { font-size:12px; color:#006a61; font-weight:600; }
    .stat-link  { font-size:12px; color:#6351a7; font-weight:600; text-decoration:none; cursor:pointer; }
    .stat-link:hover { text-decoration:underline; }

    .charts-row { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:24px; }
    .chart-card { background:#fff; border-radius:24px; padding:22px 24px; box-shadow:0 8px 20px rgba(181,162,255,.15); border:1px solid #f0eaf8; }
    .chart-card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
    .chart-card-title  { font-size:15px; font-weight:800; color:#1c1b20; }
    .chart-period-select { padding:5px 10px; border-radius:8px; border:1px solid #cac4d3; font-family:inherit; font-size:12px; color:#797582; background:#fdf7ff; outline:none; cursor:pointer; }
    .chart-period-select:focus { border-color:#6351a7; }
    .chart-canvas-wrap { position:relative; height:200px; }

    .table-card { background:#fff; border-radius:24px; padding:22px 0; box-shadow:0 8px 20px rgba(181,162,255,.15); border:1px solid #f0eaf8; margin-bottom:20px; overflow:hidden; }
    .table-card-header { display:flex; align-items:center; justify-content:space-between; padding:0 24px 16px; border-bottom:1px solid #f0eaf8; }
    .table-card-title  { font-size:15px; font-weight:800; color:#1c1b20; }

    table.report-table { width:100%; border-collapse:collapse; }
    table.report-table thead th { font-size:11px; font-weight:700; color:#797582; text-transform:uppercase; letter-spacing:.5px; padding:12px 24px; text-align:left; background:#fdf7ff; border-bottom:1px solid #f0eaf8; }
    table.report-table tbody tr { transition:background .15s; }
    table.report-table tbody tr:hover { background:#faf8ff; }
    table.report-table tbody td { padding:13px 24px; font-size:13px; color:#1c1b20; border-bottom:1px solid #f6f1ff; vertical-align:middle; }
    table.report-table tbody tr:last-child td { border-bottom:none; }

    .chip { display:inline-flex; align-items:center; padding:3px 10px; border-radius:100px; font-size:11px; font-weight:600; }
    .chip-design   { background:#ede9ff; color:#6351a7; }
    .chip-dev      { background:#d0f5f3; color:#006a61; }
    .chip-planning { background:#fff4cc; color:#6a5f00; }
    .chip-personal { background:#fde8e8; color:#ba1a1a; }
    .chip-meeting  { background:#e8f0fe; color:#1a6ef7; }

    .badge-done     { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:100px; background:#d0f5f3; color:#006a61; font-size:11px; font-weight:700; }
    .badge-progress { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:100px; background:#ede9ff; color:#6351a7; font-size:11px; font-weight:700; }

    .pagination-bar  { display:flex; align-items:center; justify-content:space-between; padding:0 24px; margin-top:4px; }
    .pagination-info { font-size:13px; color:#797582; }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .analytics-stats { grid-template-columns: repeat(2, 1fr); }
        .charts-row { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .analytics-header { flex-direction: column; align-items: stretch; }
        .export-actions { flex-direction: column; align-items: stretch; }
        .btn-ghost { justify-content: center; }
        .analytics-stats { grid-template-columns: 1fr; }
        .table-card { overflow-x: auto; }
        table.report-table { min-width: 600px; }
    }
</style>
@endpush

@section('content')

<div class="analytics-header">
    <h1>📊 Analytics & Laporan</h1>
    <div class="export-actions">
        <button class="btn-ghost">
               <a href="{{ route('tasks.export.pdf') }}" class="fab-new-task">
    📄 Export PDF
</a>
        </button>
        <button class="btn-ghost">
            <a href="{{ route('tasks.export.csv') }}" class="fab-new-task">
    📄 Export Excel
</a>
        </button>
    </div>
</div>

{{-- Stat cards --}}
<div class="analytics-stats">
    <div class="stat-card s1">
        <div class="stat-label">Total Task Selesai</div>
        <div class="stat-value">{{ $totalDone }}</div>
        <div class="stat-trend">↑ +{{ $doneThisPeriod }} minggu ini</div>
    </div>
    <div class="stat-card s2">
        <div class="stat-label">Total Jam Fokus</div>
        <div class="stat-value">{{ round($totalFocusMinutes / 60, 1) }} <span class="unit">Jam</span></div>
        <div class="stat-trend">↑ +{{ round($focusThisPeriod / 60, 1) }}h minggu ini</div>
    </div>
    <div class="stat-card s3">
        <div class="stat-label">Produktivitas Harian</div>
        <div class="stat-value">{{ $productivityPct }} <span class="unit">%</span></div>
        <div class="stat-trend">{{ $days }} hari terakhir</div>
    </div>
    <div class="stat-card s4">
        <div class="stat-label">Laporan Mingguan</div>
        <div class="stat-value" style="font-size:20px;padding-top:4px">Minggu {{ $weekNumber }}</div>
        <a class="stat-link" href="#">Lihat Semua →</a>
    </div>
</div>

{{-- Charts --}}
<div class="charts-row">
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="chart-card-title">📈 Produktivitas Harian</span>
            <select class="chart-period-select" onchange="location.href='?days='+this.value">
                <option value="7"  {{ $days == 7  ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari</option>
            </select>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="productivityChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="chart-card-title">⏱ Jam Fokus per Hari</span>
            <select class="chart-period-select" onchange="location.href='?days='+this.value">
                <option value="7"  {{ $days == 7  ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari</option>
            </select>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="focusChart"></canvas>
        </div>
    </div>
</div>

{{-- Task history table --}}
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">📋 Riwayat Penyelesaian Task</span>
        <span style="font-size:13px;color:#797582">
            Menampilkan {{ $taskHistory->firstItem() }}–{{ $taskHistory->lastItem() }} dari {{ $taskHistory->total() }} task
        </span>
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
            @forelse($taskHistory as $task)
            @php
                $catClass = match(strtolower($task->category ?? '')) {
                    'design'      => 'chip-design',
                    'development' => 'chip-dev',
                    'planning'    => 'chip-planning',
                    'personal'    => 'chip-personal',
                    'meeting'     => 'chip-meeting',
                    default       => 'chip-design',
                };
                $focusH = floor($task->focus_minutes / 60);
                $focusM = $task->focus_minutes % 60;
                $focusLabel = $focusH > 0 ? "{$focusH}j {$focusM}m" : "{$focusM}m";
                if ($task->timer_started_at) {
                    $activeMin = now()->diffInMinutes($task->timer_started_at);
                    $focusLabel .= ' (' . $activeMin . 'm aktif)';
                }
            @endphp
            <tr>
                <td style="font-weight:600">{{ $task->title }}</td>
                <td>
                    @if($task->category)
                        <span class="chip {{ $catClass }}">{{ $task->category }}</span>
                    @else —
                    @endif
                </td>
                <td>{{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}</td>
                <td>
                    {{ $task->status === 'done'
                        ? $task->updated_at->format('d M, H:i')
                        : '—' }}
                </td>
                <td>{{ $task->focus_minutes > 0 ? $focusLabel : '—' }}</td>
                <td>
                    @if($task->status === 'done')
                        <span class="badge-done">✓ Selesai</span>
                    @else
                        <span class="badge-progress">🔄 In Progress</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:32px;color:#797582">Belum ada data task.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-bar" style="margin-top:16px">
        <span class="pagination-info">Halaman {{ $taskHistory->currentPage() }} dari {{ $taskHistory->lastPage() }}</span>
        <div>{{ $taskHistory->withQueryString()->links() }}</div>
    </div>
</div>

@endsection

@push('scripts')
<script>
Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
Chart.defaults.color = '#797582';
const gridColor = 'rgba(202,196,211,.25)';

// ── Produktivitas Harian (jumlah task done per hari) ──────────────────
const prodData  = @json($productivityChart->pluck('value'));   // integer: jumlah task done
const prodMax   = Math.max(...prodData, 1);                    // dynamic max agar bar proporsional


const prodCtx = document.getElementById('productivityChart').getContext('2d');
new Chart(prodCtx, {
    type: 'bar',
    data: {
        labels: @json($productivityChart->pluck('label')),
        datasets: [{
            label: 'Task Selesai',
            data: prodData,
            // bar lebih gelap jika >= 75% dari nilai tertinggi hari itu
            backgroundColor: prodData.map(v => v >= prodMax * 0.75 ? '#6351a7' : '#ede9ff'),
            hoverBackgroundColor: '#5240a0',
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.parsed.y} task selesai`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: prodMax + 1,                          // sedikit padding di atas bar tertinggi
                grid: { color: gridColor },
                ticks: {
                    stepSize: 1,                           // integer saja, tidak ada 0.5
                    callback: v => Number.isInteger(v) ? v : null
                }
            },
            x: { grid: { display: false } }
        }
    }
});

// ── Menit Fokus per Hari ──────────────────────────────────────────────
const focusData = @json($focusChart->pluck('value'));          // integer: total focus_minutes per hari
const focusMax  = Math.max(...focusData, 30);                  // minimum axis 30 menit

const focusCtx = document.getElementById('focusChart').getContext('2d');
new Chart(focusCtx, {
    type: 'line',
    data: {
        labels: @json($focusChart->pluck('label')),
        datasets: [{
            label: 'Menit Fokus',
            data: focusData,
            borderColor: '#006a61',
            backgroundColor: 'rgba(0,106,97,.08)',
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
                    label: ctx => ` ${ctx.parsed.y} menit`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                suggestedMax: focusMax + 10,
                grid: { color: gridColor },
                ticks: {
                    callback: v => v + ' mnt',
                    maxTicksLimit: 6
                }
            },
            x: { grid: { display: false } }
        }
    }
});

</script>
@endpush