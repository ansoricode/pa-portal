<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\CourtSchedule;
use App\Models\Document;
use App\Models\Jurisdiction;
use App\Models\LhkpnReport;
use App\Models\News;
use App\Models\Official;
use App\Models\Page;
use App\Models\PanggilanGhaib;
use App\Models\Setting;
use App\Models\Shortcut;
use App\Models\Slider;
use App\Models\SurveyIndex;
use Illuminate\Database\Seeder;

/** Data awal diambil dari template HTML (SK Dirjen Badilag 2722/2026). Semua dapat diubah lewat panel admin. */
class PortalSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $this->pages();
        $this->home();
        $this->news();
        $this->legalServices();
        $this->documents();
        $this->officials();
    }

    protected function settings(): void
    {
        $data = [
            'site_name'          => 'Pengadilan Agama Contoh',
            'site_tagline'       => 'Mahkamah Agung Republik Indonesia',
            'site_description'   => 'Portal Resmi Satuan Kerja Peradilan Agama - Standar Tata Kelola Website SK Dirjen Badilag No. 2722/DJA/SK.TI1/X/2026',
            'address_street'     => 'Jl. Keadilan Utama No. 1',
            'address_village'    => 'Merdeka',
            'address_district'   => 'Contoh Utara',
            'address_province'   => 'DKI Jakarta 10110',
            'phone'              => '(021) 1234567',
            'whatsapp'           => '0812-0000-0000',
            'email'              => 'info@pa-contoh.go.id',
            'website'            => 'https://www.pa-contoh.go.id',
            'sipp_url'           => 'https://sipp.pa-contoh.go.id',
            'ecourt_url'         => 'https://ecourt.mahkamahagung.go.id',
            'putusan_url'        => 'https://putusan3.mahkamahagung.go.id',
            'putusan_satker_url' => 'https://putusan3.mahkamahagung.go.id/pengadilan/profil/pengadilan/pa-contoh.html',
            'siwas_url'          => 'https://siwas.mahkamahagung.go.id',
        ];
        foreach ($data as $k => $v) {
            Setting::put($k, $v);
        }
    }

    protected function pages(): void
    {
        $pages = [
            // ---- Profil
            ['profil', 'visi-misi', 'Visi dan Misi', 'bi-gem', 1, '<h3>Visi</h3><blockquote>"Terwujudnya Pengadilan Agama Contoh yang Agung"</blockquote><h3>Misi</h3><ol><li>Menjaga kemandirian Pengadilan Agama Contoh.</li><li>Memberikan pelayanan hukum yang berkeadilan kepada pencari keadilan.</li><li>Meningkatkan kualitas kepemimpinan Pengadilan Agama Contoh.</li><li>Meningkatkan kredibilitas dan transparansi Pengadilan Agama Contoh.</li></ol>'],
            ['profil', 'tupoksi', 'Tugas Pokok & Fungsi', 'bi-briefcase', 2, '<p>Berdasarkan Undang-Undang Nomor 7 Tahun 1989 tentang Peradilan Agama sebagaimana telah diubah dengan UU Nomor 3 Tahun 2006 dan UU Nomor 50 Tahun 2009, Pengadilan Agama bertugas dan berwenang memeriksa, memutus, dan menyelesaikan perkara di tingkat pertama antara orang-orang yang beragama Islam di bidang:</p><ul><li>Perkawinan</li><li>Waris</li><li>Wasiat</li><li>Hibah</li><li>Wakaf</li><li>Zakat</li><li>Infaq</li><li>Shadaqah</li><li>Ekonomi Syari\'ah</li></ul>'],
            ['profil', 'yurisdiksi', 'Wilayah Yurisdiksi & Peta', 'bi-geo-alt', 3, '<p>Meliputi seluruh kecamatan di wilayah Kabupaten/Kota Contoh (daftar nama kecamatan untuk Tingkat Pertama atau kabupaten/kota untuk Tingkat Banding beserta peta wilayah yurisdiksi):</p>'],
            ['profil', 'struktur', 'Struktur Organisasi', 'bi-diagram-3', 4, '<p><em>Diagram struktur organisasi memuat nama pejabat dan nama jabatan struktural/fungsional utama (staf dan tenaga honorer tidak perlu ditampilkan dalam diagram). Unggah gambar bagan melalui menu Halaman Menu.</em></p>'],
            ['profil', 'pejabat', 'Profil Pejabat & Pegawai', 'bi-people', 5, '<p>Profil aparatur memuat foto, nama lengkap dan gelar, pangkat/golongan, TMT jabatan, riwayat pendidikan, dan riwayat jabatan.</p>'],
            ['profil', 'kontak', 'Alamat, Kontak & Foto Gedung', 'bi-building', 6, null],

            // ---- Layanan Hukum
            ['layanan-hukum', 'jadwal', 'Jadwal Persidangan', 'bi-calendar-event', 1, '<p>Jadwal persidangan harian satuan kerja, terintegrasi dengan Sistem Informasi Penelusuran Perkara (SIPP).</p>'],
            ['layanan-hukum', 'panjar', 'Rincian Panjar & Radius Biaya', 'bi-cash-coin', 2, '<p>Berdasarkan SK Ketua Pengadilan tentang Panjar Biaya Perkara dan Radius Wilayah Panggilan:</p>'],
            ['layanan-hukum', 'prosedur', 'Prosedur Berperkara', 'bi-signpost-split', 3, '<p>Panduan alur pendaftaran hingga penyelesaian perkara (6 jenis prosedur wajib):</p>'],
            ['layanan-hukum', 'hak', 'Hak Pencari Keadilan', 'bi-shield-check', 4, '<p>Setiap pencari keadilan berhak mendapatkan bantuan hukum berupa layanan pembebasan biaya perkara (Prodeo) bagi masyarakat tidak mampu serta layanan konsultasi/pembuatan gugatan gratis melalui Pos Bantuan Hukum (Posbakum).</p>'],
            ['layanan-hukum', 'ghaib', 'Panggilan Ghaib & Mediasi', 'bi-telephone-inbound', 5, '<p>Daftar panggilan ghaib dan layanan mediasi.</p>'],
            ['layanan-hukum', 'statistik', 'Statistik & Biaya Perkara', 'bi-bar-chart', 6, '<p>Laporan bulanan keadaan perkara dan penggunaan biaya perkara.</p>'],
            ['layanan-hukum', 'ecourt', 'e-Court & Direktori Putusan', 'bi-laptop', 7, '<p><strong>e-Court Mahkamah Agung RI</strong> adalah layanan bagi Pengguna Terdaftar dan Pengguna Lain untuk Pendaftaran Perkara Secara Online (e-Filing), Mendapatkan Taksiran Panjar Biaya secara elektronik (e-SKUM), Pembayaran secara elektronik (e-Payment), Pemanggilan secara elektronik (e-Summons), dan Persidangan secara elektronik (e-Litigation).</p>'],

            // ---- Transparansi
            ['transparansi', 'sakip', 'Dokumen SAKIP (5 Tahun)', 'bi-clipboard-data', 1, '<p>Rencana Strategis, Rencana Kinerja Tahunan, Rencana Aksi, Perjanjian Kinerja, IKU, LKjIP, dan Laporan Tahunan.</p>'],
            ['transparansi', 'keuangan', 'Laporan Keuangan', 'bi-wallet2', 2, '<p>Transparansi pengelolaan anggaran dan keuangan satuan kerja.</p>'],
            ['transparansi', 'pbj', 'Pengadaan Barang & Jasa', 'bi-box-seam', 3, '<p>Rencana Umum Pengadaan (RUP) Barang dan Jasa satuan kerja yang terintegrasi dengan SIRUP LKPP.</p>'],
            ['transparansi', 'lhkpn', 'LHKPN / LHKASN', 'bi-person-vcard', 4, '<p>Daftar nama wajib lapor, tahun penerbitan terakhir, serta tautan dokumen <strong>Ikhtisar Kekayaan</strong> minimal 2 tahun terakhir.</p>'],

            // ---- Layanan Publik
            ['layanan-publik', 'maklumat', 'Standar & Maklumat Pelayanan', 'bi-patch-check', 1, '<blockquote>"Dengan ini kami menyatakan sanggup menyelenggarakan pelayanan sesuai standar pelayanan yang telah ditetapkan dan apabila tidak menepati janji ini, kami siap menerima sanksi sesuai peraturan perundang-undangan yang berlaku."</blockquote>'],
            ['layanan-publik', 'ppid', 'Layanan Informasi (PPID)', 'bi-info-circle', 2, '<p>Sesuai SK KMA Nomor 2-144/KMA/SK/VIII/2022 tentang Standar Pelayanan Informasi di Pengadilan.</p>'],
            ['layanan-publik', 'pengaduan', 'Pengaduan (SIWAS) & Rekapitulasi', 'bi-megaphone', 3, '<p>Prosedur pengaduan, rekapitulasi pengaduan masyarakat, dan laporan hukuman disiplin hasil pengawasan.</p>'],
            ['layanan-publik', 'disabilitas', 'Layanan Kelompok Rentan', 'bi-universal-access', 4, '<p>Fasilitas yang tersedia: jalur pemandu (guiding block), kursi roda, ruang tunggu prioritas, ruang sidang ramah anak dan perempuan, petugas pendamping disabilitas, serta fitur aksesibilitas website (Screen Reader, Kontras Tinggi, dan Pembesar Teks).</p>'],
            ['layanan-publik', 'survei', 'Survei Pelayanan Publik', 'bi-clipboard-check', 5, '<p>Memuat nilai dan laporan Survei Kepuasan Masyarakat (SKM), Survei Persepsi Anti Korupsi (SPAK), dan Survei Persepsi Kualitas Pelayanan (SPKP).</p>'],
        ];

        foreach ($pages as [$section, $slug, $title, $icon, $order, $content]) {
            Page::updateOrCreate(
                ['section' => $section, 'slug' => $slug],
                ['title' => $title, 'icon' => $icon, 'sort_order' => $order, 'content' => $content, 'is_active' => true]
            );
        }

        foreach (['Kecamatan Contoh Utara', 'Kecamatan Contoh Selatan', 'Kecamatan Contoh Timur', 'Kecamatan Contoh Barat'] as $i => $name) {
            Jurisdiction::firstOrCreate(['name' => $name], ['sort_order' => $i + 1]);
        }
    }

    protected function home(): void
    {
        if (Slider::count() === 0) {
            Slider::insert([
                ['badge' => 'PELAYANAN PRIMA', 'title' => 'Selamat Datang di Portal Resmi Pengadilan Agama Contoh', 'subtitle' => 'Informasi perkara, transparansi, dan layanan publik yang mudah diakses oleh seluruh masyarakat pencari keadilan.', 'button_text' => 'Jadwal Persidangan', 'button_url' => '/layanan-hukum/jadwal', 'sort_order' => 1, 'is_active' => true],
                ['badge' => 'BERPERKARA SECARA ELEKTRONIK', 'title' => 'Pendaftaran Perkara Mudah Melalui e-Court', 'subtitle' => 'Daftarkan perkara, bayar panjar secara virtual, dan ikuti persidangan secara elektronik tanpa harus antre di pengadilan.', 'button_text' => 'Panduan & Tautan e-Court', 'button_url' => '/layanan-hukum/ecourt', 'sort_order' => 2, 'is_active' => true],
                ['badge' => 'PERADILAN INKLUSIF', 'title' => 'Layanan Ramah Kelompok Rentan & Disabilitas', 'subtitle' => 'Kami menyediakan sarana prasarana aksesibel, pendampingan khusus, serta bantuan hukum cuma-cuma (Prodeo & Posbakum).', 'button_text' => 'Lihat Fasilitas Layanan', 'button_url' => '/layanan-publik/disabilitas', 'sort_order' => 3, 'is_active' => true],
            ]);
        }

        if (Shortcut::count() === 0) {
            Shortcut::insert([
                ['title' => 'SIPP Satker', 'description' => 'Sistem Informasi Penelusuran Perkara & Jadwal Sidang', 'icon' => 'bi-search', 'url' => 'https://sipp.pa-contoh.go.id', 'is_external' => true, 'sort_order' => 1, 'is_active' => true],
                ['title' => 'e-Court MA RI', 'description' => 'Pendaftaran, Pembayaran & Persidangan Elektronik', 'icon' => 'bi-laptop', 'url' => 'https://ecourt.mahkamahagung.go.id', 'is_external' => true, 'sort_order' => 2, 'is_active' => true],
                ['title' => 'Direktori Putusan', 'description' => 'Akses Salinan Putusan Pengadilan Agama', 'icon' => 'bi-journal-bookmark-fill', 'url' => 'https://putusan3.mahkamahagung.go.id', 'is_external' => true, 'sort_order' => 3, 'is_active' => true],
                ['title' => 'SIWAS MARI', 'description' => 'Sistem Pengawasan & Pengaduan Masyarakat', 'icon' => 'bi-shield-exclamation', 'url' => 'https://siwas.mahkamahagung.go.id', 'is_external' => true, 'sort_order' => 4, 'is_active' => true],
            ]);
        }

        if (Announcement::count() === 0) {
            Announcement::insert([
                ['badge' => 'Relaas Panggilan Ghaib', 'title' => 'Panggilan Sidang Perkara Nomor 123/Pdt.G/2026/PA.Cth', 'label' => 'Jadwal Sidang: ' . now()->addDays(21)->translatedFormat('d F Y'), 'published_at' => now()->subDays(5)->toDateString(), 'url' => '/layanan-hukum/ghaib', 'is_active' => true],
                ['badge' => 'Pengadaan Barang & Jasa', 'title' => 'Pengumuman Rencana Umum Pengadaan (RUP) Tahun Anggaran 2026', 'label' => null, 'published_at' => now()->subDays(4)->toDateString(), 'url' => '/transparansi/pbj', 'is_active' => true],
                ['badge' => 'Informasi Serta Merta', 'title' => 'Pemberitahuan Pemeliharaan Server SIPP Lokal (Sabtu, 22.00 - 24.00 WIB)', 'label' => null, 'published_at' => now()->subDays(7)->toDateString(), 'url' => '/layanan-publik/ppid', 'is_active' => true],
            ]);
        }

        if (SurveyIndex::count() === 0) {
            SurveyIndex::insert([
                ['name' => 'Indeks Kepuasan Masyarakat (IKM)', 'score' => '92,40', 'label' => 'Sangat Baik', 'icon' => 'bi-emoji-smile', 'sort_order' => 1],
                ['name' => 'Indeks Persepsi Kualitas Pelayanan (IPKP)', 'score' => '3,85', 'label' => 'Sangat Baik', 'icon' => 'bi-award', 'sort_order' => 2],
                ['name' => 'Indeks Persepsi Anti Korupsi (IPAK)', 'score' => '3,90', 'label' => 'Sangat Bersih', 'icon' => 'bi-shield-check', 'sort_order' => 3],
            ]);
        }
    }

    protected function news(): void
    {
        if (News::count() > 0) {
            return;
        }

        News::create([
            'title' => 'Implementasi Notifikasi WhatsApp Otomatis Pengambilan Akta Cerai',
            'category' => 'Inovasi Layanan',
            'excerpt' => 'Memudahkan para pihak mengetahui status penerbitan produk pengadilan secara real-time tanpa harus bolak-balik ke kantor.',
            'content' => '<p>Pengadilan Agama Contoh terus berkomitmen menghadirkan layanan berbasis teknologi informasi yang memudahkan masyarakat pencari keadilan. Melalui integrasi sistem persidangan, para pihak kini otomatis menerima notifikasi pesan singkat ketika produk pengadilan telah terbit dan siap diambil di loket PTSP.</p>',
            'author' => 'Tim IT & Kepaniteraan',
            'published_at' => now()->subDay(),
        ]);

        News::create([
            'title' => 'Optimalisasi Mediasi Elektronik dalam Sengketa Ekonomi Syariah',
            'category' => 'Opini & Gagasan Aparatur',
            'excerpt' => 'Pemanfaatan teknologi komunikasi dalam proses mediasi sebagai solusi efektif bagi para pihak di luar wilayah yurisdiksi.',
            'content' => '<p>Pemanfaatan teknologi komunikasi dalam proses mediasi menjadi solusi efektif untuk meningkatkan tingkat keberhasilan perdamaian bagi para pihak yang berdomisili di luar wilayah yurisdiksi pengadilan.</p>',
            'author' => 'Hakim Mediator',
            'published_at' => now()->subDays(9),
        ]);
    }

    protected function legalServices(): void
    {
        if (CourtSchedule::count() === 0) {
            CourtSchedule::insert([
                ['hearing_date' => today()->toDateString(), 'case_number' => '1045/Pdt.G/2026/PA.Cth', 'agenda' => 'Sidang Pertama & Mediasi', 'courtroom' => 'Ruang Sidang Utama', 'created_at' => now(), 'updated_at' => now()],
                ['hearing_date' => today()->toDateString(), 'case_number' => '1048/Pdt.P/2026/PA.Cth', 'agenda' => 'Pembuktian', 'courtroom' => 'Ruang Sidang II', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (PanggilanGhaib::count() === 0) {
            PanggilanGhaib::create([
                'party_name' => 'Fulan bin Fulan (Tergugat)',
                'case_number' => '123/Pdt.G/2026/PA.Cth',
                'hearing_date' => now()->addDays(21)->toDateString(),
            ]);
        }
    }

    protected function documents(): void
    {
        if (Document::count() > 0) {
            return;
        }

        $rows = [
            // SAKIP
            ['sakip', 'Rencana Strategis (Renstra) & Reviu Renstra', '2022–2026'],
            ['sakip', 'Rencana Kinerja Tahunan (RKT)', '2022–2026'],
            ['sakip', 'Rencana Aksi Kinerja', '2022–2026'],
            ['sakip', 'Perjanjian Kinerja (PK)', '2022–2026'],
            ['sakip', 'Indikator Kinerja Utama (IKU) & Reviu IKU', '2022–2026'],
            ['sakip', 'Laporan Kinerja Instansi Pemerintah (LKjIP)', '2021–2025'],
            ['sakip', 'Laporan Tahunan / Laporan Pelaksanaan Kegiatan', '2021–2025'],
            // Keuangan
            ['keuangan', 'Daftar Isian Pelaksanaan Anggaran (DIPA 01 & DIPA 04), Rencana & Realisasi', null],
            ['keuangan', 'Laporan Realisasi Anggaran (LRA)', null],
            ['keuangan', 'Catatan Atas Laporan Keuangan (CALK)', null],
            // PBJ
            ['pbj', 'Daftar Rencana Umum Pengadaan (RUP) Tahun 2026', null],
            // Panjar
            ['panjar', 'SK Panjar Biaya Perkara Tahun 2026 (Cerai Gugat / Talak / Gugatan)', null],
            ['panjar', 'SK Radius Biaya Panggilan Jurusita (Radius I, II, III, dan Ghaib)', null],
            // Prosedur
            ['prosedur', 'Prosedur Berperkara Tingkat Pertama', null],
            ['prosedur', 'Prosedur Upaya Hukum Banding', null],
            ['prosedur', 'Prosedur Upaya Hukum Kasasi', null],
            ['prosedur', 'Prosedur Peninjauan Kembali (PK)', null],
            ['prosedur', 'Prosedur Pengambilan Produk Pengadilan', null],
            ['prosedur', 'Prosedur Gugatan Sederhana Ekonomi Syariah', null],
            // Statistik
            ['statistik', 'Statistik Keadaan Perkara Bulanan (Sisa Bulan Lalu, Masuk, Putus, Sisa Saat Ini)', null],
            ['statistik', 'Laporan Rincian Penggunaan Biaya Perkara Per Bulan', null],
            ['statistik', 'Laporan Pengembalian Sisa Panjar Perkara Per Bulan', null],
            // PPID
            ['ppid', 'Formulir Permohonan Informasi Publik', null],
            ['ppid', 'Laporan Layanan Akses Informasi Publik', null],
            // Pengaduan
            ['pengaduan', 'Laporan Rekapitulasi Pengaduan Masyarakat', null],
            ['pengaduan', 'Laporan Hukuman Disiplin Hasil Pengawasan (Inisial & Status)', null],
        ];

        $order = [];
        foreach ($rows as [$cat, $title, $desc]) {
            $order[$cat] = ($order[$cat] ?? 0) + 1;
            Document::create([
                'category'    => $cat,
                'title'       => $title,
                'description' => $desc ? "Periode: $desc" : null,
                'sort_order'  => $order[$cat],
                'is_active'   => true,
                // Unggah berkas / isi tautan melalui panel admin; tombol muncul setelah berkas tersedia.
            ]);
        }
    }

    protected function officials(): void
    {
        if (Official::count() > 0) {
            return;
        }

        $ketua = Official::create(['name' => 'Dr. H. Fulan Bin Fulan, S.H., M.H.', 'position' => 'Ketua Pengadilan Agama', 'rank' => 'Pembina Utama Muda (IV/c)', 'category' => 'pejabat', 'sort_order' => 1, 'is_lhkpn' => true]);
        Official::create(['name' => 'H. Ahmad Fulani, S.Ag., M.H.', 'position' => 'Panitera', 'rank' => 'Pembina (IV/a)', 'category' => 'pejabat', 'sort_order' => 2, 'is_lhkpn' => true]);
        Official::create(['name' => 'Siti Fulanah, S.E., M.M.', 'position' => 'Sekretaris', 'rank' => 'Pembina (IV/a)', 'category' => 'pejabat', 'sort_order' => 3, 'is_lhkpn' => true]);

        foreach ([2024, 2025] as $year) {
            LhkpnReport::create(['official_id' => $ketua->id, 'year' => $year]);
        }
    }
}
