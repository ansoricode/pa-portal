@extends('admin.layout')
@section('title', 'Akun Saya')
@section('content')
<h1 class="h4 fw-bold mb-4">Akun Saya</h1>
<div class="card border-0 shadow-sm" style="max-width:560px">
    <div class="card-body">
        <p class="text-secondary small">Email login: <strong>{{ auth()->user()->email }}</strong> &middot; Peran: <strong>{{ auth()->user()->roleLabel() }}</strong><br>Autentikasi dua langkah: <a href="{{ route('admin.security') }}">{{ auth()->user()->hasTwoFactor() ? 'aktif' : 'belum aktif — atur sekarang' }}</a></p>
        <p class="small text-secondary">Kata sandi baru: min. 12 karakter, huruf besar &amp; kecil, angka, dan simbol. Mengganti sandi akan memutus sesi di perangkat lain.</p>
        <form method="POST" action="{{ route('admin.account.update') }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Kata Sandi Baru <span class="text-secondary fw-normal">(kosongkan jika tidak diganti)</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Ulangi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
            </div>
            <hr>
            <div class="mb-3">
                <label class="form-label fw-semibold">Kata Sandi Saat Ini (wajib untuk menyimpan)</label>
                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-pa">Simpan</button>
        </form>
    </div>
</div>
@endsection
