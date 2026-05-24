@extends('layouts.auth')

@section('title', 'Menunggu Persetujuan — Scheduly')

@section('content')
<style>
    .status-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background: #fdf7ff;
        background-image: radial-gradient(circle at 30% 70%, rgba(181, 162, 255, 0.15) 0%, transparent 55%),
                          radial-gradient(circle at 75% 25%, rgba(154, 239, 227, 0.12) 0%, transparent 55%);
    }

    .status-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 56px 48px;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 8px 40px rgba(181, 162, 255, 0.2);
        border: 1px solid #ede9ff;
        text-align: center;
    }

    /* brand */
    .brand-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 36px;
    }

    .brand-row .b-icon {
        font-size: 22px;
    }

    .brand-row .b-name {
        font-size: 18px;
        font-weight: 800;
        color: #6351a7;
        letter-spacing: -0.3px;
    }

    /* illustration */
    .illustration-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 28px;
    }

    .illustration-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(145deg, #fdf3c8, #fef9e4);
        border: 3px solid #c1b137;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        /* gentle pulse animation */
        animation: pulse-ring 2.4s ease-in-out infinite;
        box-shadow: 0 0 0 0 rgba(193, 177, 55, 0.4);
    }

    @keyframes pulse-ring {
        0%   { box-shadow: 0 0 0 0 rgba(193, 177, 55, 0.4); }
        60%  { box-shadow: 0 0 0 18px rgba(193, 177, 55, 0); }
        100% { box-shadow: 0 0 0 0 rgba(193, 177, 55, 0); }
    }

    /* status chip */
    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fef9e4;
        color: #6a5f00;
        border: 1.5px solid #c1b137;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        padding: 5px 14px;
        border-radius: 100px;
        margin-bottom: 20px;
    }

    .status-chip::before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #c1b137;
        animation: blink 1.4s ease-in-out infinite;
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.3; }
    }

    .status-title {
        font-size: 26px;
        font-weight: 800;
        color: #1c1b20;
        letter-spacing: -0.5px;
        margin-bottom: 14px;
    }

    .status-body {
        font-size: 15px;
        color: #797582;
        line-height: 1.7;
        margin-bottom: 36px;
        max-width: 340px;
        margin-left: auto;
        margin-right: auto;
    }

    /* actions */
    .action-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
        align-items: center;
    }

    .btn-refresh {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #6351a7;
        color: #fff;
        border: none;
        border-radius: 14px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 15px;
        font-weight: 700;
        padding: 13px 32px;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s, transform 0.1s;
        box-shadow: 0 4px 16px rgba(99, 81, 167, 0.3);
    }

    .btn-refresh:hover { background: #5240a0; }
    .btn-refresh:active { transform: scale(0.98); }

    .btn-refresh svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2.5;
        stroke-linecap: round;
        stroke-linejoin: round;
        transition: transform 0.3s;
    }

    .btn-refresh:hover svg { transform: rotate(180deg); }

    .logout-text-btn {
        font-size: 13px;
        color: #797582;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .logout-text-btn button {
        background: none;
        border: none;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #ba1a1a;
        cursor: pointer;
        padding: 0;
        text-decoration: none;
    }

    .logout-text-btn button:hover { text-decoration: underline; }

    /* info box at bottom */
    .info-box {
        margin-top: 32px;
        background: #f3eeff;
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 13px;
        color: #6351a7;
        text-align: left;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .info-box svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        stroke: #6351a7;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
        margin-top: 1px;
    }
</style>

<div class="status-page">
    <div class="status-card">

        <!-- Brand -->
        <div class="brand-row">
            <span class="b-icon"><i data-lucide="calendar" class="icon-sm"></i></span>
            <span class="b-name">Scheduly</span>
        </div>

        <!-- Illustration -->
        <div class="illustration-wrap">
            <div class="illustration-circle"><i data-lucide="hourglass" class="icon-lg text-primary"></i></div>
        </div>

        <!-- Status chip -->
        <div class="status-chip">PENDING</div>

        <h1 class="status-title">Akun Sedang Ditinjau</h1>

        <p class="status-body">
            Pengajuan akun kamu sedang menunggu persetujuan admin.
            Kamu akan mendapat notifikasi setelah akun disetujui.
            Proses biasanya membutuhkan waktu 1×24 jam.
        </p>

        <!-- Buttons -->
        <div class="action-group">
            <a href="{{ url()->current() }}" class="btn-refresh">
                <svg viewBox="0 0 24 24">
                    <polyline points="23 4 23 10 17 10"/>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                </svg>
                Perbarui Status
            </a>

            <div class="logout-text-btn">
                <span>Bukan akun kamu?</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>

        <!-- Info tip -->
        <div class="info-box">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <span>Cek email kamu secara berkala. Notifikasi persetujuan akan dikirim ke alamat email yang kamu daftarkan.</span>
        </div>

    </div>
</div>
@endsection
