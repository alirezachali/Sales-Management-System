<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\CustomerAccountTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CashierOverview extends Component
{
    use AuthorizesActions;

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
    |              فیلدهای فرم افزودن کار جدید (مودال کارت کارها)          |
    |--------------------------------------------------------------------|
    */
    public bool $showFormModal = false;
    public string $title = '';
    public ?string $description = null;
    public string $priority = Todo::PRIORITY_MEDIUM;
    /** تاریخ سررسید شمسی برای نمایش و انتخاب توسط کاربر (مثل 1405/06/11) */
    public ?string $due_date_jalali = null;
    public ?string $due_date = null;

    public array $priorityLabels = [
        'low' => 'کم',
        'medium' => 'متوسط',
        'high' => 'زیاد',
    ];

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->title = '';
        $this->description = null;
        $this->priority = Todo::PRIORITY_MEDIUM;
        $this->due_date = null;
        $this->due_date_jalali = null;
        $this->resetErrorBag();
    }

    /*
    |--------------------------------------------------------------------|
    | همگام‌سازی تاریخ شمسی ورودی کاربر با تاریخ میلادی سمت سرور          |
    |--------------------------------------------------------------------|
    */
    public function updatedDueDateJalali(): void
    {
        $val = trim((string) $this->due_date_jalali);

        if ($val === '') {
            $this->due_date = null;
            $this->resetErrorBag('due_date_jalali');
            return;
        }

        $gregorian = jalaliToGregorian($val);

        if ($gregorian !== null) {
            $this->due_date = $gregorian;
            $this->resetErrorBag('due_date_jalali');
        }
    }

    /** ثبت کار جدید؛ انجام‌دهنده‌ی کار خودِ همین صندوقدار است. */
    public function save(): void
    {
        $this->authorizeAction('todos.create');

        $val = trim((string) $this->due_date_jalali);

        if ($val !== '') {
            $gregorian = jalaliToGregorian($val);

            if ($gregorian === null) {
                $this->addError('due_date_jalali', 'تاریخ سررسید معتبر نیست. مثال درست: 1405/06/11');
                return;
            }

            $this->due_date = $gregorian;
        } else {
            $this->due_date = null;
        }

        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::in(array_keys($this->priorityLabels))],
        ]);

        Todo::create([
            'user_id' => auth()->id(),
            'assigned_to' => auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => Todo::STATUS_PENDING,
            'priority' => $data['priority'],
            'due_date' => $this->due_date,
        ]);

        session()->flash('success', 'کار جدید با موفقیت ثبت شد');

        $this->showFormModal = false;
        $this->resetForm();
    }

    /** تکمیل/لغو تکمیل یک کار */
    public function toggleComplete(int $id): void
    {
        $this->authorizeAction('todos.edit');

        $todo = Todo::where('assigned_to', auth()->id())->findOrFail($id);
        $todo->toggleComplete();
    }


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
        $todos = Todo::with('assignee')
            ->where('assigned_to', $userId)
            ->where('status', '!=', Todo::STATUS_COMPLETED)
            ->latest()
            ->take(10)
            ->get();

        // مشتریان بدهکار: مانده‌ی مثبت حساب (فروش/تنخواه‌نمای مثبت منهای
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
