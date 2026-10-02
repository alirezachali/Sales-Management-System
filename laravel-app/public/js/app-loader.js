/**
 * لودر سراسری: نمایش هنگام پردازش Livewire، ناوبری و ارسال فرم.
 * درخواست‌های کوتاه و poll نمایش داده نمی‌شوند.
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
                }
            }, 280);
        }
    }

    function begin() {
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
            clearTimeout(showTimer);
            showTimer = null;
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
    };

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.getAttribute('data-no-loader') != null) {
            return;
        }
        if (form.hasAttribute('wire:submit') || form.hasAttribute('wire:submit.prevent')) {
            return;
        }
        begin();
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
            begin();
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

    document.addEventListener('livewire:navigating', begin);
    document.addEventListener('livewire:navigated', end);

    if (document.readyState === 'loading') {
        begin();
        document.addEventListener('DOMContentLoaded', end);
    }
})();
