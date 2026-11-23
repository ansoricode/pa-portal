@include('public.partials.list-filter', ['paginator' => $summons])
<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead class="table-success">
            <tr><th>Nama Pihak</th><th>Nomor Perkara</th><th>Tanggal Sidang</th><th>Dokumen Relaas</th></tr>
        </thead>
        <tbody>
        @forelse($summons as $s)
            <tr>
                <td>{{ $s->party_name }}</td>
                <td>{{ $s->case_number }}</td>
                <td>{{ $s->hearing_date->translatedFormat('d F Y') }}</td>
                <td>@if($s->url)<a href="{{ $s->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-pa-primary">Lihat Relaas (PDF)</a>@else - @endif</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-secondary">Tidak ada panggilan ghaib yang cocok.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $summons->links() }}
