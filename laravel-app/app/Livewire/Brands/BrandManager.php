<?php

namespace App\Livewire\Brands;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Brand;
use App\Models\Supplier;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class BrandManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

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

    // protected array $messages = [
    //     'name.required' => __('brands.validation.name_required'),
    //     'name.unique'   => __('brands.validation.name_unique'),
    // ];

    protected function messages(): array
    {
        return [
            'name.required' => __('brands.validation.name_required'),
            'name.unique'   => __('brands.validation.name_unique'),
        ];
    }


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
        $this->authorizeAction($this->brandId ? 'brands.edit' : 'brands.create');

        $validated = $this->validate();

        $supplierIds = $validated['selectedSuppliers'] ?? [];
        unset($validated['selectedSuppliers']);

        if ($this->brandId) {
            $brand = Brand::findOrFail($this->brandId);
            $brand->update($validated);
            session()->flash('success', __('brands.messages.updated'));
        } else {
            $brand = Brand::create($validated);
            session()->flash('success', __('brands.messages.created'));
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
        $this->authorizeAction('brands.delete');

        if ($this->deletingId) {
            Brand::findOrFail($this->deletingId)->delete();
            session()->flash('success', __('brands.messages.deleted'));
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
