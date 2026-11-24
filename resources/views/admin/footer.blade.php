@extends('admin.layout')
@section('title', 'Pengaturan Footer')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-bold mb-0">Pengaturan Footer</h1>
    <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-eye me-1"></i>Preview Website</a>
</div>

<form method="POST" action="{{ route('admin.footer.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-bold" style="color:#056e4f">Informasi Umum Footer</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="footer_hours">Jam Operasional</label>
                    <textarea id="footer_hours" name="footer_hours" rows="3" class="form-control">{{ old('footer_hours', $footerHours) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="footer_copyright">Hak Cipta / Teks Bawah Footer</label>
                    <textarea id="footer_copyright" name="footer_copyright" rows="3" class="form-control">{{ old('footer_copyright', $footerCopyright ?: '&copy; '.date('Y').' '.$setting('site_name', 'Portal Satker').'. Semua hak dilindungi.') }}</textarea>
                </div>
                <div class="col-md-4"><label class="form-label fw-semibold">Facebook</label><input type="url" name="facebook_url" value="{{ old('facebook_url', $setting('facebook_url')) }}" class="form-control"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Instagram</label><input type="url" name="instagram_url" value="{{ old('instagram_url', $setting('instagram_url')) }}" class="form-control"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">YouTube</label><input type="url" name="youtube_url" value="{{ old('youtube_url', $setting('youtube_url')) }}" class="form-control"></div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong style="color:#056e4f">Tautan Footer</strong>
            <button type="button" class="btn btn-sm btn-outline-success" data-add-row="footer-links"><i class="bi bi-plus-lg me-1"></i>Tambah Tautan</button>
        </div>
        <div class="card-body" id="footer-links">
            @foreach(old('footer_links', $footerLinks) as $index => $link)
                <div class="border rounded p-3 mb-3 footer-link-row">
                    <input type="hidden" name="footer_links[{{ $index }}][icon_existing]" value="{{ $link['icon'] ?? '' }}">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3"><label class="form-label">Nama</label><input type="text" name="footer_links[{{ $index }}][name]" value="{{ $link['name'] ?? '' }}" class="form-control" required></div>
                        <div class="col-md-3"><label class="form-label">URL</label><input type="url" name="footer_links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}" class="form-control" required></div>
                        <div class="col-md-3"><label class="form-label">Keterangan</label><input type="text" name="footer_links[{{ $index }}][description]" value="{{ $link['description'] ?? '' }}" class="form-control"></div>
                        <div class="col-md-1"><label class="form-label">Urutan</label><input type="number" name="footer_links[{{ $index }}][sort_order]" value="{{ $link['sort_order'] ?? 0 }}" min="0" class="form-control"></div>
                        <div class="col-md-2 d-flex gap-2"><div class="form-check mb-2"><input type="checkbox" name="footer_links[{{ $index }}][is_active]" value="1" id="footer-active-{{ $index }}" class="form-check-input" @checked($link['is_active'] ?? true)><label class="form-check-label" for="footer-active-{{ $index }}">Aktif</label></div><button type="button" class="btn btn-sm btn-outline-danger ms-auto mb-2" data-remove-row><i class="bi bi-trash"></i></button></div>
                        <div class="col-md-5"><label class="form-label">Ikon Bootstrap atau file</label><input type="text" name="footer_links[{{ $index }}][icon]" value="{{ str_starts_with($link['icon'] ?? '', 'uploads/footer/') ? '' : ($link['icon'] ?? '') }}" class="form-control" placeholder="Contoh: bi-link-45deg"></div>
                        <div class="col-md-5"><label class="form-label">Unggah ikon</label><input type="file" name="footer_links[{{ $index }}][icon_file]" accept="image/png,image/jpeg,image/webp" class="form-control"></div>
                        @if(! empty($link['icon']) && str_starts_with($link['icon'], 'uploads/footer/'))<div class="col-md-2"><img src="{{ asset('storage/'.$link['icon']) }}" alt="" style="height:40px" class="border rounded p-1"></div>@endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong style="color:#056e4f">Ekosistem Peradilan</strong>
            <button type="button" class="btn btn-sm btn-outline-success" data-add-row="ecosystem-links"><i class="bi bi-plus-lg me-1"></i>Tambah Ekosistem</button>
        </div>
        <div class="card-body" id="ecosystem-links">
            @foreach(old('ecosystem_links', $ecosystemLinks) as $index => $link)
                <div class="border rounded p-3 mb-3 ecosystem-link-row">
                    <input type="hidden" name="ecosystem_links[{{ $index }}][logo_existing]" value="{{ $link['logo'] ?? '' }}">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4"><label class="form-label">Nama</label><input type="text" name="ecosystem_links[{{ $index }}][name]" value="{{ $link['name'] ?? '' }}" class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label">URL</label><input type="url" name="ecosystem_links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}" class="form-control" required></div>
                        <div class="col-md-1"><label class="form-label">Urutan</label><input type="number" name="ecosystem_links[{{ $index }}][sort_order]" value="{{ $link['sort_order'] ?? 0 }}" min="0" class="form-control"></div>
                        <div class="col-md-3 d-flex gap-2"><div class="form-check mb-2"><input type="checkbox" name="ecosystem_links[{{ $index }}][is_active]" value="1" id="ecosystem-active-{{ $index }}" class="form-check-input" @checked($link['is_active'] ?? true)><label class="form-check-label" for="ecosystem-active-{{ $index }}">Aktif</label></div><button type="button" class="btn btn-sm btn-outline-danger ms-auto mb-2" data-remove-row><i class="bi bi-trash"></i></button></div>
                        <div class="col-md-10"><label class="form-label">Logo</label><input type="file" name="ecosystem_links[{{ $index }}][logo_file]" accept="image/png,image/jpeg,image/webp" class="form-control"></div>
                        @if(! empty($link['logo']))<div class="col-md-2"><img src="{{ asset('storage/'.$link['logo']) }}" alt="" style="height:45px" class="border rounded p-1"></div>@endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <button class="btn btn-pa px-4">Simpan Pengaturan Footer</button>
</form>

<template id="footer-link-template">
    <div class="border rounded p-3 mb-3 footer-link-row">
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label">Nama</label><input type="text" name="footer_links[__INDEX__][name]" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">URL</label><input type="url" name="footer_links[__INDEX__][url]" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Keterangan</label><input type="text" name="footer_links[__INDEX__][description]" class="form-control"></div>
            <div class="col-md-1"><label class="form-label">Urutan</label><input type="number" name="footer_links[__INDEX__][sort_order]" value="0" min="0" class="form-control"></div>
            <div class="col-md-2 d-flex"><div class="form-check mb-2"><input type="checkbox" name="footer_links[__INDEX__][is_active]" value="1" class="form-check-input" checked><label class="form-check-label ms-1">Aktif</label></div><button type="button" class="btn btn-sm btn-outline-danger ms-auto mb-2" data-remove-row><i class="bi bi-trash"></i></button></div>
            <div class="col-md-5"><label class="form-label">Ikon Bootstrap</label><input type="text" name="footer_links[__INDEX__][icon]" class="form-control" placeholder="bi-link-45deg"></div>
            <div class="col-md-5"><label class="form-label">Unggah ikon</label><input type="file" name="footer_links[__INDEX__][icon_file]" accept="image/png,image/jpeg,image/webp" class="form-control"></div>
        </div>
    </div>
</template>
<template id="ecosystem-link-template">
    <div class="border rounded p-3 mb-3 ecosystem-link-row">
        <div class="row g-2 align-items-end">
            <div class="col-md-4"><label class="form-label">Nama</label><input type="text" name="ecosystem_links[__INDEX__][name]" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">URL</label><input type="url" name="ecosystem_links[__INDEX__][url]" class="form-control" required></div>
            <div class="col-md-1"><label class="form-label">Urutan</label><input type="number" name="ecosystem_links[__INDEX__][sort_order]" value="0" min="0" class="form-control"></div>
            <div class="col-md-3 d-flex"><div class="form-check mb-2"><input type="checkbox" name="ecosystem_links[__INDEX__][is_active]" value="1" class="form-check-input" checked><label class="form-check-label ms-1">Aktif</label></div><button type="button" class="btn btn-sm btn-outline-danger ms-auto mb-2" data-remove-row><i class="bi bi-trash"></i></button></div>
            <div class="col-md-12"><label class="form-label">Logo</label><input type="file" name="ecosystem_links[__INDEX__][logo_file]" accept="image/png,image/jpeg,image/webp" class="form-control"></div>
        </div>
    </div>
</template>
@endsection
@push('scripts')
<script>
document.querySelectorAll('[data-add-row]').forEach(function(button) {
    button.addEventListener('click', function() {
        var kind = button.dataset.addRow;
        var container = document.getElementById(kind);
        var template = document.getElementById(kind === 'footer-links' ? 'footer-link-template' : 'ecosystem-link-template');
        var index = container.querySelectorAll(kind === 'footer-links' ? '.footer-link-row' : '.ecosystem-link-row').length;
        container.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', index));
    });
});
document.addEventListener('click', function(event) {
    var button = event.target.closest('[data-remove-row]');
    if (button) button.closest('.border.rounded').remove();
});
</script>
@endpush
