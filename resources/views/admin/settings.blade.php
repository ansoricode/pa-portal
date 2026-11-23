@extends('admin.layout')
@section('title', 'Pengaturan Situs')
@section('content')
<h1 class="h4 fw-bold mb-4">Pengaturan Situs</h1>
@if($errors->any() && ! $errors->has('db'))<div class="alert alert-danger">Periksa kembali isian Anda.</div>@endif
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @foreach($groups as $title => $fields)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold" style="color:#056e4f">{{ $title }}</div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($fields as $f)
                        @php $name = $f['name']; $val = $setting($name, $f['default'] ?? null); @endphp
                        <div class="col-md-{{ in_array($f['type'], ['textarea','image','favicon']) ? 12 : 6 }}">
                            <label class="form-label fw-semibold" for="s_{{ $name }}">{{ $f['label'] }}</label>
                            @if($f['type'] === 'textarea')
                                <textarea id="s_{{ $name }}" name="{{ $name }}" rows="3" class="form-control @error($name) is-invalid @enderror">{{ old($name, $val) }}</textarea>
                            @elseif($f['type'] === 'select')
                                <select id="s_{{ $name }}" name="{{ $name }}" class="form-select @error($name) is-invalid @enderror">
                                    @foreach($f['options'] as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" @selected((string) old($name, $val) === (string) $optionValue)>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @elseif(in_array($f['type'], ['image', 'favicon']))
                                @if($val)
                                    <div class="mb-2"><img src="{{ asset('storage/'.$val) }}" alt="" style="max-height:70px" class="border rounded p-1 bg-white">
                                        <div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_{{ $name }}" value="1" id="rm_{{ $name }}"><label class="form-check-label small" for="rm_{{ $name }}">Hapus gambar{{ $f['type'] === 'favicon' ? ' (kembali ke favicon bawaan)' : ' (kembali ke gambar bawaan)' }}</label></div>
                                    </div>
                                @endif
                                <input type="file" id="s_{{ $name }}" name="{{ $name }}" accept="{{ $f['type'] === 'favicon' ? 'image/png,.ico,image/x-icon,image/vnd.microsoft.icon' : 'image/*' }}" class="form-control @error($name) is-invalid @enderror">
                            @else
                                <input type="text" id="s_{{ $name }}" name="{{ $name }}" value="{{ old($name, $val) }}" class="form-control @error($name) is-invalid @enderror">
                            @endif
                            @if(! empty($f['hint']))<div class="form-text">{{ $f['hint'] }}</div>@endif
                            @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
    <button class="btn btn-pa px-4">Simpan Pengaturan</button>
</form>
@endsection
