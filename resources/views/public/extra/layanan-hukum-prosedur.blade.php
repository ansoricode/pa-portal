@include('public.partials.list-filter', ['paginator' => $documents])
@if($documents->isEmpty())
    <p class="text-secondary fst-italic">Tidak ada prosedur yang cocok / belum dipublikasikan.</p>
@else
    <div class="row g-3 mb-3">
        @foreach($documents as $d)
            <div class="col-md-6">
                <div class="p-3 border rounded bg-light h-100 d-flex flex-column">
                    <span class="fw-semibold">{{ $documents->firstItem() + $loop->index }}. {{ $d->title }}</span>
                    @if($d->description)<small class="text-secondary mt-1">{{ $d->description }}</small>@endif
                    @if($d->url)<a href="{{ $d->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-pa-primary align-self-start mt-2">Lihat Panduan</a>@endif
                </div>
            </div>
        @endforeach
    </div>
    {{ $documents->links() }}
@endif
