<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
|                      ردیابی کاربران آنلاین سیستم
|--------------------------------------------------------------------------
| این میدل‌ور با هر درخواست کاربر لاگین‌کرده، یک کلید کش با انقضای کوتاه
| (۲ دقیقه) برایش ثبت/تمدید می‌کند. تا وقتی کاربر در سیستم فعالیت دارد
| (رفرش صفحه، Livewire poll، کلیک و ...) این کلید همیشه تازه می‌ماند و
| User::isOnline() مقدار true برمی‌گرداند. اگر کاربر سیستم را ترک کند
| (تب را ببندد یا غیرفعال بماند)، پس از انقضای کش به‌صورت خودکار
| «آفلاین» محسوب می‌شود؛ بدون نیاز به رویداد جداگانه برای خروج.
|
| برای اینکه بعد از آفلاین شدن هم بتوان «چند دقیقه پیش» را نمایش داد،
| ستون last_seen_at در دیتابیس نگه‌داشته می‌شود (حداکثر یک بار در دقیقه
| برای هر کاربر، تا فشار روی دیتابیس زیاد نشود).
|--------------------------------------------------------------------------
*/
class TrackUserOnline
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            Cache::put('user-online-' . $user->id, now()->toDateTimeString(), now()->addMinutes(2));

            if (Cache::add('user-seen-db-' . $user->id, true, 60)) {
                $user->forceFill(['last_seen_at' => now()])->saveQuietly();
            }
        }

        return $next($request);
    }
}
