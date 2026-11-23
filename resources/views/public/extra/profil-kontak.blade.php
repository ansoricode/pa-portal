<div class="row g-4">
    <div class="col-md-6">
        <ul class="list-unstyled lh-lg text-secondary">
            @if($setting('address_street'))<li><strong>Jalan:</strong> {{ $setting('address_street') }}</li>@endif
            @if($setting('address_village'))<li><strong>Desa/Kelurahan:</strong> {{ $setting('address_village') }}</li>@endif
            @if($setting('address_district'))<li><strong>Kecamatan:</strong> {{ $setting('address_district') }}</li>@endif
            @if($setting('address_province'))<li><strong>Provinsi &amp; Kode Pos:</strong> {{ $setting('address_province') }}</li>@endif
            <li><strong>Telepon / WhatsApp PTSP:</strong> {{ collect([$setting('phone'), $setting('whatsapp')])->filter()->implode(' / ') ?: '-' }}</li>
            @if($setting('email'))<li><strong>Email Resmi:</strong> <a href="mailto:{{ $setting('email') }}">{{ $setting('email') }}</a></li>@endif
            @if($setting('website'))<li><strong>Website Resmi:</strong> <a href="{{ $setting('website') }}">{{ $setting('website') }}</a></li>@endif
        </ul>
    </div>
    <div class="col-md-6">
        @if($page->image_url)
            <img src="{{ $page->image_url }}" alt="Foto Gedung Kantor" class="img-fluid rounded shadow-sm">
        @endif
    </div>
</div>
