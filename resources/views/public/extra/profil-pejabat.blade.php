@php
    $labels = config('portal.official_categories');
    $grouped = $officials->getCollection()->groupBy('category');
@endphp
@include('public.partials.list-filter', ['paginator' => $officials])
@forelse($grouped as $category => $group)
    <h3 class="h5 fw-bold text-dark mt-4 mb-3">{{ $labels[$category] ?? ucfirst($category) }}</h3>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($group as $o)
            <div class="col">
                <div class="card h-100 text-center p-3 border-top border-3 border-pa-primary">
                    <img src="{{ $o->photo_url }}" alt="Foto {{ $o->name }}" class="rounded-circle mx-auto mb-3" style="width: 110px; height: 110px; object-fit: cover;" loading="lazy">
                    <h4 class="h6 fw-bold text-pa-primary mb-1">{{ $o->name }}</h4>
                    <p class="small text-pa-secondary fw-semibold mb-2">{{ $o->position }}</p>
                    @if($o->rank)<p class="small text-secondary mb-2">{{ $o->rank }}</p>@endif
                    @if($o->tmt)<p class="small text-secondary mb-2">TMT Jabatan: {{ $o->tmt->translatedFormat('d F Y') }}</p>@endif

                    @if($o->education || $o->career)
                        <details class="small text-start mb-2">
                            <summary class="text-pa-primary fw-semibold" style="cursor:pointer">Riwayat</summary>
                            @if($o->education)<p class="mb-1 mt-2"><strong>Pendidikan:</strong><br>{!! nl2br(e($o->education)) !!}</p>@endif
                            @if($o->career)<p class="mb-0"><strong>Jabatan:</strong><br>{!! nl2br(e($o->career)) !!}</p>@endif
                        </details>
                    @endif

                    @if($o->is_lhkpn)
                        <a href="{{ route('page.show', ['transparansi', 'lhkpn']) }}" class="btn btn-sm btn-outline-success mt-auto">Ikhtisar LHKPN</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@empty
    <p class="text-secondary fst-italic">Data pejabat dan pegawai tidak ditemukan.</p>
@endforelse
<div class="mt-4">{{ $officials->links() }}</div>
<p class="text-secondary small mt-4 mb-0">Data profil aparatur terintegrasi dengan <strong>SIMTEPA Badilag</strong>.</p>
