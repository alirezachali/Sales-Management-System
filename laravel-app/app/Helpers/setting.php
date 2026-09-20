<?php

use App\Models\Setting;
use App\Http\controllers\SettingController;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {

    /**
     * مقدار یک تنظیم را برمی‌گرداند.
     *
     * تمام تنظیمات برای هر درخواست یک‌بار کش می‌شوند تا تابع setting()
     * (که در هدرِ همه‌ی صفحات چندین بار صدا زده می‌شود) هر بار یک کوئری
     * جدا روی جدول settings نزند.
     */
    function setting($key, $default = null)
    {
        static $all = null;

        if ($all === null) {
            $all = \Illuminate\Support\Facades\Cache::remember(
                'all-settings',
                now()->addMinutes(5),
                fn () => Setting::pluck('value', 'key')->all()
            );
        }

        return $all[$key] ?? $default;
    }

}

if (! function_exists('storeLogo')) {

    function storeLogo()
{
    $path = setting('store_logo');

    if ($path) {
        return asset('storage/' . $path);
    }

    return asset('images/default-logo.png');
}
}

if (! function_exists('storeFavicon')) {

    function storeFavicon()
{
    $path = setting('store_favicon');

    if ($path) {
        return asset('storage/' . $path);
    }

    return asset('images/default-favicon.ico');
}
}