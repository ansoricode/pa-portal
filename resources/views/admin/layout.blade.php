<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Admin') - Panel Admin Portal Satker</title>
    @include('partials.favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <style>
        :root { --pa-primary:#056e4f; --pa-primary-dark:#034d37; --pa-secondary:#ff9900; }
        body { background:#f4f6f8; }
        .sidebar { width:260px; min-height:100vh; background:var(--pa-primary-dark); }
        .sidebar a { color:#d1e7dd; text-decoration:none; display:block; padding:.45rem 1rem; border-left:4px solid transparent; font-size:.92rem; }
        .sidebar a:hover { background:rgba(255,255,255,.08); color:#fff; }
        .sidebar a.active { background:var(--pa-primary); color:#fff; border-left-color:var(--pa-secondary); font-weight:600; }
        .sidebar .grp { color:var(--pa-secondary); font-size:.72rem; letter-spacing:.06em; text-transform:uppercase; padding:.9rem 1rem .25rem; font-weight:700; }
        .btn-pa { background:var(--pa-primary); color:#fff; border-color:var(--pa-primary); }
        .btn-pa:hover { background:var(--pa-primary-dark); color:#fff; }
        .ql-editor { min-height:240px; background:#fff; }
        .dashboard-chart-scroll { overflow-x:auto; }
        .dashboard-chart-svg { display:block; width:100%; min-width:720px; height:auto; }
    </style>
</head>
<body>
<div class="d-flex">
    <aside class="sidebar flex-shrink-0 d-none d-lg-block">
        <div class="p-3 text-white fw-bold border-bottom border-secondary">
            <i class="bi bi-bank2 me-1"></i> Panel Admin<br>
            <small class="fw-normal text-white-50">{{ $setting('site_name', 'Portal Satker') }}</small>
        </div>
        @include('admin.partials.menu')
    </aside>

    <div class="flex-grow-1" style="min-width:0">
        <nav class="navbar navbar-light bg-white border-bottom px-3">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-label="Menu"><i class="bi bi-list"></i></button>
                <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-box-arrow-up-right me-1"></i>Lihat Website</a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="small text-secondary d-none d-md-inline">{{ auth()->user()->name }} <span class="badge bg-light text-dark border">{{ auth()->user()->roleLabel() }}</span></span>
                <form method="POST" action="{{ route('admin.logout') }}">@csrf
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right me-1"></i>Keluar</button>
                </form>
            </div>
        </nav>

        <main class="p-3 p-md-4">
            @if(session('ok'))<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i>{{ session('ok') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if(session('warning'))<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-1"></i>{{ session('warning') }}</div>@endif
            @if($errors->has('db'))<div class="alert alert-danger">{{ $errors->first('db') }}</div>@endif
            @yield('content')
        </main>
    </div>
</div>

<div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="mobileMenu" style="background:var(--pa-primary-dark)!important">
    <div class="offcanvas-header"><h5 class="offcanvas-title">Menu Admin</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button></div>
    <div class="offcanvas-body p-0 sidebar" style="width:100%;min-height:0">
        @include('admin.partials.menu')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
@stack('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
    // Editor teks kaya (Quill): salin HTML ke textarea tersembunyi saat form dikirim
    document.querySelectorAll('.quill-editor').forEach(function (el) {
        var target = document.getElementById(el.dataset.target);
        var quill = new Quill(el, {
            theme: 'snow',
            modules: { toolbar: [[{ header: [2, 3, false] }], ['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['blockquote', 'link'], ['clean']] }
        });
        quill.savedRange = { index: Math.max(0, quill.getLength() - 1), length: 0 };
        el.quillInstance = quill;
        quill.on('selection-change', function (range) {
            if (range) quill.savedRange = range;
        });
        el.closest('form').addEventListener('submit', function () {
            target.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
        });
    });
    document.querySelectorAll('[data-insert-media]').forEach(function (button) {
        button.addEventListener('mousedown', function (event) { event.preventDefault(); });
        button.addEventListener('click', function () {
            var editor = button.closest('form').querySelector('.quill-editor');
            if (!editor || !editor.quillInstance) {
                console.error('Editor isi halaman tidak ditemukan untuk menyisipkan gambar.');
                return;
            }

            var quill = editor.quillInstance;
            var range = quill.getSelection() || quill.savedRange;
            var index = Math.min(range.index, quill.getLength() - 1);
            var length = Math.min(range.length, quill.getLength() - index - 1);
            if (length) quill.deleteText(index, length, 'user');
            quill.insertEmbed(index, 'image', button.dataset.insertMedia, 'user');
            quill.setSelection(index + 1, 0, 'user');
        });
    });
    document.querySelectorAll('[data-autosubmit]').forEach(function (el) {
        el.addEventListener('change', function () { el.form.submit(); });
    });
    document.querySelectorAll('[data-news-url-picker]').forEach(function (select) {
        var urlInput = document.getElementById(select.dataset.newsUrlPicker);
        if (!urlInput) {
            console.error('Kolom URL pengumuman tidak ditemukan.');
            return;
        }

        select.addEventListener('change', function () {
            if (select.value) urlInput.value = select.value;
        });
        urlInput.addEventListener('input', function () {
            if (urlInput.value !== select.value) select.value = '';
        });
    });
    document.querySelectorAll('[data-confirm]').forEach(function (f) {
        f.addEventListener('submit', function (e) { if (!confirm(f.dataset.confirm)) e.preventDefault(); });
    });
    document.querySelectorAll('[data-copy-url]').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = document.getElementById(button.dataset.copyUrl);
            if (!input || !input.value.trim()) {
                alert('Link indikator belum diisi.');
                return;
            }

            var url;
            try {
                url = new URL(input.value.trim(), window.location.origin);
            } catch (error) {
                alert('Link indikator tidak valid.');
                return;
            }
            if (url.protocol !== 'http:' && url.protocol !== 'https:') {
                alert('Link indikator harus menggunakan HTTP atau HTTPS.');
                return;
            }

            if (!navigator.clipboard || !navigator.clipboard.writeText) {
                input.focus();
                input.select();
                alert('Salin manual link yang sudah dipilih dengan Ctrl+C.');
                return;
            }
            navigator.clipboard.writeText(url.href).then(function () {
                var original = button.textContent;
                button.textContent = 'Tersalin';
                window.setTimeout(function () { button.textContent = original; }, 1500);
            }).catch(function () {
                alert('Link gagal disalin. Silakan salin langsung dari kolom URL.');
            });
        });
    });
</script>
</body>
</html>
