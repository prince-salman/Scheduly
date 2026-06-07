@extends('layouts.auth')

@section('title', 'Buat Password Baru — Scheduly')

@section('content')
<style>
    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background: #fdf7ff;
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
        line-height: 1.5;
    }

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
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .form-group {
        margin-bottom: 24px;
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

    .form-group input:focus {
        border-color: #6351a7;
        box-shadow: 0 0 0 3px rgba(99, 81, 167, 0.12);
        background: #fff;
    }

    .form-group input.is-error { border-color: #ba1a1a; }
    
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
    .field-error { font-size: 12px; color: #ba1a1a; margin-top: 5px; }

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
</style>

<div class="login-page">
    <div class="login-card">

        <div class="login-logo">
            <div class="logo-icon-wrap"><i data-lucide="shield-check" class="icon-sm"></i></div>
        </div>

        <h1 class="login-card-title">Buat Password Baru</h1>
        <p class="login-card-sub">Masukkan password baru untuk akunmu. Pastikan menggunakan kombinasi yang kuat.</p>

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

        <form action="{{ route('password.update') }}" method="POST" novalidate>
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $request->email) }}" 
                    class="{{ $errors->has('email') ? 'is-error' : '' }}"
                    readonly
                >
            </div>

            <div class="form-group">
                <label for="password">Password Baru</label>
                <div class="input-wrap">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="{{ $errors->has('password') ? 'is-error' : '' }}"
                        required
                        autofocus
                    >
                    <button type="button" class="pw-toggle" onclick="togglePassword('password', 'eye-icon-1')">
                        <svg id="eye-icon-1" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <div class="input-wrap">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required
                    >
                    <button type="button" class="pw-toggle" onclick="togglePassword('password_confirmation', 'eye-icon-2')">
                        <svg id="eye-icon-2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary-full">Simpan Password Baru</button>
        </form>

    </div>
</div>

@push('scripts')
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
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
