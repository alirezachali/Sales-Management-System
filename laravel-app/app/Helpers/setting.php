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

/*
|--------------------------------------------------------------------------
| رندر آیکن منو/بخش: فایل‌های PNG رنگی یا کلاس آیکن‌های Bootstrap
|--------------------------------------------------------------------------
| اگر مقدار تمام .png باشد، تگ img از پوشه‌ی آیکن‌های سایدبار رندر می‌شود؛
| در غیر این صورت مثل قبل یک <i> با کلاس داده‌شده ساخته می‌شود. رشته‌ی
| $prefix معمولاً "" برای مقادیری مثل "bi bi-..." یا "bi" برای مثل "bi-..." است.
*/
if (! function_exists('iconHtml')) {

    /*
    |--------------------------------------------------------------------------
    | خروجی HtmlString برمی‌گردد تا در قالب با {{ }} صدا زده شود و Blade
    | | آن را escape نکند؛ وگرنه تگ <img> به‌صورت متن خام نمایش داده می‌شود.
    */
    function iconHtml(string $icon, string $prefix = '', string $extra = ''): \Illuminate\Support\HtmlString
    {
        $icon = trim($icon);

        if ($icon !== '' && str_ends_with(strtolower($icon), '.png')) {
            $html = '<img src="' . e(asset('images/icons/sidebar/' . $icon)) . '" alt="" class="icon-png ' . e($extra) . '">';
        } else {
            $html = '<i class="' . e(trim($prefix . ' ' . $icon . ' ' . $extra)) . '"></i>';
        }

        return new \Illuminate\Support\HtmlString($html);
    }
}