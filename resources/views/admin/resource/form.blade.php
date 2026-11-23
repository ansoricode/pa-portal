@extends('admin.layout')
@section('title', ($item ? 'Ubah ' : 'Tambah ') . $def['label'])
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 fw-bold mb-0">{{ $item ? 'Ubah' : 'Tambah' }} {{ $def['label'] }}</h1>
    <a href="{{ route('admin.resource.index', $def['key']) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

@if($errors->any() && ! $errors->has('db'))<div class="alert alert-danger">Terdapat isian yang perlu diperbaiki.</div>@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data"
              action="{{ $item ? route('admin.resource.update', [$def['key'], $item->id]) : route('admin.resource.store', $def['key']) }}">
            @csrf
            @if($item) @method('PUT') @endif

            @foreach($def['fields'] as $f)
                @php
                    $name = $f['name'];
                    $type = $f['type'];
                    $raw = $item ? $item->$name : ($f['default'] ?? null);
                    $value = old($name, $raw);
                    if (in_array($type, ['date', 'datetime']) && $value && ! is_string($value)) {
                        $value = $value->format($type === 'date' ? 'Y-m-d' : 'Y-m-d\TH:i');
                    } elseif (in_array($type, ['date', 'datetime']) && is_string($value) && $value !== '') {
                        $value = \Illuminate\Support\Carbon::parse($value)->format($type === 'date' ? 'Y-m-d' : 'Y-m-d\TH:i');
                    }
                    $invalid = $errors->has($name) ? 'is-invalid' : '';
                @endphp
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="f_{{ $name }}">{{ $f['label'] }}@if(str_contains($f['rules'] ?? '', 'required')) <span class="text-danger">*</span>@endif</label>

                    @if($type === 'text')
                        @if($def['key'] === 'announcements' && $name === 'url')
                            <label class="form-label small text-secondary mb-1" for="f_linked_news">Pilih berita yang sudah tayang (opsional)</label>
                            @if($newsOptions->isNotEmpty())
                                <select id="f_linked_news" class="form-select mb-2" data-news-url-picker="f_url">
                                    <option value="">Pilih berita untuk mengisi URL...</option>
                                    @foreach($newsOptions as $newsOption)
                                        @php $newsUrl = route('news.show', $newsOption->slug, false); @endphp
                                        <option value="{{ $newsUrl }}" @selected((string) $value === $newsUrl)>{{ $newsOption->title }}</option>
                                    @endforeach
                                </select>
                            @else
                                <div class="form-text mb-2">Belum ada berita tayang yang bisa dipilih.</div>
                            @endif
                            <div class="form-text mb-2">Memilih berita akan mengisi tautan di bawah. URL manual tetap bisa digunakan.</div>
                        @endif
                        <input type="text" id="f_{{ $name }}" name="{{ $name }}" value="{{ $value }}" class="form-control {{ $invalid }}">
                    @elseif($type === 'number')
                        <input type="number" id="f_{{ $name }}" name="{{ $name }}" value="{{ $value }}" class="form-control {{ $invalid }}" style="max-width:200px">
                    @elseif($type === 'date')
                        <input type="date" id="f_{{ $name }}" name="{{ $name }}" value="{{ $value }}" class="form-control {{ $invalid }}" style="max-width:220px">
                    @elseif($type === 'datetime')
                        <input type="datetime-local" id="f_{{ $name }}" name="{{ $name }}" value="{{ $value }}" class="form-control {{ $invalid }}" style="max-width:260px">
                    @elseif($type === 'textarea')
                        <textarea id="f_{{ $name }}" name="{{ $name }}" rows="3" class="form-control {{ $invalid }}">{{ $value }}</textarea>
                    @elseif($type === 'editor')
                        <div class="quill-editor" data-target="f_{{ $name }}">{!! $value !!}</div>
                        <textarea id="f_{{ $name }}" name="{{ $name }}" class="d-none">{{ $value }}</textarea>
                    @elseif($type === 'select')
                        @php $options = isset($f['options_config']) ? config($f['options_config']) : $f['options']; @endphp
                        <select id="f_{{ $name }}" name="{{ $name }}" class="form-select {{ $invalid }}">
                            @foreach($options as $k => $label)
                                <option value="{{ $k }}" @selected((string) $value === (string) $k)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @elseif($type === 'relation')
                        @php $options = $f['model']::orderBy($f['label_column'])->pluck($f['label_column'], 'id'); @endphp
                        <select id="f_{{ $name }}" name="{{ $name }}" class="form-select {{ $invalid }}">
                            <option value="">-- Pilih --</option>
                            @foreach($options as $k => $label)
                                <option value="{{ $k }}" @selected((string) $value === (string) $k)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @elseif($type === 'checkbox')
                        <input type="hidden" name="{{ $name }}" value="0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="f_{{ $name }}" name="{{ $name }}" value="1" @checked((bool) $value)>
                        </div>
                    @elseif($type === 'images')
                        @if($item && count((array) $item->$name))
                            <div class="row g-2 mb-3">
                                @foreach($item->$name as $path)
                                    <div class="col-6 col-md-3">
                                        <img src="{{ asset('storage/'.$path) }}" alt="Media {{ $loop->iteration }} halaman {{ $item->title }}" class="img-fluid rounded border mb-1" style="width:100%;height:100px;object-fit:cover">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_{{ $name }}[]" value="{{ $path }}" id="rm_{{ $name }}_{{ $loop->index }}">
                                            <label class="form-check-label small" for="rm_{{ $name }}_{{ $loop->index }}">Hapus gambar</label>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-1" data-insert-media="{{ '/storage/'.$path }}">Sisipkan di isi</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <input type="file" id="f_{{ $name }}" name="{{ $name }}[]" class="form-control {{ $invalid }}" accept="image/*" multiple>
                        @foreach($errors->get($name . '.*') as $messages)
                            @foreach($messages as $message)
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @endforeach
                        @endforeach
                        @error('remove_' . $name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @elseif($type === 'image' || $type === 'file')
                        @if($item && $item->$name)
                            <div class="mb-2 small">
                                @if($type === 'image')<img src="{{ asset('storage/'.$item->$name) }}" alt="" style="max-height:80px" class="rounded border me-2">@endif
                                <a href="{{ asset('storage/'.$item->$name) }}" target="_blank">{{ basename($item->$name) }}</a>
                                @if($def['key'] === 'pages' && $name === 'image')
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" data-insert-media="{{ '/storage/'.$item->$name }}">Sisipkan di isi</button>
                                @endif
                                <div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_{{ $name }}" value="1" id="rm_{{ $name }}"><label class="form-check-label" for="rm_{{ $name }}">Hapus berkas ini</label></div>
                            </div>
                        @endif
                        <input type="file" id="f_{{ $name }}" name="{{ $name }}" class="form-control {{ $invalid }}" @if($type === 'image') accept="image/*" @endif>
                    @endif

                    @if(!empty($f['help']))<div class="form-text">{{ $f['help'] }}</div>@endif
                    @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            @endforeach

            <div class="d-flex gap-2">
                <button class="btn btn-pa px-4">Simpan</button>
                <a href="{{ route('admin.resource.index', $def['key']) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
