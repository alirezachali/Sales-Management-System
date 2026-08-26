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
|--------------------------------------------------------------------------
*/
class TrackUserOnline
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            Cache::put('user-online-' . $request->user()->id, true, now()->addMinutes(2));
        }

        return $next($request);
    }
}