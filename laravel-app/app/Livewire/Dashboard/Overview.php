<?php

namespace App\Livewire\Dashboard;

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class Overview extends Component
{
    /*
    |--------------------------------------------------------------------|
    |                 بازه‌ی زمانی به‌روزرسانی خودکار (ثانیه)                |
    |--------------------------------------------------------------------|
    | با wire:poll در ویو استفاده می‌شود تا آمار داشبورد بدون رفرش صفحه   |
    | و بدون دخالت کاربر، هر چند ثانیه یک‌بار خودکار تازه شود.            |
    */
    public int $pollingSeconds = 30;

    /*
    |--------------------------------------------------------------------|
    |                              رندر                                  |
    |--------------------------------------------------------------------|
    | تمام آمارها همان منطقی است که قبلاً در DashboardController@index   |
    | محاسبه می‌شد؛ فقط محل اجرا به کامپوننت زنده منتقل شده تا با هر بار  |
    | poll، بدون رفرش صفحه دوباره محاسبه و نمایش داده شود.                |
    */
    public function render()
    {
        $today = Carbon::today();

        $todaySales = Sale::whereDate('created_at', $today)
            ->sum('final_price');

        $todayInvoices = Sale::whereDate('created_at', $today)
            ->count();

        $productsCount = Product::count();

        $lowStockProducts = Product::where('stock', '<=', 5)
            ->count();

        $latestSales = Sale::with('user')
            ->latest()
            ->take(10)
            ->get();

        $lowStockList = Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(10)
            ->get();

        [$labels, $chartData] = $this->buildChartSeries();

        // به نمودار Chart.js سمت کلاینت اطلاع می‌دهیم داده‌ی تازه‌ای آماده است.
        // کنواس نمودار در ویو با wire:ignore محافظت شده، پس با هر poll دوباره
        // ساخته نمی‌شود و فقط از طریق این رویداد آپدیت می‌شود (بدون پرش/چشمک زدن).
        $this->dispatch('sales-chart-updated', labels: $labels, data: $chartData);

        return view('livewire.dashboard.overview', compact(
            'todaySales',
            'todayInvoices',
            'productsCount',
            'lowStockProducts',
            'latestSales',
            'lowStockList',
            'labels',
            'chartData',
        ));
    }

    /**
     * ساخت برچسب‌ها و داده‌های نمودار فروش ۳۰ روز اخیر.
     *
     * @return array{0: array<int, string>, 1: array<int, float|int>}
     */
    protected function buildChartSeries(): array
    {
        $period = CarbonPeriod::create(now()->subDays(29), now());

        $labels = [];
        $data = [];

        foreach ($period as $date) {
            $labels[] = $date->format('m/d');

            $data[] = Sale::whereDate('created_at', $date)
                ->sum('final_price');
        }

        return [$labels, $data];
    }
}