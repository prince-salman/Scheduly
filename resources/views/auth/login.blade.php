@extends('layouts.auth')

@section('title', 'Masuk — Scheduly')

@section('content')
<style>
    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background: #fdf7ff;
        /* subtle radial gradient */
        background-image: radial-gradient(circle at 20% 80%, rgba(181, 162, 255, 0.18) 0%, transparent 50%),
                          radial-gradient(circle at 80% 20%, rgba(154, 239, 227, 0.15) 0%, transparent 50%);
    }

    .login-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 48px 44px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 8px 40px rgba(181, 162, 255, 0.2);
        border: 1px solid #ede9ff;
    }

    /* logo area */
    .login-logo {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 32px;
    }

    .login-logo .logo-icon-wrap {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #6351a7, #b5a2ff);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 14px;
        box-shadow: 0 4px 16px rgba(99, 81, 167, 0.25);
    }

    .login-logo .brand-name {
        font-size: 22px;
        font-weight: 800;
        color: #6351a7;
        letter-spacing: -0.4px;
    }

    .login-card-title {
        font-size: 24px;
        font-weight: 800;
        color: #1c1b20;
        text-align: center;
        margin-bottom: 6px;
        letter-spacing: -0.4px;
    }

    .login-card-sub {
        font-size: 14px;
        color: #797582;
        text-align: center;
        margin-bottom: 32px;
    }

    /* error/session alerts */
    .alert-error {
        background: #fff0f0;
        border: 1.5px solid #ba1a1a;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #ba1a1a;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .alert-error svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        margin-top: 1px;
        stroke: #ba1a1a;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* form */
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

    .input-wrap {
        position: relative;
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

    .form-group input.is-error { border-color: #ba1a1a; }

    /* password toggle */
    .input-wrap input[type="password"],
    .input-wrap input[type="text"] {
        padding-right: 44px;
    }

    .pw-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        color: #797582;
        display: flex;
        align-items: center;
    }

    .pw-toggle svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .pw-toggle:hover { color: #6351a7; }

    .field-error {
        font-size: 12px;
        color: #ba1a1a;
        margin-top: 5px;
    }

    /* remember me row */
    .form-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
    }

    /* circular checkbox */
    .remember-label {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 13px;
        color: #797582;
        cursor: pointer;
        user-select: none;
    }

    .remember-label input[type="checkbox"] { display: none; }

    .cb-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #cac4d3;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: border-color 0.15s, background 0.15s;
    }

    .remember-label input:checked ~ .cb-circle {
        border-color: #6351a7;
        background: #6351a7;
    }

    .cb-circle svg {
        width: 11px;
        height: 11px;
        stroke: #fff;
        fill: none;
        stroke-width: 3;
        stroke-linecap: round;
        stroke-linejoin: round;
        opacity: 0;
        transition: opacity 0.15s;
    }

    .remember-label input:checked ~ .cb-circle svg { opacity: 1; }

    .forgot-link {
        font-size: 13px;
        font-weight: 600;
        color: #6351a7;
        text-decoration: none;
    }

    .forgot-link:hover { text-decoration: underline; }

    /* submit */
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
    }

    .btn-primary-full:hover { background: #5240a0; }
    .btn-primary-full:active { transform: scale(0.98); }

    .register-link {
        text-align: center;
        margin-top: 20px;
        font-size: 13px;
        color: #797582;
    }

    .register-link a {
        color: #6351a7;
        font-weight: 700;
        text-decoration: none;
    }

    .register-link a:hover { text-decoration: underline; }
</style>

<div class="login-page">
    <div class="login-card">

        <!-- Logo -->
        <div class="login-logo">
            <div class="logo-icon-wrap"><i data-lucide="calendar" class="icon-sm"></i></div>
            <span class="brand-name">Scheduly</span>
        </div>

        <h1 class="login-card-title">Masuk ke Akun</h1>
        <p class="login-card-sub">Selamat datang kembali! Masukkan detail akunmu.</p>

        {{-- session error --}}
        @if (session('error'))
            <div class="alert-error">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    @foreach ($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" novalidate>
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="kamu@perusahaan.com"
                    class="{{ $errors->has('email') ? 'is-error' : '' }}"
                    autocomplete="off"
                    required
                >
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        class="{{ $errors->has('password') ? 'is-error' : '' }}"
                        autocomplete="off"
                        required
                    >
                    <!-- show/hide toggle -->
                    <button type="button" class="pw-toggle" onclick="togglePassword()" aria-label="Toggle password visibility">
                        <svg id="eye-icon" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- remember + forgot -->
            <div class="form-row">
                <label class="remember-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="cb-circle">
                        <svg viewBox="0 0 12 12"><polyline points="2 6 5 9 10 3"/></svg>
                    </span>
                    Ingat saya
                </label>

                <a href="#" class="forgot-link">Lupa password?</a>
            </div>

            <button type="submit" class="btn-primary-full">Login →</button>
        </form>

        <p class="register-link">
            Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
        </p>

    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eye-icon');

        if (input.type === 'password') {
            input.type = 'text';
            // swap to eye-off
            icon.innerHTML = `
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
            `;
        } else {
            input.type = 'password';
            icon.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            `;
        }
    }
</script>
@endpush
@endsection