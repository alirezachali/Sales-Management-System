<?php

use App\Models\Setting;
use App\Http\controllers\SettingController;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {

    function setting($key, $default = null)
    {
        return Setting::where('key', $key)->value('value') ?? $default;
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