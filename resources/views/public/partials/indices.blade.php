<div class="row g-4 row-cols-1 row-cols-md-3 mb-4">
    @forelse($indices as $idx)
        <div class="col">
            <div class="bg-white bg-opacity-10 p-4 rounded-3 text-center border-top border-4 border-pa-secondary h-100">
                <i class="bi {{ $idx->icon }} display-4 text-pa-secondary"></i>
                <h3 class="font-display fw-bold fs-6 mt-3">{{ $idx->name }}</h3>
                <p class="display-5 fw-bold my-2">{{ $idx->score }}</p>
                @if($idx->label)<span class="badge bg-pa-secondary text-dark">{{ $idx->label }}</span>@endif
            </div>
        </div>
    @empty
        <div class="col-12 text-center text-white-50">Nilai indeks survei belum dipublikasikan.</div>
    @endforelse
</div>
