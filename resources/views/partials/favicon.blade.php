@php
    $faviconPath = $setting('favicon');
    $faviconUrl = $faviconPath ? asset('storage/'.$faviconPath) : asset('favicon.svg');
    $faviconType = ! $faviconPath ? 'image/svg+xml' : (str_ends_with(strtolower($faviconPath), '.png') ? 'image/png' : 'image/x-icon');
@endphp
<link rel="icon" href="{{ $faviconUrl }}" type="{{ $faviconType }}">
