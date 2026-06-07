@extends('layouts.auth')

@section('title', 'Scheduly — Smart Task Management')

@section('content')
<style>
    /* reset for this page */
    body { margin: 0; }

    /* ===== Navbar ===== */
    .landing-nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 200;
        background: rgba(253, 247, 255, 0.9);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid #cac4d3;
        padding: 0 48px;
        height: 64px;
        display: flex;
        align-items: center;
        gap: 0;
    }

    .nav-brand {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 20px;
        font-weight: 800;
        color: #6351a7;
        text-decoration: none;
        letter-spacing: -0.3px;
    }

    .nav-brand span { font-size: 22px; }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 32px;
        margin-left: auto;
        margin-right: 32px;
    }

    .nav-links a {
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        color: #797582;
        transition: color 0.15s;
    }

    .nav-links a:hover { color: #6351a7; }

    .nav-login-btn {
        padding: 9px 22px;
        background: transparent;
        border: 2px solid #6351a7;
        color: #6351a7;
        border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }

    .nav-login-btn:hover {
        background: #6351a7;
        color: #fff;
    }

    /* ===== Main split layout ===== */
    .landing-body {
        min-height: 100vh;
        display: flex;
        padding-top: 64px; /* nav height */
    }

    /* left hero */
    .hero-side {
        flex: 1.1;
        background: linear-gradient(145deg, #6351a7 0%, #4f3e96 60%, #3b2d7d 100%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 80px 60px;
        position: relative;
        overflow: hidden;
    }

    .hero-side::before {
        content: '';
        position: absolute;
        top: -80px;
        right: -80px;
        width: 360px;
        height: 360px;
        border-radius: 50%;
        background: rgba(181, 162, 255, 0.15);
        pointer-events: none;
    }

    .hero-side::after {
        content: '';
        position: absolute;
        bottom: -60px;
        left: -60px;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(0, 106, 97, 0.2);
        pointer-events: none;
    }

    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.15);
        color: #e0d9ff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 6px 14px;
        border-radius: 100px;
        margin-bottom: 24px;
        width: fit-content;
    }

    .hero-title {
        font-size: 44px;
        font-weight: 800;
        color: #fff;
        line-height: 1.15;
        letter-spacing: -1px;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .hero-title em {
        font-style: normal;
        color: #b5a2ff;
    }

    .hero-body {
        font-size: 16px;
        color: rgba(255,255,255,0.75);
        line-height: 1.7;
        max-width: 420px;
        margin-bottom: 40px;
        position: relative;
        z-index: 1;
    }

    /* social proof */
    .social-proof {
        display: flex;
        align-items: center;
        gap: 14px;
        position: relative;
        z-index: 1;
    }

    .avatar-stack {
        display: flex;
    }

    .avatar-stack .av {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 2px solid #6351a7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: #fff;
        margin-left: -10px;
        flex-shrink: 0;
    }

    .avatar-stack .av:first-child { margin-left: 0; }

    .av-1 { background: #b5a2ff; }
    .av-2 { background: #006a61; }
    .av-3 { background: #9aefe3; color: #006a61; }
    .av-4 { background: #c1b137; color: #fff; }

    .social-proof-text {
        font-size: 13px;
        color: rgba(255,255,255,0.8);
        font-weight: 500;
    }

    .social-proof-text strong {
        color: #fff;
        font-weight: 700;
    }

    /* ===== Right: Register Card ===== */
    .form-side {
        flex: 0.9;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 48px;
        background: #fdf7ff;
    }

    .register-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 44px 40px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 8px 40px rgba(181, 162, 255, 0.2);
        border: 1px solid #ede9ff;
    }

    .card-header {
        margin-bottom: 32px;
    }

    .card-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ede9ff;
        color: #6351a7;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        padding: 5px 12px;
        border-radius: 100px;
        margin-bottom: 14px;
    }

    .card-title {
        font-size: 26px;
        font-weight: 800;
        color: #1c1b20;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }

    .card-subtitle {
        font-size: 14px;
        color: #797582;
    }

    /* form fields */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1c1b20;
        margin-bottom: 7px;
    }

    .form-group input {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #cac4d3;
        border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        color: #1c1b20;
        background: #fdf7ff;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-group input::placeholder { color: #b0abb8; }

    .form-group input:focus {
        border-color: #6351a7;
        box-shadow: 0 0 0 3px rgba(99, 81, 167, 0.12);
        background: #fff;
    }

    .form-group input.is-error {
        border-color: #ba1a1a;
    }

    .field-error {
        font-size: 12px;
        color: #ba1a1a;
        margin-top: 5px;
    }

    /* submit btn */
    .btn-primary-full {
        width: 100%;
        padding: 14px;
        background: #6351a7;
        color: #fff;
        border: none;
        border-radius: 14px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        box-shadow: 0 4px 16px rgba(99, 81, 167, 0.3);
        margin-top: 8px;
    }

    .btn-primary-full:hover { background: #5240a0; }
    .btn-primary-full:active { transform: scale(0.98); }

    .terms-note {
        font-size: 12px;
        color: #797582;
        text-align: center;
        margin-top: 16px;
        line-height: 1.5;
    }

    .terms-note a {
        color: #6351a7;
        text-decoration: none;
        font-weight: 600;
    }

    /* ===== Footer ===== */
    .landing-footer {
        background: #fff;
        border-top: 1px solid #cac4d3;
        padding: 20px 48px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        color: #797582;
    }

    .footer-links {
        display: flex;
        gap: 24px;
    }

    .footer-links a {
        color: #797582;
        text-decoration: none;
        transition: color 0.15s;
    }

    .footer-links a:hover { color: #6351a7; }

    /* responsive-ish */
    @media (max-width: 900px) {
        .hero-side { display: none; }
        .form-side { padding: 40px 24px; }
    }
</style>

<!-- ===== Navbar ===== -->
<nav class="landing-nav">
    <a href="/" class="nav-brand">
        <span><i data-lucide="calendar" class="icon-sm"></i></span> Scheduly
    </a>

    <div class="nav-links">
        <a href="#">Features</a>
        <a href="#">Pricing</a>
    </div>

    <a href="{{ route('login') }}" class="nav-login-btn">Login</a>
</nav>

<!-- ===== Main layout ===== -->
<div class="landing-body">

    <!-- Hero left -->
    <div class="hero-side">
        <div class="hero-eyebrow">
            ✨ Smarter productivity
        </div>

        <h1 class="hero-title">
            Stress-free<br>scheduling<br><em>starts here.</em>
        </h1>

        <p class="hero-body">
            Plan your day, manage your team's tasks, and hit every deadline — all in one beautiful workspace. No more scattered sticky notes.
        </p>

        <!-- social proof -->
        <div class="social-proof">
            <div class="avatar-stack">
                <div class="av av-1">JD</div>
                <div class="av av-2">AM</div>
                <div class="av av-3">SR</div>
                <div class="av av-4">+</div>
            </div>
            <div class="social-proof-text">
                <strong>Join 10,000+ users</strong><br>
                who ship faster with Scheduly
            </div>
        </div>
    </div>

    <!-- Register form right -->
    <div class="form-side">
        <div class="register-card">
            <div class="card-header">
                <div class="card-badge">🚀 Get started free</div>
                <h2 class="card-title">Pengajuan Akun</h2>
                <p class="card-subtitle">Isi detail kamu untuk memulai. Admin akan meninjau dalam 1x24 jam.</p>
            </div>

            @if ($errors->any())
                <div style="background:#fff0f0;border:1.5px solid #ba1a1a;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#ba1a1a;">
                    <strong>Oops!</strong> Ada beberapa masalah:
                    <ul style="margin-top:6px;padding-left:18px;">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" novalidate>
                @csrf

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="e.g. Andi Wirawan"
                        class="{{ $errors->has('name') ? 'is-error' : '' }}"
                        autocomplete="name"
                        required
                    >
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Work Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="kamu@perusahaan.com"
                        class="{{ $errors->has('email') ? 'is-error' : '' }}"
                        autocomplete="email"
                        required
                    >
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Min. 8 karakter"
                        class="{{ $errors->has('password') ? 'is-error' : '' }}"
                        autocomplete="new-password"
                        required
                    >
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Ulangi password"
                    autocomplete="new-password"
                >
            </div>

                <button type="submit" class="btn-primary-full">
                    Register Now →
                </button>

                <p class="terms-note">
                    Dengan mendaftar, kamu menyetujui <a href="#">Terms of Service</a>
                    dan <a href="#">Privacy Policy</a> kami.
                    Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>.
                </p>
            </form>
        </div>
    </div>

</div>

<!-- ===== Footer ===== -->
<footer class="landing-footer">
    <span>&copy; {{ date('Y') }} Scheduly. Crafted by <a href="https://portofolio-salman.netlify.app/#guestbook" target="_blank" rel="noopener" style="text-decoration:none;color:#6351a7;font-weight:600;">salman</a> & alfihra.</span>
    <div class="footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Contact Support</a>
    </div>
</footer>
@endsection