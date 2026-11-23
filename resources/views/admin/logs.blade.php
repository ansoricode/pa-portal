@extends('admin.layout')
@section('title', 'Log Aktivitas')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 fw-bold mb-0"><i class="bi bi-journal-text me-2" style="color:#056e4f"></i>Log Aktivitas</h1>
    <form method="GET" class="d-flex flex-wrap gap-2">
        <select name="aksi" class="form-select form-select-sm" style="width:auto" data-autosubmit>
            <option value="">Semua aksi</option>
            @foreach(\App\Models\ActivityLog::ACTIONS as $key => [$label, $color])
                <option value="{{ $key }}" @selected($action === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="dari" value="{{ $from }}" class="form-control form-control-sm" style="width:auto" aria-label="Dari tanggal">
        <input type="date" name="sampai" value="{{ $to }}" class="form-control form-control-sm" style="width:auto" aria-label="Sampai tanggal">
        <input type="search" name="q" value="{{ $q }}" class="form-control form-control-sm" style="width:200px" placeholder="Cari pengguna, IP, data...">
        <select name="per_page" class="form-select form-select-sm" style="width:auto" data-autosubmit aria-label="Jumlah per halaman">
            @foreach([25, 50, 100] as $n)<option value="{{ $n }}" @selected((int) request('per_page', 25) === $n)>{{ $n }}/hal</option>@endforeach
        </select>
        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
    </form>
</div>
<div class="alert alert-info small py-2"><i class="bi bi-info-circle me-1"></i>Log bersifat baca-saja dan tidak dapat diubah dari panel. Hapus otomatis data lama dengan <code>php artisan portal:prune</code>.</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Keterangan</th><th>IP</th></tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                @php [$label, $color] = \App\Models\ActivityLog::ACTIONS[$log->action] ?? [$log->action, 'secondary']; @endphp
                <tr>
                    <td class="text-nowrap small">{{ $log->created_at->translatedFormat('d M Y H:i:s') }}</td>
                    <td>{{ $log->user_name ?? '-' }}</td>
                    <td><span class="badge bg-{{ $color }}">{{ $label }}</span></td>
                    <td class="small">
                        {{ $log->description }}
                        @if($log->subject_label)<div class="text-secondary">{{ $log->subject_type }}: {{ $log->subject_label }}</div>@endif
                        @if($log->properties)
                            <details><summary class="text-secondary" style="cursor:pointer">Rincian</summary>
                                <pre class="small bg-light border rounded p-2 mb-0" style="white-space:pre-wrap">{{ json_encode($log->properties, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) }}</pre>
                            </details>
                        @endif
                    </td>
                    <td class="small text-nowrap">{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada aktivitas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $logs->links() }}</div>
@endsection
