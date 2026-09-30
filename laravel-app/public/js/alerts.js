/**
 * Flash alerts + notification sounds.
 *
 * - Auto-dismiss dismissible alerts after 5s
 * - Play Web Audio tones for success / error / warning / info / scan
 * - Respects window.APP_SOUNDS.notify and window.APP_SOUNDS.barcode
 */
(function () {
    var DELAY_MS = 5000;
    var FADE_MS = 350;
    var SOUND_COOLDOWN_MS = 280;
    var round = 0;
    var dismissed = {};
    var lastBumpAt = 0;
    var lastSoundAt = 0;
    var lastSoundKey = '';
    var audioCtx = null;

    function soundsEnabled(kind) {
        var cfg = window.APP_SOUNDS || {};
        if (kind === 'scan') {
            return cfg.barcode !== false;
        }
        return cfg.notify !== false;
    }

    function ensureAudio() {
        if (audioCtx) {
            return audioCtx;
        }

        var Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) {
            return null;
        }

        audioCtx = new Ctx();
        return audioCtx;
    }

    function unlockAudio() {
        var ctx = ensureAudio();
        if (!ctx) {
            return;
        }

        if (ctx.state === 'suspended') {
            ctx.resume().catch(function () {});
        }
    }

    /**
     * Play a short synthesised tone sequence.
     * @param {Array<{freq:number, dur:number, gap?:number, type?:string, gain?:number}>} notes
     */
    function playNotes(notes) {
        var ctx = ensureAudio();
        if (!ctx) {
            return;
        }

        if (ctx.state === 'suspended') {
            ctx.resume().catch(function () {});
        }

        var t = ctx.currentTime + 0.01;

        notes.forEach(function (note) {
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            var peak = note.gain != null ? note.gain : 0.08;
            var dur = note.dur || 0.12;
            var gap = note.gap != null ? note.gap : 0.04;

            osc.type = note.type || 'sine';
            osc.frequency.setValueAtTime(note.freq, t);

            gain.gain.setValueAtTime(0.0001, t);
            gain.gain.exponentialRampToValueAtTime(peak, t + 0.015);
            gain.gain.exponentialRampToValueAtTime(0.0001, t + dur);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(t);
            osc.stop(t + dur + 0.02);

            t += dur + gap;
        });
    }

    var PATTERNS = {
        success: [
            { freq: 660, dur: 0.09, gap: 0.03, type: 'sine', gain: 0.07 },
            { freq: 880, dur: 0.14, gap: 0, type: 'sine', gain: 0.08 },
        ],
        error: [
            { freq: 280, dur: 0.14, gap: 0.05, type: 'square', gain: 0.045 },
            { freq: 180, dur: 0.2, gap: 0, type: 'square', gain: 0.04 },
        ],
        warning: [
            { freq: 520, dur: 0.1, gap: 0.06, type: 'triangle', gain: 0.06 },
            { freq: 520, dur: 0.14, gap: 0, type: 'triangle', gain: 0.055 },
        ],
        info: [
            { freq: 740, dur: 0.12, gap: 0, type: 'sine', gain: 0.055 },
        ],
        scan: [
            { freq: 1200, dur: 0.06, gap: 0.02, type: 'sine', gain: 0.06 },
            { freq: 1500, dur: 0.05, gap: 0, type: 'sine', gain: 0.05 },
        ],
    };

    function playSound(kind, forceKey) {
        kind = kind || 'info';

        if (!soundsEnabled(kind)) {
            return;
        }

        if (!PATTERNS[kind]) {
            kind = 'info';
        }

        var now = Date.now();
        var key = forceKey || (kind + ':' + round);

        if (key === lastSoundKey && now - lastSoundAt < SOUND_COOLDOWN_MS) {
            return;
        }

        if (now - lastSoundAt < 90 && !forceKey) {
            return;
        }

        lastSoundAt = now;
        lastSoundKey = key;

        try {
            playNotes(PATTERNS[kind]);
        } catch (e) {
            // ignore autoplay / audio errors
        }
    }

    function kind(alert) {
        var forced = (alert.dataset.notifySound || alert.dataset.sound || '').toLowerCase();
        if (forced && PATTERNS[forced]) {
            return forced;
        }

        if (alert.classList.contains('alert-success')) {
            return 'success';
        }
        if (alert.classList.contains('alert-danger')) {
            return 'error';
        }
        if (alert.classList.contains('alert-warning')) {
            return 'warning';
        }
        if (alert.classList.contains('alert-info') || alert.classList.contains('alert-primary')) {
            return 'info';
        }

        return 'info';
    }

    function isNotifyAlert(alert) {
        if (!alert || !alert.classList || !alert.classList.contains('alert')) {
            return false;
        }

        // آلرت‌های ثابت / بازخورد زنده UI صدا نگیرند
        var forced = (alert.dataset.notifySound || alert.dataset.sound || '').toLowerCase();
        if (forced === 'off' || forced === '0' || forced === 'false') {
            return false;
        }

        if (alert.classList.contains('co-total')) {
            return false;
        }

        if (alert.hasAttribute('data-flash-alert') || alert.hasAttribute('data-notify-sound')) {
            return true;
        }

        // فقط پیام‌های dismissible (معمولاً flash سراسری)
        return alert.classList.contains('alert-dismissible');
    }

    function keyFor(alert) {
        if (!alert.dataset.flashRound) {
            alert.dataset.flashRound = String(round);
        }
        return alert.dataset.flashRound + ':' + kind(alert) + ':' + (alert.textContent || '').trim().slice(0, 80);
    }

    function isPollCommit(commit) {
        var calls = (commit && commit.calls) || [];
        if (!calls.length) {
            return false;
        }

        return calls.every(function (call) {
            var method = call && call.method;
            return method === '$refresh' || method === '$poll';
        });
    }

    function bumpRound() {
        var now = Date.now();
        if (now - lastBumpAt < 80) {
            return;
        }
        lastBumpAt = now;
        round += 1;
    }

    function dismiss(alert) {
        if (!alert || !alert.parentNode) {
            return;
        }

        dismissed[keyFor(alert)] = true;
        dismissed[round + ':' + kind(alert)] = true;

        if (alert.dataset.dismissing === '1') {
            alert.remove();
            return;
        }

        alert.dataset.dismissing = '1';
        alert.classList.remove('show');
        alert.style.transition = 'opacity ' + FADE_MS + 'ms ease, transform ' + FADE_MS + 'ms ease';
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-6px)';

        setTimeout(function () {
            if (alert.parentNode) {
                alert.remove();
            }
        }, FADE_MS);
    }

    function schedule(alert) {
        if (!isNotifyAlert(alert)) {
            return;
        }

        var key = keyFor(alert);

        if (dismissed[key]) {
            if (alert.classList.contains('alert-dismissible') || alert.hasAttribute('data-flash-alert')) {
                alert.remove();
            }
            return;
        }

        if (alert.dataset.autoAlert === '1') {
            return;
        }

        alert.dataset.autoAlert = '1';

        if (!alert.dataset.soundPlayed) {
            alert.dataset.soundPlayed = '1';
            playSound(kind(alert), key);
            alert.classList.add('flash-alert-pop');
        }

        if (alert.classList.contains('alert-dismissible') || alert.hasAttribute('data-flash-alert')) {
            setTimeout(function () {
                dismiss(alert);
            }, DELAY_MS);
        }
    }

    var lastValidationSig = '';

    function scanValidationErrors(root) {
        root = root || document;
        var texts = [];

        root.querySelectorAll('.invalid-feedback').forEach(function (n) {
            var t = (n.textContent || '').trim();
            if (t) {
                texts.push(t);
            }
        });

        // خطای عمومی کامپوننت که گاهی فقط با @error رندر می‌شود قبلاً با data-notify-sound پوشش داده شده

        if (!texts.length) {
            lastValidationSig = '';
            return;
        }

        var sig = texts.join('|').slice(0, 120);
        if (sig === lastValidationSig) {
            return;
        }

        lastValidationSig = sig;
        playSound('error', 'feedback:' + sig);
    }

    function scan(root) {
        root = root || document;
        root.querySelectorAll('[data-flash-alert], [data-notify-sound], .alert.alert-dismissible').forEach(schedule);
    }

    document.addEventListener('click', function (e) {
        unlockAudio();

        var btn = e.target.closest('.alert .btn-close, .alert [data-bs-dismiss="alert"]');
        if (!btn) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();
        dismiss(btn.closest('.alert'));
    }, true);

    document.addEventListener('keydown', unlockAudio, { once: false, passive: true });
    document.addEventListener('pointerdown', unlockAudio, { once: false, passive: true });

    function bindLivewire() {
        if (typeof Livewire === 'undefined' || !Livewire.hook) {
            return;
        }

        if (window.__appAlertsLivewireBound) {
            return;
        }
        window.__appAlertsLivewireBound = true;

        function onCommit({ commit, succeed }) {
            if (!isPollCommit(commit)) {
                bumpRound();
            }

            if (typeof succeed === 'function') {
                succeed(function () {
                    requestAnimationFrame(function () {
                        scan();
                        if (!isPollCommit(commit)) {
                            scanValidationErrors();
                        }
                    });
                });
            }
        }

        Livewire.hook('commit.prepare', onCommit);
        Livewire.hook('commit', onCommit);

        // رویداد سراسری: $this->dispatch('app-sound', type: 'scan|success|error|warning|info')
        Livewire.on('app-sound', function (event) {
            var type = (event && (event.type || (event[0] && event[0].type))) || 'info';
            playSound(type, 'event:' + type + ':' + Date.now());
        });
    }

    document.addEventListener('livewire:init', function () {
        bindLivewire();
        scan();
    });

    if (typeof Livewire !== 'undefined') {
        bindLivewire();
    }

    document.addEventListener('livewire:navigated', function () {
        bumpRound();
        scan();
    });

    // API عمومی
    window.AppSound = {
        play: playSound,
        unlock: unlockAudio,
        success: function () { playSound('success'); },
        error: function () { playSound('error'); },
        warning: function () { playSound('warning'); },
        info: function () { playSound('info'); },
        scan: function () { playSound('scan'); },
    };

    scan();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scan);
    }

    if (typeof MutationObserver !== 'undefined') {
        var timer = null;
        var observer = new MutationObserver(function () {
            if (timer) {
                clearTimeout(timer);
            }
            timer = setTimeout(function () {
                scan();
            }, 30);
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }
})();
