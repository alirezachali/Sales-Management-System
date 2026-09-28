<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('online-shop:publish-catalog')->everyFiveMinutes();
Schedule::command('online-shop:pull-orders')->everyMinute();
Schedule::command('online-shop:pull-customers')->everyFiveMinutes();

/* زمان‌بند هشدارها: هر دقیقه سرویس‌هایی که زمان اجرایشان رسیده را در صف
   می‌گذارد. اجرای هر سرویس با Job انجام می‌شود و نتیجه در کش ذخیره می‌شود
   تا نمایش هشدارها هیچ کوئری‌ای روی دیتابیس نزند. */
Schedule::command('alerts:dispatch')->everyMinute()->withoutOverlapping(10);
