@extends('admin.layout')
@section('title', $user ? 'Ubah Pengguna' : 'Tambah Pengguna')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 fw-bold mb-0">{{ $user ? 'Ubah' : 'Tambah' }} Pengguna</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}">
                    @csrf
                    @if($user) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="name">Nama</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user?->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="email">Email (untuk login)</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user?->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="role">Peran</label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror">
                            @foreach(config('portal.roles') as $key => $r)
                                <option value="{{ $key }}" @selected(old('role', $user?->role ?? 'editor') === $key)>{{ $r['label'] }}</option>
                            @endforeach
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="password">Kata Sandi {{ $user ? '(kosongkan jika tidak diganti)' : '(sementara)' }}</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" @if(!$user) required @endif>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Min. 12 karakter, huruf besar &amp; kecil, angka, dan simbol. Pengguna wajib menggantinya saat login pertama.</div>
                    </div>
                    <div class="mb-4">
                        <input type="hidden" name="is_active" value="0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $user?->is_active ?? true))>
                            <label class="form-check-label" for="is_active">Akun aktif</label>
                        </div>
                    </div>
                    <button class="btn btn-pa px-4">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body small">
                <div class="fw-bold mb-2">Hak akses per peran</div>
                @foreach(config('portal.roles') as $key => $r)
                    <div class="mb-2"><span class="badge bg-{{ $key === 'superadmin' ? 'danger' : 'secondary' }}">{{ $r['label'] }}</span>
                        <div class="text-secondary">
                            @if($r['resources'] === '*') Semua menu konten
                            @else {{ collect($r['resources'])->map(fn ($k) => config("admin_resources.$k.label"))->implode(', ') }}
                            @endif
                            @if($r['settings']), Pengaturan Situs @endif
                            @if($r['users']), Pengguna @endif
                            @if($r['logs']), Log Aktivitas @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($user)
            <div class="card border-0 shadow-sm">
                <div class="card-body small">
                    <div class="fw-bold mb-2">Autentikasi Dua Langkah</div>
                    <p class="mb-2">Status: <span class="badge {{ $user->hasTwoFactor() ? 'bg-success' : 'bg-warning text-dark' }}">{{ $user->hasTwoFactor() ? 'Aktif' : 'Belum diatur' }}</span></p>
                    @if($user->hasTwoFactor() || $user->two_factor_secret)
                        <form method="POST" action="{{ route('admin.users.reset2fa', $user) }}" data-confirm="Reset 2FA pengguna ini? Semua sesinya akan diputus.">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger">Reset 2FA</button>
                        </form>
                        <div class="text-secondary mt-2">Gunakan jika perangkat hilang dan kode pemulihan habis. Pengguna harus mendaftarkan ulang.</div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
