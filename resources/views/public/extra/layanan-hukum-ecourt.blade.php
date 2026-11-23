<div class="d-flex flex-wrap gap-2 mt-3">
    <a href="{{ $setting('ecourt_url', 'https://ecourt.mahkamahagung.go.id') }}" target="_blank" rel="noopener noreferrer" class="btn btn-pa-secondary">Portal e-Court MA RI</a>
    <a href="{{ $setting('putusan_url', 'https://putusan3.mahkamahagung.go.id') }}" target="_blank" rel="noopener noreferrer" class="btn btn-pa-primary">Direktori Putusan MA RI</a>
    @if($setting('putusan_satker_url'))
        <a href="{{ $setting('putusan_satker_url') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success">Direktori Putusan Khusus Satker</a>
    @endif
</div>
