@php $small = $small ?? false; @endphp
<div class="card h-100 {{ $small ? 'border' : 'shadow-sm' }}">
    <img src="{{ $n->image_url }}" class="card-img-top" alt="{{ $n->title }}" loading="lazy" style="aspect-ratio:5/3;object-fit:cover">
    <div class="card-body d-flex flex-column">
        <span class="badge {{ $n->badge_class }} mb-2 align-self-start">{{ $n->category }}</span>
        <h4 class="font-display fw-bold {{ $small ? 'h6' : 'h5' }} text-pa-primary">{{ $n->title }}</h4>
        <p class="text-secondary small mb-2">{{ $n->published_at->translatedFormat('d F Y') }}@if($n->author) | {{ $n->author }}@endif</p>
        @if($small && $n->excerpt)<p class="text-secondary small line-clamp-3">{{ $n->excerpt }}</p>@endif
        <a href="{{ route('news.show', $n->slug) }}" class="{{ $small ? 'small fw-bold text-pa-primary text-decoration-none' : 'btn btn-sm btn-pa-secondary align-self-start' }} mt-auto">
            {{ $small ? 'Baca Selengkapnya →' : 'Baca Artikel' }}
        </a>
    </div>
</div>
