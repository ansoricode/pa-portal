@extends('layouts.app')
@section('title', $page->title)
@if($page->content)
    @section('meta_description', \Illuminate\Support\Str::limit(strip_tags($page->content), 160))
@endif
@if($page->image_url)
    @section('og_image', $page->image_url)
@endif
@section('content')
@php
    $imageIsEmbedded = $page->image && str_contains($page->content ?? '', 'storage/' . $page->image);
    $galleryMedia = collect($page->media ?? [])
        ->reject(fn ($path) => str_contains($page->content ?? '', 'storage/' . $path));
@endphp
<div class="container-lg py-5">
    <h1 class="font-display h2 fw-bold text-pa-primary mb-4">{{ $sectionCfg['heading'] }}</h1>
    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="bg-white rounded-3 shadow-sm p-3 position-sticky" style="top: 6.5rem;">
                <nav class="nav flex-column gap-1" aria-label="Sub-navigasi {{ $sectionCfg['title'] }}">
                    @foreach($pages as $p)
                        <a href="{{ route('page.show', [$section, $p->slug]) }}" class="nav-link sidebar-nav-link fw-semibold py-2 px-3 {{ $p->id === $page->id ? 'active' : '' }}">
                            @if($p->icon)<i class="bi {{ $p->icon }} me-2"></i>@endif{{ $p->title }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </aside>

        <div class="col-lg-9">
            <div class="bg-white p-4 p-md-5 rounded-3 shadow-sm">
                <h2 class="font-display h3 fw-bold text-pa-primary border-bottom border-2 border-pa-secondary pb-2 mb-4">{{ $page->title }}</h2>

                @if($page->content)
                    <div class="page-body mb-4">{!! $page->content !!}</div>
                @endif

                @includeIf('public.extra.' . $section . '-' . $page->slug)

                @if($page->image_url && $page->slug !== 'kontak' && ! $imageIsEmbedded)
                    <img src="{{ $page->image_url }}" alt="{{ $page->title }}" class="img-fluid rounded border shadow-sm mt-3">
                @endif

                @if($galleryMedia->isNotEmpty())
                    <section class="page-media-gallery mt-5" aria-labelledby="page-media-heading">
                        <h3 id="page-media-heading" class="font-display h5 fw-bold text-pa-primary mb-3">Galeri {{ $page->title }}</h3>
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3">
                            @foreach($galleryMedia as $path)
                                <div class="col">
                                    <a class="page-media-item d-block rounded-3 shadow-sm" href="{{ asset('storage/'.$path) }}" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('storage/'.$path) }}" alt="{{ $page->title }} - gambar {{ $loop->iteration }}" loading="lazy">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
