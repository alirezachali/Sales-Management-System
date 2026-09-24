<?php

namespace App\Livewire\OnlineOrders;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\OnlineOrder;
use App\Services\OnlineShop\ProcessOnlineOrder;
use App\Services\OnlineShop\PullOnlineOrders;
use Livewire\Component;
use Livewire\WithPagination;

class OnlineOrderManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    public string $search = '';

    public string $filterStatus = 'all';

    public ?int $dispatchingId = null;

    public string $courier_name = '';

    public string $courier_phone = '';

    public ?int $rejectingId = null;

    public string $reject_reason = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function pullNow(PullOnlineOrders $puller): void
    {
        $this->authorizeAction('online-orders.view');

        try {
            $count = $puller->handle();
            session()->flash('success', $count ? "{$count} سفارش جدید دریافت شد." : 'سفارش جدیدی نبود.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function fulfill(int $id, ProcessOnlineOrder $processor): void
    {
        $this->authorizeAction('online-orders.process');

        try {
            $processor->fulfill(OnlineOrder::query()->findOrFail($id));
            session()->flash('success', 'فاکتور صادر و موجودی کم شد.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openDispatch(int $id): void
    {
        $this->dispatchingId = $id;
        $this->courier_name = '';
        $this->courier_phone = '';
    }

    public function dispatchOrder(ProcessOnlineOrder $processor): void
    {
        $this->authorizeAction('online-orders.dispatch');
        $this->validate([
            'courier_name' => 'required|string|max:120',
            'courier_phone' => 'required|string|max:32',
        ]);

        try {
            $processor->dispatch(
                OnlineOrder::query()->findOrFail($this->dispatchingId),
                $this->courier_name,
                $this->courier_phone,
            );
            $this->dispatchingId = null;
            session()->flash('success', 'سفارش به پیک سپرده شد.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function deliver(int $id, ProcessOnlineOrder $processor): void
    {
        $this->authorizeAction('online-orders.dispatch');

        try {
            $processor->deliver(OnlineOrder::query()->findOrFail($id));
            session()->flash('success', 'تحویل ثبت شد.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openReject(int $id): void
    {
        $this->rejectingId = $id;
        $this->reject_reason = '';
    }

    public function rejectOrder(ProcessOnlineOrder $processor): void
    {
        $this->authorizeAction('online-orders.process');
        $this->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        try {
            $processor->reject(
                OnlineOrder::query()->findOrFail($this->rejectingId),
                $this->reject_reason,
            );
            $this->rejectingId = null;
            session()->flash('success', 'سفارش رد شد.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $orders = OnlineOrder::query()
            ->when($this->search !== '', function ($q) {
                $q->where(function ($q) {
                    $q->where('customer_name', 'like', '%'.$this->search.'%')
                        ->orWhere('customer_phone', 'like', '%'.$this->search.'%')
                        ->orWhere('idempotency_key', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);

        $stats = [
            'received' => OnlineOrder::query()->whereIn('status', ['received', 'pending'])->count(),
            'packing' => OnlineOrder::query()->where('status', 'packing')->count(),
            'out_for_delivery' => OnlineOrder::query()->where('status', 'out_for_delivery')->count(),
            'delivered' => OnlineOrder::query()->where('status', 'delivered')->count(),
        ];

        return view('livewire.online-orders.online-order-manager', compact('orders', 'stats'));
    }
}
