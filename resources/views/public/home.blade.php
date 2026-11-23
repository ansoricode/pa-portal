@extends('layouts.app')
@section('title', 'Beranda')
@section('content')

{{-- Hero slider (maks. 3-5 slide) --}}
@if($sliders->isNotEmpty())
<section id="hero-carousel" class="carousel slide" data-bs-ride="carousel" aria-label="Sorotan layanan">
    @if($sliders->count() > 1)
    <div class="carousel-indicators">
        @foreach($sliders as $i => $s)
            <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}" aria-label="Slide {{ $i + 1 }}"></button>
        @endforeach
    </div>
    @endif
    <div class="carousel-inner">
        @foreach($sliders as $i => $s)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}" style="background-image: url('{{ $s->image_url }}');">
                <div class="container-lg py-5 d-flex flex-column justify-content-center text-white hero-content" style="min-height: 55vh;">
                    @if($s->badge)<span class="badge bg-pa-secondary text-dark fw-bold align-self-start mb-3 px-3 py-2">{{ $s->badge }}</span>@endif
                    <h2 class="font-display display-5 fw-bold">{{ $s->title }}</h2>
                    @if($s->subtitle)<p class="mt-3 fs-5 text-white-50" style="max-width: 42rem;">{{ $s->subtitle }}</p>@endif
                    @if($s->link && $s->button_text)
                        <div class="mt-4">
                            <a href="{{ $s->link }}" class="btn btn-pa-secondary btn-lg px-4">{{ $s->button_text }} <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @if($sliders->count() > 1)
    <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Slide Sebelumnya</span></button>
    <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Slide Berikutnya</span></button>
    @endif
</section>
@endif

{{-- Pintasan layanan --}}
@if($shortcuts->isNotEmpty())
<section class="py-5 bg-white">
    <div class="container-lg">
        <div class="text-center mb-5">
            <h2 class="font-display h2 fw-bold text-pa-primary">Pintasan Layanan Utama</h2>
            <p class="text-secondary mx-auto" style="max-width: 40rem;">Akses langsung ke sistem informasi perkara, produk pengadilan, dan saluran pengaduan masyarakat.</p>
        </div>
        <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-4">
            @foreach($shortcuts as $sc)
                <div class="col">
                    <a href="{{ $sc->link }}" @if($sc->is_external) target="_blank" rel="noopener noreferrer" @endif class="d-block bg-light p-4 rounded-3 text-center shadow-sm text-decoration-none h-100 card-shortcut">
                        <i class="bi {{ $sc->icon }} display-4"></i>
                        <h3 class="font-display fw-bold fs-5 mt-3 text-pa-primary">{{ $sc->title }}</h3>
                        @if($sc->description)<p class="text-secondary small mt-2 mb-0">{{ $sc->description }}</p>@endif
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Pengumuman & berita --}}
<section class="py-5">
    <div class="container-lg">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="bg-white p-4 rounded-3 shadow-sm h-100 border-top border-4 border-pa-primary">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h3 class="font-display fw-bold h5 text-pa-primary mb-0">Pengumuman &amp; Panggilan Ghaib</h3>
                        <a href="{{ route('announcements.index') }}" class="small text-pa-primary fw-semibold text-decoration-none">Semua &rarr;</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($announcements as $a)
                            <a href="{{ $a->link }}" class="list-group-item list-group-item-action px-0 py-3">
                                <span class="badge bg-pa-secondary text-dark mb-1">{{ $a->badge }}</span>
                                <p class="fw-semibold mb-1 text-dark">{{ $a->title }}</p>
                                <small class="text-secondary"><i class="bi bi-calendar3 me-1"></i>{{ $a->label ?: $a->published_at?->translatedFormat('d F Y') }}</small>
                            </a>
                        @empty
                            <p class="text-secondary small mb-0">Belum ada pengumuman.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="bg-white p-4 rounded-3 shadow-sm h-100 border-top border-4 border-pa-secondary">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h3 class="font-display fw-bold h5 text-pa-primary mb-0">Berita Pelayanan &amp; Inovasi Satker</h3>
                        <a href="{{ route('news.index') }}" class="small text-pa-primary fw-semibold text-decoration-none">Indeks Berita &rarr;</a>
                    </div>
                    <div class="row g-3">
                        @forelse($latestNews as $n)
                            <div class="col-md-6">
                                @include('public.partials.news-card', ['n' => $n, 'small' => true])
                            </div>
                        @empty
                            <p class="text-secondary small mb-0">Belum ada berita.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Indeks survei --}}
<section class="py-5 bg-pa-primary text-white">
    <div class="container-lg">
        <div class="text-center mb-5">
            <h2 class="font-display h2 fw-bold">Indeks Kepuasan &amp; Integritas Pelayanan</h2>
            <p class="text-white-50 mx-auto" style="max-width: 40rem;">Transparansi hasil survei pelayanan publik sebagai komitmen peningkatan kualitas layanan peradilan.</p>
        </div>
        @include('public.partials.indices', ['indices' => $indices])
        <div class="pt-3">
            <div class="survey-official-card mx-auto mb-4 rounded-4 p-3 p-md-4 text-start">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <span class="survey-official-icon" aria-hidden="true">
                            <i class="bi bi-patch-check-fill fs-4"></i>
                        </span>
                        <div>
                            <span class="badge rounded-pill bg-success-subtle text-success-emphasis">Survei Resmi Ditjen Badilag &bull; SURVELAG</span>
                            <h3 class="h5 fw-bold text-dark mt-2 mb-1">Survei Kepuasan Layanan</h3>
                            <p class="small text-secondary mb-0">Sampaikan penilaian layanan PA Kodya Kediri melalui sistem survei resmi Badilag.</p>
                        </div>
                    </div>
                    <a href="https://survei.badilag.net/s/c948693eed67bdcfec6ca939896d87b1"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="Isi survei resmi Badilag, membuka tab baru"
                       class="btn btn-pa-primary text-nowrap align-self-start align-self-md-center">
                        Isi Survei Badilag <i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="text-center">
            <button type="button" class="btn btn-pa-secondary btn-lg px-4" data-bs-toggle="modal" data-bs-target="#survey-modal">
                <i class="bi bi-chat-square-text me-2"></i>Beri Masukan untuk Pelayanan Kami
            </button>
            <p class="small text-white-50 mt-2 mb-0">Formulir internal portal untuk penilaian layanan PTSP dan website.</p>
        </div>
    </div>
</section>
@endsection
