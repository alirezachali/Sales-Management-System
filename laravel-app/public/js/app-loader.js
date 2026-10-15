/**
 * لودر سراسری: نمایش هنگام پردازش Livewire، ناوبری و ارسال فرم.
 * درخواست‌های کوتاه و poll نمایش داده نمی‌شوند.
 *
 * متن لودر از روی متد در حال اجرا انتخاب می‌شود (app-loader-messages.js) تا در
 * عملیات سنگین مثل خروجی اکسل، پشتیبان‌گیری یا نهایی‌سازی موجودی پیام مناسب دیده شود.
 */
(function () {
    var DELAY_MS = 220;
    var MIN_VISIBLE_MS = 320;
    var count = 0;
    var showTimer = null;
    var shownAt = 0;
    var el = null;

    function node() {
        if (!el) {
            el = document.getElementById('app-loader');
        }
        return el;
    }

    function texts() {
        return (typeof window !== 'undefined' && window.AppLoaderMessages) || null;
    }

    function textNode() {
        var n = node();
        if (!n) {
            return null;
        }
        return n.querySelector('.app-loader__text');
    }

    /** تعیین متن لودر پیش از نمایش؛ متن خالی به پیام پیش‌فرض برمی‌گردد. */
    function setMessage(message) {
        var target = textNode();
        if (!target) {
            return;
        }

        var dict = texts();
        var fallback = (dict && dict.fallback) || 'در حال پردازش';
        var text = (message && String(message).trim()) || fallback;

        if (target.textContent === text) {
            return;
        }

        target.textContent = text;

        // اگر لودر همین حالا دیده می‌شود، تغییر متن را نرم نشان بده
        var n = node();
        var caption = n && n.querySelector('.app-loader__caption');
        if (caption && n.classList.contains('is-visible')) {
            caption.classList.remove('is-swapping');
            void caption.offsetWidth;
            caption.classList.add('is-swapping');
        }
    }

    /** پیام مناسب برای یک commit لایووایر. */
    function commitMessage(commit) {
        var dict = texts();
        return dict ? dict.forCommit(commit) : '';
    }

    /** پیام دستی روی خود فرم یا دکمه ارسال: data-loader-message="..." */
    function formMessage(form, submitter) {
        if (!form || !form.getAttribute) {
            return '';
        }

        var fromButton = submitter && submitter.getAttribute
            ? submitter.getAttribute('data-loader-message')
            : null;
        var fromForm = form.getAttribute('data-loader-message');

        return fromButton || fromForm || '';
    }

    function apply(visible) {
        var n = node();
        if (!n) {
            return;
        }
        if (visible) {
            n.hidden = false;
            n.setAttribute('aria-busy', 'true');
            requestAnimationFrame(function () {
                n.classList.add('is-visible');
            });
            shownAt = Date.now();
        } else {
            n.classList.remove('is-visible');
            n.setAttribute('aria-busy', 'false');
            setTimeout(function () {
                if (count === 0 && n) {
                    n.hidden = true;
                    setMessage('');
                    var caption = n.querySelector('.app-loader__caption');
                    if (caption) {
                        caption.classList.remove('is-swapping');
                    }
                }
            }, 280);
        }
    }

    function begin(message) {
        setMessage(message);
        count += 1;
        if (count === 1 && !showTimer) {
            showTimer = setTimeout(function () {
                showTimer = null;
                if (count > 0) {
                    apply(true);
                }
            }, DELAY_MS);
        }
    }

    function end() {
        count = Math.max(0, count - 1);
        if (count > 0) {
            return;
        }
        if (showTimer) {
            // هنوز چیزی نمایش داده نشده؛ فقط پیام آماده‌شده را پاک کن
            clearTimeout(showTimer);
            showTimer = null;
            setMessage('');
            var n = node();
            var caption = n && n.querySelector('.app-loader__caption');
            if (caption) {
                caption.classList.remove('is-swapping');
            }
            return;
        }
        var wait = Math.max(0, MIN_VISIBLE_MS - (Date.now() - shownAt));
        setTimeout(function () {
            if (count === 0) {
                apply(false);
            }
        }, wait);
    }

    function isBackgroundCommit(commit) {
        if (!commit) {
            return false;
        }
        var calls = commit.calls || [];
        if (!calls.length) {
            return false;
        }
        return calls.every(function (c) {
            var m = (c && c.method) ? String(c.method) : '';
            return m === '$refresh' || m === 'refresh' || m.indexOf('poll') === 0;
        });
    }

    window.AppLoader = {
        show: begin,
        hide: end,
        setMessage: setMessage
    };

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.getAttribute('data-no-loader') != null) {
            return;
        }
        if (form.hasAttribute('wire:submit') || form.hasAttribute('wire:submit.prevent')) {
            return;
        }
        begin(formMessage(form, e.submitter));
    }, true);

    window.addEventListener('beforeunload', function () {
        begin();
    });

    function bindLivewire() {
        if (!window.Livewire || typeof Livewire.hook !== 'function' || Livewire.__appLoaderBound) {
            return;
        }
        Livewire.__appLoaderBound = true;
        Livewire.hook('commit', function (ctx) {
            if (isBackgroundCommit(ctx.commit)) {
                return;
            }
            begin(commitMessage(ctx.commit));
            if (typeof ctx.succeed === 'function') {
                ctx.succeed(end);
            }
            if (typeof ctx.fail === 'function') {
                ctx.fail(end);
            }
        });
    }

    document.addEventListener('livewire:init', bindLivewire);
    bindLivewire();

    document.addEventListener('livewire:navigating', function () {
        begin();
    });
    document.addEventListener('livewire:navigated', end);

    if (document.readyState === 'loading') {
        begin();
        document.addEventListener('DOMContentLoaded', end);
    }
})();
