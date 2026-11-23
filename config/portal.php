<?php

return [

    // Seksi menu utama yang berisi halaman dinamis (tabel `pages`)
    'sections' => [
        'profil' => ['title' => 'Profil Pengadilan',           'heading' => 'Profil Pengadilan'],
        'layanan-hukum' => ['title' => 'Layanan Hukum',               'heading' => 'Layanan Hukum (Kepaniteraan)'],
        'transparansi' => ['title' => 'Transparansi',                'heading' => 'Transparansi & Akuntabilitas (Kesekretariatan)'],
        'layanan-publik' => ['title' => 'Layanan Publik & Pengaduan',  'heading' => 'Layanan Publik & Pengaduan'],
    ],

    'document_categories' => [
        'sakip' => 'SAKIP (Renstra, RKT, PK, IKU, LKjIP, Laporan Tahunan)',
        'keuangan' => 'Laporan Keuangan (DIPA, LRA, CALK)',
        'pbj' => 'Pengadaan Barang & Jasa (RUP)',
        'panjar' => 'Panjar Biaya & Radius Panggilan',
        'prosedur' => 'Prosedur Berperkara',
        'statistik' => 'Statistik & Laporan Biaya Perkara',
        'ppid' => 'Layanan Informasi (PPID)',
        'pengaduan' => 'Pengaduan & Hukuman Disiplin',
    ],

    // Kategori dokumen -> halaman tujuan (dipakai pencarian internal)
    'category_page' => [
        'sakip' => ['transparansi', 'sakip'],
        'keuangan' => ['transparansi', 'keuangan'],
        'pbj' => ['transparansi', 'pbj'],
        'panjar' => ['layanan-hukum', 'panjar'],
        'prosedur' => ['layanan-hukum', 'prosedur'],
        'statistik' => ['layanan-hukum', 'statistik'],
        'ppid' => ['layanan-publik', 'ppid'],
        'pengaduan' => ['layanan-publik', 'pengaduan'],
    ],

    'news_categories' => [
        'Inovasi Layanan' => 'Inovasi Layanan',
        'Pelayanan Masyarakat' => 'Pelayanan Masyarakat',
        'Opini & Gagasan Aparatur' => 'Opini & Gagasan Aparatur',
        'Pengumuman' => 'Pengumuman',
        'Kegiatan' => 'Kegiatan',
    ],

    'official_categories' => [
        'pejabat' => 'Pejabat',
        'pegawai' => 'Pegawai',
        'honorer' => 'Tenaga Honorer',
    ],

    // Pengaturan situs (tabel `settings`), dikelompokkan untuk form admin
    'settings' => [
        'Identitas Satker' => [
            ['name' => 'site_name',        'label' => 'Nama Satker',            'type' => 'text'],
            ['name' => 'site_tagline',     'label' => 'Instansi Induk',         'type' => 'text'],
            ['name' => 'logo',             'label' => 'Logo (PNG/JPG/WEBP)',    'type' => 'image'],
        ],
        'SEO & Tampilan' => [
            ['name' => 'seo_title',       'label' => 'Nama Situs untuk Judul SEO', 'type' => 'text', 'hint' => 'Ditampilkan bersama judul halaman di tab browser dan hasil pencarian.'],
            ['name' => 'site_description', 'label' => 'Deskripsi Meta (SEO)',       'type' => 'textarea', 'hint' => 'Ringkasan situs yang digunakan sebagai deskripsi bawaan halaman.'],
            ['name' => 'seo_keywords',    'label' => 'Kata Kunci SEO',              'type' => 'textarea', 'hint' => 'Pisahkan kata/frasa dengan koma.'],
            ['name' => 'favicon',         'label' => 'Favicon Situs (PNG/ICO)',     'type' => 'favicon', 'hint' => 'Disarankan gambar persegi 32×32 atau 48×48 piksel. PNG dan ICO, maks. 1 MB.'],
            ['name' => 'og_image',        'label' => 'Gambar Pratinjau Tautan',     'type' => 'image', 'hint' => 'Dipakai saat halaman dibagikan ke media sosial/aplikasi pesan. Disarankan 1200×630 piksel.'],
        ],
        'Alamat & Kontak' => [
            ['name' => 'address_street',   'label' => 'Jalan',                  'type' => 'text'],
            ['name' => 'address_village',  'label' => 'Desa/Kelurahan',         'type' => 'text'],
            ['name' => 'address_district', 'label' => 'Kecamatan',              'type' => 'text'],
            ['name' => 'address_province', 'label' => 'Provinsi & Kode Pos',    'type' => 'text'],
            ['name' => 'phone',            'label' => 'Telepon',                'type' => 'text'],
            ['name' => 'whatsapp',         'label' => 'WhatsApp PTSP',          'type' => 'text'],
            ['name' => 'email',            'label' => 'Email Resmi',            'type' => 'text'],
            ['name' => 'website',          'label' => 'Website Resmi',          'type' => 'text'],
        ],
        'Media Sosial' => [
            ['name' => 'facebook_url',  'label' => 'Facebook',  'type' => 'text'],
            ['name' => 'instagram_url', 'label' => 'Instagram', 'type' => 'text'],
            ['name' => 'youtube_url',   'label' => 'YouTube',   'type' => 'text'],
        ],
        'Tautan Sistem' => [
            ['name' => 'sipp_url',           'label' => 'SIPP Satker',                    'type' => 'text'],
            ['name' => 'ecourt_url',         'label' => 'e-Court MA RI',                  'type' => 'text'],
            ['name' => 'putusan_url',        'label' => 'Direktori Putusan MA RI',        'type' => 'text'],
            ['name' => 'putusan_satker_url', 'label' => 'Direktori Putusan Khusus Satker', 'type' => 'text'],
            ['name' => 'siwas_url',          'label' => 'SIWAS MARI',                     'type' => 'text'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Keamanan panel admin
    |--------------------------------------------------------------------------
    */
    // URL halaman login & prefix panel. Ganti lewat .env (jangan pakai nilai bawaan di produksi).
    'admin_login_path' => env('ADMIN_LOGIN_PATH', 'masuk-petugas'),
    'admin_path' => env('ADMIN_PATH', 'panel-satker'),
    // Daftar IP/CIDR yang boleh mengakses login & panel, pisahkan koma. Kosong = tanpa batasan.
    'admin_allowed_ips' => env('ADMIN_ALLOWED_IPS', ''),
    // Wajibkan semua pengguna panel memakai 2FA
    'require_2fa' => env('REQUIRE_2FA', true),
    '2fa_issuer' => env('APP_NAME', 'Portal Satker'),
    // true = CSP hanya melaporkan (untuk uji coba), false = CSP diberlakukan
    'csp_report_only' => env('CSP_REPORT_ONLY', false),

    'roles' => [
        'superadmin' => ['label' => 'Super Admin',          'resources' => '*', 'settings' => true,  'users' => true,  'logs' => true],
        'admin' => ['label' => 'Admin Konten',         'resources' => '*', 'settings' => true,  'users' => false, 'logs' => false],
        'editor' => ['label' => 'Editor Berita & Beranda', 'resources' => ['news', 'announcements', 'sliders', 'shortcuts', 'pages'], 'settings' => false, 'users' => false, 'logs' => false],
        'operator' => ['label' => 'Operator Kepaniteraan/Kesekretariatan', 'resources' => ['court_schedules', 'panggilan_ghaib', 'documents', 'jurisdictions', 'officials', 'lhkpn_reports', 'survey_indices'], 'settings' => false, 'users' => false, 'logs' => false],
    ],
];
