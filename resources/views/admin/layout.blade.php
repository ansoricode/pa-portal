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
        .ql-editor img { max-width:100%; height:auto; cursor:pointer; }
        .ql-editor img.img-left { float:left; margin:.25rem 1rem .5rem 0; }
        .ql-editor img.img-right { float:right; margin:.25rem 0 .5rem 1rem; }
        .ql-editor img.img-center { display:block; float:none; margin:.5rem auto; }
        .ql-editor img.img-w-25 { width:25%; }
        .ql-editor img.img-w-50 { width:50%; }
        .ql-editor img.img-w-75 { width:75%; }
        .ql-editor img.img-w-100 { width:100%; }
        .ql-editor img.img-selected { outline:3px solid var(--pa-secondary); }
        .img-toolbar { position:absolute; z-index:20; display:none; gap:.25rem; flex-wrap:wrap; max-width:340px; padding:.35rem; background:#fff; border:1px solid #ced4da; border-radius:.375rem; box-shadow:0 .25rem .75rem rgba(0,0,0,.15); }
        .img-toolbar button { border:1px solid #ced4da; background:#f8f9fa; border-radius:.25rem; font-size:.78rem; padding:.15rem .5rem; }
        .img-toolbar button:hover, .img-toolbar button.active { background:var(--pa-primary); color:#fff; border-color:var(--pa-primary); }
        .img-toolbar .sep { width:100%; height:0; }
        .tag-input { display:flex; flex-wrap:wrap; gap:.35rem; align-items:center; height:auto; min-height:38px; cursor:text; }
        .tag-input.is-invalid { border-color:#dc3545; }
        .tag-input-chips { display:contents; }
        .tag-chip { display:inline-flex; align-items:center; gap:.3rem; background:#e3f1ea; color:var(--pa-primary-dark); border-radius:999px; padding:.1rem .35rem .1rem .7rem; font-size:.85rem; }
        .tag-chip button { border:0; background:transparent; color:inherit; line-height:1; padding:0 .25rem; font-size:1.05rem; }
        .tag-chip button:hover { color:#dc3545; }
        .tag-input-field { flex:1 1 140px; min-width:140px; border:0; outline:0; padding:.15rem 0; background:transparent; }
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
    // Perataan paragraf memakai kelas (ql-align-*), bukan style inline, agar lolos sanitizer HTML.
    Quill.register(Quill.import('attributors/class/align'), true);

    // Gambar: posisi (img-left/center/right) dan lebar (img-w-25/50/75/100) disimpan sebagai kelas.
    var IMG_ALIGNS = ['left', 'center', 'right'];
    var IMG_SIZES = ['25', '50', '75', '100'];
    var BaseImage = Quill.import('formats/image');
    class ImageBlot extends BaseImage {
        static formats(node) {
            var formats = BaseImage.formats(node);
            IMG_ALIGNS.forEach(function (a) { if (node.classList.contains('img-' + a)) formats.imgalign = a; });
            IMG_SIZES.forEach(function (s) { if (node.classList.contains('img-w-' + s)) formats.imgsize = s; });
            return formats;
        }
        format(name, value) {
            if (name === 'imgalign') {
                IMG_ALIGNS.forEach(function (a) { this.domNode.classList.remove('img-' + a); }, this);
                if (value) this.domNode.classList.add('img-' + value);
            } else if (name === 'imgsize') {
                IMG_SIZES.forEach(function (s) { this.domNode.classList.remove('img-w-' + s); }, this);
                if (value) this.domNode.classList.add('img-w-' + value);
            } else {
                super.format(name, value);
            }
        }
    }
    Quill.register(ImageBlot, true);

    var imageToolbar = document.createElement('div');
    imageToolbar.className = 'img-toolbar';
    [['imgalign', 'left', 'Kiri'], ['imgalign', 'center', 'Tengah'], ['imgalign', 'right', 'Kanan'], ['imgalign', '', 'Normal'], ['sep'],
        ['imgsize', '25', '25%'], ['imgsize', '50', '50%'], ['imgsize', '75', '75%'], ['imgsize', '100', '100%'], ['imgsize', '', 'Asli']].forEach(function (def) {
        if (def[0] === 'sep') {
            var sep = document.createElement('div');
            sep.className = 'sep';
            imageToolbar.appendChild(sep);
            return;
        }
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = def[2];
        btn.dataset.format = def[0];
        btn.dataset.value = def[1];
        imageToolbar.appendChild(btn);
    });
    document.body.appendChild(imageToolbar);
    var activeImage = null;
    var activeQuill = null;

    function hideImageToolbar() {
        if (activeImage) activeImage.classList.remove('img-selected');
        activeImage = null;
        activeQuill = null;
        imageToolbar.style.display = 'none';
    }

    function refreshImageToolbar() {
        if (!activeImage) return;
        var rect = activeImage.getBoundingClientRect();
        imageToolbar.style.display = 'flex';
        imageToolbar.style.top = (window.scrollY + rect.bottom + 6) + 'px';
        imageToolbar.style.left = (window.scrollX + Math.max(8, rect.left)) + 'px';
        imageToolbar.querySelectorAll('button').forEach(function (btn) {
            var prefix = btn.dataset.format === 'imgalign' ? 'img-' : 'img-w-';
            var group = btn.dataset.format === 'imgalign' ? IMG_ALIGNS : IMG_SIZES;
            var current = group.filter(function (v) { return activeImage.classList.contains(prefix + v); })[0] || '';
            btn.classList.toggle('active', current === btn.dataset.value);
        });
    }

    imageToolbar.addEventListener('mousedown', function (event) { event.preventDefault(); });
    imageToolbar.addEventListener('click', function (event) {
        var btn = event.target.closest('button');
        if (!btn || !activeImage || !activeQuill) return;
        var blot = Quill.find(activeImage);
        if (!blot) { hideImageToolbar(); return; }
        activeQuill.formatText(activeQuill.getIndex(blot), 1, btn.dataset.format, btn.dataset.value || false, 'user');
        refreshImageToolbar();
    });
    document.addEventListener('mousedown', function (event) {
        if (!imageToolbar.contains(event.target) && !(event.target.tagName === 'IMG' && event.target.closest('.ql-editor'))) hideImageToolbar();
    });
    window.addEventListener('resize', refreshImageToolbar);

    // Editor teks kaya (Quill): salin HTML ke textarea tersembunyi saat form dikirim
    document.querySelectorAll('.quill-editor').forEach(function (el) {
        var target = document.getElementById(el.dataset.target);
        var quill = new Quill(el, {
            theme: 'snow',
            modules: { toolbar: [[{ header: [2, 3, false] }], ['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], [{ align: [] }], ['blockquote', 'link'], ['clean']] }
        });
        quill.root.addEventListener('click', function (event) {
            if (event.target.tagName !== 'IMG') return;
            if (activeImage) activeImage.classList.remove('img-selected');
            activeImage = event.target;
            activeQuill = quill;
            activeImage.classList.add('img-selected');
            refreshImageToolbar();
        });
        quill.on('text-change', function () {
            if (activeImage && !document.body.contains(activeImage)) hideImageToolbar();
        });
        quill.savedRange = { index: Math.max(0, quill.getLength() - 1), length: 0 };
        el.quillInstance = quill;
        quill.on('selection-change', function (range) {
            if (range) quill.savedRange = range;
        });
        el.closest('form').addEventListener('submit', function () {
            hideImageToolbar();
            target.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
        });
    });
    document.querySelectorAll('[data-tag-input]').forEach(function (box) {
        var store = box.querySelector('textarea');
        var field = box.querySelector('.tag-input-field');
        var chips = box.querySelector('.tag-input-chips');
        var counter = box.parentNode.querySelector('[data-tag-count]');
        var tags = [];

        function sync() {
            store.value = tags.join('\n');
            if (counter) counter.textContent = tags.length;
            chips.textContent = '';
            tags.forEach(function (tag, index) {
                var chip = document.createElement('span');
                chip.className = 'tag-chip';
                chip.appendChild(document.createTextNode(tag));
                var remove = document.createElement('button');
                remove.type = 'button';
                remove.setAttribute('aria-label', 'Hapus ' + tag);
                remove.textContent = '×';
                remove.addEventListener('click', function () { tags.splice(index, 1); sync(); field.focus(); });
                chip.appendChild(remove);
                chips.appendChild(chip);
            });
        }

        function addTags(text) {
            text.split(/[\r\n,]+/).forEach(function (part) {
                var tag = part.trim();
                if (tag && !tags.some(function (t) { return t.toLowerCase() === tag.toLowerCase(); })) tags.push(tag);
            });
            sync();
        }

        field.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                addTags(field.value);
                field.value = '';
            } else if (event.key === 'Backspace' && !field.value && tags.length) {
                tags.pop();
                sync();
            }
        });
        field.addEventListener('paste', function (event) {
            var text = (event.clipboardData || window.clipboardData).getData('text');
            if (/[\r\n,]/.test(text)) {
                event.preventDefault();
                addTags(text);
            }
        });
        field.addEventListener('blur', function () { addTags(field.value); field.value = ''; });
        box.addEventListener('click', function () { field.focus(); });
        box.closest('form').addEventListener('submit', function () { addTags(field.value); field.value = ''; });
        addTags(store.value);
    });
    document.querySelectorAll('[data-map-input]').forEach(function (input) {
        var group = input.closest('.mb-3');
        var status = group.querySelector('[data-map-status]');
        var preview = group.querySelector('[data-map-preview]');

        // Kode iframe dari Google Maps otomatis diubah menjadi URL-nya saja
        function extractSrc() {
            var m = input.value.match(/<iframe[^>]*\ssrc=["']([^"']+)["']/i);
            if (m) input.value = m[1].replace(/&amp;/g, '&');
        }

        function embedUrl(url) {
            var m;
            if (!/^https:\/\/(www\.google\.com|maps\.google\.com)\/maps/.test(url)) return null;
            if (url.indexOf('https://www.google.com/maps/embed') === 0) return url;
            if ((m = url.match(/\/maps\/place\/([\w%+.,-]+)/)) || (m = url.match(/[?&](?:q|query)=([\w%+.,-]+)/))) return 'https://www.google.com/maps?q=' + m[1] + '&output=embed';
            if ((m = url.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/))) return 'https://www.google.com/maps?q=' + m[1] + ',' + m[2] + '&output=embed';
            return null;
        }

        input.addEventListener('input', extractSrc);
        input.addEventListener('paste', function () { setTimeout(extractSrc, 0); });
        group.querySelector('[data-map-check]').addEventListener('click', function () {
            extractSrc();
            var url = input.value.trim();
            status.className = 'form-text';
            preview.classList.add('d-none');
            if (!url) {
                status.textContent = 'Tautan masih kosong.';
                return;
            }
            var embed = embedUrl(url);
            if (!embed) {
                status.textContent = 'Tautan tidak dikenali. Gunakan Bagikan > Sematkan peta di Google Maps (tautan pendek maps.app.goo.gl tidak bisa disematkan).';
                status.classList.add('text-danger');
                return;
            }
            status.textContent = 'Tautan valid.';
            status.classList.add('text-success');
            preview.src = embed;
            preview.classList.remove('d-none');
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
