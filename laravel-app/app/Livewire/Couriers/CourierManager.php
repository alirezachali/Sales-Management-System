<?php

namespace App\Livewire\Couriers;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Courier;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class CourierManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    public string $search = '';

    public string $filterActive = 'all';

    public bool $showFormModal = false;
    public ?int $courierId = null;
    public string $name = '';
    public string $phone = '';
    public string $vehicle_type = 'motorcycle';
    public string $plate_number = '';
    public ?string $notes = null;
    public bool $is_active = true;

    public bool $showDeleteModal = false;
    public ?int $deletingId = null;
    public string $deletingName = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:32'],
            'vehicle_type' => ['required', Rule::in(['motorcycle', 'bicycle', 'car', 'walking', 'other'])],
            'plate_number' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $courier = Courier::findOrFail($id);

        $this->courierId = $courier->id;
        $this->name = $courier->name;
        $this->phone = $courier->phone;
        $this->vehicle_type = $courier->vehicle_type;
        $this->plate_number = $courier->plate_number;
        $this->notes = $courier->notes;
        $this->is_active = (bool) $courier->is_active;

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeAction($this->courierId ? 'couriers.edit' : 'couriers.create');

        $this->validate();

        if ($this->courierId) {
            $courier = Courier::findOrFail($this->courierId);
            $courier->update($this->only(['name', 'phone', 'vehicle_type', 'plate_number', 'notes', 'is_active']));
            session()->flash('success', 'پیک به‌روزرسانی شد.');
        } else {
            Courier::create($this->only(['name', 'phone', 'vehicle_type', 'plate_number', 'notes', 'is_active']));
            session()->flash('success', 'پیک جدید ثبت شد.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $courier = Courier::findOrFail($id);
        $this->deletingId = $id;
        $this->deletingName = $courier->name;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorizeAction('couriers.delete');

        $courier = Courier::findOrFail($this->deletingId);
        $courier->delete();

        session()->flash('success', 'پیک حذف شد.');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->deletingName = '';
    }

    private function resetForm(): void
    {
        $this->reset(['courierId', 'name', 'phone', 'plate_number', 'notes', 'deletingId', 'deletingName']);
        $this->vehicle_type = 'motorcycle';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $couriers = Courier::query()
            ->when($this->search !== '', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('plate_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterActive !== 'all', fn ($q) => $q->where('is_active', $this->filterActive === 'active'))
            ->withCount('onlineOrders')
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->paginate(15);

        $vehicleTypes = [
            'motorcycle' => 'موتورسیکلت',
            'bicycle' => 'دوچرخه',
            'car' => 'خودرو',
            'walking' => 'پیاده',
            'other' => 'سایر',
        ];

        return view('livewire.couriers.courier-manager', [
            'couriers' => $couriers,
            'vehicleTypes' => $vehicleTypes,
        ]);
    }
}