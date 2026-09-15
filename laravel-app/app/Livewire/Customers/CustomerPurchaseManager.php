<?php

namespace App\Livewire\Customers;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Customer;
use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerPurchaseManager extends Component
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

    public string $sortBy = 'amount';

    /*
    |--------------------------------------------------------------------|
    |                    مودال لیست خریدهای یک مشتری                       |
    |--------------------------------------------------------------------|
    */
    public bool $showPurchasesModal = false;

    public ?int $purchasesCustomerId = null;

    /*
    |--------------------------------------------------------------------|
    |                        مودال مشاهده جزئیات فاکتور                    |
    |--------------------------------------------------------------------|
    */
    public bool $showInvoiceModal = false;

    public ?int $invoiceSaleId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('search');
        $this->sortBy = 'amount';
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    |                              مودال‌ها                                |
    |--------------------------------------------------------------------|
    */
    public function openPurchases(int $customerId): void
    {
        $this->authorizeAction('customers.view');

        $this->purchasesCustomerId = $customerId;
        $this->showPurchasesModal = true;
    }

    public function openInvoice(int $saleId): void
    {
        $this->authorizeAction('sales.view');

        $this->invoiceSaleId = $saleId;
        $this->showInvoiceModal = true;
    }

    public function closeInvoiceModal(): void
    {
        $this->showInvoiceModal = false;
        $this->invoiceSaleId = null;
    }

    public function closeModals(): void
    {
        $this->showPurchasesModal = false;
        $this->showInvoiceModal = false;
        $this->purchasesCustomerId = null;
        $this->invoiceSaleId = null;
    }

    /*
    |--------------------------------------------------------------------|
    |                                کوئری‌ها                              |
    |--------------------------------------------------------------------|
    */
    public function render()
    {
        /* آمار خرید هر مشتری (شامل فاکتورهای لغوشده هم می‌شود) */
        $stats = Sale::query()
            ->whereNotNull('customer_id')
            ->selectRaw(
                'customer_id, '.
                'COUNT(*) AS total_purchases, '.
                'COALESCE(SUM(final_price), 0) AS total_purchases_amount, '.
                'MAX(created_at) AS last_purchase_date'
            )
            ->groupBy('customer_id');

        $buyers = Customer::query()
            ->with('role')
            ->joinSub($stats, 'purchase_stats', 'purchase_stats.customer_id', '=', 'customers.id')
            ->when($this->search !== '', fn ($q) => $q->search($this->search))
            ->select(
                'customers.*',
                'purchase_stats.total_purchases',
                'purchase_stats.total_purchases_amount',
                'purchase_stats.last_purchase_date'
            )
            ->orderBy(
                match ($this->sortBy) {
                    'name' => 'customers.first_name',
                    'count' => 'purchase_stats.total_purchases',
                    'date' => 'purchase_stats.last_purchase_date',
                    default => 'purchase_stats.total_purchases_amount',
                },
                $this->sortBy === 'name' ? 'asc' : 'desc'
            )
            ->paginate(15);

        /* داده‌های مودال لیست خریدهای مشتری */
        $purchasesCustomer = null;
        $customerInvoices = collect();

        if ($this->showPurchasesModal && $this->purchasesCustomerId) {
            $purchasesCustomer = Customer::find($this->purchasesCustomerId);

            if ($purchasesCustomer) {
                $customerInvoices = Sale::query()
                    ->withSum('items', 'quantity')
                    ->where('customer_id', $purchasesCustomer->id)
                    ->latest('id')
                    ->paginate(10, ['*'], 'invoices_page');
            }
        }

        /* داده‌های مودال جزئیات فاکتور */
        $invoiceSale = null;

        if ($this->showInvoiceModal && $this->invoiceSaleId) {
            $invoiceSale = Sale::query()
                ->with(['items.product', 'payments', 'user', 'customer'])
                ->find($this->invoiceSaleId);
        }

        return view('livewire.customers.customer-purchase-manager', [
            'buyers' => $buyers,
            'purchasesCustomer' => $purchasesCustomer,
            'customerInvoices' => $customerInvoices,
            'invoiceSale' => $invoiceSale,
        ]);
    }
}
