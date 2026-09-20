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
}