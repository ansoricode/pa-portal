@extends('admin.layout')
@section('title', 'Keamanan Akun')
@section('content')
<h1 class="h4 fw-bold mb-4"><i class="bi bi-shield-lock me-2" style="color:#056e4f"></i>Keamanan Akun</h1>

@if($recoveryCodes)
    <div class="alert alert-warning border-2">
        <h2 class="h6 fw-bold"><i class="bi bi-key me-1"></i>Simpan kode pemulihan ini sekarang</h2>
        <p class="small mb-2">Kode hanya ditampilkan <strong>sekali</strong>. Tiap kode bisa dipakai satu kali jika Anda kehilangan perangkat autentikator. Simpan di tempat aman (pengelola kata sandi / brankas), bukan di komputer yang sama dengan akun ini.</p>
        <div class="row row-cols-2 row-cols-md-4 g-2 font-monospace">
            @foreach($recoveryCodes as $c)<div class="col"><div class="bg-white border rounded px-2 py-1 text-center">{{ $c }}</div></div>@endforeach
        </div>
    </div>
@endif

<div class="card border-0 shadow-sm mb-4" style="max-width:720px">
    <div class="card-body">
        <h2 class="h6 fw-bold">Autentikasi Dua Langkah (2FA)</h2>

        @if($user->hasTwoFactor())
            <p><span class="badge bg-success">Aktif</span> sejak {{ $user->two_factor_confirmed_at->translatedFormat('d F Y H:i') }}.
               Sisa kode pemulihan: <strong>{{ count($user->two_factor_recovery_codes ?? []) }}</strong></p>

            <form method="POST" action="{{ route('admin.security.regenerate') }}" class="row g-2 align-items-end mb-3">
                @csrf
                <div class="col-md-7">
                    <label class="form-label small fw-semibold">Kata sandi saat ini (untuk membuat ulang kode pemulihan)</label>
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5"><button class="btn btn-outline-success w-100">Buat Ulang Kode Pemulihan</button></div>
            </form>

            @if(! config('portal.require_2fa'))
                <hr>
                <form method="POST" action="{{ route('admin.security.disable') }}" class="row g-2 align-items-end" data-confirm="Nonaktifkan 2FA? Akun Anda akan kurang terlindungi.">
                    @csrf
                    <div class="col-md-7"><input type="password" name="current_password" class="form-control" placeholder="Kata sandi saat ini" required></div>
                    <div class="col-md-5"><button class="btn btn-outline-danger w-100">Nonaktifkan 2FA</button></div>
                </form>
            @else
                <p class="small text-secondary mb-0">2FA diwajibkan oleh kebijakan sistem sehingga tidak dapat dinonaktifkan.</p>
            @endif

        @elseif($secret)
            <p class="small text-secondary">1) Pasang aplikasi autentikator (Google Authenticator, Microsoft Authenticator, Authy, dsb). 2) Pindai kode QR atau masukkan kunci manual. 3) Ketik kode 6 digit yang muncul untuk konfirmasi.</p>
            <div class="row g-4 align-items-center">
                <div class="col-md-5 text-center">
                    @if($qr)
                        <div class="border rounded p-2 d-inline-block bg-white" style="width:220px">{!! $qr !!}</div>
                    @else
                        <div class="alert alert-secondary small">QR tidak tersedia (paket <code>bacon/bacon-qr-code</code> belum terpasang). Gunakan kunci manual di samping.</div>
                    @endif
                </div>
                <div class="col-md-7">
                    <label class="form-label small fw-semibold">Kunci manual</label>
                    <div class="font-monospace bg-light border rounded p-2 mb-3 user-select-all">{{ trim(chunk_split($secret, 4, ' ')) }}</div>
                    <form method="POST" action="{{ route('admin.security.confirm') }}">
                        @csrf
                        <label class="form-label small fw-semibold" for="code">Kode 6 digit</label>
                        <div class="d-flex gap-2">
                            <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required>
                            <button class="btn btn-pa">Aktifkan</button>
                        </div>
                        @error('code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </form>
                </div>
            </div>
        @else
            <p><span class="badge bg-secondary">Belum aktif</span></p>
            <p class="small text-secondary">Tambahkan lapisan keamanan kedua: selain sandi, login memerlukan kode sementara dari ponsel Anda.</p>
            <form method="POST" action="{{ route('admin.security.enable') }}">@csrf <button class="btn btn-pa">Mulai Aktifkan 2FA</button></form>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm" style="max-width:720px">
    <div class="card-body small text-secondary">
        <div class="fw-bold text-dark mb-1">Login terakhir</div>
        {{ $user->last_login_at ? $user->last_login_at->translatedFormat('d F Y H:i') . ' dari IP ' . $user->last_login_ip : '-' }}
        <div class="mt-2">Jika ada login yang tidak Anda kenali, segera ganti kata sandi di <a href="{{ route('admin.account') }}">Akun Saya</a> dan hubungi Super Admin.</div>
    </div>
</div>
@endsection
