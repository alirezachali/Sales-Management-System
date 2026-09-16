<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\AuthorizesActions;
use App\Livewire\Concerns\HasTodoQuickAdd;
use App\Models\CustomerAccountTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Livewire\Component;

class CashierOverview extends Component
{
    use AuthorizesActions;
    use HasTodoQuickAdd;

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
    | آمار داشبورد صندوقدار: فقط فروش‌ها و فاکتورهای ثبت‌شده توسط خودِ    |
    | همین کاربر (صندوقدار جاری) محاسبه و نمایش داده می‌شود.              |
    */
    public function render()
    {
        $userId = auth()->id();
        $today = Carbon::today();

        // مبلغ فروش امروز همین صندوقدار
        $todaySales = Sale::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->sum('final_price');

        // تعداد فاکتورهای فروش امروز همین صندوقدار
        $todayInvoices = Sale::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();

        // تعداد کل کالاهای فروش‌رفته امروز توسط همین صندوقدار
        $todayItemsSold = SaleItem::whereHas('sale', function ($query) use ($userId, $today) {
                $query->where('user_id', $userId)
                    ->whereDate('created_at', $today);
            })
            ->sum('quantity');

        // آخرین فروش‌های ثبت‌شده توسط همین صندوقدار
        $latestSales = Sale::with('customer')
            ->where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get();

        // کارهای انجام‌نشده‌ی این کاربر برای نمایش در کارت لیست کارها
        $todos = $this->currentUserTodos();

        // مشتریان بدهکار: مانده‌ی مثبت حساب (فروش/تنظیم مثبت منهای
        // پرداخت‌ها و عودت‌ها)؛ دقیقاً همان منطقی که در سرویس
        // CustomerAccountService::balance استفاده شده.
        $debtors = CustomerAccountTransaction::query()
            ->selectRaw("customer_id, SUM(CASE WHEN type IN ('sale','adjustment') THEN amount ELSE -amount END) as debt_amount")
            ->groupBy('customer_id')
            ->havingRaw("SUM(CASE WHEN type IN ('sale','adjustment') THEN amount ELSE -amount END) > 0")
            ->orderByDesc('debt_amount')
            ->with('customer')
            ->take(10)
            ->get()
            ->filter(fn ($row) => $row->customer !== null);

        return view('livewire.dashboard.cashier-overview', compact(
            'todaySales',
            'todayInvoices',
            'todayItemsSold',
            'latestSales',
            'todos',
            'debtors',
        ));
    }
}
