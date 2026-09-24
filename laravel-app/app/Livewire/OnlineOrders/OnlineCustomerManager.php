<?php

namespace App\Livewire\OnlineOrders;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Customer;
use App\Services\OnlineShop\PullOnlineCustomers;
use Livewire\Component;
use Livewire\WithPagination;

class OnlineCustomerManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function pullNow(PullOnlineCustomers $puller): void
    {
        $this->authorizeAction('customers.view');

        try {
            $count = $puller->handle();
            session()->flash('success', $count ? "{$count} مشتری همگام شد." : 'مشتری جدیدی نبود.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $customers = Customer::query()
            ->whereNotNull('online_user_id')
            ->when($this->search !== '', function ($q) {
                $q->search($this->search);
            })
            ->latest('registered_online_at')
            ->paginate(15);

        return view('livewire.online-orders.online-customer-manager', compact('customers'));
    }
}
