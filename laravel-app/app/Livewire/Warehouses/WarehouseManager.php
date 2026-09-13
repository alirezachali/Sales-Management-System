<?php

namespace App\Livewire\Warehouses;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class WarehouseManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    public string $search = '';

    public ?int $viewingId = null;

    public string $viewSearch = '';

    // فرم انبار
    public ?int $warehouseId = null;
    public string $name = '';
    public string $code = '';
    public ?string $address = null;
    public ?string $phone = null;
    public ?string $manager_name = null;
    public bool $is_default = false;
    public bool $is_active = true;
    public ?string $notes = null;

    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'code' => [
                'required', 'string', 'max:30',
                Rule::unique('warehouses', 'code')->ignore($this->warehouseId),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'manager_name' => ['nullable', 'string', 'max:120'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'نام انبار الزامی است.',
            'code.required' => 'کد انبار الزامی است.',
            'code.unique' => 'این کد انبار قبلاً ثبت شده است.',
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
        $warehouse = Warehouse::findOrFail($id);

        $this->warehouseId = $warehouse->id;
        $this->name = $warehouse->name;
        $this->code = $warehouse->code;
        $this->address = $warehouse->address;
        $this->phone = $warehouse->phone;
        $this->manager_name = $warehouse->manager_name;
        $this->is_default = (bool) $warehouse->is_default;
        $this->is_active = (bool) $warehouse->is_active;
        $this->notes = $warehouse->notes;

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeAction($this->warehouseId ? 'warehouses.edit' : 'warehouses.create');

        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            if ($this->warehouseId) {
                $warehouse = Warehouse::findOrFail($this->warehouseId);
                $warehouse->update($validated);
            } else {
                $warehouse = Warehouse::create($validated);
            }

            if ($validated['is_default']) {
                Warehouse::where('id', '!=', $warehouse->id)
                    ->update(['is_default' => false]);
            }
        });

        session()->flash('success', $this->warehouseId ? 'انبار به‌روزرسانی شد.' : 'انبار جدید ثبت شد.');

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function viewInventory(int $id): void
    {
        $this->viewingId = $id;
        $this->viewSearch = '';
    }

    public function closeInventory(): void
    {
        $this->viewingId = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorizeAction('warehouses.delete');

        $warehouse = Warehouse::findOrFail($this->deletingId);

        if ($warehouse->is_default) {
            session()->flash('error', 'امکان حذف انبار پیش‌فرض وجود ندارد.');
            $this->showDeleteModal = false;

            return;
        }

        if ($warehouse->stocks()->where('quantity', '!=', 0)->exists()) {
            session()->flash('error', 'ابتدا موجودی این انبار را به انبار دیگری منتقل کنید.');
            $this->showDeleteModal = false;

            return;
        }

        $warehouse->delete();
        session()->flash('success', 'انبار حذف شد.');

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
    }

    private function resetForm(): void
    {
        $this->reset(['warehouseId', 'name', 'code', 'address', 'phone', 'manager_name', 'notes']);
        $this->is_default = false;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $warehouses = Warehouse::query()
            ->withCount('stocks')
            ->withSum('stocks', 'quantity')
            ->when($this->search, function ($q) {
                $q->where(fn ($w) => $w
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%"));
            })
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->paginate(12);

        $viewing = $this->viewingId ? Warehouse::find($this->viewingId) : null;

        $inventory = collect();

        if ($viewing) {
            $inventory = ProductWarehouseStock::with('product:id,name,barcode,unit,stock')
                ->where('warehouse_id', $viewing->id)
                ->whereHas('product', function ($q) {
                    if ($this->viewSearch !== '') {
                        $q->where(fn ($w) => $w
                            ->where('name', 'like', "%{$this->viewSearch}%")
                            ->orWhere('barcode', 'like', "%{$this->viewSearch}%"));
                    }
                })
                ->orderBy('quantity', 'desc')
                ->paginate(15);
        }

        return view('livewire.warehouses.warehouse-manager', [
            'warehouses' => $warehouses,
            'viewing' => $viewing,
            'inventory' => $inventory,
            'totalStockUnits' => ProductWarehouseStock::sum('quantity'),
            'activeCount' => Warehouse::where('is_active', true)->count(),
        ]);
    }
}
