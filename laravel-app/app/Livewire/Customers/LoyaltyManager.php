<?php

namespace App\Livewire\Customers;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Customer;
use App\Models\PointTransaction;
use App\Services\LoyaltyService;
use Livewire\Component;
use Livewire\WithPagination;

class LoyaltyManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    public string $search = '';

    public ?int $adjustCustomerId = null;
    public $adjustPoints = 0;
    public ?string $adjustReason = null;
    public bool $showAdjustModal = false;

    public ?int $historyCustomerId = null;
    public bool $showHistoryModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openAdjust(int $customerId): void
    {
        $this->authorizeAction('loyalty.adjust');

        $this->adjustCustomerId = $customerId;
        $this->adjustPoints = 0;
        $this->adjustReason = null;
        $this->showAdjustModal = true;
    }

    public function saveAdjust(LoyaltyService $loyalty): void
    {
        $this->authorizeAction('loyalty.adjust');

        $this->validate([
            'adjustPoints' => ['required', 'integer'],
            'adjustReason' => ['required', 'string', 'max:255'],
        ], [
            'adjustPoints.required' => 'مقدار امتیاز (مثبت یا منفی) را وارد کنید.',
            'adjustReason.required' => 'دلیل تنظیم امتیاز الزامی است.',
        ]);

        $customer = Customer::findOrFail($this->adjustCustomerId);

        try {
            $loyalty->adjust($customer, (int) $this->adjustPoints, $this->adjustReason);
            session()->flash('success', 'امتیاز مشتری به‌روزرسانی شد.');
        } catch (\InvalidArgumentException $e) {
            $this->addError('adjustPoints', $e->getMessage());

            return;
        }

        $this->showAdjustModal = false;
    }

    public function openHistory(int $customerId): void
    {
        $this->historyCustomerId = $customerId;
        $this->showHistoryModal = true;
    }

    public function closeModals(): void
    {
        $this->showAdjustModal = false;
        $this->showHistoryModal = false;
        $this->historyCustomerId = null;
    }

    public function render()
    {
        $customers = Customer::query()
            ->with('role:id,name,color,icon')
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->orderByDesc('points')
            ->paginate(15);

        $history = collect();
        $historyCustomer = null;

        if ($this->showHistoryModal && $this->historyCustomerId) {
            $historyCustomer = Customer::find($this->historyCustomerId);

            $history = PointTransaction::with('user:id,name')
                ->where('customer_id', $this->historyCustomerId)
                ->latest()
                ->limit(100)
                ->get();
        }

        return view('livewire.customers.loyalty-manager', [
            'customers' => $customers,
            'history' => $history,
            'historyCustomer' => $historyCustomer,
            'topEarners' => Customer::orderByDesc('points')->limit(5)->get(),
            'totalPoints' => (int) Customer::sum('points') - (int) Customer::sum('spent_points'),
        ]);
    }
}
