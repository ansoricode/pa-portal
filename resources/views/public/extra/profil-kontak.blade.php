@php
    $buildingPhotoPath = $setting('building_photo');
    $buildingPhotoUrl = $buildingPhotoPath ? asset('storage/'.$buildingPhotoPath) : $page->image_url;
    $buildingPhotoCaption = $buildingPhotoPath ? $setting('building_photo_caption') : null;
    $buildingPhotoPosition = $setting('building_photo_position', 'top');
    $storedContactSocial = json_decode((string) $setting('footer_social', ''), true);
    $footerSocial = is_array($storedContactSocial) ? $storedContactSocial : array_values(array_filter([
        $setting('facebook_url') ? ['name' => 'Facebook', 'url' => $setting('facebook_url'), 'icon' => 'bi-facebook', 'sort_order' => 10, 'is_active' => true] : null,
        $setting('instagram_url') ? ['name' => 'Instagram', 'url' => $setting('instagram_url'), 'icon' => 'bi-instagram', 'sort_order' => 20, 'is_active' => true] : null,
        $setting('youtube_url') ? ['name' => 'YouTube', 'url' => $setting('youtube_url'), 'icon' => 'bi-youtube', 'sort_order' => 30, 'is_active' => true] : null,
    ]));
    $footerSocial = collect($footerSocial)
        ->filter(fn ($item) => ($item['is_active'] ?? true) && ! empty($item['name']) && ! empty($item['url']))
        ->sortBy(fn ($item) => (int) ($item['sort_order'] ?? 0))
        ->values()
        ->all();
@endphp
@if($buildingPhotoUrl && $buildingPhotoPosition === 'top')
    @include('public.partials.building-photo')
@endif
<div class="row g-4">
    <div class="col-12">
        <ul class="list-unstyled lh-lg text-secondary">
            @if($setting('address_street'))<li><strong>Jalan:</strong> {{ $setting('address_street') }}</li>@endif
            @if($setting('address_village'))<li><strong>Desa/Kelurahan:</strong> {{ $setting('address_village') }}</li>@endif
            @if($setting('address_district'))<li><strong>Kecamatan:</strong> {{ $setting('address_district') }}</li>@endif
            @if($setting('address_province'))<li><strong>Provinsi &amp; Kode Pos:</strong> {{ $setting('address_province') }}</li>@endif
            @php
                $contactWhatsapp = trim((string) $setting('whatsapp', ''));
                $contactWhatsappUrl = '';
                if ($contactWhatsapp !== '') {
                    if (preg_match('/^https?:\/\//i', $contactWhatsapp)) {
                        $contactWhatsappUrl = $contactWhatsapp;
                    } else {
                        $contactWhatsappNumber = preg_replace('/\D+/', '', $contactWhatsapp);
                        if (str_starts_with($contactWhatsappNumber, '0')) {
                            $contactWhatsappNumber = '62'.substr($contactWhatsappNumber, 1);
                        } elseif (str_starts_with($contactWhatsappNumber, '8')) {
                            $contactWhatsappNumber = '62'.$contactWhatsappNumber;
                        }
                        $contactWhatsappUrl = $contactWhatsappNumber !== '' ? 'https://wa.me/'.$contactWhatsappNumber : '';
                    }
                }
            @endphp
            @if($setting('phone'))<li><strong>Telepon:</strong> {{ $setting('phone') }}</li>@endif
            @if($contactWhatsappUrl)
                <li><strong>WhatsApp PTSP:</strong> <a href="{{ $contactWhatsappUrl }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp me-1"></i>{{ $contactWhatsapp }}</a></li>
            @endif
            @if($setting('email'))<li><strong>Email Resmi:</strong> <a href="mailto:{{ $setting('email') }}">{{ $setting('email') }}</a></li>@endif
            @if($setting('website'))<li><strong>Website Resmi:</strong> <a href="{{ $setting('website') }}">{{ $setting('website') }}</a></li>@endif
            @if($footerSocial)
                <li>
                    <strong>Media Sosial:</strong>
                    <span class="d-inline-flex gap-3 ms-2">
                        @foreach($footerSocial as $social)
                            @php
                                $contactSocialIcon = strtolower(trim((string) ($social['icon'] ?? '')));
                                $contactSocialName = strtolower(trim((string) ($social['name'] ?? '')));
                                $contactKnownIcons = [
                                    'facebook' => 'bi-facebook',
                                    'fb' => 'bi-facebook',
                                    'instagram' => 'bi-instagram',
                                    'ig' => 'bi-instagram',
                                    'youtube' => 'bi-youtube',
                                    'tiktok' => 'bi-tiktok',
                                    'twitter' => 'bi-twitter-x',
                                    'x' => 'bi-twitter-x',
                                    'whatsapp' => 'bi-whatsapp',
                                    'telegram' => 'bi-telegram',
                                    'linkedin' => 'bi-linkedin',
                                ];
                                $contactSocialIcon = str_starts_with($contactSocialIcon, 'bi-')
                                    ? $contactSocialIcon
                                    : ($contactKnownIcons[$contactSocialName] ?? ($contactKnownIcons[$contactSocialIcon] ?? 'bi-link-45deg'));
                            @endphp
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['name'] }}" title="{{ $social['name'] }}">
                                <i class="{{ $contactSocialIcon }}"></i>
                            </a>
                        @endforeach
                    </span>
                </li>
            @endif
        </ul>
    </div>
</div>
@if($setting('google_maps_embed_url'))
    <section class="mt-4" aria-labelledby="maps-heading">
        <h3 id="maps-heading" class="h5 fw-bold text-pa-primary mb-3">Lokasi Pengadilan</h3>
        <div class="ratio ratio-16x9">
            <iframe src="{{ $setting('google_maps_embed_url') }}" title="Peta lokasi pengadilan" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
    </section>
@endif
@if($buildingPhotoUrl && $buildingPhotoPosition === 'bottom')
    @include('public.partials.building-photo')
@endif
