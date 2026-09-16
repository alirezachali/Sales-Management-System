(function () {
    'use strict';

    if (!window.APP_HOTKEYS) return;

    var config = window.APP_HOTKEYS;

    // نگاشت نام‌های نمایشی کلیدها به مقدار واقعی e.key
    var keyAliases = {
        esc: 'escape',
        return: 'enter',
        spacebar: 'space',
        up: 'arrowup',
        down: 'arrowdown',
        left: 'arrowleft',
        right: 'arrowright',
        ins: 'insert',
        del: 'delete',
        pgup: 'pageup',
        pgdn: 'pagedown',
    };

    function parseCombo(str) {
        var parts = String(str).split('+').map(function (p) {
            return p.trim();
        }).filter(Boolean);

        if (parts.length === 0) return null;

        var combo = { ctrl: false, alt: false, shift: false, meta: false, key: '' };
        var reserved = {
            ctrl: 'ctrl', control: 'ctrl',
            alt: 'alt', option: 'alt',
            shift: 'shift',
            meta: 'meta', win: 'meta', cmd: 'meta', command: 'meta',
        };

        parts.forEach(function (part) {
            var lower = part.toLowerCase();

            if (reserved[lower]) {
                combo[reserved[lower]] = true;
            } else {
                combo.key = lower === ' ' ? 'space' : lower;
                combo.key = keyAliases[combo.key] || combo.key;
            }
        });

        return combo.key ? combo : null;
    }

    function matches(combo, e) {
        var key = e.key === ' ' ? 'space' : e.key.toLowerCase();

        return combo.ctrl === e.ctrlKey &&
            combo.alt === e.altKey &&
            combo.shift === e.shiftKey &&
            combo.meta === e.metaKey &&
            combo.key === key;
    }

    function isTypingTarget(el) {
        if (!el) return false;
        var tag = el.tagName;
        return tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || el.isContentEditable;
    }

    function visibleModal() {
        var modals = document.querySelectorAll('.modal.show');
        return modals.length ? modals[modals.length - 1] : null;
    }

    var handlers = {
        form_save: function (combo, e, modal) {
            if (!modal || combo.key !== 'enter') return false;

            var el = document.activeElement;

            // داخل textarea یا روی دکمه/لینک متمرکز، Enter دستکاری نمی‌شود
            if (el && (el.tagName === 'TEXTAREA' || el.isContentEditable ||
                el.tagName === 'SELECT' || el.tagName === 'BUTTON' || el.tagName === 'A')) {
                return false;
            }

            var submit = modal.querySelector('form button[type="submit"]');
            if (!submit || submit.disabled) return false;

            e.preventDefault();
            submit.click();
            return true;
        },

        form_cancel: function (combo, e, modal) {
            if (!modal || combo.key !== 'escape') return false;

            var closer = modal.querySelector('button[wire\\:click="closeModals"]') ||
                modal.querySelector('.btn-close');

            if (!closer) return false;

            e.preventDefault();
            closer.click();
            return true;
        },
    };

    document.addEventListener('keydown', function (e) {
        if (!config.enabled || !config.keys || e.isComposing) return;

        // هنگام ضبط کلید در صفحه تنظیمات، موتور نباید وارد عمل شود
        var active = document.activeElement;
        if (active && active.classList && active.classList.contains('hotkey-capture')) return;

        var modal = visibleModal();
        var typing = isTypingTarget(document.activeElement);

        Object.keys(config.keys).forEach(function (action) {
            var combo = parseCombo(config.keys[action]);
            if (!combo || !matches(combo, e)) return;

            var isFunctionKey = /^f\d{1,2}$/.test(combo.key);
            var hasModifier = combo.ctrl || combo.alt || combo.meta;

            // کلیدهای ساده (حروف/اعداد) فقط بیرون از فیلدها یا با مودیفایر اجرا می‌شوند؛
            // Enter و Esc استثنا هستند چون هندلر مخصوص خودشان بررسی‌شان را دارد.
            if (typing && !isFunctionKey && !hasModifier &&
                combo.key !== 'enter' && combo.key !== 'escape') {
                return;
            }

            if (handlers[action]) {
                handlers[action](combo, e, modal);
                return;
            }

            var target = document.querySelector('[data-hotkey="' + action + '"]');
            if (!target) return;

            e.preventDefault();

            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName)) {
                target.focus();
                if (target.select) target.select();
            } else {
                target.click();
            }
        });
    });
})();
