@if($jurisdictions->isNotEmpty())
    <div class="row g-3 mb-3">
        @foreach($jurisdictions as $j)
            @php
                $villages = $j->villageList();
            @endphp
            <div class="{{ $j->map_embed_url ? 'col-12' : 'col-md-6' }}">
                <article class="yd-card h-100">
                    <header class="yd-card-head">
                        <span class="yd-number">{{ $loop->iteration }}</span>
                        <h3 class="yd-title">{{ $j->name }}</h3>
                        @if($j->radius)
                            <span class="yd-radius" title="Radius"><i class="bi bi-bullseye" aria-hidden="true"></i> {{ $j->radius }}</span>
                        @endif
                    </header>
                    @if($villages)
                        <div class="yd-card-body">
                            <div class="yd-count">{{ count($villages) }} desa/kelurahan</div>
                            <ul class="yd-chips">
                                @foreach($villages as $village)
                                    <li>{{ $village }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if($j->map_embed_url)
                        <details class="yd-file">
                            <summary class="btn btn-sm btn-outline-success"><i class="bi bi-geo-alt" aria-hidden="true"></i> Lihat peta</summary>
                            <iframe src="{{ $j->map_embed_url }}" title="Peta yurisdiksi {{ $j->name }}" class="yd-embed" loading="lazy" referrerpolicy="no-referrer" allowfullscreen></iframe>
                        </details>
                    @endif
                </article>
            </div>
        @endforeach
    </div>
@else
    <p class="text-secondary fst-italic">Daftar wilayah yurisdiksi belum diisi.</p>
@endif
