@if($jurisdictions->isNotEmpty())
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <ul class="list-group">
                @foreach($jurisdictions as $j)
                    <li class="list-group-item">{{ $loop->iteration }}. {{ $j->name }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@else
    <p class="text-secondary fst-italic">Daftar wilayah yurisdiksi belum diisi.</p>
@endif
