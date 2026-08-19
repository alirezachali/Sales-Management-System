<?php

namespace App\Livewire\Brands;

use App\Models\Brand;
use App\Models\Supplier;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class BrandManager extends Component
{
    use WithPagination;

    public string $search = '';

    // فیلدهای فرم
    public ?int $brandId = null;
    public string $name = '';
    public ?string $description = null;
    public ?string $logo = null;
    public bool $is_active = true;
    public array $selectedSuppliers = [];

    // کنترل مودال‌ها
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected array $messages = [
        'name.required' => 'وارد کردن نام برند الزامی است.',
        'name.unique'   => 'برندی با این نام قبلاً ثبت شده است.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'name'                => [
                'required', 'string', 'max:100',
                Rule::unique('brands', 'name')->ignore($this->brandId),
            ],
            'description'         => ['nullable', 'string'],
            'logo'                => ['nullable', 'string', 'max:100'],
            'is_active'           => ['boolean'],
            'selectedSuppliers'   => ['array'],
            'selectedSuppliers.*' => ['exists:suppliers,id'],
        ];
    }

    /**
     * باز کردن مودال برای ساخت برند جدید
     */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    /**
     * باز کردن مودال برای ویرایش یک برند (همراه با تامین‌کنندگان متصل)
     */
    public function openEditModal(int $id): void
    {
        $brand = Brand::with('suppliers:id')->findOrFail($id);

        $this->brandId            = $brand->id;
        $this->name                = $brand->name;
        $this->description         = $brand->description;
        $this->logo                = $brand->logo;
        $this->is_active           = (bool) $brand->is_active;
        $this->selectedSuppliers   = $brand->suppliers->pluck('id')->toArray();

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    /**
     * ذخیره (ساخت یا ویرایش) برند + همگام‌سازی تامین‌کنندگان مرتبط
     */
    public function save(): void
    {
        $validated = $this->validate();

        $supplierIds = $validated['selectedSuppliers'] ?? [];
        unset($validated['selectedSuppliers']);

        if ($this->brandId) {
            $brand = Brand::findOrFail($this->brandId);
            $brand->update($validated);
            session()->flash('success', 'اطلاعات برند بروزرسانی شد.');
        } else {
            $brand = Brand::create($validated);
            session()->flash('success', 'برند جدید با موفقیت ثبت شد.');
        }

        // همگام‌سازی جدول واسط brand_supplier
        $brand->suppliers()->sync($supplierIds);

        $this->showFormModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Brand::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'برند حذف شد.');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
    }

    private function resetForm(): void
    {
        $this->reset(['brandId', 'name', 'description', 'logo', 'selectedSuppliers']);
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $brands = Brand::withCount('suppliers')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        return view('livewire.brands.brand-manager', [
            'brands'       => $brands,
            'allSuppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}