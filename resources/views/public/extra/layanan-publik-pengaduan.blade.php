<div class="alert alert-warning border-start border-4 border-pa-secondary">
    <strong>Sistem Pengawasan Mahkamah Agung RI (SIWAS):</strong> laporkan dugaan pelanggaran perilaku aparatur pengadilan melalui
    <a href="{{ $setting('siwas_url', 'https://siwas.mahkamahagung.go.id') }}" target="_blank" rel="noopener noreferrer" class="fw-bold text-dark">{{ preg_replace('#^https?://#', '', $setting('siwas_url', 'https://siwas.mahkamahagung.go.id')) }}</a>.
</div>
@include('public.partials.doc-list', ['documents' => $documents, 'btnClass' => 'btn-pa-primary', 'btnText' => 'Lihat Laporan'])
