<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * اعمال زبان فعال برنامه بر اساس تنظیم system_language.
     * این تنظیم هم از منوی ناوبری (کلیک روی پرچم) و هم از صفحه‌ی تنظیمات
     * مقداردهی می‌شود و به‌عنوان «زبان برنامه» عمل می‌کند.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = ['fa', 'en'];

        try {
            $locale = setting('system_language', config('app.locale'));

            if (in_array($locale, $allowed, true)) {
                app()->setLocale($locale);
            }
        } catch (\Throwable $e) {
            // در صورت بروز خطا (مثلاً در دسترس نبودن دیتابیس) از زبان پیش‌فرض کانفیگ استفاده می‌شود
        }

        return $next($request);
    }
}
