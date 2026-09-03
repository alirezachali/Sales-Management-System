<?php

use Hekmatinasser\Verta\Verta;

if (! function_exists('jalaliDate')) {

    function jalaliDate($date, ?string $format = null): ?string
    {
        if (empty($date)) {
            return null;
        }

        $format ??= setting('date_format', 'Y/m/d');

        return Verta::instance($date)->format($format);
    }

}

if (! function_exists('jalaliDateTime')) {

    function jalaliDateTime($date, ?string $format = null): ?string
    {
        if (empty($date)) {
            return null;
        }

        $format ??= setting('date_format', 'Y/m/d') . ' H:i';

        return Verta::instance($date)->format($format);
    }

}

if (! function_exists('jalaliTime')) {

    function jalaliTime($date, string $format = 'H:i'): ?string
    {
        if (empty($date)) {
            return null;
        }

        return Verta::instance($date)->format($format);
    }

}

if (! function_exists('normalizeJalaliInput')) {

    /*
    |------------------------------------------------------------------|
    | نرمال‌سازی ورودی شمسی کاربر: ارقام فارسی/عربی به انگلیسی،         |
    | یکدست‌سازی جداکننده‌ها به خط تیره، حذف بخش ساعت. خروجی مثل:       |
    | 1405-06-01  (مناسب برای Verta::parse که با date_parse کار می‌کند) |
    |------------------------------------------------------------------|
    */
    function normalizeJalaliInput(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        // تبدیل ارقام فارسی و عربی به انگلیسی
        $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $value = str_replace($fa, $en, $value);
        $value = str_replace($ar, $en, $value);

        // فقط بخش تاریخ (قبل از فاصله) را نگه می‌داریم
        $value = explode(' ', preg_replace('/\s+/', ' ', $value))[0];

        // یکدست‌سازی جداکننده‌ها
        $value = str_replace(['/', '.', '\\', '_'], '-', $value);
        $value = preg_replace('/-+/', '-', $value);
        $value = trim($value, '-');

        if (! preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $value)) {
            return null;
        }

        return $value;
    }

}

if (! function_exists('jalaliToGregorian')) {

    /*
    |------------------------------------------------------------------|
    | تبدیل تاریخ شمسی (ورودی کاربر، مثل 1405/06/01) به میلادی          |
    | با فرمت Y-m-d برای استفاده در کوئری‌های سمت سرور. در صورت          |
    | نامعتبر بودن ورودی، null برمی‌گرداند.                            |
    |------------------------------------------------------------------|
    */
    function jalaliToGregorian(?string $jalaliDate): ?string
    {
        $normalized = normalizeJalaliInput($jalaliDate);

        if ($normalized === null) {
            return null;
        }

        try {
            return Verta::parse($normalized)->toCarbon()->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

}

if (! function_exists('gregorianToJalaliInput')) {

    /*
    |------------------------------------------------------------------|
    | تبدیل تاریخ میلادی/کاربن به رشته شمسی مناسب اینپوت                |
    | (مثل 1405/06/01). فقط برای نمایش در ویو استفاده می‌شود.           |
    |------------------------------------------------------------------|
    */
    function gregorianToJalaliInput($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Verta::instance($date)->format('Y/m/d');
        } catch (\Throwable) {
            return null;
        }
    }

}