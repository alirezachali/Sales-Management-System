<?php

namespace App\Livewire\Customers;

use App\Models\CustomerRole;
use Livewire\Component;

class CustomerRoleManager extends Component
{
    public ?int $editingId = null;

    public string $name = '';
    public string $icon = 'bi-award';
    public string $color = 'secondary';
    public $sort_order = 0;
    public $discount_percent = 0;
    public $min_purchase_count = 0;
    public $min_purchase_amount = 0;
    public ?string $description = null;
    public bool $is_default = false;
    public bool $is_active = true;

    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:30'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_purchase_count' => ['required', 'integer', 'min:0'],
            'min_purchase_amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'وارد کردن عنوان رده الزامی است.',
            'discount_percent.max' => 'درصد تخفیف نمی‌تواند بیشتر از ۱۰۰ باشد.',
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $role = CustomerRole::findOrFail($id);

        $this->editingId = $role->id;
        $this->name = $role->name;
        $this->icon = $role->icon ?? 'bi-award';
        $this->color = $role->color;
        $this->sort_order = $role->sort_order;
        $this->discount_percent = $role->discount_percent;
        $this->min_purchase_count = $role->min_purchase_count;
        $this->min_purchase_amount = $role->min_purchase_amount;
        $this->description = $role->description;
        $this->is_default = (bool) $role->is_default;
        $this->is_active = (bool) $role->is_active;

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->icon = 'bi-award';
        $this->color = 'secondary';
        $this->sort_order = 0;
        $this->discount_percent = 0;
        $this->min_purchase_count = 0;
        $this->min_purchase_amount = 0;
        $this->description = null;
        $this->is_default = false;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $role = CustomerRole::findOrFail($this->editingId);
            $role->update($data);
            session()->flash('success', 'رده باشگاه مشتریان با موفقیت ویرایش شد');
        } else {
            $role = CustomerRole::create($data);
            session()->flash('success', 'رده باشگاه مشتریان با موفقیت ثبت شد');
        }

        // فقط یک رده می‌تواند «پیش‌فرض» باشد؛ در صورت انتخاب، بقیه غیرفعال می‌شوند
        if ($role->is_default) {
            CustomerRole::where('id', '!=', $role->id)->update(['is_default' => false]);
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            CustomerRole::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'رده باشگاه مشتریان حذف شد');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function render()
    {
        return view('livewire.customers.customer-role-manager', [
            'roles' => CustomerRole::withCount('customers')->orderBy('sort_order')->get(),
        ]);
    }
}