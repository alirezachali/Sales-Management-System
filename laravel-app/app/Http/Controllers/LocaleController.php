<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    /**
     * تغییر زبان برنامه با کلیک روی پرچم در منوی ناوبری.
     * مقدار انتخاب‌شده در تنظیم system_language ذخیره می‌شود و
     * میدل‌ور SetLocale آن را در همه‌ی درخواست‌ها اعمال می‌کند.
     */
    public function switch(string $locale): RedirectResponse
    {
        $allowed = ['fa', 'en'];

        if (in_array($locale, $allowed, true)) {
            Setting::updateOrCreate(
                ['key' => 'system_language'],
                ['value' => $locale]
            );

            app()->setLocale($locale);
        }

        return redirect()->back();
    }
}
