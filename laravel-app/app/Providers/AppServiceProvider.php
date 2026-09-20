<?php

namespace App\Providers;

use App\Models\CustomerAccountTransaction;
use App\Models\MessageRecipient;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Observers\CustomerAccountTransactionObserver;
use App\Observers\MessageRecipientObserver;
use App\Observers\ProductObserver;
use App\Observers\SettingObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Product::observe(ProductObserver::class);
        Setting::observe(SettingObserver::class);
        MessageRecipient::observe(MessageRecipientObserver::class);
        CustomerAccountTransaction::observe(CustomerAccountTransactionObserver::class);

        $this->applyConfiguredTimezone();
        $this->applyConfiguredSessionLifetime();

        /* کاربر super-admin همیشه به همه‌جا دسترسی دارد؛
           خروجی null یعنی تصمیم‌گیری به Gateهای بعدی سپرده شود. */
        Gate::before(fn ($user) => $user->role?->name === Role::SUPER_ADMIN ? true : null);

        /* برای هر مجوز ثبت‌شده در دیتابیس یک Gate همنام تعریف می‌شود تا
           @can در Blade، $user->can() و میدل‌ور can: روی روت‌ها کار کنند.
           اگر جدول مجوزها هنوز ساخته نشده باشد (مثلاً هنگام migrate یا
           تست‌های دیتابیس in-memory) از ساخت Gateها صرف‌نظر می‌شود. */
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
                foreach (Permission::pluck('name') as $permission) {
                    Gate::define($permission, fn ($user) => $user->hasPermission($permission));
                }
            }
        } catch (\Throwable) {
            // دیتابیس آماده نیست؛ Gateها در درخواست بعدی ساخته می‌شوند
        }
    }

    /*
    |--------------------------------------------------------------------|
    |            اعمال منطقه‌ی زمانی ذخیره‌شده در تنظیمات                 |
    |--------------------------------------------------------------------|
    | مقدار timezone از جدول settings خوانده و هم روی PHP/Carbon و هم    |
    | روی نشست دیتابیس اعمال می‌شود تا تاریخ/ساعتِ ذخیره‌شده و نمایش    |
    | داده‌شده در همه‌ی بخش‌ها یکدست و درست باشد. مقدار پیش‌فرض (در    |
    | صورت نبود تنظیم یا خطای دیتابیس) همان زمان محلی ایران است.       |
    */
    protected function applyConfiguredTimezone(): void
    {
        try {
            $timezone = setting('timezone') ?: 'Asia/Tehran';
        } catch (\Throwable) {
            return;
        }

        if (! in_array($timezone, \DateTimeZone::listIdentifiers(), true)) {
            $timezone = 'Asia/Tehran';
        }

        date_default_timezone_set($timezone);
        config(['app.timezone' => $timezone]);

        // جلسه‌ی دیتابیس هم باید همان منطقه‌ی زمانی را دنبال کند تا
        // توابع SQL مثل NOW() و مقایسه‌ی تاریخ‌ها با PHP هم‌سو باشند.
        try {
            DB::statement("SET time_zone = ?", [$timezone]);
        } catch (\Throwable) {
            // برخی درایورها/میزبان‌ها نام منطقه را قبول نمی‌کنند؛ نادیده می‌گیریم.
        }
    }

    /*
    |--------------------------------------------------------------------|
    |          اعمال «مدت زمان انقضای نشست» ذخیره‌شده در تنظیمات          |
    |--------------------------------------------------------------------|
    | مقدار session_timeout از جدول settings خوانده و روی                 |
    | config('session.lifetime') تنظیم می‌شود تا کاربر بتواند از داخل     |
    | صفحه‌ی تنظیمات، مدت زمان انقضای نشست را داینامیک عوض کند. این کار  |
    | بعد از آماده‌شدن دیتابیس (در boot) و قبل از اینکه middleware شروع  |
    | نشست مقدار lifetime را بخواند انجام می‌شود. مقدار پیش‌فرض ۱۲۰       |
    | دقیقه است.                                                        |
    */
    protected function applyConfiguredSessionLifetime(): void
    {
        try {
            $minutes = (int) \App\Models\Setting::where('key', 'session_timeout')->value('value');

            if ($minutes < 1) {
                $minutes = 120;
            }

            config(['session.lifetime' => $minutes]);
        } catch (\Throwable) {
            // دیتابیس در دسترس نیست؛ مقدار پیش‌فرضِ کانفیگ حفظ می‌شود
        }
    }
}