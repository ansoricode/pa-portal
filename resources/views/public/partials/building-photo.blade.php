<figure class="mb-0">
    <img src="{{ $buildingPhotoUrl }}" alt="Foto gedung {{ $setting('site_name', 'kantor pengadilan') }}" class="img-fluid rounded shadow-sm w-100" style="max-height: 34rem; object-fit: cover;">
    @if($buildingPhotoCaption)
        <figcaption class="text-secondary small text-center mt-2">{{ $buildingPhotoCaption }}</figcaption>
    @endif
</figure>
