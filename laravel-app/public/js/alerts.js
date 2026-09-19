/**
 * Flash alerts: close button + auto-dismiss after 5s.
 * A dismissed alert is only blocked for the current Livewire round
 * (so poll/morph cannot restore it), but the next user action can show a new one.
 */
(function () {
    var DELAY_MS = 5000;
    var FADE_MS = 350;
    var round = 0;
    var dismissed = {};
    var lastBumpAt = 0;

    function kind(alert) {
        if (alert.classList.contains('alert-success')) {
            return 'success';
        }
        if (alert.classList.contains('alert-danger')) {
            return 'error';
        }
        return 'other';
    }

    function keyFor(alert) {
        if (!alert.dataset.flashRound) {
            alert.dataset.flashRound = String(round);
        }
        return alert.dataset.flashRound + ':' + kind(alert);
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
        var key = keyFor(alert);

        if (dismissed[key]) {
            alert.remove();
            return;
        }

        if (alert.dataset.autoAlert === '1') {
            return;
        }

        alert.dataset.autoAlert = '1';
        setTimeout(function () {
            dismiss(alert);
        }, DELAY_MS);
    }

    function scan() {
        document.querySelectorAll('[data-flash-alert], .alert.alert-dismissible').forEach(schedule);
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.alert .btn-close, .alert [data-bs-dismiss="alert"]');
        if (!btn) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();
        dismiss(btn.closest('.alert'));
    }, true);

    function bindLivewire() {
        if (typeof Livewire === 'undefined' || !Livewire.hook) {
            return;
        }

        function onCommit({ commit }) {
            if (!isPollCommit(commit)) {
                bumpRound();
            }
        }

        Livewire.hook('commit.prepare', onCommit);
        Livewire.hook('commit', onCommit);
    }

    document.addEventListener('livewire:init', function () {
        bindLivewire();
        scan();
    });
    bindLivewire();

    document.addEventListener('livewire:navigated', function () {
        bumpRound();
        scan();
    });

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
            timer = setTimeout(scan, 30);
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }
})();
