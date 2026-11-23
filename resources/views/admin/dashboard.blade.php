@extends('admin.layout')
@section('title', 'Dashboard')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">Dashboard</h1>
        <p class="text-secondary small mb-0">Ringkasan kunjungan dan konten portal.</p>
    </div>
    <span class="badge rounded-pill bg-white text-secondary border fw-normal">
        <i class="bi bi-clock me-1" aria-hidden="true"></i>
        Diperbarui {{ now()->format('d/m/Y H:i') }}
    </span>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-4">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="rounded-3 bg-success-subtle text-success p-3"><i class="bi bi-people fs-4" aria-hidden="true"></i></span>
                <div>
                    <div class="small text-secondary">Pengunjung Hari Ini</div>
                    <div class="h3 fw-bold mb-0">{{ number_format($visits['today'], 0, ',', '.') }}</div>
                    <div class="small text-secondary">Unik per sesi per hari</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-4">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="rounded-3 bg-primary-subtle text-primary p-3"><i class="bi bi-calendar2-month fs-4" aria-hidden="true"></i></span>
                <div>
                    <div class="small text-secondary">Kunjungan Bulan Ini</div>
                    <div class="h3 fw-bold mb-0">{{ number_format($visits['month'], 0, ',', '.') }}</div>
                    <div class="small text-secondary">Akumulasi harian</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-4">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="rounded-3 bg-warning-subtle text-warning p-3"><i class="bi bi-graph-up-arrow fs-4" aria-hidden="true"></i></span>
                <div>
                    <div class="small text-secondary">Total Kunjungan</div>
                    <div class="h3 fw-bold mb-0">{{ number_format($visits['total'], 0, ',', '.') }}</div>
                    <div class="small text-secondary">Sejak statistik dicatat</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-4">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="rounded-3 bg-warning-subtle text-warning p-3"><i class="bi bi-star fs-4" aria-hidden="true"></i></span>
                <div>
                    <div class="small text-secondary">Rata-rata Survei</div>
                    <div class="h3 fw-bold mb-0">{{ $surveyCount ? $surveyAvg.' / 4' : '-' }}</div>
                    <div class="small text-secondary">{{ number_format($surveyCount, 0, ',', '.') }} masukan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="card border-0 shadow-sm rounded-4 mb-4" aria-labelledby="visit-trend-heading">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h2 id="visit-trend-heading" class="h5 fw-bold mb-1">Tren Kunjungan</h2>
                <p class="small text-secondary mb-0">30 hari terakhir. Satu sesi dihitung sekali per hari.</p>
            </div>
            <span class="badge rounded-pill bg-success-subtle text-success fw-medium">
                <i class="bi bi-bar-chart-line me-1" aria-hidden="true"></i>Pengunjung harian
            </span>
        </div>

        <div class="dashboard-chart-scroll">
            <svg class="dashboard-chart-svg" viewBox="0 0 900 245" role="img" aria-labelledby="visit-chart-title visit-chart-description">
                <title id="visit-chart-title">Grafik pengunjung unik harian selama 30 hari terakhir</title>
                <desc id="visit-chart-description">Grafik batang menunjukkan jumlah pengunjung unik per hari.</desc>
                <g fill="none" stroke="#e9ecef" stroke-width="1">
                    <line x1="48" y1="55" x2="876" y2="55"></line>
                    <line x1="48" y1="105" x2="876" y2="105"></line>
                    <line x1="48" y1="155" x2="876" y2="155"></line>
                    <line x1="48" y1="205" x2="876" y2="205"></line>
                </g>
                <g fill="#6c757d" font-size="12">
                    <text x="5" y="59">{{ number_format($visitTrendMax, 0, ',', '.') }}</text>
                    <text x="5" y="109">{{ number_format((int) ceil($visitTrendMax / 2), 0, ',', '.') }}</text>
                    <text x="29" y="209">0</text>
                </g>
                @foreach($visitChart as $day)
                    <rect class="dashboard-visit-bar" x="{{ $day['x'] }}" y="{{ $day['y'] }}" width="17" height="{{ $day['height'] }}" rx="4" fill="#056e4f">
                        <title>{{ $day['date'] }}: {{ number_format($day['count'], 0, ',', '.') }} pengunjung</title>
                    </rect>
                    @if($day['show_label'])
                        <text x="{{ $day['x'] + 8 }}" y="233" text-anchor="middle" fill="#6c757d" font-size="11">{{ $day['label'] }}</text>
                    @endif
                @endforeach
            </svg>
        </div>
        <p class="small text-secondary mb-0 mt-2">Angka berasal dari pencatatan kunjungan per sesi per hari, bukan jumlah pengguna yang sedang online.</p>
    </div>
</section>

@if($resources->has('news') || $resources->has('announcements'))
    <div class="row g-3 mb-4">
        @if($resources->has('news'))
            <div class="col-lg-6">
                <section class="card h-100 border-0 shadow-sm rounded-4" aria-labelledby="popular-news-heading">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <h2 id="popular-news-heading" class="h6 fw-bold mb-1">Berita Paling Banyak Dikunjungi</h2>
                                <p class="small text-secondary mb-0">Konten yang sudah tayang.</p>
                            </div>
                            <a href="{{ route('admin.resource.index', 'news') }}" class="btn btn-sm btn-outline-success text-nowrap">Kelola</a>
                        </div>
                        @forelse($popularNews as $article)
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <span class="badge rounded-pill bg-light text-secondary border">{{ $loop->iteration }}</span>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold text-truncate">{{ $article->title }}</div>
                                    <div class="small text-secondary">{{ $article->category }}</div>
                                </div>
                                <span class="small fw-semibold text-nowrap">{{ number_format($article->view_count, 0, ',', '.') }} kunjungan</span>
                            </div>
                        @empty
                            <p class="text-secondary small mb-0 py-3">Belum ada berita yang tayang.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        @endif

        @if($resources->has('announcements'))
            <div class="col-lg-6">
                <section class="card h-100 border-0 shadow-sm rounded-4" aria-labelledby="popular-announcements-heading">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <h2 id="popular-announcements-heading" class="h6 fw-bold mb-1">Pengumuman Paling Sering Diklik</h2>
                                <p class="small text-secondary mb-0">Penghitung bertambah saat tautan dibuka.</p>
                            </div>
                            <a href="{{ route('admin.resource.index', 'announcements') }}" class="btn btn-sm btn-outline-success text-nowrap">Kelola</a>
                        </div>
                        @forelse($popularAnnouncements as $announcement)
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <span class="badge rounded-pill bg-light text-secondary border">{{ $loop->iteration }}</span>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold text-truncate">{{ $announcement->title }}</div>
                                    <div class="small text-secondary">{{ $announcement->badge }}</div>
                                </div>
                                <span class="small fw-semibold text-nowrap">{{ number_format($announcement->view_count, 0, ',', '.') }} klik</span>
                            </div>
                        @empty
                            <p class="text-secondary small mb-0 py-3">Belum ada pengumuman aktif.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        @endif
    </div>
@endif

<h2 class="h6 fw-bold text-secondary text-uppercase mb-3">Ringkasan Data</h2>
<div class="row g-3">
    @foreach($resources as $key => $definition)
        <div class="col-6 col-md-4 col-xl-3">
            <a href="{{ route('admin.resource.index', $key) }}" class="card border-0 shadow-sm text-decoration-none h-100 rounded-4">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi {{ $definition['icon'] }} fs-2 text-success" aria-hidden="true"></i>
                    <div>
                        <div class="h4 fw-bold mb-0 text-dark">{{ number_format($counts[$key], 0, ',', '.') }}</div>
                        <div class="small text-secondary">{{ $definition['label'] }}</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection
