@if ($paginator->hasPages())
    <nav class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2" aria-label="Navigasi halaman data">
        <small class="text-secondary">Menampilkan {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data</small>
        <ul class="pagination pagination-sm mb-0 flex-wrap">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo; Sebelumnya</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Sebelumnya</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &raquo;</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">Berikutnya &raquo;</span></li>
            @endif
        </ul>
    </nav>
@endif
