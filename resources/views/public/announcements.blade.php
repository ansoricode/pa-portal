@extends('layouts.app')
@section('title', 'Pengumuman')
@section('content')
<div class="container-lg py-5">
    <h1 class="font-display h2 fw-bold text-pa-primary mb-2">Pengumuman &amp; Panggilan Ghaib</h1>
    <p class="text-secondary mb-4">Arsip pengumuman resmi satuan kerja.</p>

    <div class="bg-white p-4 rounded-3 shadow-sm">
        @include('public.partials.list-filter', ['paginator' => $announcements])

        <div class="list-group list-group-flush mb-3">
            @forelse($announcements as $a)
                <a href="{{ $a->link }}" class="list-group-item list-group-item-action px-0 py-3">
                    <span class="badge bg-pa-secondary text-dark mb-1">{{ $a->badge }}</span>
                    <p class="fw-semibold mb-1 text-dark">{{ $a->title }}</p>
                    <small class="text-secondary"><i class="bi bi-calendar3 me-1"></i>{{ $a->label ?: $a->published_at?->translatedFormat('d F Y') }}</small>
                </a>
            @empty
                <p class="text-secondary mb-0">Tidak ada pengumuman yang cocok.</p>
            @endforelse
        </div>
        {{ $announcements->links() }}
    </div>
</div>
@endsection
