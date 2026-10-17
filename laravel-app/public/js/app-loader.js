/**
 * لودر سراسری: نمایش هنگام پردازش Livewire، ناوبری و ارسال فرم.
 * درخواست‌های کوتاه و poll نمایش داده نمی‌شوند.
 *
 * متن لودر از روی متد در حال اجرا انتخاب می‌شود (app-loader-messages.js) تا در
 * عملیات سنگین مثل خروجی اکسل، پشتیبان‌گیری یا نهایی‌سازی موجودی پیام مناسب دیده شود.
 *
 * پیشرفت واقعی: برای عملیات سنگینی که سمت سرور درصد گزارش می‌کنند (خروجی اکسل،
 * پشتیبان‌گیری، نهایی‌سازی انبارگردانی) یک توکن تصادفی ساخته می‌شود، به هدر
 * X-Progress-Token همان درخواست اضافه می‌شود و مرورگر هر چند صد میلی‌ثانیه
 * درصد را از /api/app-progress می‌پرسد. سرور فایل پیشرفت را در
 * storage/app/progress می‌نویسد (App\Support\Progress\ProgressReporter).
 * اگر پیشرفتی گزارش نشود، لودر مثل قبل در حالت نامعین (اسکنر) می‌ماند.
 */
(function () {
    var DELAY_MS = 220;
    var MIN_VISIBLE_MS = 320;
    // فاصله‌ی پرسیدن پیشرفت از سرور؛ تعادل بین روانی و بار روی سرور
    var POLL_MS = 400;
    var HEADER = 'X-Progress-Token';
    var TOKEN_PLACEHOLDER = 'PLACEHOLDER';
    var count = 0;
    var showTimer = null;
    var shownAt = 0;
    var el = null;

    // ---------- وضعیت پیشرفت واقعی ----------
    var token = null;
    var pollTimer = null;
    var determinate = false;
    var litBars = -1;
    var lastPercent = null;
    // شماره‌ی نوبت هر پولینگ؛ پاسخ‌هایی که دیر رسیده یا متعلق به عملیات
    // قبلی‌اند باید قبل از رندر رد شوند تا درصد کهنه دوباره ظاهر نشود.
    var pollSeq = 0;
    var appliedSeq = 0;

    function node() {
        if (!el) {
            el = document.getElementById('app-loader');
        }
        return el;
    }

    function texts() {
        return (typeof window !== 'undefined' && window.AppLoaderMessages) || null;
    }

    /*
    |--------------------------------------------------------------------|
    |                        پیشرفت واقعی (سرور)                          |
    |--------------------------------------------------------------------|
    */

    /** آدرس پایه‌ی خواندن پیشرفت؛ از خود مارکاپ لودر خوانده می‌شود. */
    function progressUrlTemplate() {
        var n = node();
        return (n && n.getAttribute('data-progress-url')) || '';
    }

    /** ساخت توکن تصادفی ۳۲ کاراکتری برای شناسایی همین عملیات. */
    function newToken() {
        var bytes = new Uint8Array(16);

        if (window.crypto && window.crypto.getRandomValues) {
            window.crypto.getRandomValues(bytes);
        } else {
            for (var i = 0; i < bytes.length; i++) {
                bytes[i] = Math.floor(Math.random() * 256);
            }
        }

        var out = '';

        for (var j = 0; j < bytes.length; j++) {
            out += ('0' + bytes[j].toString(16)).slice(-2);
        }

        return out;
    }

    /** المان‌های مربوط به نوار پیشرفت. */
    function progressNodes() {
        var n = node();

        if (!n) {
            return null;
        }

        return {
            row: n.querySelector('.app-loader__progress'),
            percent: n.querySelector('.app-loader__percent'),
            bars: n.querySelectorAll('.app-loader__bar')
        };
    }

    /** روشن/خاموش کردن حالت قطعی (پرتو روی موقعیت پیشرفت می‌ایستد). */
    function setDeterminate(on) {
        var n = node();

        if (!n || determinate === on) {
            return;
        }

        determinate = on;
        n.classList.toggle('is-determinate', on);

        if (!on) {
            lightBars(0, true);
        }
    }

    /**
     * میله‌های عبورشده را روشن می‌کند؛ یعنی خود بارکد هم میزان پیشرفت را نشان می‌دهد.
     */
    function lightBars(ratio, force) {
        var p = progressNodes();

        if (!p || !p.bars.length) {
            return;
        }

        var target = 0;

        for (var i = 0; i < p.bars.length; i++) {
            var at = parseFloat(p.bars[i].style.getPropertyValue('--at'));

            if (!isNaN(at) && at <= ratio + 0.0001) {
                target++;
            }
        }

        if (!force && target === litBars) {
            return;
        }

        litBars = target;

        for (var j = 0; j < p.bars.length; j++) {
            p.bars[j].classList.toggle('is-lit', j < target);
        }
    }

    /** نمایش درصد و برچسب گزارش‌شده از سرور. */
    function applyProgress(data) {
        if (!data || data.state === 'idle') {
            return;
        }

        if (data.label) {
            setMessage(data.label);
        }

        var percent = (typeof data.percent === 'number') ? data.percent : null;
        var p = progressNodes();

        if (!p) {
            return;
        }

        setDeterminate(percent !== null);

        if (percent === null) {
            // فقط مرحله عوض شده و شمارش دقیقی نیست؛ نوار درصد پنهان می‌ماند
            if (p.row) {
                p.row.hidden = true;
            }

            lastPercent = null;

            return;
        }

        var ratio = Math.max(0, Math.min(1, percent / 100));
        var n = node();

        if (n) {
            n.style.setProperty('--progress-ratio', String(ratio));
        }

        if (p.row) {
            p.row.hidden = false;
        }

        if (percent !== lastPercent && p.percent) {
            p.percent.textContent = percent + '٪';
        }

        lastPercent = percent;

        lightBars(ratio, false);
    }

    function fetchProgress() {
        var template = progressUrlTemplate();

        if (!token || !template || typeof window.fetch !== 'function') {
            return;
        }

        var url = template.replace(TOKEN_PLACEHOLDER, token);
        var fetchingToken = token;
        var mySeq = ++pollSeq;

        /*
         * یک پارامتر بی‌معنی به آدرس اضافه می‌شود تا هیچ کشی (مرورگر یا پروکسی)
         * پاسخ یک عملیات قبلی را برنگرداند؛ وگرنه درصد عملیات تازه، کهنه دیده می‌شود.
         */
        url += (url.indexOf('?') === -1 ? '?' : '&') + '_=' + Date.now();

        window.fetch(url, {
            headers: { Accept: 'application/json' },
            // بدون کوکی: این مسیر بی‌سشن است و نباید پشت قفل سشن منتظر بماند
            credentials: 'omit',
            cache: 'no-store'
        }).then(function (response) {
            return response.ok ? response.json() : null;
        }).then(function (data) {
            // فقط پاسخ تازه‌ی همین عملیات اعمال می‌شود:
            //  - اگر توکن عوض شده، پاسخ متعلق به عملیات قبلی است
            //  - اگر پاسخ زودتری قبلاً اعمال شده، این یک جواب کهنه/عقب‌مانده است
            if (data && token === fetchingToken && mySeq >= appliedSeq) {
                appliedSeq = mySeq;
                applyProgress(data);
            }
        }).catch(function () {
            // خطای شبکه نباید هیچ اثری روی تجربه‌ی کاربر بگذارد
        });
    }

    function startPolling() {
        if (pollTimer || !token) {
            return;
        }

        pollTimer = setInterval(fetchProgress, POLL_MS);
        fetchProgress();
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    /** پاک کردن وضعیت پیشرفت برای عملیات بعدی. */
    function resetProgress() {
        stopPolling();

        // پاسخ‌های در پروازِ عملیات قبلی باید بی‌اثر شوند
        appliedSeq = ++pollSeq;

        var n = node();
        var p = progressNodes();

        if (n) {
            n.style.removeProperty('--progress-ratio');
        }

        setDeterminate(false);

        if (p) {
            if (p.row) {
                p.row.hidden = true;
            }

            if (p.percent) {
                p.percent.textContent = '';
            }
        }

        token = null;
        lastPercent = null;
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
            startPolling();
        } else {
            stopPolling();
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
                    resetProgress();
                }
            }, 280);
        }
    }

    function begin(message) {
        setMessage(message);

        // عملیات تازه: توکن پیشرفت جدید (اگر هوک درخواست زودتر توکنی ساخته باشد، همان کافی است)
        if (count === 0 && !token) {
            token = newToken();
        }

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

        // عملیات تمام شده؛ توکن همین جا باطل می‌شود تا در عملیات بعدی
        // توکن تازه ساخته شود و پولینگ به فایل عملیات قبلی گیر نکند
        token = null;

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
            // عملیات تمام شد؛ توکن پیشرفت برای عملیات بعدی تازه ساخته می‌شود
            resetProgress();
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

        /*
         * توکن پیشرفت را روی هدر همان درخواست می‌گذارد تا سرور بتواند درصد
         * همین عملیات را در فایل پیشرفت بنویسد. اگر هوک commit زودتر توکنی
         * ساخته باشد، همان استفاده می‌شود.
         */
        Livewire.hook('request', function (ctx) {
            if (!ctx || !ctx.options) {
                return;
            }

            if (!token) {
                token = newToken();
            }

            var headers = ctx.options.headers;

            if (headers && typeof headers.set === 'function') {
                headers.set(HEADER, token);
            } else {
                ctx.options.headers = headers || {};
                ctx.options.headers[HEADER] = token;
            }
        });

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
