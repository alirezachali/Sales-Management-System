<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
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

        /* کاربر super-admin همیشه به همه‌جا دسترسی دارد؛
           خروجی null یعنی تصمیم‌گیری به Gateهای بعدی سپرده شود. */
        Gate::before(fn ($user) => $user->role?->name === Role::SUPER_ADMIN ? true : null);

        /* برای هر مجوز ثبت‌شده در دیتابیس یک Gate همنام تعریف می‌شود تا
           @can در Blade، $user->can() و میدل‌ور can: روی روت‌ها کار کنند. */
        foreach (Permission::pluck('name') as $permission) {
            Gate::define($permission, fn ($user) => $user->hasPermission($permission));
        }
    }
}