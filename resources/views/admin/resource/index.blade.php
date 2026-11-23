@extends('admin.layout')
@section('title', $def['label'])
@section('content')
@php
    $fieldMap = collect($def['fields'])->keyBy('name');
    $canCreate = ($def['create'] ?? true) !== false;
    $canEdit = ($def['edit'] ?? true) !== false;
    $filters = $def['filters'] ?? [];
    $sort = request('sort');
    $dir = request('dir') === 'desc' ? 'desc' : 'asc';
    $hasFilter = request()->filled('q') || collect($filters)->keys()->contains(fn ($k) => request()->filled('f_' . $k));
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 fw-bold mb-0"><i class="bi {{ $def['icon'] }} me-2" style="color:#056e4f"></i>{{ $def['label'] }}</h1>
    @if($canCreate)<a href="{{ route('admin.resource.create', $def['key']) }}" class="btn btn-sm btn-pa"><i class="bi bi-plus-lg me-1"></i>Tambah</a>@endif
</div>
@if(!empty($def['hint']))<div class="alert alert-info small py-2"><i class="bi bi-info-circle me-1"></i>{{ $def['hint'] }}</div>@endif

{{-- Pencarian, filter, jumlah per halaman --}}
<form method="GET" class="card card-body border-0 shadow-sm mb-3 py-2">
    <div class="row g-2 align-items-end">
        @if(!empty($def['search']))
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold mb-1" for="q">Cari</label>
                <input type="search" id="q" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Kata kunci..." maxlength="100">
            </div>
        @endif
        @foreach($filters as $col => $f)
            @php $opts = isset($f['options_config']) ? config($f['options_config']) : $f['options']; @endphp
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1" for="f_{{ $col }}">{{ $f['label'] }}</label>
                <select id="f_{{ $col }}" name="f_{{ $col }}" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($opts as $k => $label)
                        <option value="{{ $k }}" @selected((string) request('f_' . $col) === (string) $k)>{{ \Illuminate\Support\Str::limit($label, 32) }}</option>
                    @endforeach
                </select>
            </div>
        @endforeach
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold mb-1" for="per_page">Per halaman</label>
            <select id="per_page" name="per_page" class="form-select form-select-sm" data-autosubmit>
                @foreach([10, 15, 25, 50, 100] as $n)<option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>@endforeach
            </select>
        </div>
        @if($sort)<input type="hidden" name="sort" value="{{ $sort }}"><input type="hidden" name="dir" value="{{ $dir }}">@endif
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-sm btn-pa"><i class="bi bi-funnel me-1"></i>Terapkan</button>
            @if($hasFilter || $sort)<a href="{{ route('admin.resource.index', $def['key']) }}" class="btn btn-sm btn-outline-secondary">Reset</a>@endif
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    @foreach($def['columns'] as $col => $label)
                        @php
                            $sortable = ! str_contains($col, '.');
                            $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
                        @endphp
                        <th class="text-nowrap">
                            @if($sortable)
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $col, 'dir' => $nextDir, 'page' => null]) }}" class="text-decoration-none text-dark" title="Urutkan menurut {{ $label }}">
                                    {{ $label }}
                                    @if($sort === $col)<i class="bi bi-caret-{{ $dir === 'asc' ? 'up' : 'down' }}-fill small" style="color:#056e4f"></i>@else<i class="bi bi-arrow-down-up small text-black-50"></i>@endif
                                </a>
                            @else
                                {{ $label }}
                            @endif
                        </th>
                    @endforeach
                    <th style="width:140px" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    @foreach($def['columns'] as $col => $label)
                        @php
                            $type = $fieldMap[$col]['type'] ?? 'text';
                            $val = data_get($item, $col);
                        @endphp
                        <td>
                            @if($type === 'checkbox')
                                <span class="badge {{ $val ? 'bg-success' : 'bg-secondary' }}">{{ $val ? 'Ya' : 'Tidak' }}</span>
                            @elseif($type === 'image' && $val)
                                <img src="{{ asset('storage/'.$val) }}" alt="" style="height:36px" class="rounded border">
                            @elseif($val instanceof \Carbon\CarbonInterface)
                                {{ $val->translatedFormat($col === 'created_at' || $type === 'datetime' ? 'd M Y H:i' : 'd M Y') }}
                            @elseif($type === 'select' && isset($fieldMap[$col]['options_config']))
                                {{ config($fieldMap[$col]['options_config'])[$val] ?? $val }}
                            @elseif($type === 'select' && isset($fieldMap[$col]['options']))
                                {{ $fieldMap[$col]['options'][$val] ?? $val }}
                            @else
                                {{ \Illuminate\Support\Str::limit((string) $val, 70) }}
                            @endif
                        </td>
                    @endforeach
                    <td class="text-end text-nowrap">
                        @if($canEdit)<a href="{{ route('admin.resource.edit', [$def['key'], $item->id]) }}" class="btn btn-sm btn-outline-success" title="Ubah"><i class="bi bi-pencil"></i></a>@endif
                        <form method="POST" action="{{ route('admin.resource.destroy', [$def['key'], $item->id]) }}" class="d-inline" data-confirm="Hapus data ini? Tindakan tidak dapat dibatalkan.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ count($def['columns']) + 1 }}" class="text-center text-secondary py-4">{{ $hasFilter ? 'Tidak ada data yang cocok dengan pencarian/filter.' : 'Belum ada data.' }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">
    @if($items->hasPages())
        {{ $items->links() }}
    @else
        <small class="text-secondary">Total {{ $items->total() }} data</small>
    @endif
</div>
@endsection
