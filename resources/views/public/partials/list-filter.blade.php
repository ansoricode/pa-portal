{{-- Filter daftar publik (GET). Opsi: $years (koleksi tahun), $showDate (bool), $paginator (untuk jumlah data) --}}
@php
    $years = $years ?? collect();
    $showDate = $showDate ?? false;
    $hasFilter = request()->filled('q') || request()->filled('tahun') || request()->filled('tanggal');
@endphp
<form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-end mb-3">
    <div class="col-md-5">
        <label class="form-label small fw-semibold mb-1" for="flt-q">Cari</label>
        <input type="search" id="flt-q" name="q" value="{{ request('q') }}" class="form-control form-control-sm" maxlength="100" placeholder="Kata kunci...">
    </div>
    @if($years->count() > 1)
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold mb-1" for="flt-tahun">Tahun</label>
            <select id="flt-tahun" name="tahun" class="form-select form-select-sm">
                <option value="">Semua</option>
                @foreach($years as $y)<option value="{{ $y }}" @selected((string) request('tahun') === (string) $y)>{{ $y }}</option>@endforeach
            </select>
        </div>
    @endif
    @if($showDate)
        <div class="col-6 col-md-3">
            <label class="form-label small fw-semibold mb-1" for="flt-tanggal">Tanggal sidang</label>
            <input type="date" id="flt-tanggal" name="tanggal" value="{{ request('tanggal') }}" class="form-control form-control-sm">
        </div>
    @endif
    <div class="col-auto d-flex gap-2">
        <button class="btn btn-pa-primary btn-sm">Terapkan</button>
        @if($hasFilter)<a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm">Reset</a>@endif
    </div>
</form>
@isset($paginator)
    <p class="small text-secondary mb-2">{{ number_format($paginator->total(), 0, ',', '.') }} data ditemukan</p>
@endisset
