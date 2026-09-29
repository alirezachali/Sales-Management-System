/**
 * نوار تب صفحه تنظیمات:
 *  - نشانگر متحرک زیر تب فعال
 *  - پیمایش با کلیدهای جهت‌دار (راست/چپ، متناسب با RTL) و Home/End
 *
 * نشانگر داخل یک لایه‌ی بدون اسکرول (settings-tabs-wrap) قرار می‌گیرد و
 * موقعیتش از روی مستطیل واقعیِ تب فعال محاسبه می‌شود؛ به‌این‌ترتیب در حالت
 * RTL و وقتی نوار تب‌ها اسکرول می‌شود هم دقیق می‌ماند. با هر رندر مجدد
 * لایووایر یا تغییر اندازه، خودش را دوباره هم‌تراز می‌کند.
 */
(function () {
    'use strict';

    function setup(wrap) {
        if (wrap.dataset.settingsTabsReady === '1') {
            return;
        }

        var bar = wrap.querySelector('.settings-tabs');

        if (!bar) {
            return;
        }

        wrap.dataset.settingsTabsReady = '1';

        var indicator = document.createElement('span');
        indicator.className = 'settings-tabs-indicator';
        indicator.setAttribute('aria-hidden', 'true');
        wrap.appendChild(indicator);

        var pendingFrame = null;
        var pendingFocusKey = null;
        var hasFocus = false;

        function links() {
            return Array.prototype.slice.call(bar.querySelectorAll('.nav-link'));
        }

        function linkKey(link) {
            if (!link) {
                return null;
            }

            if (link.dataset.tabKey) {
                return link.dataset.tabKey;
            }

            var match = (link.getAttribute('wire:click') || '').match(/selectTab\(\s*['"]([^'"]+)['"]\s*\)/);

            return match ? match[1] : null;
        }

        function activeLink() {
            var list = links();

            for (var i = 0; i < list.length; i++) {
                if (list[i].classList.contains('active')) {
                    return list[i];
                }
            }

            return list[0] || null;
        }

        function ensureIndicator() {
            if (!indicator.isConnected) {
                wrap.appendChild(indicator);
            }
        }

        function placeIndicator(link) {
            ensureIndicator();

            if (!link) {
                indicator.style.opacity = '0';
                return;
            }

            var wrapRect = wrap.getBoundingClientRect();
            var linkRect = link.getBoundingClientRect();

            indicator.style.opacity = '1';
            indicator.style.width = linkRect.width + 'px';
            indicator.style.transform = 'translateX(' + (linkRect.left - wrapRect.left) + 'px)';
        }

        function syncAria(current) {
            links().forEach(function (link) {
                var isActive = link === current;

                link.setAttribute('role', 'tab');
                link.setAttribute('aria-selected', isActive ? 'true' : 'false');
                link.tabIndex = isActive ? 0 : -1;
            });
        }

        /* اگر تب فعال بیرون از ناحیه‌ی دید نوار باشد، آن را به دید می‌آوریم */
        function ensureActiveVisible(link) {
            if (!link) {
                return;
            }

            var barRect = bar.getBoundingClientRect();
            var linkRect = link.getBoundingClientRect();

            if (linkRect.left < barRect.left - 1 || linkRect.right > barRect.right + 1) {
                link.scrollIntoView({ block: 'nearest', inline: 'nearest' });
            }
        }

        function refresh(focusKey) {
            var current = activeLink();

            syncAria(current);
            placeIndicator(current);
            ensureActiveVisible(current);

            if (focusKey) {
                var target = links().filter(function (link) {
                    return linkKey(link) === focusKey;
                })[0];

                if (target) {
                    target.focus({ preventScroll: true });
                }
            }
        }

        function schedule(focusKey) {
            if (focusKey) {
                pendingFocusKey = focusKey;
            }

            if (pendingFrame) {
                return;
            }

            pendingFrame = window.requestAnimationFrame(function () {
                pendingFrame = null;

                var key = pendingFocusKey;
                pendingFocusKey = null;

                refresh(key);
            });
        }

        /* کلیک ماوس */
        wrap.addEventListener('click', function (event) {
            var link = event.target && event.target.closest ? event.target.closest('.nav-link') : null;

            if (link) {
                schedule(linkKey(link));
            }
        });

        /* پیمایش با کلیدهای جهت‌دار (متناسب با جهت چیدمان RTL/LTR) */
        wrap.addEventListener('keydown', function (event) {
            if (['ArrowRight', 'ArrowLeft', 'Home', 'End'].indexOf(event.key) === -1) {
                return;
            }

            var list = links();

            if (!list.length) {
                return;
            }

            var current = list.indexOf(document.activeElement);

            if (current === -1) {
                current = list.indexOf(activeLink());
            }

            if (current === -1) {
                current = 0;
            }

            var rtl = window.getComputedStyle(bar).direction === 'rtl';
            var next = current;

            if (event.key === 'Home') {
                next = 0;
            } else if (event.key === 'End') {
                next = list.length - 1;
            } else if (event.key === 'ArrowRight') {
                next = rtl ? current - 1 : current + 1;
            } else if (event.key === 'ArrowLeft') {
                next = rtl ? current + 1 : current - 1;
            }

            if (next < 0) {
                next = list.length - 1;
            }

            if (next >= list.length) {
                next = 0;
            }

            event.preventDefault();

            var target = list[next];

            target.focus({ preventScroll: true });
            schedule(linkKey(target));
            target.click();
        });

        /* حفظ فوکوس بعد از رندر مجدد لایووایر */
        wrap.addEventListener('focusin', function () {
            hasFocus = true;
        });

        wrap.addEventListener('focusout', function (event) {
            if (!event.relatedTarget || !wrap.contains(event.relatedTarget)) {
                hasFocus = false;
            }
        });

        /* با اسکرول افقی نوار، نشانگر هم جابه‌جا می‌شود */
        bar.addEventListener('scroll', function () {
            schedule();
        });

        /* هر رندر مجدد لایووایر */
        var observer = new MutationObserver(function () {
            if (pendingFocusKey === null && hasFocus && !wrap.contains(document.activeElement)) {
                pendingFocusKey = linkKey(activeLink());
            }

            schedule();
        });

        observer.observe(wrap, { childList: true, subtree: true, attributes: true, attributeFilter: ['class'] });

        if (window.ResizeObserver) {
            new ResizeObserver(function () {
                schedule();
            }).observe(wrap);
        }

        window.addEventListener('resize', function () {
            schedule();
        });

        /* نخستین رسم بدون انیمیشن، بعد از آن انیمیشن روشن می‌شود */
        syncAria(activeLink());
        placeIndicator(activeLink());

        window.requestAnimationFrame(function () {
            wrap.classList.add('is-ready');
        });

        /* پس از لود فونت (تغییر عرض برچسب‌ها) دوباره دقیق هم‌تراز کن */
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function () {
                wrap.classList.remove('is-ready');
                placeIndicator(activeLink());

                window.requestAnimationFrame(function () {
                    wrap.classList.add('is-ready');
                });
            });
        }
    }

    function boot() {
        document.querySelectorAll('[data-settings-tabs]').forEach(setup);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    document.addEventListener('livewire:navigated', boot);

    document.addEventListener('livewire:init', function () {
        if (window.Livewire && window.Livewire.hook) {
            window.Livewire.hook('morphed', boot);
        }
    });
})();
