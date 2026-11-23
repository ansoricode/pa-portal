/* Fitur aksesibilitas (WCAG): perbesar/perkecil teks, kontras tinggi, pembaca layar */
(function () {
    var fontSize = 16, speaking = false;

    function applyFont() {
        document.documentElement.style.setProperty('--base-font-size', fontSize + 'px');
    }
    try {
        var saved = parseInt(localStorage.getItem('a11y_font'), 10);
        if (saved >= 12 && saved <= 24) fontSize = saved;
        if (localStorage.getItem('a11y_contrast') === '1') document.body.classList.add('high-contrast');
    } catch (e) {}
    applyFont();

    function label(text) {
        var el = document.getElementById('srLabel');
        if (el) el.textContent = text;
    }

    window.toggleA11yPanel = function () {
        document.getElementById('a11yPanel').classList.toggle('show');
    };
    window.adjustFontSize = function (delta) {
        fontSize = Math.min(24, Math.max(12, fontSize + delta));
        applyFont();
        try { localStorage.setItem('a11y_font', fontSize); } catch (e) {}
    };
    window.toggleHighContrast = function () {
        document.body.classList.toggle('high-contrast');
        try { localStorage.setItem('a11y_contrast', document.body.classList.contains('high-contrast') ? '1' : '0'); } catch (e) {}
    };
    window.toggleScreenReader = function () {
        if (!('speechSynthesis' in window)) return;
        if (speaking) {
            window.speechSynthesis.cancel();
            speaking = false;
            label('Aktifkan Pembaca Layar');
            return;
        }
        var main = document.getElementById('main-content');
        var u = new SpeechSynthesisUtterance((main ? main.innerText : document.body.innerText).slice(0, 1500));
        u.lang = 'id-ID';
        u.onend = function () { speaking = false; label('Aktifkan Pembaca Layar'); };
        window.speechSynthesis.speak(u);
        speaking = true;
        label('Hentikan Pembaca Layar');
    };
    window.resetA11y = function () {
        fontSize = 16;
        applyFont();
        document.body.classList.remove('high-contrast');
        try { localStorage.removeItem('a11y_font'); localStorage.removeItem('a11y_contrast'); } catch (e) {}
        if ('speechSynthesis' in window) window.speechSynthesis.cancel();
        speaking = false;
        label('Aktifkan Pembaca Layar');
    };

    // Delegasi klik (tanpa atribut onclick agar kompatibel dengan Content-Security-Policy)
    document.addEventListener('click', function (e) {
        var el = e.target.closest('[data-a11y]');
        if (!el) return;
        switch (el.getAttribute('data-a11y')) {
            case 'toggle': window.toggleA11yPanel(); break;
            case 'font-up': window.adjustFontSize(2); break;
            case 'font-down': window.adjustFontSize(-2); break;
            case 'contrast': window.toggleHighContrast(); break;
            case 'reader': window.toggleScreenReader(); break;
            case 'reset': window.resetA11y(); break;
        }
    });
})();
