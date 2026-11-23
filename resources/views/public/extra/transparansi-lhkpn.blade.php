@include('public.partials.list-filter', ['paginator' => $officials])
<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead class="table-success">
            <tr><th>Nama Pejabat</th><th>Jabatan</th><th>Tahun Lapor</th><th>Ikhtisar Kekayaan</th></tr>
        </thead>
        <tbody>
        @forelse($officials as $o)
            <tr>
                <td>{{ $o->name }}</td>
                <td>{{ $o->position }}</td>
                <td>{{ $o->lhkpnReports->pluck('year')->implode(' & ') ?: '-' }}</td>
                <td>
                    @forelse($o->lhkpnReports as $r)
                        @if($r->url)<a href="{{ $r->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-pa-primary mb-1">Ikhtisar {{ $r->year }} (PDF)</a>@endif
                    @empty
                        -
                    @endforelse
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-secondary">Data LHKPN tidak ditemukan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $officials->links() }}
