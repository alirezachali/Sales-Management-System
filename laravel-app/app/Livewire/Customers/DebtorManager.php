<?php

namespace App\Livewire\Customers;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Customer;
use App\Models\CustomerAccountTransaction;
use App\Models\Payment;
use App\Models\Sale;
use App\Services\CustomerAccountService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DebtorManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    protected string $paginationTheme = 'bootstrap';

    /*
    |--------------------------------------------------------------------|
    |                              فیلترها                                |
    |--------------------------------------------------------------------|
    */
    public string $search = '';

    /*
    |--------------------------------------------------------------------|
    |                          مودال ثبت پرداخت                            |
    |--------------------------------------------------------------------|
    */
    public bool $showPayModal = false;
    public ?int $payingCustomerId = null;
    public string $pay_method = 'cash';
    public string $pay_amount = '';
    public string $pay_cash = '';
    public string $pay_card = '';

    /*
    |--------------------------------------------------------------------|
    |                        مودال سوابق خرید و پرداخت                     |
    |--------------------------------------------------------------------|
    */
    public bool $showHistoryModal = false;
    public ?int $historyCustomerId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('search');
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    |                        مودال ثبت پرداخت بدهی                         |
    |--------------------------------------------------------------------|
    */
    public function openPayModal(int $id): void
    {
        $this->payingCustomerId = $id;
        $this->pay_method = 'cash';
        $this->pay_amount = '';
        $this->pay_cash = '';
        $this->pay_card = '';
        $this->resetErrorBag();
        $this->showPayModal = true;
    }

    public function pay(): void
    {
        $this->authorizeAction('customers.balance');

        $customer = Customer::findOrFail($this->payingCustomerId);
        $balance = app(CustomerAccountService::class)->balance($customer->id);

        $this->validate(
            match ($this->pay_method) {
                'mixed' => [
                    'pay_cash' => ['required', 'numeric', 'min:1'],
                    'pay_card' => ['required', 'numeric', 'min:1'],
                ],
                default => [
                    'pay_amount' => ['required', 'numeric', 'min:1'],
                ],
            },
            [
                'pay_amount.required' => 'وارد کردن مبلغ پرداختی الزامی است.',
                'pay_amount.min' => 'مبلغ پرداختی باید بزرگ‌تر از صفر باشد.',
                'pay_cash.required' => 'وارد کردن مبلغ نقدی الزامی است.',
                'pay_cash.min' => 'مبلغ نقدی باید بزرگ‌تر از صفر باشد.',
                'pay_card.required' => 'وارد کردن مبلغ کارتخوان الزامی است.',
                'pay_card.min' => 'مبلغ کارتخوان باید بزرگ‌تر از صفر باشد.',
            ]
        );

        $parts = match ($this->pay_method) {
            'cash' => ['cash' => round((float) $this->pay_amount, 2)],
            'card' => ['card' => round((float) $this->pay_amount, 2)],
            'mixed' => [
                'cash' => round((float) $this->pay_cash, 2),
                'card' => round((float) $this->pay_card, 2),
            ],
        };

        $total = array_sum($parts);

        if ($total > $balance + 0.001) {
            $field = $this->pay_method === 'mixed' ? 'pay_cash' : 'pay_amount';
            $this->addError($field, 'مجموع مبلغ پرداختی از کل بدهی مشتری بیشتر است.');

            return;
        }

        DB::transaction(function () use ($customer, $parts) {
            // تخصیص پرداخت به فاکتورهای نسیه‌ی تسویه‌نشده (قدیمی‌ترین اول)
            $remaining = $parts;

            $openSales = Sale::query()
                ->where('customer_id', $customer->id)
                ->where('payment_type', 'credit')
                ->whereRaw('paid_amount < final_price')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($openSales as $sale) {
                $outstanding = (float) $sale->final_price - (float) $sale->paid_amount;

                foreach (['cash', 'card'] as $type) {
                    if (($remaining[$type] ?? 0) <= 0 || $outstanding <= 0) {
                        continue;
                    }

                    $apply = min($remaining[$type], $outstanding);

                    Payment::create([
                        'sale_id' => $sale->id,
                        'payment_type' => $type,
                        'amount' => $apply,
                    ]);

                    $sale->paid_amount = (float) $sale->paid_amount + $apply;
                    $remaining[$type] -= $apply;
                    $outstanding -= $apply;
                }

                $sale->save();
            }

            // ثبت تهاتر بدهی در گردش حساب مشتری (کسر از بدهی)
            $labels = ['cash' => 'نقدی', 'card' => 'کارتخوان'];

            foreach ($parts as $type => $amount) {
                if ($amount <= 0) {
                    continue;
                }

                CustomerAccountTransaction::create([
                    'customer_id' => $customer->id,
                    'type' => 'payment',
                    'amount' => $amount,
                    'description' => 'پرداخت بدهی ('.$labels[$type].')',
                ]);
            }
        });

        session()->flash(
            'success',
            'پرداخت '.number_format($total).' '.setting('currency', '').
            ' برای مشتری '.$customer->full_name.' با موفقیت ثبت شد'
        );

        $this->closeModals();
    }

    /*
    |--------------------------------------------------------------------|
    |                       مودال سوابق خرید و پرداخت                      |
    |--------------------------------------------------------------------|
    */
    public function openHistory(int $id): void
    {
        $this->historyCustomerId = $id;
        $this->showHistoryModal = true;
    }

    public function closeModals(): void
    {
        $this->showPayModal = false;
        $this->showHistoryModal = false;
        $this->payingCustomerId = null;
        $this->historyCustomerId = null;
        $this->pay_amount = '';
        $this->pay_cash = '';
        $this->pay_card = '';
        $this->resetErrorBag();
    }

    /*
    |--------------------------------------------------------------------|
    |                                کوئری‌ها                              |
    |--------------------------------------------------------------------|
    */
    private function debtorsQuery()
    {
        $balances = CustomerAccountTransaction::query()
            ->selectRaw(
                'customer_id, '.
                "COALESCE(SUM(CASE WHEN type IN ('sale','adjustment') THEN amount ELSE -amount END), 0) AS debt_balance"
            )
            ->groupBy('customer_id');

        $query = Customer::query()
            ->with('role')
            ->joinSub($balances, 'account_balances', 'account_balances.customer_id', '=', 'customers.id')
            ->where('account_balances.debt_balance', '>', 0)
            ->select('customers.*', 'account_balances.debt_balance')
            ->orderByDesc('account_balances.debt_balance');

        if ($this->search !== '') {
            $query->search($this->search);
        }

        return $query;
    }

    public function render()
    {
        $debtors = $this->debtorsQuery()->paginate(15);

        /* آمار کلی: تعداد بدهکاران و جمع کل بدهی */
        $balances = CustomerAccountTransaction::query()
            ->selectRaw(
                'customer_id, '.
                "COALESCE(SUM(CASE WHEN type IN ('sale','adjustment') THEN amount ELSE -amount END), 0) AS bal"
            )
            ->groupBy('customer_id');

        $stats = Customer::query()
            ->joinSub($balances, 'b', 'b.customer_id', '=', 'customers.id')
            ->where('b.bal', '>', 0)
            ->selectRaw('COUNT(*) as debtor_count, COALESCE(SUM(b.bal), 0) as total_debt')
            ->first();

        $todayCredit = CustomerAccountTransaction::query()
            ->where('type', 'sale')
            ->whereDate('created_at', today())
            ->sum('amount');

        /* داده‌های مودال ثبت پرداخت */
        $payingCustomer = null;
        $payingBalance = null;

        if ($this->showPayModal && $this->payingCustomerId) {
            $payingCustomer = Customer::find($this->payingCustomerId);

            if ($payingCustomer) {
                $payingBalance = app(CustomerAccountService::class)->balance($payingCustomer->id);
            }
        }

        /* داده‌های مودال سوابق */
        $historyCustomer = null;
        $historyTransactions = null;
        $historyBalance = null;

        if ($this->showHistoryModal && $this->historyCustomerId) {
            $historyCustomer = Customer::find($this->historyCustomerId);

            if ($historyCustomer) {
                $historyTransactions = $historyCustomer
                    ->accountTransactions()
                    ->latest()
                    ->limit(50)
                    ->get();
                $historyBalance = app(CustomerAccountService::class)->balance($historyCustomer->id);
            }
        }

        return view('livewire.customers.debtor-manager', [
            'debtors' => $debtors,
            'debtorCount' => (int) ($stats->debtor_count ?? 0),
            'totalDebt' => (float) ($stats->total_debt ?? 0),
            'todayCredit' => (float) $todayCredit,
            'payingCustomer' => $payingCustomer,
            'payingBalance' => $payingBalance,
            'historyCustomer' => $historyCustomer,
            'historyTransactions' => $historyTransactions,
            'historyBalance' => $historyBalance,
        ]);
    }
}
