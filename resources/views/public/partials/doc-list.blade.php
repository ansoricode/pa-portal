@php $btnClass = $btnClass ?? 'btn-pa-primary'; $btnText = $btnText ?? 'Lihat Dokumen'; @endphp
@include('public.partials.list-filter', ['paginator' => $documents])

@if($documents->isEmpty())
    <p class="text-secondary fst-italic">Tidak ada dokumen yang cocok / belum dipublikasikan.</p>
@else
    <div class="list-group mb-3">
        @foreach($documents as $doc)
            <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                <span>
                    {{ $documents->firstItem() + $loop->index }}. {{ $doc->title }}
                    @if($doc->year)<span class="badge bg-light text-dark border ms-1">{{ $doc->year }}</span>@endif
                    @if($doc->period)<small class="d-block text-secondary">Periode: {{ $doc->period }}</small>
                    @elseif($doc->description)<small class="d-block text-secondary">{{ $doc->description }}</small>@endif
                </span>
                @if($doc->url)
                    <a href="{{ $doc->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm {{ $btnClass }} text-nowrap">{{ $btnText }}</a>
                @else
                    <span class="badge bg-secondary">Segera tersedia</span>
                @endif
            </div>
        @endforeach
    </div>
    {{ $documents->links() }}
@endif
