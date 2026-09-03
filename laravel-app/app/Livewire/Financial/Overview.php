<?php

namespace App\Livewire\Financial;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Overview extends Component
{
    /*
    |--------------------------------------------------------------------|
    | ورودی‌های بازه: کاربر تاریخ شمسی وارد/انتخاب می‌کند (مثل 1405/06/01)|
    | و سمت سرور به میلادی تبدیل می‌شود. پیش‌فرض: ماه شمسی جاری.          |
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
    | حل بازه‌ی نهایی میلادی برای کوئری‌ها. اگر ورودی نامعتبر باشد،      |
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

    public function render()
    {
        $range = $this->resolvedRange();
        $monthStart = $range['start'];
        $monthEnd = $range['end'];

        // فقط برای نمایش در ویو (شمسی) — سمت سرور از میلادی بالا استفاده می‌شود
        $displayFrom = gregorianToJalaliInput($monthStart);
        $displayTo = gregorianToJalaliInput($monthEnd);

        // ۱) مبلغ فروش بازه (مبلغ نهایی فاکتورها)
        $salesMonth = (float) Sale::whereBetween(DB::raw('DATE(created_at)'), [$monthStart, $monthEnd])
            ->sum('final_price');

        // ۲) سود ناخالص فروش بازه: Σ (مبلغ فروش - قیمت خرید)
        // SaleItem قیمت خرید را ذخیره نمی‌کند، پس از buy_price فعلی محصول استفاده می‌کنیم.
        $saleItems = SaleItem::whereHas('sale', function ($q) use ($monthStart, $monthEnd) {
            $q->whereBetween(DB::raw('DATE(created_at)'), [$monthStart, $monthEnd]);
        })->with('product:id,buy_price')->get();

        $grossProfit = 0.0;
        foreach ($saleItems as $item) {
            $buyPrice = (float) ($item->product?->buy_price ?? 0);
            $grossProfit += (float) $item->line_total - ((float) $item->quantity * $buyPrice);
        }

        // ۳) حقوق پرداختی بازه = هزینه‌هایی که به کارمند وصل‌اند
        $salaryMonth = (float) Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
            ->whereNotNull('employee_id')
            ->sum('amount');

        // ۴) سایر هزینه‌های بازه = هزینه‌هایی که به کارمند وصل نیستند
        $otherExpensesMonth = (float) Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
            ->whereNull('employee_id')
            ->sum('amount');

        $totalExpensesMonth = $salaryMonth + $otherExpensesMonth;

        // ۵) سود خالص بازه = سود ناخالص - کل هزینه‌ها (حقوق + سایر)
        $netProfit = $grossProfit - $totalExpensesMonth;

        // ۶) ارزش موجودی انبار به قیمت خرید: Σ (موجودی × قیمت خرید) — لحظه‌ای، مستقل از بازه
        $inventoryValue = (float) Product::selectRaw('COALESCE(SUM(stock * buy_price), 0) as total')->value('total');

        return view('livewire.financial.overview', [
            'salesMonth' => $salesMonth,
            'grossProfit' => $grossProfit,
            'salaryMonth' => $salaryMonth,
            'otherExpensesMonth' => $otherExpensesMonth,
            'totalExpensesMonth' => $totalExpensesMonth,
            'netProfit' => $netProfit,
            'inventoryValue' => $inventoryValue,
            'displayFrom' => $displayFrom,
            'displayTo' => $displayTo,
            'isDefaultMonth' => $range['isDefaultMonth'],
            'isFallback' => $range['isFallback'],
        ]);
    }
}
