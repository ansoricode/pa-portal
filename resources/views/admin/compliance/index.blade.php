@extends('admin.layout')
@section('title', 'Monitoring Kepatuhan')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <h1 class="h4 fw-bold mb-1">Monitoring Kepatuhan Website</h1>
        <p class="text-secondary mb-0">Indikator berdasarkan Lampiran II SK Dirjen Badilag Nomor 2722/DJA/SK.TI1/X/2026.</p>
    </div>
</div>

<div class="alert alert-info">
    Status diperbarui manual setelah pengecekan. Tautan bawaan mengarah ke halaman terkait; sesuaikan kolom URL bila bukti berada di alamat lain.
    Status “Perlu perbaikan (5)” wajib disertai catatan. Tombol Salin menyalin URL lengkap sesuai domain yang sedang dibuka.
    Pemeriksaan eksternal hanya berjalan untuk host pada Pengaturan Situs &gt; Tautan Sistem dan tidak mengikuti redirect.
</div>

<div class="row g-2 mb-4">
    <div class="col-6 col-lg"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-secondary">Total indikator</div><div class="h4 fw-bold mb-0">{{ $summary['total'] }}</div></div></div></div>
    <div class="col-6 col-lg"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-secondary">Sudah diperiksa</div><div class="h4 fw-bold text-primary mb-0">{{ $summary['checked'] }}</div></div></div></div>
    <div class="col-6 col-lg"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-secondary">Sesuai</div><div class="h4 fw-bold text-success mb-0">{{ $summary['compliant'] }}</div></div></div></div>
    <div class="col-6 col-lg"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-secondary">Perlu perbaikan</div><div class="h4 fw-bold text-warning mb-0">{{ $summary['needs_improvement'] }}</div></div></div></div>
    <div class="col-6 col-lg"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-secondary">Belum sesuai</div><div class="h4 fw-bold text-danger mb-0">{{ $summary['noncompliant'] }}</div></div></div></div>
    <div class="col-6 col-lg"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-secondary">Belum diperiksa</div><div class="h4 fw-bold text-secondary mb-0">{{ $summary['unchecked'] }}</div></div></div></div>
</div>

@foreach($groups as $group)
    <section class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h2 class="h6 fw-bold mb-0">{{ $group['title'] }}</h2>
                <span class="badge text-bg-success">Bobot {{ $group['weight'] }}</span>
            </div>
            <div class="small text-secondary mt-1">{{ $group['description'] }}</div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width:1100px">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width:25%">Indikator</th>
                        <th scope="col" style="width:31%">URL bukti</th>
                        <th scope="col" style="width:15%">Status</th>
                        <th scope="col" style="width:23%">Catatan</th>
                        <th scope="col" style="width:6%">Simpan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['items'] as $code => $indicator)
                        @php
                            $check = $indicator['check'];
                            $statusField = 'status_' . $code;
                            $urlField = 'target_url_' . $code;
                            $notesField = 'notes_' . $code;
                            $urlId = 'check-url-' . $code;
                            $formId = 'check-form-' . $code;
                            $statusClass = match($check->status) {
                                'compliant' => 'text-bg-success',
                                'needs_improvement' => 'text-bg-warning',
                                'noncompliant' => 'text-bg-danger',
                                'not_applicable' => 'text-bg-secondary',
                                default => 'text-bg-light',
                            };
                            $linkStatusClass = match($check->link_status) {
                                'available' => 'text-bg-success',
                                'redirect' => 'text-bg-warning',
                                'broken' => 'text-bg-danger',
                                'unknown' => 'text-bg-secondary',
                                'blocked' => 'text-bg-dark',
                                default => 'text-bg-light',
                            };
                        @endphp
                        <tr id="check-{{ $code }}">
                            <td>
                                <div class="fw-semibold">{{ $indicator['label'] }}</div>
                                <span class="badge {{ $statusClass }} mt-1">{{ $statuses[$check->status] ?? $statuses['unchecked'] }}</span>
                                @if($check->checked_at)
                                    <div class="small text-secondary mt-1">Dicek {{ $check->checked_at->format('d/m/Y H:i') }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input id="{{ $urlId }}" type="text" class="form-control @error($urlField) is-invalid @enderror"
                                           name="{{ $urlField }}" value="{{ old($urlField, $check->target_url) }}" form="{{ $formId }}" required>
                                    <button type="button" class="btn btn-outline-secondary" data-copy-url="{{ $urlId }}" aria-label="Salin URL {{ $indicator['label'] }}">Salin</button>
                                    <a class="btn btn-outline-primary" href="{{ $indicator['absolute_url'] }}" target="_blank" rel="noopener noreferrer">Buka</a>
                                </div>
                                @error($urlField)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                    <form method="POST" action="{{ route('admin.compliance.check-link', $code) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">Cek tautan</button>
                                    </form>
                                    <span class="badge {{ $linkStatusClass }}">{{ $linkStatuses[$check->link_status] ?? $linkStatuses['unchecked'] }}</span>
                                    @if($check->link_http_status)<span class="small text-secondary">HTTP {{ $check->link_http_status }}</span>@endif
                                </div>
                                @if($check->link_message)
                                    <div class="small text-secondary mt-1">{{ $check->link_message }}</div>
                                @endif
                                @if($check->link_checked_at)
                                    <div class="small text-secondary">Cek terakhir {{ $check->link_checked_at->format('d/m/Y H:i') }}</div>
                                @endif
                            </td>
                            <td>
                                <select class="form-select form-select-sm @error($statusField) is-invalid @enderror" name="{{ $statusField }}" form="{{ $formId }}">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" @selected(old($statusField, $check->status) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error($statusField)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </td>
                            <td>
                                <textarea class="form-control form-control-sm @error($notesField) is-invalid @enderror" name="{{ $notesField }}" rows="2" maxlength="2000"
                                          form="{{ $formId }}" placeholder="Catatan pengecekan/perbaikan">{{ old($notesField, $check->notes) }}</textarea>
                                @error($notesField)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </td>
                            <td>
                                <form id="{{ $formId }}" method="POST" action="{{ route('admin.compliance.update', $code) }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-pa" aria-label="Simpan {{ $indicator['label'] }}">
                                        <i class="bi bi-save"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endforeach
@endsection
