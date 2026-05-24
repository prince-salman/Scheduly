@extends('layouts.auth')

@section('title', 'Pengajuan Ditolak — Scheduly')

@section('content')
<style>
    .rejected-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background: #fdf7ff;
        background-image: radial-gradient(circle at 25% 75%, rgba(186, 26, 26, 0.06) 0%, transparent 55%),
                          radial-gradient(circle at 80% 20%, rgba(181, 162, 255, 0.12) 0%, transparent 55%);
    }

    .rejected-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 56px 48px;
        width: 100%;
        max-width: 480px;
        box-shadow: 0 8px 40px rgba(181, 162, 255, 0.18);
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

    .brand-row .b-icon { font-size: 22px; }

    .brand-row .b-name {
        font-size: 18px;
        font-weight: 800;
        color: #6351a7;
        letter-spacing: -0.3px;
    }

    /* icon illustration */
    .illustration-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 28px;
    }

    .reject-icon-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(145deg, #fff0f0, #ffe4e4);
        border: 3px solid #f59393;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 24px rgba(186, 26, 26, 0.15);
    }

    .reject-icon-circle svg {
        width: 48px;
        height: 48px;
        stroke: #ba1a1a;
        fill: none;
        stroke-width: 2.5;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* status chip */
    .status-chip-rejected {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff0f0;
        color: #ba1a1a;
        border: 1.5px solid #f59393;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        padding: 5px 14px;
        border-radius: 100px;
        margin-bottom: 20px;
    }

    .status-chip-rejected::before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #ba1a1a;
    }

    .rejected-title {
        font-size: 26px;
        font-weight: 800;
        color: #1c1b20;
        letter-spacing: -0.5px;
        margin-bottom: 14px;
    }

    .rejected-body {
        font-size: 15px;
        color: #797582;
        line-height: 1.7;
        margin-bottom: 24px;
        max-width: 360px;
        margin-left: auto;
        margin-right: auto;
    }

    /* rejection reason box */
    .reason-box {
        background: #fff8f8;
        border: 1.5px solid #f59393;
        border-radius: 14px;
        padding: 18px 22px;
        margin-bottom: 32px;
        text-align: left;
    }

    .reason-box-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #ba1a1a;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .reason-box-label svg {
        width: 13px;
        height: 13px;
        stroke: #ba1a1a;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .reason-text {
        font-size: 14px;
        color: #1c1b20;
        line-height: 1.6;
    }

    /* CTA */
    .action-group {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .btn-re-register {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #6351a7;
        color: #fff;
        text-decoration: none;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 700;
        padding: 13px 36px;
        transition: background 0.2s, transform 0.1s;
        box-shadow: 0 4px 16px rgba(99, 81, 167, 0.3);
        width: 100%;
    }

    .btn-re-register:hover { background: #5240a0; }
    .btn-re-register:active { transform: scale(0.98); }

    .support-link {
        font-size: 13px;
        color: #797582;
    }

    .support-link a {
        color: #6351a7;
        font-weight: 600;
        text-decoration: none;
    }

    .support-link a:hover { text-decoration: underline; }

    /* tip box */
    .tip-box {
        margin-top: 28px;
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

    .tip-box svg {
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

<div class="rejected-page">
    <div class="rejected-card">

        <!-- Brand -->
        <div class="brand-row">
            <span class="b-icon"><i data-lucide="calendar" class="icon-sm"></i></span>
            <span class="b-name">Scheduly</span>
        </div>

        <!-- Icon illustration -->
        <div class="illustration-wrap">
            <div class="reject-icon-circle">
                <!-- X icon -->
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
        </div>

        <!-- Status chip -->
        <div class="status-chip-rejected">DITOLAK</div>

        <h1 class="rejected-title">Pengajuan Ditolak</h1>

        <p class="rejected-body">
            Maaf, pengajuan akun kamu tidak dapat kami setujui saat ini.
            Berikut alasan yang diberikan oleh admin:
        </p>

        <!-- Rejection reason -->
        <div class="reason-box">
            <div class="reason-box-label">
                <svg viewBox="0 0 24 24">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Alasan Penolakan
            </div>
            <p class="reason-text">{{ $reason ?? 'Tidak ada alasan yang diberikan.' }}</p>
        </div>

        <!-- Actions -->
        <div class="action-group">
            <a href="{{ route('register') }}" class="btn-re-register">
                Daftar Ulang →
            </a>
            <p class="support-link">
                Ada pertanyaan? <a href="#">Hubungi Support</a>
            </p>
        </div>

        <!-- Tip -->
        <div class="tip-box">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <span>Saat mendaftar ulang, pastikan kamu menggunakan email kerja yang valid dan mengisi semua informasi dengan benar.</span>
        </div>

    </div>
</div>
@endsection
