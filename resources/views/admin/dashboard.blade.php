@extends('admin.layout')
@section('title', 'Dashboard')
@section('content')
<h1 class="h4 fw-bold mb-4">Dashboard</h1>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-secondary small">Pengunjung Hari Ini</div><div class="h3 fw-bold mb-0" style="color:#056e4f">{{ number_format($visits['today'], 0, ',', '.') }}</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-secondary small">Bulan Ini</div><div class="h3 fw-bold mb-0" style="color:#056e4f">{{ number_format($visits['month'], 0, ',', '.') }}</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-secondary small">Total Kunjungan</div><div class="h3 fw-bold mb-0" style="color:#056e4f">{{ number_format($visits['total'], 0, ',', '.') }}</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-secondary small">Rata-rata Survei ({{ $surveyCount }} masukan)</div><div class="h3 fw-bold mb-0" style="color:#ff9900">{{ $surveyCount ? $surveyAvg . ' / 4' : '-' }}</div></div></div></div>
</div>

<h2 class="h6 fw-bold text-secondary text-uppercase mb-3">Ringkasan Data</h2>
<div class="row g-3">
    @foreach($resources as $key => $d)
        <div class="col-6 col-md-4 col-xl-3">
            <a href="{{ route('admin.resource.index', $key) }}" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi {{ $d['icon'] }} fs-2" style="color:#056e4f"></i>
                    <div>
                        <div class="h4 fw-bold mb-0 text-dark">{{ $counts[$key] }}</div>
                        <div class="small text-secondary">{{ $d['label'] }}</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection
