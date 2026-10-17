<?php

use App\Http\Controllers\ProgressController;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------------------------------------|
|                        درخواست‌های بدون سشن                          |
|----------------------------------------------------------------------|
| این مسیرها با میدل‌ور web اجرا نمی‌شوند. به همین دلیل درخواست پولینگ   |
| «درصد پیشرفت» لودر، پشت قفل سشنِ عملیات سنگین نمی‌ماند و بی‌درنگ      |
| پاسخ می‌گیرد. نتیجه با توکن تصادفی محافظت می‌شود و داده‌ی حساسی ندارد. |
|                                                                      |
*/

Route::get('app-progress/{token}', [ProgressController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{8,64}')
    // محدودیت نرخ پیش‌فرض api برای پولینگ چند بار در ثانیه کافی نیست
    ->withoutMiddleware(ThrottleRequests::class)
    ->name('progress.show');
