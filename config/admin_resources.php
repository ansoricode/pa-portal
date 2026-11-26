<?php

use App\Models\Announcement;
use App\Models\CourtSchedule;
use App\Models\Document;
use App\Models\Jurisdiction;
use App\Models\LhkpnReport;
use App\Models\News;
use App\Models\Official;
use App\Models\Page;
use App\Models\PanggilanGhaib;
use App\Models\Shortcut;
use App\Models\Slider;
use App\Models\SurveyIndex;
use App\Models\SurveyResponse;

/*
 | Definisi CRUD panel admin. Satu definisi = satu menu CRUD lengkap.
 | Tipe field: text, textarea, editor, number, date, datetime, select, relation, checkbox, image, file
 */
return [

    'sliders' => [
        'group' => 'Beranda', 'label' => 'Slider Beranda', 'icon' => 'bi-images',
        'model' => Slider::class, 'order' => ['sort_order', 'asc'],
        'search' => ['title', 'badge'], 'filters' => ['is_active' => ['label' => 'Status', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']]],
        'hint' => 'Maksimal 3–5 slide aktif sesuai SK 2722/2026 Bab III huruf C.',
        'columns' => ['title' => 'Judul', 'badge' => 'Label', 'sort_order' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            ['name' => 'badge', 'label' => 'Label Kecil (badge)', 'type' => 'text', 'rules' => 'nullable|string|max:100'],
            ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'rules' => 'required|string|max:200'],
            ['name' => 'subtitle', 'label' => 'Deskripsi', 'type' => 'textarea', 'rules' => 'nullable|string|max:500'],
            ['name' => 'button_text', 'label' => 'Teks Tombol', 'type' => 'text', 'rules' => 'nullable|string|max:100'],
            ['name' => 'button_url', 'label' => 'Tautan Tombol', 'type' => 'text', 'rules' => 'nullable|string|max:255', 'help' => 'Contoh: /layanan-hukum/ecourt atau https://...'],
            ['name' => 'image', 'label' => 'Gambar Latar (1920x544 disarankan)', 'type' => 'image'],
            ['name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number', 'default' => 0, 'rules' => 'nullable|integer|min:0'],
            ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'default' => true],
        ],
    ],

    'shortcuts' => [
        'group' => 'Beranda', 'label' => 'Pintasan Layanan', 'icon' => 'bi-lightning-charge',
        'model' => Shortcut::class, 'order' => ['sort_order', 'asc'],
        'search' => ['title', 'url', 'description'], 'filters' => ['is_active' => ['label' => 'Status', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']]],
        'columns' => ['title' => 'Judul', 'url' => 'Tautan', 'sort_order' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'rules' => 'required|string|max:120'],
            ['name' => 'description', 'label' => 'Deskripsi Singkat', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
            ['name' => 'icon', 'label' => 'Ikon Bootstrap Icons', 'type' => 'text', 'rules' => 'required|string|max:60', 'default' => 'bi-link-45deg', 'help' => 'Contoh: bi-search, bi-laptop. Daftar: icons.getbootstrap.com'],
            ['name' => 'url', 'label' => 'Tautan', 'type' => 'text', 'rules' => 'required|string|max:255'],
            ['name' => 'is_external', 'label' => 'Buka di tab baru (tautan eksternal)', 'type' => 'checkbox', 'default' => true],
            ['name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number', 'default' => 0, 'rules' => 'nullable|integer|min:0'],
            ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'default' => true],
        ],
    ],

    'announcements' => [
        'group' => 'Beranda', 'label' => 'Pengumuman Beranda', 'icon' => 'bi-megaphone',
        'model' => Announcement::class, 'order' => ['published_at', 'desc'],
        'search' => ['title', 'badge'], 'filters' => ['is_active' => ['label' => 'Status', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']]],
        'columns' => ['badge' => 'Label', 'title' => 'Judul', 'published_at' => 'Tanggal', 'view_count' => 'Pengunjung', 'is_active' => 'Aktif'],
        'fields' => [
            ['name' => 'badge', 'label' => 'Label (mis. Relaas Panggilan Ghaib)', 'type' => 'text', 'rules' => 'required|string|max:100'],
            ['name' => 'title', 'label' => 'Judul Pengumuman', 'type' => 'text', 'rules' => 'required|string|max:255'],
            ['name' => 'label', 'label' => 'Keterangan Tanggal (opsional)', 'type' => 'text', 'rules' => 'nullable|string|max:150', 'help' => 'Mis. "Jadwal Sidang: 10 Desember 2026". Jika kosong, tanggal terbit yang ditampilkan.'],
            ['name' => 'published_at', 'label' => 'Tanggal Terbit', 'type' => 'date', 'rules' => 'nullable|date'],
            ['name' => 'url', 'label' => 'Tautan (opsional)', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
            ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'default' => true],
        ],
    ],

    'survey_indices' => [
        'group' => 'Beranda', 'label' => 'Indeks Survei (IKM/IPKP/IPAK)', 'icon' => 'bi-graph-up',
        'model' => SurveyIndex::class, 'order' => ['sort_order', 'asc'],
        'search' => ['name', 'label'],
        'columns' => ['name' => 'Indeks', 'score' => 'Nilai', 'label' => 'Predikat'],
        'fields' => [
            ['name' => 'name', 'label' => 'Nama Indeks', 'type' => 'text', 'rules' => 'required|string|max:150'],
            ['name' => 'score', 'label' => 'Nilai', 'type' => 'text', 'rules' => 'required|string|max:20'],
            ['name' => 'label', 'label' => 'Predikat (mis. Sangat Baik)', 'type' => 'text', 'rules' => 'nullable|string|max:60'],
            ['name' => 'icon', 'label' => 'Ikon Bootstrap Icons', 'type' => 'text', 'rules' => 'required|string|max:60', 'default' => 'bi-emoji-smile'],
            ['name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number', 'default' => 0, 'rules' => 'nullable|integer|min:0'],
        ],
    ],

    'news' => [
        'group' => 'Berita', 'label' => 'Berita & Publikasi', 'icon' => 'bi-newspaper',
        'model' => News::class, 'order' => ['published_at', 'desc'], 'search' => ['title', 'category'],
        'filters' => ['category' => ['label' => 'Kategori', 'options_config' => 'portal.news_categories'], 'is_published' => ['label' => 'Tayang', 'options' => ['1' => 'Tayang', '0' => 'Draf']]],
        'columns' => ['title' => 'Judul', 'category' => 'Kategori', 'published_at' => 'Terbit', 'view_count' => 'Pengunjung', 'is_published' => 'Tayang'],
        'fields' => [
            ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'rules' => 'required|string|max:255'],
            ['name' => 'category', 'label' => 'Kategori', 'type' => 'select', 'options_config' => 'portal.news_categories', 'rules' => 'required|string|max:100'],
            ['name' => 'excerpt', 'label' => 'Ringkasan', 'type' => 'textarea', 'rules' => 'nullable|string|max:500'],
            ['name' => 'content', 'label' => 'Isi Berita', 'type' => 'editor', 'rules' => 'required|string'],
            ['name' => 'image', 'label' => 'Gambar Utama', 'type' => 'image'],
            ['name' => 'author', 'label' => 'Penulis', 'type' => 'text', 'rules' => 'nullable|string|max:150'],
            ['name' => 'published_at', 'label' => 'Waktu Terbit', 'type' => 'datetime', 'rules' => 'nullable|date'],
            ['name' => 'is_published', 'label' => 'Tayangkan', 'type' => 'checkbox', 'default' => true],
        ],
    ],

    'pages' => [
        'group' => 'Halaman Statis', 'label' => 'Halaman Menu', 'icon' => 'bi-file-earmark-text',
        'model' => Page::class, 'order' => ['section', 'asc'], 'order2' => ['sort_order', 'asc'], 'search' => ['title', 'slug'],
        'filters' => ['section' => ['label' => 'Seksi', 'options' => ['profil' => 'Profil', 'layanan-hukum' => 'Layanan Hukum', 'transparansi' => 'Transparansi', 'layanan-publik' => 'Layanan Publik']], 'is_active' => ['label' => 'Status', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']]],
        'hint' => 'Halaman menu Profil, Layanan Hukum, Transparansi, dan Layanan Publik. Tabel/daftar data (jadwal, dokumen, pejabat, dll.) dikelola di menu masing-masing dan tampil otomatis pada halaman dengan slug yang sama.',
        'columns' => ['section' => 'Seksi', 'title' => 'Judul', 'slug' => 'Slug', 'sort_order' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            ['name' => 'section', 'label' => 'Seksi Menu', 'type' => 'select', 'options' => [
                'profil' => 'Profil Pengadilan', 'layanan-hukum' => 'Layanan Hukum', 'transparansi' => 'Transparansi', 'layanan-publik' => 'Layanan Publik & Pengaduan',
            ], 'rules' => 'required|in:profil,layanan-hukum,transparansi,layanan-publik'],
            ['name' => 'slug', 'label' => 'Slug URL', 'type' => 'text', 'rules' => 'required|alpha_dash|max:80', 'help' => 'Huruf kecil, angka, tanda minus. Halaman bawaan: jangan ubah slug agar daftar data otomatis tetap tampil.'],
            ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'rules' => 'required|string|max:200'],
            ['name' => 'icon', 'label' => 'Ikon Sidebar (Bootstrap Icons)', 'type' => 'text', 'rules' => 'nullable|string|max:60'],
            ['name' => 'content', 'label' => 'Isi Halaman', 'type' => 'editor', 'rules' => 'nullable|string'],
            ['name' => 'image', 'label' => 'Gambar (peta, bagan struktur, foto gedung, dll.)', 'type' => 'image', 'help' => 'Setelah gambar disimpan, buka kembali halaman dan klik “Sisipkan di isi” untuk menaruhnya di posisi kursor.'],
            ['name' => 'media', 'label' => 'Galeri Media', 'type' => 'images', 'help' => 'Unggah maksimal 10 gambar per halaman (JPG, PNG, atau WEBP; maks. 4 MB per gambar). Simpan halaman, buka kembali, lalu klik “Sisipkan di isi” untuk menaruh gambar di posisi kursor. Gambar yang tidak disisipkan tampil di galeri bawah.'],
            ['name' => 'sort_order', 'label' => 'Urutan di Menu', 'type' => 'number', 'default' => 0, 'rules' => 'nullable|integer|min:0'],
            ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'default' => true],
        ],
    ],

    'court_schedules' => [
        'group' => 'Layanan Hukum', 'label' => 'Jadwal Persidangan', 'icon' => 'bi-calendar-event',
        'model' => CourtSchedule::class, 'order' => ['hearing_date', 'desc'], 'search' => ['case_number', 'agenda'],
        'columns' => ['hearing_date' => 'Tanggal', 'case_number' => 'Nomor Perkara', 'agenda' => 'Agenda', 'courtroom' => 'Ruang'],
        'fields' => [
            ['name' => 'hearing_date', 'label' => 'Tanggal Sidang', 'type' => 'date', 'rules' => 'required|date'],
            ['name' => 'case_number', 'label' => 'Nomor Perkara', 'type' => 'text', 'rules' => 'required|string|max:100'],
            ['name' => 'agenda', 'label' => 'Agenda Sidang', 'type' => 'text', 'rules' => 'required|string|max:150'],
            ['name' => 'courtroom', 'label' => 'Ruang Sidang', 'type' => 'text', 'rules' => 'required|string|max:100'],
        ],
    ],

    'panggilan_ghaib' => [
        'group' => 'Layanan Hukum', 'label' => 'Panggilan Ghaib', 'icon' => 'bi-telephone-inbound',
        'model' => PanggilanGhaib::class, 'order' => ['hearing_date', 'desc'], 'search' => ['party_name', 'case_number'],
        'columns' => ['party_name' => 'Nama Pihak', 'case_number' => 'Nomor Perkara', 'hearing_date' => 'Tanggal Sidang'],
        'fields' => [
            ['name' => 'party_name', 'label' => 'Nama Pihak', 'type' => 'text', 'rules' => 'required|string|max:200'],
            ['name' => 'case_number', 'label' => 'Nomor Perkara', 'type' => 'text', 'rules' => 'required|string|max:100'],
            ['name' => 'hearing_date', 'label' => 'Tanggal Sidang', 'type' => 'date', 'rules' => 'required|date'],
            ['name' => 'file_path', 'label' => 'Dokumen Relaas (PDF)', 'type' => 'file'],
            ['name' => 'external_url', 'label' => 'atau Tautan Dokumen', 'type' => 'text', 'rules' => 'nullable|url|max:255'],
        ],
    ],

    'sakip_documents' => [
        'group' => 'Dokumen', 'label' => 'Dokumen SAKIP', 'icon' => 'bi-clipboard-data',
        'model' => Document::class, 'fixed' => ['category' => 'sakip'],
        'order' => ['sort_order', 'asc'], 'search' => ['title', 'period'],
        'filters' => ['is_active' => ['label' => 'Status', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']]],
        'hint' => 'Kelola tujuh jenis dokumen SAKIP. Unggah berkas atau isi tautan; dokumen tanpa berkas/tautan tetap tampil sebagai "Segera tersedia".',
        'columns' => ['title' => 'Nama Dokumen', 'period' => 'Periode', 'sort_order' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            ['name' => 'title', 'label' => 'Nama Dokumen', 'type' => 'text', 'rules' => 'required|string|max:255'],
            ['name' => 'period', 'label' => 'Periode', 'type' => 'text', 'rules' => 'required|string|max:30', 'help' => 'Contoh: 2022–2026 atau 2025.'],
            ['name' => 'file_path', 'label' => 'Unggah Berkas', 'type' => 'file', 'rules' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png|max:20480'],
            ['name' => 'external_url', 'label' => 'atau Tautan Dokumen', 'type' => 'text', 'rules' => 'nullable|url|max:255', 'help' => 'Isi salah satu: unggah berkas atau tautan eksternal. Keduanya boleh dikosongkan untuk draf.'],
            ['name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number', 'default' => 0, 'rules' => 'nullable|integer|min:0'],
            ['name' => 'is_active', 'label' => 'Tampilkan di halaman publik', 'type' => 'checkbox', 'default' => true],
        ],
    ],

    'documents' => [
        'group' => 'Dokumen', 'label' => 'Dokumen & Laporan', 'icon' => 'bi-folder2-open',
        'model' => Document::class, 'order' => ['category', 'asc'], 'search' => ['title', 'category', 'period'],
        'filters' => ['category' => ['label' => 'Kategori', 'options_config' => 'portal.document_categories'], 'is_active' => ['label' => 'Status', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']]],
        'hint' => 'SAKIP, laporan keuangan, RUP, SK panjar, prosedur berperkara, statistik, PPID, dan pengaduan. Dokumen tampil otomatis di halaman sesuai kategori.',
        'columns' => ['category' => 'Kategori', 'title' => 'Judul', 'year' => 'Tahun', 'period' => 'Periode', 'is_active' => 'Aktif'],
        'fields' => [
            ['name' => 'category', 'label' => 'Kategori', 'type' => 'select', 'options_config' => 'portal.document_categories', 'rules' => 'required|string|max:50'],
            ['name' => 'title', 'label' => 'Judul Dokumen', 'type' => 'text', 'rules' => 'required|string|max:255'],
            ['name' => 'description', 'label' => 'Keterangan', 'type' => 'textarea', 'rules' => 'nullable|string|max:1000'],
            ['name' => 'year', 'label' => 'Tahun', 'type' => 'number', 'rules' => 'nullable|integer|min:2000|max:2100'],
            ['name' => 'period', 'label' => 'Periode (opsional)', 'type' => 'text', 'rules' => 'nullable|string|max:30'],
            ['name' => 'file_path', 'label' => 'Berkas (PDF/Office/ZIP)', 'type' => 'file'],
            ['name' => 'external_url', 'label' => 'atau Tautan Eksternal', 'type' => 'text', 'rules' => 'nullable|url|max:255'],
            ['name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number', 'default' => 0, 'rules' => 'nullable|integer|min:0'],
            ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'default' => true],
        ],
    ],

    'jurisdictions' => [
        'group' => 'Profil & Aparatur', 'label' => 'Wilayah Yurisdiksi', 'icon' => 'bi-geo-alt',
        'model' => Jurisdiction::class, 'order' => ['sort_order', 'asc'],
        'search' => ['name'],
        'columns' => ['name' => 'Nama Kecamatan/Kabupaten/Kota', 'sort_order' => 'Urutan'],
        'fields' => [
            ['name' => 'name', 'label' => 'Nama Wilayah', 'type' => 'text', 'rules' => 'required|string|max:150'],
            ['name' => 'villages', 'label' => 'Desa/Kelurahan', 'type' => 'tags', 'rules' => 'nullable|string|max:5000', 'help' => 'Ketik nama desa/kelurahan lalu tekan Enter atau koma. Anda juga bisa menempel banyak nama sekaligus.'],
            ['name' => 'radius', 'label' => 'Radius', 'type' => 'text', 'rules' => 'nullable|string|max:100', 'help' => 'Contoh: 15 km dari pengadilan.'],
            ['name' => 'map_url', 'label' => 'Peta Google Maps (embed)', 'type' => 'map', 'rules' => 'nullable|url:https|max:2000|starts_with:https://www.google.com/maps,https://maps.google.com/maps', 'help' => 'Di Google Maps pilih Bagikan > Sematkan peta, lalu tempel kode iframe atau URL-nya di sini. Klik "Cek peta" untuk pratinjau.'],
            ['name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number', 'default' => 0, 'rules' => 'nullable|integer|min:0'],
        ],
    ],

    'officials' => [
        'group' => 'Profil & Aparatur', 'label' => 'Pejabat & Pegawai', 'icon' => 'bi-people',
        'model' => Official::class, 'order' => ['sort_order', 'asc'], 'search' => ['name', 'position'],
        'filters' => ['category' => ['label' => 'Kategori', 'options_config' => 'portal.official_categories'], 'is_lhkpn' => ['label' => 'LHKPN', 'options' => ['1' => 'Wajib lapor', '0' => 'Tidak']], 'is_active' => ['label' => 'Status', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']]],
        'columns' => ['name' => 'Nama', 'position' => 'Jabatan', 'category' => 'Kategori', 'is_lhkpn' => 'Wajib LHKPN', 'is_active' => 'Aktif'],
        'fields' => [
            ['name' => 'name', 'label' => 'Nama Lengkap & Gelar', 'type' => 'text', 'rules' => 'required|string|max:200'],
            ['name' => 'position', 'label' => 'Jabatan', 'type' => 'text', 'rules' => 'required|string|max:150'],
            ['name' => 'rank', 'label' => 'Pangkat/Golongan', 'type' => 'text', 'rules' => 'nullable|string|max:100'],
            ['name' => 'nip', 'label' => 'NIP (opsional, tidak ditampilkan publik)', 'type' => 'text', 'rules' => 'nullable|string|max:30'],
            ['name' => 'category', 'label' => 'Kategori', 'type' => 'select', 'options_config' => 'portal.official_categories', 'rules' => 'required|in:pejabat,pegawai,honorer'],
            ['name' => 'photo', 'label' => 'Foto', 'type' => 'image'],
            ['name' => 'tmt', 'label' => 'TMT Jabatan', 'type' => 'date', 'rules' => 'nullable|date'],
            ['name' => 'education', 'label' => 'Riwayat Pendidikan', 'type' => 'textarea', 'rules' => 'nullable|string|max:2000'],
            ['name' => 'career', 'label' => 'Riwayat Jabatan', 'type' => 'textarea', 'rules' => 'nullable|string|max:2000'],
            ['name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number', 'default' => 0, 'rules' => 'nullable|integer|min:0'],
            ['name' => 'is_lhkpn', 'label' => 'Wajib lapor LHKPN/LHKASN', 'type' => 'checkbox', 'default' => false],
            ['name' => 'is_active', 'label' => 'Tampilkan', 'type' => 'checkbox', 'default' => true],
        ],
    ],

    'lhkpn_reports' => [
        'group' => 'Profil & Aparatur', 'label' => 'LHKPN / LHKASN', 'icon' => 'bi-shield-check',
        'model' => LhkpnReport::class, 'order' => ['year', 'desc'], 'with' => ['official'],
        'search' => ['official.name', 'year'],
        'hint' => 'Unggah ikhtisar kekayaan minimal 2 tahun terakhir. Pejabat harus ditandai "Wajib lapor" di menu Pejabat & Pegawai.',
        'columns' => ['official.name' => 'Pejabat', 'year' => 'Tahun'],
        'fields' => [
            ['name' => 'official_id', 'label' => 'Pejabat', 'type' => 'relation', 'model' => Official::class, 'label_column' => 'name', 'rules' => 'required|exists:officials,id'],
            ['name' => 'year', 'label' => 'Tahun Lapor', 'type' => 'number', 'rules' => 'required|integer|min:2000|max:2100'],
            ['name' => 'file_path', 'label' => 'Ikhtisar Kekayaan (PDF)', 'type' => 'file'],
            ['name' => 'external_url', 'label' => 'atau Tautan', 'type' => 'text', 'rules' => 'nullable|url|max:255'],
        ],
    ],

    'survey_responses' => [
        'group' => 'Survei', 'label' => 'Masukan Survei Masuk', 'icon' => 'bi-chat-square-text',
        'model' => SurveyResponse::class, 'order' => ['id', 'desc'],
        'search' => ['comment'], 'filters' => ['rating' => ['label' => 'Nilai', 'options' => ['4' => '4 - Sangat Baik', '3' => '3 - Baik', '2' => '2 - Cukup', '1' => '1 - Kurang']]],
        'create' => false, 'edit' => false,
        'columns' => ['rating' => 'Nilai (1-4)', 'comment' => 'Saran/Masukan', 'created_at' => 'Waktu'],
        'fields' => [],
    ],
];
