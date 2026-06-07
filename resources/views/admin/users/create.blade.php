@extends('layouts.admin')
@section('title', 'Tambah Pengguna Baru')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap');

    .cu-wrap { font-family: 'Instrument Sans', sans-serif; color: #18171c; max-width: 600px; margin: 0 auto; }

    /* Header */
    .cu-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; gap: 16px; }
    .cu-header-left h1 { font-size: 24px; font-weight: 700; letter-spacing: -0.6px; color: #18171c; margin: 0 0 6px; line-height: 1.2; }
    .cu-header-left p { font-size: 13.5px; color: #78757f; margin: 0; font-weight: 400; line-height: 1.5; }
    
    .cu-btn-back {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fff; color: #4a4460; font-family: 'Instrument Sans', sans-serif;
        font-size: 13px; font-weight: 600; padding: 8px 14px; border-radius: 9px;
        border: 1px solid #e0dcea; cursor: pointer; text-decoration: none;
        transition: background 0.15s, border-color 0.15s; white-space: nowrap;
    }
    .cu-btn-back:hover { background: #f5f3fa; border-color: #c5bfda; }
    .cu-btn-back svg { width: 14px; height: 14px; stroke: currentColor; stroke-width: 2.5; fill: none; }

    /* Form Container */
    .cu-form-card {
        background: #fff; border: 1px solid #eceaf1; border-radius: 16px;
        padding: 32px 36px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .cu-form-group { margin-bottom: 22px; }
    .cu-label { display: block; font-size: 13px; font-weight: 600; color: #18171c; margin-bottom: 8px; }
    .cu-input, .cu-select {
        width: 100%; padding: 11px 14px; border: 1.5px solid #e0dcea; border-radius: 10px;
        font-family: 'Instrument Sans', sans-serif; font-size: 14px; color: #18171c;
        outline: none; transition: border-color 0.15s, box-shadow 0.15s; background: #faf9fd;
    }
    .cu-select { appearance: none; background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" fill="none" stroke="%239895a2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>'); background-repeat: no-repeat; background-position: right 14px center; background-size: 16px; padding-right: 40px; }
    .cu-input:focus, .cu-select:focus { border-color: #8b7fd4; box-shadow: 0 0 0 3px rgba(139,127,212,.12); background: #fff; }
    .cu-input::placeholder { color: #b8b4c4; }

    .cu-btn-submit {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; background: #18171c; color: #fff; font-family: 'Instrument Sans', sans-serif;
        font-size: 14.5px; font-weight: 600; padding: 13px; border-radius: 10px;
        border: none; cursor: pointer; transition: background 0.15s, transform 0.1s;
        margin-top: 10px;
    }
    .cu-btn-submit:hover { background: #2e2d35; }
    .cu-btn-submit:active { transform: scale(0.98); }
    .cu-btn-submit svg { width: 16px; height: 16px; stroke: currentColor; stroke-width: 2.5; fill: none; }

    .cu-error-box { background: #fef0f0; border: 1px solid #fecaca; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; font-size: 13px; color: #b91c1c; }
    .cu-error-box strong { display: block; margin-bottom: 4px; font-size: 13.5px; }
    .cu-error-box ul { margin: 0; padding-left: 20px; }
</style>
@endpush

@section('content')
<div class="cu-wrap">

    <div class="cu-header">
        <div class="cu-header-left">
            <h1>Tambah Pengguna</h1>
            <p>Buat akun pengguna secara manual tanpa harus menunggu pendaftaran.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="cu-btn-back">
            <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali
        </a>
    </div>

    <div class="cu-form-card">
        @if ($errors->any())
            <div class="cu-error-box">
                <strong>Terdapat beberapa kesalahan:</strong>
                <ul>
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="cu-form-group">
                <label for="name" class="cu-label">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" class="cu-input" required>
            </div>

            <div class="cu-form-group">
                <label for="email" class="cu-label">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="budi@example.com" class="cu-input" required>
            </div>

            <div class="cu-form-group">
                <label for="phone" class="cu-label">Nomor Telepon (Opsional)</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 08123456789" class="cu-input">
            </div>

            <div class="cu-form-group">
                <label for="role" class="cu-label">Role / Peran</label>
                <select id="role" name="role" class="cu-select" required>
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User Biasa</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>

            <div class="cu-form-group">
                <label for="password" class="cu-label">Password</label>
                <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" class="cu-input" required>
            </div>

            <div class="cu-form-group">
                <label for="password_confirmation" class="cu-label">Ulangi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang password di atas" class="cu-input" required>
            </div>

            <button type="submit" class="cu-btn-submit">
                <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan Pengguna
            </button>
        </form>
    </div>

</div>
@endsection
