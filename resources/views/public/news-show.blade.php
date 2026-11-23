@extends('layouts.app')
@section('title', $article->title)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($article->excerpt ?: $article->content), 160))
@section('og_type', 'article')
@if($article->image)
    @section('og_image', $article->image_url)
@endif
@section('content')
<div class="container-lg py-5">
    <a href="{{ route('news.index') }}" class="btn btn-outline-success mb-4"><i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Berita</a>
    <div class="row g-4">
        <div class="col-lg-8">
            <article class="bg-white p-4 p-md-5 rounded-3 shadow-sm">
                <span class="badge {{ $article->badge_class }} mb-2">{{ $article->category }}</span>
                <h1 class="font-display h2 fw-bold text-pa-primary">{{ $article->title }}</h1>
                <p class="text-secondary small border-bottom pb-3 mb-4">
                    {{ $article->published_at->translatedFormat('d F Y') }}@if($article->author) | Penulis: {{ $article->author }}@endif
                    | <i class="bi bi-eye me-1" aria-hidden="true"></i>{{ number_format($article->view_count, 0, ',', '.') }} pengunjung
                </p>
                @if($article->image)
                    <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="img-fluid rounded mb-4 w-100" style="max-height: 420px; object-fit: cover;">
                @endif
                <div class="page-body">{!! $article->content !!}</div>
            </article>
        </div>
        <aside class="col-lg-4">
            <div class="bg-white p-4 rounded-3 shadow-sm">
                <h2 class="font-display h6 fw-bold text-pa-primary border-bottom pb-2">Berita Lainnya</h2>
                @forelse($related as $r)
                    <a href="{{ route('news.show', $r->slug) }}" class="d-block text-decoration-none py-2 border-bottom">
                        <span class="fw-semibold text-dark d-block">{{ $r->title }}</span>
                        <small class="text-secondary">{{ $r->published_at->translatedFormat('d F Y') }}</small>
                    </a>
                @empty
                    <p class="text-secondary small mb-0">Belum ada berita lain.</p>
                @endforelse
            </div>
        </aside>
    </div>
</div>
@endsection
