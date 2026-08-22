<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    /**
     * نمایش صفحه‌ی داشبورد مدیریتی.
     *
     * تمام منطق واکشی آمار (فروش امروز، فاکتورها، کالاهای کم‌موجود،
     * آخرین فروش‌ها و نمودار ۳۰ روز اخیر) به کامپوننت Livewire
     * App\Livewire\Dashboard\Overview منتقل شده است. این ویو فقط
     * لایوت اصلی برنامه را بارگذاری کرده و کامپوننت زنده را در خود
     * جای می‌دهد؛ دقیقاً همان الگویی که برای ماژول‌های محصولات،
     * مشتریان و تامین‌کنندگان استفاده شده (resources/views/dashboard/index.blade.php
     * فقط <livewire:dashboard.overview /> را رندر می‌کند).
     *
     * مسیر (routes/web.php) بدون تغییر باقی مانده است:
     * Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
     */
    public function index()
    {
        return view('dashboard.index');
    }
}