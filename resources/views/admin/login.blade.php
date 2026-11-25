<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Masuk - Panel Admin</title>
    @include('partials.favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body style="background:linear-gradient(135deg,#056e4f,#034d37);min-height:100vh" class="d-flex align-items-center">
<div class="container" style="max-width:420px">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-bank2 display-5" style="color:#056e4f"></i>
                <h1 class="h5 fw-bold mt-2 mb-0">Panel Admin</h1>
                <small class="text-secondary">{{ $setting('site_name', 'Portal Satker') }}</small>
            </div>
            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
                </div>
                <button class="btn w-100 text-white" style="background:#056e4f">Masuk</button>
            </form>
        </div>
    </div>
    <p class="text-center text-white-50 small mt-3"><a href="{{ route('home') }}" class="text-white-50">&larr; Kembali ke website</a></p>
</div>
</body>
</html>
