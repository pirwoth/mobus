/**
 * mobus-theme.js
 * Bottom-right dropdown theme switcher — 4 themes persisted via localStorage.
 * Themes: dark | lime-dark | light-lime | light-warm
 */
(function () {
    var STORAGE_KEY = 'mobus_theme';

    var THEMES = [
        {
            key:    'dark',
            name:   'Deep Dark',
            desc:   'Charcoal dark, white buttons',
            cls:    'swatch-dark',
            swatch: '#2f2f2f',
        },
        {
            key:    'lime-dark',
            name:   'Lime Dark',
            desc:   'Forest green, bright lime',
            cls:    'swatch-lime-dark',
            swatch: null, // gradient — set inline
        },
        {
            key:    'light-lime',
            name:   'Lime Light',
            desc:   'Crisp white, emerald green',
            cls:    'swatch-light-lime',
            swatch: null,
        },
        {
            key:    'light-warm',
            name:   'Warm Light',
            desc:   'Creamy sand, amber accents',
            cls:    'swatch-light-warm',
            swatch: null,
        },
    ];

    var SWATCH_GRADIENTS = {
        'dark':        '#2f2f2f',
        'lime-dark':   'linear-gradient(135deg,#181f1b 50%,#2dd17a 50%)',
        'light-lime':  'linear-gradient(135deg,#f0f9f5 50%,#0d9e62 50%)',
        'light-warm':  'linear-gradient(135deg,#faf6f1 50%,#c77c37 50%)',
    };

    var _switcher = null;

    /* ── Apply a theme ─────────────────────────────────────── */
    function applyTheme(key) {
        document.documentElement.setAttribute('data-theme', key);
        document.body.setAttribute('data-theme', key);
        localStorage.setItem(STORAGE_KEY, key);
        syncUI(key);
    }

    /* ── Sync picker UI to current theme ───────────────────── */
    function syncUI(key) {
        if (!_switcher) return;
        var theme = THEMES.find(function (t) { return t.key === key; });

        // Update trigger label + swatch
        var lbl = _switcher.querySelector('.theme-trigger-label');
        if (lbl && theme) lbl.textContent = theme.name;

        var activeSw = _switcher.querySelector('.theme-active-swatch');
        if (activeSw) activeSw.style.background = SWATCH_GRADIENTS[key] || '#2f2f2f';

        // Update option rows
        _switcher.querySelectorAll('.theme-option').forEach(function (el) {
            var active = el.getAttribute('data-theme') === key;
            el.classList.toggle('active', active);
        });
    }

    /* ── Toggle dropdown open/closed ───────────────────────── */
    function toggleDropdown() {
        _switcher.classList.toggle('open');
    }

    function closeDropdown() {
        _switcher.classList.remove('open');
    }

    /* ── Build the DOM ─────────────────────────────────────── */
    function buildSwitcher() {
        if (document.getElementById('theme-switcher')) {
            _switcher = document.getElementById('theme-switcher');
            return;
        }

        /* Wrapper */
        var sw = document.createElement('div');
        sw.id = 'theme-switcher';
        sw.className = 'theme-switcher';

        /* ── Dropdown panel (built first so it's behind trigger in DOM) */
        var dropdown = document.createElement('div');
        dropdown.className = 'theme-dropdown';

        THEMES.forEach(function (t) {
            var row = document.createElement('button');
            row.type = 'button';
            row.className = 'theme-option';
            row.setAttribute('data-theme', t.key);
            row.innerHTML =
                '<span class="theme-option-swatch ' + t.cls + '"></span>' +
                '<span class="theme-option-info">' +
                    '<span class="theme-option-name">' + t.name + '</span>' +
                    '<span class="theme-option-desc">' + t.desc + '</span>' +
                '</span>' +
                '<span class="theme-option-check">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" ' +
                    'stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' +
                    '<polyline points="20 6 9 17 4 12"/></svg>' +
                '</span>';

            row.addEventListener('click', function () {
                applyTheme(t.key);
                closeDropdown();
            });
            dropdown.appendChild(row);
        });

        sw.appendChild(dropdown);

        /* ── Trigger button */
        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'theme-trigger';
        trigger.setAttribute('aria-label', 'Change colour theme');
        trigger.innerHTML =
            /* palette icon */
            '<span class="theme-trigger-icon">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" ' +
            'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
            '<circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/>' +
            '<circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/>' +
            '<circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/>' +
            '<circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/>' +
            '<path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/>' +
            '</svg></span>' +
            /* current theme label */
            '<span class="theme-trigger-label">Theme</span>' +
            /* active swatch dot */
            '<span class="theme-active-swatch"></span>' +
            /* chevron */
            '<span class="theme-trigger-chevron">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" ' +
            'stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' +
            '<polyline points="18 15 12 9 6 15"/></svg>' +
            '</span>';

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleDropdown();
        });

        sw.appendChild(trigger);
        document.body.appendChild(sw);
        _switcher = sw;

        /* Close when clicking outside */
        document.addEventListener('click', function (e) {
            if (_switcher && !_switcher.contains(e.target)) {
                closeDropdown();
            }
        });
    }

    /* ── Bootstrap ─────────────────────────────────────────── */
    function init() {
        var saved = localStorage.getItem(STORAGE_KEY) || 'dark';
        document.documentElement.setAttribute('data-theme', saved);
        document.body.setAttribute('data-theme', saved);
        buildSwitcher();
        syncUI(saved);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
