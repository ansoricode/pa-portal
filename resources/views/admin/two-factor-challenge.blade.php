<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Verifikasi 2 Langkah - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body style="background:linear-gradient(135deg,#056e4f,#034d37);min-height:100vh" class="d-flex align-items-center">
<div class="container" style="max-width:420px">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock display-5" style="color:#056e4f"></i>
                <h1 class="h5 fw-bold mt-2 mb-1">Verifikasi Dua Langkah</h1>
                <small class="text-secondary">Masukkan 6 digit kode dari aplikasi autentikator Anda, atau salah satu kode pemulihan.</small>
            </div>
            <form method="POST" action="{{ route('admin.2fa.verify') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="code">Kode Verifikasi</label>
                    <input type="text" id="code" name="code" class="form-control form-control-lg text-center @error('code') is-invalid @enderror" inputmode="numeric" autocomplete="one-time-code" maxlength="32" required autofocus>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn w-100 text-white" style="background:#056e4f">Verifikasi &amp; Masuk</button>
            </form>
            <p class="text-center small mt-3 mb-0"><a href="{{ route('login') }}" class="text-secondary">&larr; Kembali ke login</a></p>
        </div>
    </div>
</div>
</body>
</html>
