@extends('layouts.app')
@section('title', 'Berita & Publikasi')
@section('content')
<div class="container-lg py-5">
    <h1 class="font-display h2 fw-bold text-pa-primary mb-2">Berita, Inovasi &amp; Gagasan Aparatur</h1>
    <p class="text-secondary mb-4">Publikasi informasi pelayanan masyarakat, inovasi satuan kerja, serta opini aparatur mengenai reformasi birokrasi peradilan.</p>

    <form method="GET" action="{{ route('news.index') }}" class="d-flex gap-2 mb-3" style="max-width: 520px;">
        @if($category)<input type="hidden" name="kategori" value="{{ $category }}">@endif
        <input type="search" name="q" value="{{ $term }}" class="form-control form-control-sm" maxlength="100" placeholder="Cari berita..." aria-label="Cari berita">
        <button class="btn btn-pa-secondary btn-sm">Cari</button>
        @if($term)<a href="{{ route('news.index', array_filter(['kategori' => $category])) }}" class="btn btn-outline-secondary btn-sm">Reset</a>@endif
    </form>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('news.index') }}" class="btn btn-sm {{ $category ? 'btn-outline-success' : 'btn-pa-primary' }}">Semua</a>
        @foreach(config('portal.news_categories') as $c)
            <a href="{{ route('news.index', ['kategori' => $c]) }}" class="btn btn-sm {{ $category === $c ? 'btn-pa-primary' : 'btn-outline-success' }}">{{ $c }}</a>
        @endforeach
    </div>

    <div class="row g-4 row-cols-1 row-cols-md-2">
        @forelse($news as $n)
            <div class="col">@include('public.partials.news-card', ['n' => $n, 'small' => false])</div>
        @empty
            <div class="col-12"><p class="text-secondary">Tidak ada berita yang cocok.</p></div>
        @endforelse
    </div>

    <div class="news-pagination mt-4">{{ $news->links() }}</div>
</div>
@endsection
