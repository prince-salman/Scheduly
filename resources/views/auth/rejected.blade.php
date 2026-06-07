@extends('layouts.auth')

@section('title', 'Akun Dinonaktifkan — Scheduly')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    .rj-page {
        font-family: 'Instrument Sans', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background: #f7f6fa;
        background-image:
            radial-gradient(ellipse 60% 50% at 10% 90%, rgba(185,28,28,.06) 0%, transparent 70%),
            radial-gradient(ellipse 50% 40% at 90% 10%, rgba(139,127,212,.09) 0%, transparent 70%);
    }

    .rj-card {
        background: #fff;
        border-radius: 22px;
        width: 100%;
        max-width: 460px;
        border: 1px solid #eceaf1;
        overflow: hidden;
    }

    /* Top accent bar */
    .rj-accent-bar {
        height: 4px;
        background: linear-gradient(90deg, #b91c1c 0%, #dc2626 50%, #ef4444 100%);
    }

    .rj-body {
        padding: 40px 40px 36px;
        text-align: center;
    }

    /* Brand */
    .rj-brand {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        margin-bottom: 36px;
    }
    .rj-brand-dot {
        width: 28px;
        height: 28px;
        background: #18171c;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rj-brand-dot svg {
        width: 14px;
        height: 14px;
        stroke: #fff;
        stroke-width: 2;
        fill: none;
    }
    .rj-brand-name {
        font-size: 16px;
        font-weight: 700;
        color: #18171c;
        letter-spacing: -0.3px;
    }

    /* Icon */
    .rj-icon-wrap {
        margin: 0 auto 24px;
        width: 72px;
        height: 72px;
        background: #fef2f2;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #fecaca;
    }
    .rj-icon-wrap svg {
        width: 32px;
        height: 32px;
        stroke: #b91c1c;
        stroke-width: 2;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* Status pill */
    .rj-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 100px;
        margin-bottom: 18px;
    }
    .rj-pill::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #b91c1c;
    }

    .rj-title {
        font-size: 22px;
        font-weight: 700;
        color: #18171c;
        letter-spacing: -0.5px;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .rj-desc {
        font-size: 14px;
        color: #78757f;
        line-height: 1.65;
        margin-bottom: 24px;
        max-width: 340px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Reason box */
    .rj-reason {
        background: #faf9fd;
        border: 1px solid #eceaf1;
        border-left: 3px solid #b91c1c;
        border-radius: 10px;
        padding: 16px 18px;
        margin-bottom: 28px;
        text-align: left;
    }
    .rj-reason-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: #b91c1c;
        margin-bottom: 7px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .rj-reason-label svg {
        width: 12px;
        height: 12px;
        stroke: #b91c1c;
        stroke-width: 2;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
    .rj-reason-text {
        font-size: 13.5px;
        color: #3d3a47;
        line-height: 1.6;
        font-weight: 400;
    }

    /* Actions */
    .rj-actions { display: flex; flex-direction: column; gap: 10px; }

    .rj-btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        background: #18171c;
        color: #fff;
        font-family: 'Instrument Sans', sans-serif;
        font-size: 14px;
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 11px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, transform 0.1s;
        letter-spacing: -0.1px;
    }
    .rj-btn-primary:hover { background: #2e2d35; }
    .rj-btn-primary:active { transform: scale(0.99); }
    .rj-btn-primary svg {
        width: 15px;
        height: 15px;
        stroke: #fff;
        stroke-width: 2;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rj-support {
        font-size: 13px;
        color: #9895a2;
    }
    .rj-support a {
        color: #5a4d9e;
        font-weight: 600;
        text-decoration: none;
    }
    .rj-support a:hover { text-decoration: underline; }

    /* Footer tip */
    .rj-footer {
        background: #faf9fd;
        border-top: 1px solid #eceaf1;
        padding: 16px 40px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        text-align: left;
    }
    .rj-footer svg {
        width: 15px;
        height: 15px;
        stroke: #8b7fd4;
        stroke-width: 2;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .rj-footer p {
        font-size: 12.5px;
        color: #78757f;
        line-height: 1.55;
    }
    .rj-footer strong {
        color: #4a4460;
        font-weight: 600;
    }
</style>

<div class="rj-page">
    <div class="rj-card">
        <div class="rj-accent-bar"></div>
        <div class="rj-body">

            {{-- Brand --}}
            <div class="rj-brand">
                <div class="rj-brand-dot">
                    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <span class="rj-brand-name">Scheduly</span>
            </div>

            {{-- Icon --}}
            <div class="rj-icon-wrap">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>

            {{-- Status --}}
            <div class="rj-pill">Akun Dinonaktifkan</div>

            <h1 class="rj-title">Akses Ditolak</h1>

            <p class="rj-desc">
                Akun kamu tidak dapat mengakses platform saat ini.
                Berikut adalah alasan yang diberikan oleh admin.
            </p>

            {{-- Reason --}}
            <div class="rj-reason">
                <div class="rj-reason-label">
                    <svg viewBox="0 0 24 24">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    Alasan
                </div>
                <p class="rj-reason-text">{{ $reason ?? 'Tidak ada alasan yang diberikan.' }}</p>
            </div>

            {{-- Actions --}}
            <div class="rj-actions">
                <form action="{{ route('logout') }}" method="POST" style="width:100%">
                    @csrf
                    <button type="submit" class="rj-btn-primary">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Daftar Ulang dengan Akun Baru
                    </button>
                </form>
                <p class="rj-support">
                    Merasa ini keliru? <a href="#">Hubungi Support</a>
                </p>
            </div>

        </div>

        {{-- Footer tip --}}
        <div class="rj-footer">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <p>Saat mendaftar ulang, gunakan <strong>email kerja yang valid</strong> dan pastikan semua informasi diisi dengan benar agar pengajuan dapat disetujui.</p>
        </div>
    </div>
</div>
@endsection
