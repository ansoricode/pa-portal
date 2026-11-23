@include('public.partials.list-filter', ['paginator' => $schedules, 'showDate' => true])
<p class="small text-secondary">@if(request()->filled('tanggal'))Menampilkan jadwal pada tanggal terpilih.@else Menampilkan jadwal hari ini dan mendatang. Pilih tanggal untuk melihat hari tertentu.@endif</p>
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-success">
            <tr><th>No</th><th>Tanggal Sidang</th><th>Nomor Perkara</th><th>Agenda Sidang</th><th>Ruang Sidang</th></tr>
        </thead>
        <tbody>
        @forelse($schedules as $s)
            <tr>
                <td>{{ $schedules->firstItem() + $loop->index }}</td>
                <td>{{ $s->hearing_date->translatedFormat('d F Y') }}</td>
                <td>{{ $s->case_number }}</td>
                <td>{{ $s->agenda }}</td>
                <td>{{ $s->courtroom }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-secondary">Tidak ada jadwal sidang yang cocok.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $schedules->links() }}
@if($setting('sipp_url'))
    <a href="{{ $setting('sipp_url') }}" target="_blank" rel="noopener noreferrer" class="btn btn-pa-primary mt-3">Buka Jadwal Lengkap di SIPP Satker</a>
@endif
