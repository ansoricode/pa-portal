@extends('layouts.app')
@section('title', 'Pencarian')
@section('content')
<div class="container-lg py-5">
    <h1 class="font-display h2 fw-bold text-pa-primary mb-3">Pencarian Informasi</h1>
    <form action="{{ route('search') }}" method="GET" class="d-flex gap-2 mb-4" style="max-width: 640px;">
        <input type="search" name="q" value="{{ $q }}" class="form-control" minlength="2" maxlength="100" placeholder="Kata kunci..." required>
        <button class="btn btn-pa-secondary">Cari</button>
    </form>

    @if(mb_strlen($q) < 2)
        <p class="text-secondary">Masukkan minimal 2 karakter.</p>
    @elseif($pages->total() + $news->total() + $documents->total() === 0)
        <div class="alert alert-secondary">Tidak ditemukan hasil untuk <strong>{{ $q }}</strong>.</div>
    @else
        <div class="bg-white p-4 rounded-3 shadow-sm">
            @if($pages->total() > 0)
                <h2 class="h6 fw-bold text-pa-primary text-uppercase">Halaman ({{ $pages->total() }})</h2>
                <div class="list-group mb-3">
                    @foreach($pages as $p)
                        <a href="{{ route('page.show', [$p->section, $p->slug]) }}" class="list-group-item list-group-item-action fw-semibold text-pa-primary">
                            {{ $p->title }} <small class="text-secondary fw-normal">— {{ config('portal.sections')[$p->section]['title'] ?? '' }}</small>
                        </a>
                    @endforeach
                </div>
                <div class="mb-4">{{ $pages->links() }}</div>
            @endif

            @if($documents->total() > 0)
                <h2 class="h6 fw-bold text-pa-primary text-uppercase">Dokumen ({{ $documents->total() }})</h2>
                <div class="list-group mb-3">
                    @foreach($documents as $d)
                        @php $target = config('portal.category_page')[$d->category] ?? null; @endphp
                        <a href="{{ $target ? route('page.show', $target) : '#' }}" class="list-group-item list-group-item-action">
                            <span class="fw-semibold text-pa-primary">{{ $d->title }}</span>
                            <small class="text-secondary">— {{ config('portal.document_categories')[$d->category] ?? $d->category }}@if($d->year), {{ $d->year }}@endif</small>
                        </a>
                    @endforeach
                </div>
                <div class="mb-4">{{ $documents->links() }}</div>
            @endif

            @if($news->total() > 0)
                <h2 class="h6 fw-bold text-pa-primary text-uppercase">Berita ({{ $news->total() }})</h2>
                <div class="list-group mb-3">
                    @foreach($news as $n)
                        <a href="{{ route('news.show', $n->slug) }}" class="list-group-item list-group-item-action">
                            <span class="fw-semibold text-pa-primary">{{ $n->title }}</span>
                            <small class="d-block text-secondary">{{ $n->published_at->translatedFormat('d F Y') }}</small>
                        </a>
                    @endforeach
                </div>
                {{ $news->links() }}
            @endif
        </div>
    @endif
</div>
@endsection
