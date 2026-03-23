/**
 * Theme Manager SIRH CHNP
 * Gestion des thèmes : light, dark, system
 */

// Stockage des éléments modifiés par le mode sombre pour restauration
var _darkModifiedElements = [];

// Appliquer immédiatement au chargement (évite le flash)
(function () {
    var savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);
})();

function applyTheme(theme) {
    var effectiveTheme = theme;

    if (theme === 'system') {
        effectiveTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    document.documentElement.setAttribute('data-theme', effectiveTheme);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            handleInlineStyles(effectiveTheme);
        });
    } else {
        handleInlineStyles(effectiveTheme);
    }
}

function handleInlineStyles(theme) {
    if (theme === 'dark') {
        forceThemeOnInlineStyles();
    } else {
        restoreInlineStyles();
    }
}

/**
 * Restaure les styles inline modifiés par le mode sombre
 * Appelé quand on repasse en mode clair pour éviter les éléments sombres résiduels
 */
function restoreInlineStyles() {
    _darkModifiedElements.forEach(function (item) {
        if (item.origBgColor !== undefined) item.el.style.backgroundColor = item.origBgColor;
        if (item.origBg !== undefined)      item.el.style.background      = item.origBg;
        if (item.origBorder !== undefined)  item.el.style.borderColor     = item.origBorder;
        if (item.origColor !== undefined)   item.el.style.color           = item.origColor;
    });
    _darkModifiedElements = [];
}

/**
 * Corrige les éléments qui ont des styles inline avec fond blanc/clair
 * Sauvegarde les valeurs originales pour restauration ultérieure
 */
function forceThemeOnInlineStyles() {
    _darkModifiedElements = [];

    var whitePatterns = ['#fff', '#FFF', '#ffffff', '#FFFFFF', 'white', 'rgb(255, 255, 255)', 'rgb(255,255,255)'];

    // Parcourir tous les éléments avec style inline
    document.querySelectorAll('[style]').forEach(function (el) {
        var style = el.getAttribute('style') || '';

        var hasBg = style.includes('background-color') || style.includes('background:') || style.includes('background ');
        if (!hasBg) return;

        var isWhite = whitePatterns.some(function (p) { return style.includes(p); });
        if (!isWhite) return;

        // Ne pas modifier les boutons colorés ni les badges
        if (el.matches('.btn, .badge, .alert, [class*="bg-primary"], [class*="bg-success"], [class*="bg-danger"], [class*="bg-warning"]')) return;

        var record = {
            el: el,
            origBgColor: el.style.backgroundColor,
            origBg:      el.style.background,
            origBorder:  el.style.borderColor,
            origColor:   el.style.color
        };

        el.style.backgroundColor = '#161b22';
        el.style.borderColor = '#30363d';
        if (!el.style.color || el.style.color === 'rgb(0, 0, 0)' || el.style.color === '#000' || el.style.color === '#111827') {
            record.origColor = el.style.color;
            el.style.color = '#e6edf3';
        }

        _darkModifiedElements.push(record);
    });

    // Corriger les gradients blancs/clairs
    document.querySelectorAll('[style*="linear-gradient"]').forEach(function (el) {
        var style = el.getAttribute('style') || '';
        if (!el.matches('.btn, .badge, .sidebar, .sidebar *')) {
            var hasBg = style.includes('linear-gradient(135deg, #F8FAFF') ||
                        style.includes('linear-gradient(135deg, #f') ||
                        style.includes('#E3F2FD') || style.includes('#EFF6FF') ||
                        style.includes('#F8FAFF') || style.includes('#F0F7FF');
            if (hasBg) {
                var record = {
                    el: el,
                    origBgColor: el.style.backgroundColor,
                    origBg:      el.style.background,
                    origBorder:  el.style.borderColor,
                    origColor:   el.style.color
                };
                el.style.background = 'linear-gradient(135deg, #161b22 0%, #21262d 100%)';
                _darkModifiedElements.push(record);
            }
        }
    });
}

// Réagir aux changements de préférence système
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
    var currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'system') {
        applyTheme('system');
    }
});

// Alpine.js component
document.addEventListener('alpine:init', function () {
    Alpine.data('themeManager', function () {
        return {
            currentTheme: localStorage.getItem('theme') || 'light',

            init: function () {
                applyTheme(this.currentTheme);
            },

            setTheme: function (theme) {
                this.currentTheme = theme;
                localStorage.setItem('theme', theme);
                applyTheme(theme);
                this.saveToServer(theme);
            },

            getIcon: function () {
                if (this.currentTheme === 'light') return 'fa-sun';
                if (this.currentTheme === 'dark') return 'fa-moon';
                return 'fa-desktop';
            },

            getLabel: function () {
                if (this.currentTheme === 'light') return 'Clair';
                if (this.currentTheme === 'dark') return 'Sombre';
                return 'Système';
            },

            saveToServer: function (theme) {
                var csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) return;

                fetch('/preferences/theme', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ theme: theme })
                }).catch(function (err) {
                    console.log('Theme sync:', err);
                });
            }
        };
    });
});
