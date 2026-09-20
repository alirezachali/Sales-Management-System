<?php

namespace App\Observers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * بعد از هر تغییر در تنظیمات، کشِ setting() باطل می‌شود تا مقادیر تازه
 * بلافاصله در همه‌ی صفحات اعمال شوند (وگرنه تا ۵ دقیقه کهنه می‌ماند).
 */
class SettingObserver
{
    public function saved(Setting $setting): void
    {
        Cache::forget('all-settings');
    }

    public function deleted(Setting $setting): void
    {
        Cache::forget('all-settings');
    }
}
