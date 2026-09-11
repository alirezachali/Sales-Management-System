<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * نمایش صفحه‌ی داشبورد مدیریتی (نقش‌های مدیر و مدیر کل).
     *
     * تمام منطق واکشی آمار (فروش امروز، فاکتورها، کالاهای کم‌موجود،
     * آخرین فروش‌ها و نمودار ۳۰ روز اخیر) به کامپوننت Livewire
     * App\Livewire\Dashboard\Overview منتقل شده است. این ویو فقط
     * لایوت اصلی برنامه را بارگذاری کرده و کامپوننت زنده را در خود
     * جای می‌دهد؛ دقیقاً همان الگویی که برای ماژول‌های محصولات،
     * مشتریان و تامین‌کنندگان استفاده شده (resources/views/dashboard/index.blade.php
     * فقط <livewire:dashboard.overview /> را رندر می‌کند).
     *
     * اگر کاربر نقش دیگری داشته باشد (صندوقدار، حسابدار یا انباردار)
     * به داشبورد اختصاصی خودش هدایت می‌شود.
     */
    public function index()
    {
        $routeName = Auth::user()->dashboardRouteName();

        if ($routeName !== 'dashboard') {
            return redirect()->route($routeName);
        }

        return view('dashboard.index');
    }

    /** نمایش داشبورد نقش صندوقدار */
    public function cashier()
    {
        return $this->renderForRole(Role::CASHIER, 'dashboard.cashier');
    }

    /** نمایش داشبورد نقش حسابدار */
    public function accountant()
    {
        return $this->renderForRole(Role::ACCOUNTANT, 'dashboard.accountant');
    }

    /** نمایش داشبورد نقش انباردار */
    public function warehouse()
    {
        return $this->renderForRole(Role::WAREHOUSE, 'dashboard.warehouse');
    }

    /**
     * رندر ویوی داشبورد اختصاصی یک نقش؛ اگر نقش کاربر جاری با نقش
     * هدف هم‌خوانی نداشته باشد به داشبورد خودش هدایت می‌شود.
     */
    private function renderForRole(string $roleName, string $view)
    {
        $user = Auth::user();

        if ($user->role?->name !== $roleName) {
            return redirect()->route($user->dashboardRouteName());
        }

        return view($view);
    }
}
