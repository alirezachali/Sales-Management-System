/**
 * پیام‌های هوشمند لودر سراسری.
 *
 * کلیدهای byMethod دقیقاً نام متدهای Livewire در این پروژه هستند؛ برای هر عملیات
 * سنگین تازه کافی است نام متد و پیام مناسبش اینجا اضافه شود. اگر متدی پیدا نشد،
 * الگوهای patterns و در نهایت پیام پیش‌فرض استفاده می‌شوند.
 *
 * این فایل عمداً بدون وابستگی به DOM نوشته شده تا جداگانه قابل تست باشد.
 */
(function (global) {
    'use strict';

    /** پیام پیش‌فرض وقتی عملیات مشخصی تشخیص داده نشود. */
    var fallback = 'در حال پردازش';

    /** وقتی فقط مقدار پراپرتی تغییر کرده باشد (فیلتر/جست‌وجوی زنده). */
    var updateMessage = 'در حال به‌روزرسانی…';

    /** پیام اختصاصی هر متد — عملیات‌های سنگین و زمان‌بر. */
    var byMethod = {
        // ---------- خروجی و فایل ----------
        exportExcel: 'در حال آماده‌سازی فایل اکسل…',
        exportCsv: 'در حال آماده‌سازی فایل CSV…',
        downloadBackup: 'در حال آماده‌سازی فایل پشتیبان…',
        downloadQr: 'در حال آماده‌سازی بارکد…',
        printInvoice: 'در حال آماده‌سازی فاکتور برای چاپ…',
        printSlip: 'در حال آماده‌سازی رسید برای چاپ…',

        // ---------- پشتیبان‌گیری ----------
        createBackup: 'در حال گرفتن نسخه پشتیبان…',
        restoreBackup: 'در حال بازیابی نسخه پشتیبان…',
        deleteBackup: 'در حال حذف نسخه پشتیبان…',

        // ---------- انبار ----------
        finalize: 'در حال نهایی‌سازی شمارش انبار…',
        saveCounts: 'در حال ذخیره شمارش انبار…',
        closeCount: 'در حال بستن شمارش انبار…',
        saveAdjust: 'در حال اعمال اصلاح موجودی…',
        confirmReceive: 'در حال ثبت دریافت کالا…',

        // ---------- فروش و پرداخت ----------
        checkout: 'در حال ثبت فروش…',
        pay: 'در حال ثبت پرداخت…',
        recordPayment: 'در حال ثبت پرداخت…',
        saveTx: 'در حال ثبت تراکنش…',

        // ---------- سفارش‌ها ----------
        approve: 'در حال تأیید سفارش…',
        rejectOrder: 'در حال رد سفارش…',
        deliver: 'در حال تحویل سفارش…',
        dispatchOrder: 'در حال ارسال سفارش…',
        fulfill: 'در حال آماده‌سازی سفارش…',

        // ---------- امتیاز وفاداری ----------
        applyAllPoints: 'در حال اعمال امتیازها…',
        refreshCustomerPoints: 'در حال به‌روزرسانی امتیازها…',

        // ---------- هشدارها ----------
        saveAlertService: 'در حال ذخیره تنظیمات هشدار…',
        runAlertServiceNow: 'در حال ارسال هشدارها…',

        // ---------- گزارش و داده ----------
        buildReport: 'در حال تهیه گزارش…',
        loadRows: 'در حال بارگذاری اطلاعات…',
        pullNow: 'در حال دریافت اطلاعات…',
        recalculateRole: 'در حال بازمحاسبه دسترسی‌ها…',
        updatePassword: 'در حال به‌روزرسانی رمز عبور…'
    };

    /** الگوهای عمومی برای متدهایی که پیام اختصاصی ندارند. */
    var patterns = [
        { re: /^(export|download)/, msg: 'در حال آماده‌سازی فایل خروجی…' },
        { re: /^print/, msg: 'در حال آماده‌سازی چاپ…' },
        { re: /^(save|store|create|update)/, msg: 'در حال ذخیره اطلاعات…' },
        { re: /^(delete|remove|destroy)/, msg: 'در حال حذف…' },
        { re: /^(refresh|reload)/, msg: 'در حال به‌روزرسانی…' },
        { re: /^(import|upload)/, msg: 'در حال بارگذاری اطلاعات…' }
    ];

    /** استخراج نام متدهای صدا زده‌شده از یک commit لایووایر. */
    function methodsOf(commit) {
        var calls = (commit && commit.calls) || [];
        var methods = [];

        for (var i = 0; i < calls.length; i++) {
            var method = calls[i] && calls[i].method;
            if (method) {
                methods.push(String(method));
            }
        }

        return methods;
    }

    /** انتخاب پیام بر اساس فهرست نام متدها. */
    function messageFor(methods) {
        var i;
        var j;

        for (i = 0; i < methods.length; i++) {
            if (Object.prototype.hasOwnProperty.call(byMethod, methods[i])) {
                return byMethod[methods[i]];
            }
        }

        for (j = 0; j < patterns.length; j++) {
            for (i = 0; i < methods.length; i++) {
                if (patterns[j].re.test(methods[i])) {
                    return patterns[j].msg;
                }
            }
        }

        return fallback;
    }

    /** پیام مناسب برای یک commit لایووایر. */
    function forCommit(commit) {
        var methods = methodsOf(commit);

        if (methods.length) {
            return messageFor(methods);
        }

        var hasUpdates = commit && commit.updates && Object.keys(commit.updates).length > 0;
        if (hasUpdates) {
            return updateMessage;
        }

        return fallback;
    }

    global.AppLoaderMessages = {
        fallback: fallback,
        updateMessage: updateMessage,
        byMethod: byMethod,
        patterns: patterns,
        methodsOf: methodsOf,
        messageFor: messageFor,
        forCommit: forCommit
    };
})(typeof window !== 'undefined' ? window : globalThis);
