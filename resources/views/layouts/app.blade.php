@php
    $siteName = $setting('site_name', 'Pengadilan Agama Contoh');
    $seoSiteName = trim((string) $setting('seo_title', $siteName)) ?: $siteName;
    $logo = $setting('logo') ? asset('storage/' . $setting('logo')) : 'https://app.badilag.net/assets/images/logo_ma.png';
    $pageTitle = trim($__env->yieldContent('title'));
    $metaTitle = trim($__env->yieldContent('meta_title')) ?: ($pageTitle !== '' ? $pageTitle . ' | ' . $seoSiteName : $seoSiteName);
    $metaDescription = trim(strip_tags((string) $__env->yieldContent('meta_description'))) ?: $setting('site_description', 'Portal Resmi Satuan Kerja Peradilan Agama');
    $metaKeywords = trim((string) $setting('seo_keywords', ''));
    $ogImage = trim($__env->yieldContent('og_image')) ?: ($setting('og_image') ? asset('storage/' . $setting('og_image')) : $logo);
    $ogTitle = trim($__env->yieldContent('og_title')) ?: $metaTitle;
    $ogDescription = trim(strip_tags((string) $__env->yieldContent('og_description'))) ?: $metaDescription;
    $ogType = trim($__env->yieldContent('og_type')) ?: 'website';
    $sections = config('portal.sections');
    $whatsapp = trim((string) $setting('whatsapp', ''));
    $whatsappUrl = '';
    if ($whatsapp !== '') {
        if (preg_match('/^https?:\/\//i', $whatsapp)) {
            $whatsappUrl = $whatsapp;
        } else {
            $whatsappNumber = preg_replace('/\D+/', '', $whatsapp);
            if (str_starts_with($whatsappNumber, '0')) {
                $whatsappNumber = '62'.substr($whatsappNumber, 1);
            } elseif (str_starts_with($whatsappNumber, '8')) {
                $whatsappNumber = '62'.$whatsappNumber;
            }
            $whatsappUrl = $whatsappNumber !== '' ? 'https://wa.me/'.$whatsappNumber : '';
        }
    }
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
    @include('partials.favicon')
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
        <div class="container-lg d-flex justify-content-between align-items-center gap-2 topbar-shell">
            <div class="d-flex align-items-center gap-3 topbar-contact">
                @if($setting('phone'))<span><i class="bi bi-telephone-fill text-pa-secondary me-1"></i> {{ $setting('phone') }}</span>@endif
                @if($setting('email'))<span class="d-none d-md-inline"><i class="bi bi-envelope-fill text-pa-secondary me-1"></i> {{ $setting('email') }}</span>@endif
                <span class="d-none d-lg-inline"><i class="bi bi-shield-lock-fill text-pa-secondary me-1"></i> Portal Resmi Terverifikasi (.go.id)</span>
            </div>
            <div class="d-flex align-items-center gap-2 gap-md-3 topbar-actions">
                @if($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-none fw-semibold topbar-link" aria-label="WhatsApp PTSP" title="WhatsApp PTSP">
                        <i class="bi bi-whatsapp text-pa-secondary me-md-1"></i><span class="topbar-label">WhatsApp PTSP</span>
                    </a>
                @endif
                <a href="{{ $setting('siwas_url', 'https://siwas.mahkamahagung.go.id') }}" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-none fw-semibold topbar-link" aria-label="SIWAS MARI" title="SIWAS MARI">
                    <i class="bi bi-megaphone-fill text-pa-secondary me-md-1"></i><span class="topbar-label">SIWAS MARI</span>
                </a>
                <span class="text-white-50 topbar-separator">|</span>
                <div class="d-flex gap-2 topbar-social">
                    @php
                        $topbarSocial = collect($footerSocial);
                        $topbarPrimary = $topbarSocial->take(3);
                        $topbarOverflow = $topbarSocial->slice(3);
                    @endphp
                    @foreach($topbarPrimary as $social)
                        @php
                            $headerSocialIcon = strtolower(trim((string) ($social['icon'] ?? '')));
                            $headerSocialName = strtolower(trim((string) ($social['name'] ?? '')));
                            $headerKnownIcons = [
                                'facebook' => 'bi-facebook',
                                'fb' => 'bi-facebook',
                                'instagram' => 'bi-instagram',
                                'ig' => 'bi-instagram',
                                'youtube' => 'bi-youtube',
                                'tiktok' => 'bi-tiktok',
                                'twitter' => 'bi-twitter-x',
                                'x' => 'bi-twitter-x',
                                'whatsapp' => 'bi-whatsapp',
                                'telegram' => 'bi-telegram',
                                'linkedin' => 'bi-linkedin',
                                'threads' => 'bi-threads',
                            ];
                            $headerSocialIcon = str_starts_with($headerSocialIcon, 'bi-')
                                ? $headerSocialIcon
                                : ($headerKnownIcons[$headerSocialName] ?? ($headerKnownIcons[$headerSocialIcon] ?? 'bi-link-45deg'));
                        @endphp
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['name'] }}" title="{{ $social['name'] }}" class="text-white">
                            <i class="bi {{ $headerSocialIcon }}"></i>
                        </a>
                    @endforeach
                    @if($topbarOverflow->isNotEmpty())
                        <div class="dropdown">
                            <button type="button" class="btn btn-link text-white p-0 border-0 topbar-social-more" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Media sosial lainnya" title="Media sosial lainnya">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1 topbar-social-menu">
                                @foreach($topbarOverflow as $social)
                                    @php
                                        $overflowIcon = strtolower(trim((string) ($social['icon'] ?? '')));
                                        $overflowName = strtolower(trim((string) ($social['name'] ?? '')));
                                        $overflowKnownIcons = [
                                            'facebook' => 'bi-facebook',
                                            'instagram' => 'bi-instagram',
                                            'youtube' => 'bi-youtube',
                                            'tiktok' => 'bi-tiktok',
                                            'twitter' => 'bi-twitter-x',
                                            'x' => 'bi-twitter-x',
                                            'whatsapp' => 'bi-whatsapp',
                                            'telegram' => 'bi-telegram',
                                            'linkedin' => 'bi-linkedin',
                                            'threads' => 'bi-threads',
                                        ];
                                        $overflowIcon = str_starts_with($overflowIcon, 'bi-')
                                            ? $overflowIcon
                                            : ($overflowKnownIcons[$overflowName] ?? 'bi-link-45deg');
                                    @endphp
                                    <li>
                                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="dropdown-item d-flex align-items-center gap-2">
                                            <i class="bi {{ $overflowIcon }}"></i>
                                            <span>{{ $social['name'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
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

<footer class="site-footer bg-dark text-white pt-5 pb-4">
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
                @if($setting('footer_hours'))
                    <p class="small text-white-50 mb-2"><i class="bi bi-clock me-1"></i>{{ $setting('footer_hours') }}</p>
                @endif
                <div class="site-footer-social d-flex gap-3">
                    @foreach($footerSocial as $social)
                        @php
                            $socialName = strtolower(trim((string) ($social['name'] ?? '')));
                            $socialIcon = strtolower(trim((string) ($social['icon'] ?? '')));
                            $knownSocialIcons = [
                                'facebook' => 'bi-facebook',
                                'fb' => 'bi-facebook',
                                'instagram' => 'bi-instagram',
                                'ig' => 'bi-instagram',
                                'youtube' => 'bi-youtube',
                                'tiktok' => 'bi-tiktok',
                                'twitter' => 'bi-twitter-x',
                                'x' => 'bi-twitter-x',
                                'whatsapp' => 'bi-whatsapp',
                                'telegram' => 'bi-telegram',
                                'linkedin' => 'bi-linkedin',
                            ];
                            $bootstrapIcon = str_starts_with($socialIcon, 'bi-')
                                ? $socialIcon
                                : ($knownSocialIcons[$socialName] ?? ($knownSocialIcons[$socialIcon] ?? null));
                        @endphp
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['name'] }}" title="{{ $social['name'] }}" class="text-white">
                            @if($bootstrapIcon)
                                <i class="bi {{ $bootstrapIcon }}" aria-hidden="true"></i>
                            @else
                                <i class="bi bi-link-45deg" aria-hidden="true"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
            @if($footerLinks)
                <div class="col-md-6 col-lg-3">
                    <h3 class="font-display fw-bold fs-6 text-pa-secondary mb-3">{{ $setting('footer_links_title', 'Tautan Footer') }}</h3>
                    <ul class="list-unstyled small d-grid gap-2">
                        @foreach($footerLinks as $link)
                            <li>
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="text-white-50 text-decoration-none d-flex align-items-start gap-2">
                                    @if(! empty($link['icon']))
                                        @if(str_starts_with($link['icon'], 'uploads/footer/') || str_starts_with($link['icon'], 'http'))
                                            <img src="{{ str_starts_with($link['icon'], 'http') ? $link['icon'] : asset('storage/'.$link['icon']) }}" alt="" style="height:18px;width:18px;object-fit:contain">
                                        @else
                                            <i class="bi {{ str_starts_with($link['icon'], 'bi-') ? $link['icon'] : 'bi-'.$link['icon'] }}"></i>
                                        @endif
                                    @else
                                        <i class="bi bi-link-45deg"></i>
                                    @endif
                                    <span>{{ $link['name'] }}@if(! empty($link['description']))<small class="d-block text-white-50">{{ $link['description'] }}</small>@endif</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="col-md-6 col-lg-{{ $footerLinks ? 4 : 7 }}">
                <h3 class="font-display fw-bold fs-6 text-pa-secondary mb-3">{{ $setting('footer_ecosystem_title', 'Tautan Ekosistem Peradilan') }}</h3>
                <ul class="list-unstyled small d-grid gap-2">
                    @foreach($footerEcosystem as $link)
                        <li>
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="text-white-50 text-decoration-none d-flex align-items-center gap-2">
                                @if(! empty($link['icon']))
                                    <i class="bi {{ str_starts_with($link['icon'], 'bi-') ? $link['icon'] : 'bi-'.$link['icon'] }}"></i>
                                @elseif(! empty($link['logo']))
                                    <span class="d-inline-flex align-items-center justify-content-center" style="position:relative;width:22px;height:22px">
                                        <i class="bi bi-bank ecosystem-icon-fallback" aria-hidden="true"></i>
                                        <img src="{{ asset('storage/'.$link['logo']) }}" alt="" data-ecosystem-logo style="position:absolute;height:22px;width:22px;object-fit:contain">
                                    </span>
                                @else
                                    <i class="bi bi-bank"></i>
                                @endif
                                {{ $link['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <h3 class="font-display fw-bold fs-6 text-pa-secondary mt-4 mb-2">{{ $setting('footer_stats_title', 'Statistik Kunjungan Website') }}</h3>
                <ul class="list-unstyled small text-white-50 d-grid gap-1 bg-white bg-opacity-10 p-2 rounded mb-0">
                    <li class="d-flex justify-content-between"><span>Hari ini:</span> <strong class="text-white">{{ number_format($visitStats['today'], 0, ',', '.') }}</strong></li>
                    <li class="d-flex justify-content-between"><span>Bulan ini:</span> <strong class="text-white">{{ number_format($visitStats['month'], 0, ',', '.') }}</strong></li>
                    <li class="d-flex justify-content-between"><span>Total:</span> <strong class="text-pa-secondary">{{ number_format($visitStats['total'], 0, ',', '.') }}</strong></li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="text-center small text-white-50">
            {!! $setting('footer_copyright', '&copy; '.date('Y').' '.$siteName.'. Standar Tata Kelola Website SK Dirjen Badilag No. 2722/DJA/SK.TI1/X/2026.') !!}
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
                <h5 class="modal-title font-display fw-bold" id="surveyModalLabel">Masukan Internal PTSP &amp; Website</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="small text-secondary">Formulir internal portal ini tidak menggantikan survei resmi Ditjen Badilag.</p>
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
<script nonce="{{ $cspNonce ?? '' }}">
    document.querySelectorAll('[data-ecosystem-logo]').forEach(function (image) {
        image.addEventListener('error', function () {
            image.style.display = 'none';
        });
    });
</script>
@stack('scripts')
</body>
</html>
