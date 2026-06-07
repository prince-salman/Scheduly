@extends('layouts.admin')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Tambah Pengguna Baru</h1>
        <p class="page-subtitle">Buat akun pengguna secara manual tanpa harus menunggu persetujuan pendaftaran.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.users.index') }}" class="btn-primary" style="background:#fff;color:#6351a7;border:1px solid #ede9ff;">
            &larr; Kembali ke Daftar
        </a>
    </div>
</div>

<div class="form-container" style="max-width: 600px; background: #fff; border-radius: 16px; border: 1px solid #ede9ff; padding: 32px;">
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

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 20px;">
            <label for="name" style="display:block;font-size:13px;font-weight:600;color:#1c1b20;margin-bottom:7px;">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" style="width:100%;padding:12px 16px;border:1.5px solid #cac4d3;border-radius:12px;font-family:'Plus Jakarta Sans', sans-serif;font-size:14px;outline:none;" required>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="email" style="display:block;font-size:13px;font-weight:600;color:#1c1b20;margin-bottom:7px;">Alamat Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="budi@example.com" style="width:100%;padding:12px 16px;border:1.5px solid #cac4d3;border-radius:12px;font-family:'Plus Jakarta Sans', sans-serif;font-size:14px;outline:none;" required>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="phone" style="display:block;font-size:13px;font-weight:600;color:#1c1b20;margin-bottom:7px;">Nomor Telepon (Opsional)</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 08123456789" style="width:100%;padding:12px 16px;border:1.5px solid #cac4d3;border-radius:12px;font-family:'Plus Jakarta Sans', sans-serif;font-size:14px;outline:none;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="role" style="display:block;font-size:13px;font-weight:600;color:#1c1b20;margin-bottom:7px;">Role / Peran</label>
            <select id="role" name="role" style="width:100%;padding:12px 16px;border:1.5px solid #cac4d3;border-radius:12px;font-family:'Plus Jakarta Sans', sans-serif;font-size:14px;outline:none;background:#fff;" required>
                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User Biasa</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Super Admin</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="password" style="display:block;font-size:13px;font-weight:600;color:#1c1b20;margin-bottom:7px;">Password</label>
            <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" style="width:100%;padding:12px 16px;border:1.5px solid #cac4d3;border-radius:12px;font-family:'Plus Jakarta Sans', sans-serif;font-size:14px;outline:none;" required>
        </div>

        <div style="margin-bottom: 28px;">
            <label for="password_confirmation" style="display:block;font-size:13px;font-weight:600;color:#1c1b20;margin-bottom:7px;">Ulangi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password di atas" style="width:100%;padding:12px 16px;border:1.5px solid #cac4d3;border-radius:12px;font-family:'Plus Jakarta Sans', sans-serif;font-size:14px;outline:none;" required>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;padding:14px;justify-content:center;font-size:15px;border-radius:12px;">
            <i data-lucide="save" style="width:18px;height:18px;margin-right:6px;"></i> Simpan Pengguna Baru
        </button>
    </form>
</div>
@endsection
