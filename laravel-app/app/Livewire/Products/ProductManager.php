<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\BarcodeService;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ProductManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /*
    |--------------------------------------------------------------------|
    |                              فیلترها                                |
    |--------------------------------------------------------------------|
    */
    public string $search = '';
    public string $filterCategoryId = '';

    /*
    |--------------------------------------------------------------------|
    |                        فیلدهای فرم افزودن/ویرایش                    |
    |--------------------------------------------------------------------|
    */
    public ?int $editingProductId = null;
    public string $barcode = '';
    public string $name = '';
    public string $category_id = '';
    public $buy_price = 0;
    public $sell_price = 0;
    public $stock = 0;
    public string $unit = 'عدد';
    public string $is_active = '1';

    /*
    |--------------------------------------------------------------------|
    |                        کنترل نمایش مودال‌ها                          |
    |--------------------------------------------------------------------|
    */
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    /*
    |--------------------------------------------------------------------|
    |                        واکنش به تغییر فیلترها                       |
    |--------------------------------------------------------------------|
    */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategoryId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterCategoryId']);
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    |                          قوانین اعتبارسنجی                          |
    |--------------------------------------------------------------------|
    */
    protected function rules(): array
    {
        return [
            'barcode' => [
                'required',
                'string',
                'max:50',
                $this->editingProductId
                    ? Rule::unique('products', 'barcode')->ignore($this->editingProductId)
                    : Rule::unique('products', 'barcode'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'barcode.required' => 'وارد کردن بارکد الزامی است.',
            'barcode.unique' => 'این بارکد قبلاً برای کالای دیگری ثبت شده است.',
            'name.required' => 'وارد کردن نام کالا الزامی است.',
            'category_id.exists' => 'دسته‌بندی انتخاب‌شده معتبر نیست.',
            'buy_price.required' => 'وارد کردن قیمت خرید الزامی است.',
            'sell_price.required' => 'وارد کردن قیمت فروش الزامی است.',
            'stock.required' => 'وارد کردن موجودی الزامی است.',
            'unit.required' => 'وارد کردن واحد الزامی است.',
        ];
    }

    /*
    |--------------------------------------------------------------------|
    |                              مودال‌ها                               |
    |--------------------------------------------------------------------|
    */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal(int $productId): void
    {
        $product = Product::findOrFail($productId);

        $this->editingProductId = $product->id;
        $this->barcode = $product->barcode;
        $this->name = $product->name;
        $this->category_id = (string) $product->category_id;
        $this->buy_price = $product->buy_price;
        $this->sell_price = $product->sell_price;
        $this->stock = $product->stock;
        $this->unit = $product->unit;
        $this->is_active = $product->is_active ? '1' : '0';

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function confirmDelete(int $productId): void
    {
        $this->deletingId = $productId;
        $this->showDeleteModal = true;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    /*
    |--------------------------------------------------------------------|
    |                          تولید بارکد خودکار                         |
    |--------------------------------------------------------------------|
    */
    public function generateBarcode(BarcodeService $barcodeService): void
    {
        $this->barcode = $barcodeService->generate();
    }

    /*
    |--------------------------------------------------------------------|
    |                          ذخیره (افزودن/ویرایش)                      |
    |--------------------------------------------------------------------|
    */
    public function save(): void
    {
        $data = $this->validate();
        $data['category_id'] = $data['category_id'] ?: null;

        if ($this->editingProductId) {
            $product = Product::findOrFail($this->editingProductId);
            $product->update($data);

            session()->flash('success', 'کالا با موفقیت ویرایش شد');
        } else {
            $product = Product::create($data);

            if ($product->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'initial',
                    'quantity' => $product->stock,
                    'description' => 'موجودی اولیه کالا',
                ]);
            }

            session()->flash('success', 'کالا با موفقیت ثبت شد');
        }

        $this->showFormModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    |                                 حذف                                 |
    |--------------------------------------------------------------------|
    */
    public function delete(): void
    {
        if ($this->deletingId) {
            Product::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'کالا با موفقیت حذف شد');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    |                             ریست فرم                                |
    |--------------------------------------------------------------------|
    */
    public function resetForm(): void
    {
        $this->editingProductId = null;
        $this->barcode = '';
        $this->name = '';
        $this->category_id = '';
        $this->buy_price = 0;
        $this->sell_price = 0;
        $this->stock = 0;
        $this->unit = 'عدد';
        $this->is_active = '1';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Product::with('category');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('barcode', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterCategoryId !== '') {
            $query->where('category_id', $this->filterCategoryId);
        }

        $products = $query->latest()->paginate(20);

        return view('livewire.products.product-manager', [
            'products' => $products,
            'categories' => Category::all(),
            'totalProducts' => Product::count(),
            'activeProducts' => Product::where('is_active', true)->count(),
            'inactiveProducts' => Product::where('is_active', false)->count(),
            'lowStockProducts' => Product::where('stock', '<=', 5)->count(),
        ]);
        // توجه: برخلاف تلاش اول، اینجا از ->layout() استفاده نمی‌کنیم. این کامپوننت
        // به‌عنوان یک full-page Livewire route رندر نمی‌شود؛ بلکه درست مثل ماژول
        // تامین‌کنندگان، داخل یک ویوی Blade معمولی (resources/views/products/index.blade.php)
        // که خودش @extends('layouts.app') را دارد، با تگ <livewire:products.product-manager />
        // قرار می‌گیرد. همین چیزی بود که در پیاده‌سازی اول باعث خالی ماندن صفحه شد:
        // Livewire به‌صورت پیش‌فرض دنبال resources/views/components/layouts/app.blade.php
        // (لایوت کامپوننتی) می‌گشت که در این پروژه اصلاً وجود ندارد.
    }
}