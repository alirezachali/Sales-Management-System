<?php

namespace App\Livewire\Dashboard;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Todo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
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

        $productsCount = cache()->rememberForever('dashboard-products-count', fn () => Product::count());

        $lowStockProducts = cache()->rememberForever('dashboard-low-stock-count', fn () => Product::where('stock', '<=', 5)
            ->count());

        $latestSales = cache()->rememberForever('dashboard-latest-sales', fn () => Sale::with('user')
            ->latest('created_at')
            ->take(10)
            ->get());

        $lowStockList = cache()->rememberForever('dashboard-low-stock-list', fn () => Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->orderBy('id')
            ->take(10)
            ->get());

        $inProgressTodos = cache()->rememberForever('dashboard-todos-in-progress', fn () => Todo::where('status', 'in_progress')
            ->with('assignee')
            ->latest()
            ->take(10)
            ->get());

        [$labels, $chartData] = $this->buildChartSeries();

        // نمودار پرفروش‌ترین کالاهای ماه جاری شمسی
        [$topProductLabels, $topProductData, $topProductRevenue] = $this->buildTopProducts();

        // تفکیک مالی ماه جاری شمسی برای چارت دایره‌ای
        $finance = $this->buildFinanceBreakdown();

        // به نمودار Chart.js سمت کلاینت اطلاع می‌دهیم داده‌ی تازه‌ای آماده است.
        // کنواس نمودارها در ویو با wire:ignore محافظت شده‌اند، پس با هر poll
        // دوباره ساخته نمی‌شوند و فقط از طریق این رویدادها آپدیت می‌شوند
        // (بدون پرش/چشمک زدن).
        $this->dispatch('sales-chart-updated', labels: $labels, data: $chartData);
        $this->dispatch('top-products-chart-updated', labels: $topProductLabels, data: $topProductData, revenue: $topProductRevenue);
        $this->dispatch('finance-chart-updated', labels: $finance['labels'], data: $finance['data'], colors: $finance['colors'], total: $finance['sales']);

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
            'topProductLabels',
            'topProductData',
            'topProductRevenue',
            'finance',
        ));
    }

    /**
     * بازه‌ی میلادی ماه جاری شمسی (اول تا آخر ماه) برای کوئری‌ها.
     *
     * @return array{0: string, 1: string}
     */
    protected function jalaliMonthRange(): array
    {
        return [
            Verta::now()->startMonth()->toCarbon()->toDateString(),
            Verta::now()->endMonth()->toCarbon()->toDateString(),
        ];
    }

    /**
     * پرفروش‌ترین کالاهای ماه جاری بر اساس تعداد فروش.
     *
     * خروجی سه آرایه‌ی هم‌طول است: برچسب (نام کالا)، تعداد فروش و مبلغ درآمد؛
     * این‌گونه مستقیم قابل پاس دادن به Chart.js سمت کلاینت است.
     *
     * @return array{0: array<int, string>, 1: array<int, float>, 2: array<int, float>}
     */
    protected function buildTopProducts(int $limit = 7): array
    {
        [$monthStart, $monthEnd] = $this->jalaliMonthRange();

        $rows = cache()->remember("dashboard-top-products-{$monthStart}", now()->addMinutes(5), fn () => SaleItem::query()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->whereBetween(DB::raw('DATE(sales.created_at)'), [$monthStart, $monthEnd])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc(DB::raw('SUM(sale_items.quantity)'))
            ->limit($limit)
            ->get([
                'products.name as name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.line_total) as total_revenue'),
            ]));

        return [
            $rows->pluck('name')->all(),
            $rows->map(fn ($row) => round((float) $row->total_qty, 3))->all(),
            $rows->map(fn ($row) => (float) $row->total_revenue)->all(),
        ];
    }

    /**
     * تفکیک مالی ماه جاری برای چارت دایره‌ای.
     *
     * فروش ماه به چهار جزء تقسیم می‌شود: بهای تمام‌شده‌ی کالا، حقوق کارمندان،
     * سایر هزینه‌ها و سود خالص. اگر سود منفی باشد به‌جای «سود خالص» برچسب
     * «زیان» با مقدار مطلق نمایش داده می‌شود تا چارت معنادار بماند. منطق
     * محاسبه دقیقاً هم‌خوان با گزارش مالی (App\Livewire\Financial\Overview) است
     * تا اعداد داشبورد و گزارش مالی با هم اختلاف نداشته باشند.
     *
     * @return array<string, mixed>
     */
    protected function buildFinanceBreakdown(): array
    {
        [$monthStart, $monthEnd] = $this->jalaliMonthRange();

        $totals = cache()->remember("dashboard-finance-{$monthStart}", now()->addMinutes(5), function () use ($monthStart, $monthEnd) {
            // مبلغ فروش بازه
            $sales = (float) Sale::whereBetween(DB::raw('DATE(created_at)'), [$monthStart, $monthEnd])
                ->sum('final_price');

            // سود ناخالص: Σ (مبلغ آیتم فروش - تعداد × قیمت خرید)
            $saleItems = SaleItem::whereHas('sale', function ($query) use ($monthStart, $monthEnd) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$monthStart, $monthEnd]);
            })->with('product:id,buy_price')->get();

            $grossProfit = 0.0;
            foreach ($saleItems as $item) {
                $buyPrice = (float) ($item->product?->buy_price ?? 0);
                $grossProfit += (float) $item->line_total - ((float) $item->quantity * $buyPrice);
            }

            // حقوق کارمندان = هزینه‌های متصل به کارمند
            $salary = (float) Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->whereNotNull('employee_id')
                ->sum('amount');

            // سایر هزینه‌ها = هزینه‌های بدون کارمند
            $otherExpenses = (float) Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->whereNull('employee_id')
                ->sum('amount');

            return [
                'sales' => $sales,
                'salary' => $salary,
                'otherExpenses' => $otherExpenses,
                'netProfit' => $grossProfit - ($salary + $otherExpenses),
                'cogs' => max(0, $sales - $grossProfit),
            ];
        });

        $isLoss = $totals['netProfit'] < 0;

        $slices = [
            ['label' => __('dash.admin.fin_cogs'), 'value' => $totals['cogs'], 'color' => '#6366f1'],
            ['label' => __('dash.admin.fin_salary'), 'value' => $totals['salary'], 'color' => '#f59e0b'],
            ['label' => __('dash.admin.fin_other_expenses'), 'value' => $totals['otherExpenses'], 'color' => '#ef4444'],
            [
                'label' => $isLoss ? __('dash.admin.fin_loss') : __('dash.admin.fin_net_profit'),
                'value' => abs($totals['netProfit']),
                'color' => $isLoss ? '#b91c1c' : '#10b981',
            ],
        ];

        return [
            'sales' => $totals['sales'],
            'salary' => $totals['salary'],
            'otherExpenses' => $totals['otherExpenses'],
            'netProfit' => $totals['netProfit'],
            'isLoss' => $isLoss,
            'labels' => array_column($slices, 'label'),
            'data' => array_map(fn ($slice) => round($slice['value'], 2), $slices),
            'colors' => array_column($slices, 'color'),
        ];
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
