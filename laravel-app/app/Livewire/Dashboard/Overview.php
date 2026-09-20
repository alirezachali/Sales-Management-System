<?php

namespace App\Livewire\Dashboard;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Todo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class Overview extends Component
{
    /*
    |--------------------------------------------------------------------------
    |                 بازه زمانی به‌روزرسانی خودکار (ثانیه)
    |--------------------------------------------------------------------------
    | با wire:poll در ویو استفاده می‌شود تا آمار داشبورد بدون رفرش صفحه
    | و بدون دخالت کاربر، هر چند ثانیه یک بار خودکار تازه شود.
    */
    public int $pollingSeconds = 900;

    /*
    |--------------------------------------------------------------------------
    |                              رندر
    |--------------------------------------------------------------------------
    | تمام آمارهای ساده استفاده از caching انجام می‌شود تا دوباره محاسبه نشوند
    */
    public function render()
    {
        $today = Carbon::today();

        $todaySales = cache()->remember("dashboard-today-sales-{$today}", now()->addMinutes(5), fn () => Sale::whereDate('created_at', $today)
            ->sum('final_price'));

        $todayInvoices = cache()->remember("dashboard-today-invoices-{$today}", now()->addMinutes(5), fn () => Sale::whereDate('created_at', $today)
            ->count());

        $productsCount = cache()->remember('dashboard-products-count', now()->addMinutes(15), fn () => Product::count());

        $lowStockProducts = cache()->remember('dashboard-low-stock-count', now()->addMinutes(15), fn () => Product::where('stock', '<=', 5)
            ->count());

        $latestSales = cache()->remember('dashboard-latest-sales', now()->addMinutes(15), fn () => Sale::with('user')
            ->latest('created_at')
            ->take(10)
            ->get());

        $lowStockList = cache()->remember('dashboard-low-stock-list', now()->addMinutes(15), fn () => Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->orderBy('id')
            ->take(10)
            ->get());

        $inProgressTodos = cache()->remember('dashboard-todos-in-progress', now()->addMinutes(15), fn () => Todo::where('status', 'in_progress')
            ->with('assignee')
            ->latest()
            ->take(10)
            ->get());

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
            'inProgressTodos',
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
            $labels[] = jalaliDate($date, 'm/d');

            $data[] = cache()->remember("dashboard-chart-{$date->toDateString()}", now()->addMinutes(5), fn () => Sale::whereDate('created_at', $date)
                ->sum('final_price'));
        }

        return [$labels, $data];
    }
}
