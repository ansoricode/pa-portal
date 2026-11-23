@php
    $siteName = $setting('site_name', 'Pengadilan Agama Contoh');
    $seoSiteName = trim((string) $setting('seo_title', $siteName)) ?: $siteName;
    $logo = $setting('logo') ? asset('storage/' . $setting('logo')) : 'https://app.badilag.net/assets/images/logo_ma.png';
    $pageTitle = trim($__env->yieldContent('title'));
    $metaTitle = trim($__env->yieldContent('meta_title')) ?: ($pageTitle !== '' ? $pageTitle . ' | ' . $seoSiteName : $seoSiteName);
    $metaDescription = trim(strip_tags((string) $__env->yieldContent('meta_description'))) ?: $setting('site_description', 'Portal Resmi Satuan Kerja Peradilan Agama');
    $metaKeywords = trim((string) $setting('seo_keywords', ''));
    $faviconPath = $setting('favicon');
    $faviconUrl = $faviconPath ? asset('storage/' . $faviconPath) : asset('favicon.ico');
    $faviconType = $faviconPath && str_ends_with(strtolower($faviconPath), '.png') ? 'image/png' : 'image/x-icon';
    $ogImage = trim($__env->yieldContent('og_image')) ?: ($setting('og_image') ? asset('storage/' . $setting('og_image')) : $logo);
    $ogTitle = trim($__env->yieldContent('og_title')) ?: $metaTitle;
    $ogDescription = trim(strip_tags((string) $__env->yieldContent('og_description'))) ?: $metaDescription;
    $ogType = trim($__env->yieldContent('og_type')) ?: 'website';
    $sections = config('portal.sections');
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription }}">
    @if($metaKeywords !== '')<meta name="keywords" content="{{ $metaKeywords }}">@endif
    <meta name="theme-color" content="#056e4f">
    <title>{{ $metaTitle }}</title>
    <link rel="icon" href="{{ $faviconUrl }}" type="{{ $faviconType }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    @if($ogImage)<meta name="twitter:image" content="{{ $ogImage }}">@endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    @stack('head')
</head>
<body class="bg-light text-dark">

<a href="#main-content" class="visually-hidden-focusable position-absolute p-2 bg-white">Lewati ke konten utama</a>

<header class="sticky-top shadow-sm bg-white">
    {{-- Top bar kontak --}}
    <div class="bg-pa-primary text-white py-1" style="font-size: 0.85rem;">
        <div class="container-lg d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-3">
                @if($setting('phone'))<span><i class="bi bi-telephone-fill text-pa-secondary me-1"></i> {{ $setting('phone') }}</span>@endif
                @if($setting('email'))<span class="d-none d-md-inline"><i class="bi bi-envelope-fill text-pa-secondary me-1"></i> {{ $setting('email') }}</span>@endif
                <span class="d-none d-lg-inline"><i class="bi bi-shield-lock-fill text-pa-secondary me-1"></i> Portal Resmi Terverifikasi (.go.id)</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ $setting('siwas_url', 'https://siwas.mahkamahagung.go.id') }}" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-none fw-semibold">
                    <i class="bi bi-megaphone-fill text-pa-secondary me-1"></i> SIWAS MARI
                </a>
                <span class="text-white-50">|</span>
                <div class="d-flex gap-2">
                    @if($setting('facebook_url'))<a href="{{ $setting('facebook_url') }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="text-white"><i class="bi bi-facebook"></i></a>@endif
                    @if($setting('instagram_url'))<a href="{{ $setting('instagram_url') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="text-white"><i class="bi bi-instagram"></i></a>@endif
                    @if($setting('youtube_url'))<a href="{{ $setting('youtube_url') }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="text-white"><i class="bi bi-youtube"></i></a>@endif
                </div>
            </div>
        </div>
    </div>

    {{-- Navigasi 6 menu utama --}}
    <nav class="navbar navbar-expand-wide bg-white py-2" aria-label="Navigasi Utama Website">
        <div class="container-lg">
            <a class="navbar-brand d-flex align-items-center gap-3" href="{{ route('home') }}">
                <img src="{{ $logo }}" alt="Logo {{ $siteName }}" style="height: 3.2rem; width: auto;" class="object-fit-contain">
                <div class="lh-sm">
                    <span class="d-block font-display fw-bold text-pa-primary fs-5 text-uppercase">{{ $siteName }}</span>
                    <span class="d-block text-secondary small fw-medium">{{ $setting('site_tagline', 'Mahkamah Agung Republik Indonesia') }}</span>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Buka Menu Navigasi">
                <i class="bi bi-list fs-1 text-pa-primary"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
                    </li>

                    @foreach($sections as $key => $cfg)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is($key . '*') ? 'active' : '' }}" href="{{ route('page.show', $key) }}" id="menu-{{ $key }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $cfg['title'] }}
                            </a>
                            <ul class="dropdown-menu shadow border-0" aria-labelledby="menu-{{ $key }}">
                                @foreach(($navPages[$key] ?? collect()) as $p)
                                    <li><a class="dropdown-item py-2" href="{{ route('page.show', [$key, $p->slug]) }}">{{ $p->title }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('berita*') ? 'active' : '' }}" href="{{ route('news.index') }}">Berita &amp; Publikasi</a>
                    </li>

                    <li class="nav-item nav-search-item">
                        <button class="btn btn-outline-success btn-sm d-flex align-items-center gap-1 rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Buka Pencarian Internal">
                            <i class="bi bi-search"></i><span class="d-xl-none d-xxl-inline">Cari</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

@if(session('survey_ok'))
    <div class="container-lg mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('survey_ok') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    </div>
@endif

<main id="main-content">
    @yield('content')
</main>

<footer class="bg-dark text-white pt-5 pb-4">
    <div class="container-lg">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $logo }}" alt="Logo" class="bg-white p-1 rounded-circle" style="height: 3rem; width: 3rem; object-fit: contain;">
                    <div>
                        <h2 class="font-display fw-bold fs-5 mb-0 text-white">{{ $siteName }}</h2>
                        <small class="text-white-50">{{ $setting('site_tagline', 'Mahkamah Agung Republik Indonesia') }}</small>
                    </div>
                </div>
                <p class="text-white-50 small">
                    {{ collect([$setting('address_street'), $setting('address_village') ? 'Kel. ' . $setting('address_village') : null, $setting('address_district') ? 'Kec. ' . $setting('address_district') : null, $setting('address_province')])->filter()->implode(', ') }}<br>
                    @if($setting('phone'))Telepon: {{ $setting('phone') }}@endif
                    @if($setting('email')) | Email: {{ $setting('email') }}@endif
                </p>
            </div>
            <div class="col-md-6 col-lg-4">
                <h3 class="font-display fw-bold fs-6 text-pa-secondary mb-3">Tautan Ekosistem Peradilan</h3>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a href="https://www.mahkamahagung.go.id" target="_blank" rel="noopener noreferrer" class="text-white-50 text-decoration-none">Mahkamah Agung RI</a></li>
                    <li><a href="https://badilag.mahkamahagung.go.id" target="_blank" rel="noopener noreferrer" class="text-white-50 text-decoration-none">Ditjen Badan Peradilan Agama</a></li>
                    <li><a href="https://jdih.mahkamahagung.go.id" target="_blank" rel="noopener noreferrer" class="text-white-50 text-decoration-none">JDIH Mahkamah Agung RI</a></li>
                    <li><a href="https://simtepa.badilag.net" target="_blank" rel="noopener noreferrer" class="text-white-50 text-decoration-none">SIMTEPA Badilag</a></li>
                </ul>
            </div>
            <div class="col-md-6 col-lg-3">
                <h3 class="font-display fw-bold fs-6 text-pa-secondary mb-3">Statistik Kunjungan Website</h3>
                <ul class="list-unstyled small text-white-50 d-grid gap-2 bg-white bg-opacity-10 p-3 rounded">
                    <li class="d-flex justify-content-between"><span>Pengunjung Hari Ini:</span> <strong class="text-white">{{ number_format($visitStats['today'], 0, ',', '.') }}</strong></li>
                    <li class="d-flex justify-content-between"><span>Bulan Ini:</span> <strong class="text-white">{{ number_format($visitStats['month'], 0, ',', '.') }}</strong></li>
                    <li class="d-flex justify-content-between"><span>Total Kunjungan:</span> <strong class="text-pa-secondary">{{ number_format($visitStats['total'], 0, ',', '.') }}</strong></li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="text-center small text-white-50">
            &copy; {{ date('Y') }} {{ $siteName }}. Standar Tata Kelola Website SK Dirjen Badilag No. 2722/DJA/SK.TI1/X/2026.
        </div>
    </div>
</footer>

{{-- Widget aksesibilitas --}}
<div class="a11y-widget">
    <div id="a11yPanel" class="a11y-panel card shadow-lg border-2 border-pa-primary mb-2">
        <div class="card-header bg-pa-primary text-white fw-bold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-universal-access me-1"></i> Sarana Disabilitas</span>
            <button type="button" class="btn-close btn-close-white btn-sm" data-a11y="toggle" aria-label="Tutup Panel Disabilitas"></button>
        </div>
        <div class="card-body d-grid gap-2">
            <button type="button" class="btn btn-outline-success btn-sm text-start fw-semibold" data-a11y="font-up"><i class="bi bi-zoom-in me-2"></i>Perbesar Teks (Zoom In)</button>
            <button type="button" class="btn btn-outline-success btn-sm text-start fw-semibold" data-a11y="font-down"><i class="bi bi-zoom-out me-2"></i>Perkecil Teks (Zoom Out)</button>
            <button type="button" class="btn btn-outline-dark btn-sm text-start fw-semibold" data-a11y="contrast"><i class="bi bi-circle-half me-2"></i>Mode Kontras Tinggi</button>
            <button type="button" class="btn btn-pa-secondary btn-sm text-start fw-bold" data-a11y="reader"><i class="bi bi-volume-up-fill me-2"></i><span id="srLabel">Aktifkan Pembaca Layar</span></button>
            <button type="button" class="btn btn-link btn-sm text-secondary text-decoration-none" data-a11y="reset">Reset Tampilan Standar</button>
        </div>
    </div>
    <button type="button" class="btn btn-pa-secondary rounded-pill shadow-lg px-3 py-2 fw-bold float-end border border-2 border-dark" data-a11y="toggle" aria-label="Menu Aksesibilitas Penyandang Disabilitas">
        <i class="bi bi-universal-access fs-5 me-1"></i> Disabilitas
    </button>
</div>

{{-- Modal pencarian internal --}}
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-pa-primary text-white">
                <h5 class="modal-title font-display fw-bold" id="searchModalLabel"><i class="bi bi-search me-2"></i>Pencarian Informasi Website</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('search') }}" method="GET" class="d-flex gap-2">
                    <input type="search" name="q" class="form-control" minlength="2" placeholder="Ketik kata kunci (mis. jadwal sidang, panjar, DIPA, LHKPN)..." required>
                    <button type="submit" class="btn btn-pa-secondary">Cari</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal survei --}}
<div class="modal fade" id="survey-modal" tabindex="-1" aria-labelledby="surveyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-pa-primary text-white">
                <h5 class="modal-title font-display fw-bold" id="surveyModalLabel">Survei Pelayanan Publik Terpadu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('survey.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="ratingInput" class="form-label fw-semibold">Penilaian Kualitas Pelayanan PTSP &amp; Website:</label>
                        <select id="ratingInput" name="rating" class="form-select" required>
                            <option value="">-- Pilih Penilaian --</option>
                            <option value="4">Sangat Baik</option>
                            <option value="3">Baik</option>
                            <option value="2">Cukup</option>
                            <option value="1">Kurang</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="saranInput" class="form-label fw-semibold">Saran / Masukan Perbaikan:</label>
                        <textarea id="saranInput" name="comment" class="form-control" rows="3" maxlength="1000" placeholder="Tuliskan masukan Anda..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-pa-primary w-100">Kirim Survei</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/portal.js') }}"></script>
@stack('scripts')
</body>
</html>
