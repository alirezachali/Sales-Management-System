<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\AuthorizesActions;
use App\Livewire\Concerns\HasTodoQuickAdd;
use App\Models\Cashbox;
use App\Models\CustomerAccountTransaction;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AccountantOverview extends Component
{
    use AuthorizesActions;
    use HasTodoQuickAdd;

    /*
    |--------------------------------------------------------------------|
    |              بازه‌ی زمانی به‌روزرسانی خودکار (ثانیه)                 |
    |--------------------------------------------------------------------|
    | با wire:poll در ویو استفاده می‌شود تا آمار داشبورد بدون رفرش صفحه    |
    | هر چند ثانیه یک‌بار خودکار تازه شود.                               |
    */
    public int $pollingSeconds = 300;

    /*
    |--------------------------------------------------------------------|
    |     ورودی‌های بازه: کاربر تاریخ شمسی وارد/انتخاب می‌کند (مثل          |
    |     1405/06/01) و سمت سرور به میلادی تبدیل می‌شود. پیش‌فرض: ماه     |
    |     شمسی جاری.                                                       |
    |--------------------------------------------------------------------|
    */
    public string $dateFromJalali = '';
    public string $dateToJalali = '';

    /** خطاهای اعتبارسنجی بازه (کلیدها: from ،to ،range) */
    public array $dateErrors = [];

    public function mount(): void
    {
        $this->resetToCurrentMonth();
    }

    /** بازگرداندن بازه به ماه شمسی جاری (پیش‌فرض صفحه) */
    public function resetToCurrentMonth(): void
    {
        $this->dateFromJalali = Verta::now()->startMonth()->format('Y/m/d');
        $this->dateToJalali = Verta::now()->endMonth()->format('Y/m/d');
        $this->dateErrors = [];
    }

    /** اعتبارسنجی ورودی‌های شمسی و نرمال‌سازی نمایش آن‌ها */
    public function applyDates(): void
    {
        $this->dateErrors = [];

        $from = jalaliToGregorian($this->dateFromJalali);
        $to = jalaliToGregorian($this->dateToJalali);

        if ($from === null) {
            $this->dateErrors['from'] = 'تاریخ شروع معتبر نیست. مثال درست: 1405/06/01';
        }

        if ($to === null) {
            $this->dateErrors['to'] = 'تاریخ پایان معتبر نیست. مثال درست: 1405/06/31';
        }

        if ($from !== null && $to !== null && $from > $to) {
            $this->dateErrors['range'] = 'تاریخ شروع نمی‌تواند بعد از تاریخ پایان باشد.';
        }

        // نرمال‌سازی قالب نمایش (فقط وقتی معتبر است)
        if ($from !== null) {
            $this->dateFromJalali = gregorianToJalaliInput($from) ?? $this->dateFromJalali;
        }

        if ($to !== null) {
            $this->dateToJalali = gregorianToJalaliInput($to) ?? $this->dateToJalali;
        }
    }

    /*
    |--------------------------------------------------------------------|
    | حل بازه‌ی نهایی میلادی برای کوئری‌ها. اگر ورودی نامعتبر باشد،       |
    | به ماه جاری برمی‌گردیم تا صفحه هیچ‌وقت خالی/خطادار نشود.            |
    |--------------------------------------------------------------------|
    *
    * @return array{start: string, end: string, isFallback: bool, isDefaultMonth: bool}
    */
    protected function resolvedRange(): array
    {
        $from = jalaliToGregorian($this->dateFromJalali);
        $to = jalaliToGregorian($this->dateToJalali);

        $isFallback = false;

        if ($from === null || $to === null || $from > $to) {
            $from = Verta::now()->startMonth()->toCarbon()->toDateString();
            $to = Verta::now()->endMonth()->toCarbon()->toDateString();
            $isFallback = true;
        }

        $defaultStart = Verta::now()->startMonth()->toCarbon()->toDateString();
        $defaultEnd = Verta::now()->endMonth()->toCarbon()->toDateString();

        return [
            'start' => $from,
            'end' => $to,
            'isFallback' => $isFallback,
            'isDefaultMonth' => $from === $defaultStart && $to === $defaultEnd,
        ];
    }

    /*
    |--------------------------------------------------------------------|
    |                               رندر                                 |
    |--------------------------------------------------------------------|
    | داشبورد حسابدار: نمای مالی کل مجموعه — فروش‌ها، هزینه‌ها، سود،       |
    | حقوق‌ها، مانده‌ی صندوق‌ها، بدهی‌ها و طلب‌ها. آمارهای سنگین کش       |
    | می‌شوند تا هر poll به کوئری‌های اضافه و سنگین منجر نشود.           |
    */
    public function render()
    {
        $today = Carbon::today();
        $range = $this->resolvedRange();
        $monthStart = $range['start'];
        $monthEnd = $range['end'];
        $monthStartJalali = gregorianToJalaliInput($monthStart);
        $monthEndJalali = gregorianToJalaliInput($monthEnd);
        $isFallback = $range['isFallback'];
        $isDefaultMonth = $range['isDefaultMonth'];

        // ---------- الف) فروش امروز ----------
        $todaySales = Sale::where('status', '!=', 'cancelled')
            ->whereDate('created_at', $today)
            ->sum('final_price');

        $todayInvoices = Sale::where('status', '!=', 'cancelled')
            ->whereDate('created_at', $today)
            ->count();

        // ---------- ب) هزینه امروز ----------
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');

        // ---------- ج) فروش و سود ماه جاری ----------
        $sales = Sale::where('status', '!=', 'cancelled')
            ->whereBetween(DB::raw('DATE(created_at)'), [$monthStart, $monthEnd])
            ->with('items');

        $revenue = (float) (clone $sales)->sum('final_price');
        $discounts = (float) (clone $sales)->sum('discount');
        $invoiceCount = (clone $sales)->count();

        // بهای تمام‌شده‌ی کالای فروش‌رفته ماه از ستون cost_price ثبت‌شده در آیتم‌ها
        $cogs = (float) SaleItem::whereIn('sale_id', (clone $sales)->pluck('id'))
            ->sum(DB::raw('cost_price * quantity'));

        // هزینه‌های جاری بازه (بدون حقوق و دستمزد که جدا از Payroll خوانده می‌شود)
        $operatingExpenses = (float) Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
            ->sum('amount');

        // حقوق و دستمزد بازه: فیش‌ها به تفکیک ماه شمسی ذخیره شده‌اند؛ مجموع
        // همه‌ی ماه‌های شمسی‌ای که در بازه‌ی انتخابی قرار می‌گیرند حساب می‌شود.
        $payrollMonths = $this->shamsiMonthsInRange($monthStart, $monthEnd);

        $payrollMonth = (float) Payroll::where(function ($q) use ($payrollMonths) {
            foreach ($payrollMonths as $pm) {
                $q->orWhere(function ($qq) use ($pm) {
                    $qq->where('year', $pm[0])->where('month', $pm[1]);
                });
            }
        })->sum('net_pay');

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $operatingExpenses - $payrollMonth;
        $margin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0;

        // ---------- د) ارزش موجودی انبار ----------
        $inventoryValue = (float) \App\Models\Product::selectRaw('COALESCE(SUM(stock * buy_price), 0) as total')->value('total');

        // ---------- ه) مانده‌ی صندوق‌ها ----------
        $cashboxes = Cashbox::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'balance']);

        $totalCashboxBalance = (float) $cashboxes->sum('balance');

        // ---------- و) بدهی‌ها (پرداخت‌نی) و طلب‌ها (دریافت‌نی) ----------
        // بدهی به تامین‌کنندگان و سایر طلبکارها
        $debts = Debt::where('status', '!=', 'paid')->get();
        $totalDebtPayable = (float) $debts->sum(fn ($d) => $d->amount - $d->paid_amount);
        $overdueDebts = $debts->filter(fn ($d) => $d->due_date_status === 'overdue');

        // طلب از مشتریان (مانده‌ی مثبت حساب؛ همان منطق CashierOverview)
        $customerReceivables = cache()->remember('accountant-customer-receivables', now()->addMinutes(5), function () {
            return CustomerAccountTransaction::query()
                ->selectRaw("customer_id, SUM(CASE WHEN type IN ('sale','adjustment') THEN amount ELSE -amount END) as receivable")
                ->groupBy('customer_id')
                ->havingRaw("SUM(CASE WHEN type IN ('sale','adjustment') THEN amount ELSE -amount END) > 0")
                ->orderByDesc('receivable')
                ->with('customer')
                ->take(10)
                ->get()
                ->filter(fn ($row) => $row->customer !== null);
        });
        $totalCustomerReceivable = (float) $customerReceivables->sum('receivable');

        // ---------- ز) آخرین تراکنش‌های مالی ----------
        $latestSales = Sale::with(['user', 'customer'])
            ->where('status', '!=', 'cancelled')
            ->latest('created_at')
            ->take(8)
            ->get();

        $latestExpenses = Expense::with('category')
            ->latest('expense_date')
            ->take(8)
            ->get();

        // ---------- ح) حقوق‌های در انتظار پرداخت (برای بازه‌ی انتخابی) ----------
        $pendingPayrolls = Payroll::with('employee')
            ->where(function ($q) use ($payrollMonths) {
                foreach ($payrollMonths as $pm) {
                    $q->orWhere(function ($qq) use ($pm) {
                        $qq->where('year', $pm[0])->where('month', $pm[1]);
                    });
                }
            })
            ->where('status', '!=', 'paid')
            ->take(8)
            ->get();

        // ---------- ط) نمودار بازه‌ی انتخابی (فروش در برابر هزینه) ----------
        [$chartLabels, $chartSales, $chartExpenses] = $this->buildChartSeries($monthStart, $monthEnd);

        // به نمودار Chart.js سمت کلاینت اطلاع می‌دهیم داده‌ی تازه‌ای آماده است.
        // کنواس نمودار در ویو با wire:ignore محافظت شده، پس با هر poll دوباره
        // ساخته نمی‌شود و فقط از طریق این رویداد آپدیت می‌شود.
        $this->dispatch('accountant-chart-updated', labels: $chartLabels, sales: $chartSales, expenses: $chartExpenses);

        $todos = $this->currentUserTodos();

        return view('livewire.dashboard.accountant-overview', [
            'todaySales' => $todaySales,
            'todayInvoices' => $todayInvoices,
            'todayExpenses' => $todayExpenses,
            'revenue' => $revenue,
            'discounts' => $discounts,
            'invoiceCount' => $invoiceCount,
            'cogs' => $cogs,
            'operatingExpenses' => $operatingExpenses,
            'payrollMonth' => $payrollMonth,
            'grossProfit' => $grossProfit,
            'netProfit' => $netProfit,
            'margin' => $margin,
            'inventoryValue' => $inventoryValue,
            'cashboxes' => $cashboxes,
            'totalCashboxBalance' => $totalCashboxBalance,
            'debts' => $debts,
            'totalDebtPayable' => $totalDebtPayable,
            'overdueDebts' => $overdueDebts,
            'overdueDebtCount' => $overdueDebts->count(),
            'customerReceivables' => $customerReceivables,
            'totalCustomerReceivable' => $totalCustomerReceivable,
            'latestSales' => $latestSales,
            'latestExpenses' => $latestExpenses,
            'pendingPayrolls' => $pendingPayrolls,
            'monthStartJalali' => $monthStartJalali,
            'monthStartJalali' => $monthStartJalali,
            'monthEndJalali' => $monthEndJalali,
            'isFallback' => $isFallback,
            'isDefaultMonth' => $isDefaultMonth,
            'chartLabels' => $chartLabels,
            'chartSales' => $chartSales,
            'chartExpenses' => $chartExpenses,
            'todos' => $todos,
        ]);
    }

    /**
     * فهرست ماه‌های شمسی (سال، ماه) که بازه‌ی میلادی داده‌شده را پوشش می‌دهند.
     * فیش‌های حقوقی بر اساس سال/ماه شمسی ذخیره شده‌اند؛ برای محاسبه‌ی حقوق
     * بازه‌ی انتخابی باید همه‌ی ماه‌های شمسیِ داخل بازه را در نظر گرفت.
     *
     * @return array<int, array{0: int, 1: int}>
     */
    protected function shamsiMonthsInRange(string $start, string $end): array
    {
        $from = Verta::instance(Carbon::parse($start))->startMonth();
        $to = Verta::instance(Carbon::parse($end))->endMonth();

        $months = [];
        $cursor = $from->copy();

        while ($cursor <= $to) {
            $key = $cursor->format('Y-m');

            if (! isset($months[$key])) {
                $months[$key] = [
                    (int) $cursor->format('Y'),
                    (int) $cursor->format('m'),
                ];
            }

            $cursor = $cursor->copy()->addMonth();
        }

        return array_values($months);
    }

    /**
     * ساخت برچسب‌ها و داده‌ی نمودار فروش در برابر هزینه برای بازه‌ی انتخابی.
     * برای جلوگیری از ایجاد نمودار بیش ازحد شلوغ، بازه بر اساس روز (تا ۳۱
     * روز) یا ماه (برای بازه‌های بلندتر) به بخش‌هایی تقسیم می‌شود.
     *
     * @return array{0: array<int, string>, 1: array<int, float>, 2: array<int, float>}
     */
    protected function buildChartSeries(string $start, string $end): array
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);
        $dayCount = (int) $startDate->diffInDays($endDate) + 1;

        $labels = [];
        $sales = [];
        $expenses = [];

        // بازه طولانی (بیش از ۳۱ روز) به صورت ماهانه تراکم می‌شود
        if ($dayCount > 31) {
            return $this->buildMonthlyChartSeries($startDate, $endDate);
        }

        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $labels[] = jalaliDate($date, 'm/d');

            $sales[] = cache()->remember("accountant-chart-sales-{$date->toDateString()}", now()->addMinutes(5), fn () => (float) Sale::where('status', '!=', 'cancelled')
                ->whereDate('created_at', $date)
                ->sum('final_price'));

            $expenses[] = cache()->remember("accountant-chart-expenses-{$date->toDateString()}", now()->addMinutes(5), fn () => (float) Expense::whereDate('expense_date', $date)
                ->sum('amount'));
        }

        return [$labels, $sales, $expenses];
    }

    /**
     * نمودار ماهانه برای بازه‌های بلندتر از ۳۱ روز؛ به‌جای روز، هر ماه شمسی
     * یک نقطه دارد.
     *
     * @return array{0: array<int, string>, 1: array<int, float>, 2: array<int, float>}
     */
    protected function buildMonthlyChartSeries(Carbon $startDate, Carbon $endDate): array
    {
        $labels = [];
        $sales = [];
        $expenses = [];

        foreach ($this->shamsiMonthsInRange($startDate->toDateString(), $endDate->toDateString()) as [$year, $month]) {
            if (count($labels) >= 24) {
                break;
            }

            $v = Verta::parse($year.'-'.str_pad((string) $month, 2, '0', STR_PAD_LEFT).'-01');
            $mStart = $v->toCarbon()->toDateString();
            $mEnd = $v->copy()->endMonth()->toCarbon()->toDateString();

            // فقط ماه‌هایی که با بازه‌ی انتخابی تلاقی دارند را لحاظ می‌کنیم
            $s = max($mStart, $startDate->toDateString());
            $e = min($mEnd, $endDate->toDateString());

            $labels[] = $year.'/'.str_pad((string) $month, 2, '0', STR_PAD_LEFT);

            $sales[] = (float) Sale::where('status', '!=', 'cancelled')
                ->whereBetween(DB::raw('DATE(created_at)'), [$s, $e])
                ->sum('final_price');

            $expenses[] = (float) Expense::whereBetween('expense_date', [$s, $e])
                ->sum('amount');
        }

        return [$labels, $sales, $expenses];
    }
}
