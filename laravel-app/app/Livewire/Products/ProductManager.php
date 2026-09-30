<?php

namespace App\Livewire\Products;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\StockMovement;
use App\Services\BarcodeService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductManager extends Component
{
    use WithPagination;
    use WithFileUploads;
    use AuthorizesActions;

    // protected string $paginationTheme = 'bootstrap';

    /*
    |--------------------------------------------------------------------------
    |                              فیلترها                                |
    |--------------------------------------------------------------------------
    */
    public string $search = '';
    public string $filterCategoryId = '';

    /*
    |--------------------------------------------------------------------------
    |                        فیلدهای فرم افزودن/ویرایش                    |
    |--------------------------------------------------------------------------
    */
    public ?int $editingProductId = null;
    public string $barcode = '';
    public string $name = '';
    public string $category_id = '';
    public string $brand_id = '';
    public $buy_price = 0;
    public $sell_price = 0;
    public $stock = 0;
    public string $unit = 'عدد';
    public string $is_active = '1';

    // تصاویر محصول: صف جدید (TemporaryUploadedFile) و تصاویر ذخیره‌شده‌ی فعلی
    public array $photos = [];
    public array $existingImages = [];

    /*
    |--------------------------------------------------------------------------
    |                        کنترل نمایش مودال‌ها                          |
    |--------------------------------------------------------------------------
    */
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    /*
    |--------------------------------------------------------------------------
    |                        واکنش به تغییر فیلترها                       |
    |--------------------------------------------------------------------------
    */
    protected string $lastSearchTerm = '';

    protected int $lastFilterCategoryId = 0;

    public function updatingSearch(): void
    {
        if ($this->lastSearchTerm !== $this->search) {
            $this->lastSearchTerm = $this->search;
            $this->resetPage();
        }
    }

    public function updatingFilterCategoryId(): void
    {
        if ($this->lastFilterCategoryId !== (int) $this->filterCategoryId) {
            $this->lastFilterCategoryId = (int) $this->filterCategoryId;
            $this->resetPage();
        }
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
            'brand_id' => ['nullable', 'exists:brands,id'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
            'photos' => ['array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:4096'],
            'existingImages' => ['array', 'max:10'],
            'existingImages.*.id' => ['nullable', 'integer'],
            'existingImages.*.path' => ['nullable', 'string'],
            // 'brand_id' validation handled above
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
            'photos.max' => 'حداکثر ۱۰ تصویر مجاز است.',
            'photos.*.image' => 'فایل انتخاب‌شده باید تصویر باشد.',
            'photos.*.mimes' => 'فرمت تصویر مجاز نیست (jpeg, png, jpg, webp, gif).',
            'photos.*.max' => 'هر تصویر حداکثر ۴ مگابایت باشد.',
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
        $product = Product::with('images')->findOrFail($productId);

        $this->editingProductId = $product->id;
        $this->barcode = $product->barcode;
        $this->name = $product->name;
        $this->category_id = (string) $product->category_id;
        $this->brand_id = (string) $product->brand_id;
        $this->buy_price = $product->buy_price;
        $this->sell_price = $product->sell_price;
        $this->stock = $product->stock;
        $this->unit = $product->unit;
        $this->is_active = $product->is_active ? '1' : '0';
        $this->photos = [];
        $this->existingImages = $product->images->map(fn ($img) => [
            'id' => $img->id,
            'path' => $img->path,
        ])->all();

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    /**
     * حذف یک تصویر ذخیره‌شده‌ی موجود (فقط از لیست محلی؛ هنگام ذخیره اعمال می‌شود)
     */
    public function removeExistingImage(int $imageId): void
    {
        $this->existingImages = array_values(array_filter(
            $this->existingImages,
            fn ($img) => (int) $img['id'] !== $imageId
        ));
    }

    /**
     * حذف یک تصویر موقتِ تازه انتخاب‌شده از صف
     */
    public function removePhoto(int $index): void
    {
        $photos = $this->photos;
        unset($photos[$index]);
        $this->photos = array_values($photos);
    }

    /**
     * جابه‌جایی ترتیب تصاویر موقت (بالا/پایین)
     */
    public function movePhoto(int $index, string $direction): void
    {
        $photos = $this->photos;
        $target = $direction === 'up' ? $index - 1 : $index + 1;
        if ($target < 0 || $target >= count($photos)) {
            return;
        }
        $tmp = $photos[$target];
        $photos[$target] = $photos[$index];
        $photos[$index] = $tmp;
        $this->photos = array_values($photos);
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
        $this->authorizeAction($this->editingProductId ? 'products.edit' : 'products.create');

        $data = $this->validate();
        $data['category_id'] = $data['category_id'] ?: null;
        $data['brand_id'] = $data['brand_id'] ?: null;

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

        // ذخیره تصاویر
        $this->syncImages($product);

        $this->showFormModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    /**
     * همگام‌سازی تصاویر محصول:
     *  - حذف تصاویر موجودی که کاربر از لیست برداشته است (هم از DB و هم از دیسک)
     *  - ذخیره تصاویر جدید انتخاب‌شده
     */
    protected function syncImages(Product $product): void
    {
        // ۱) حذف تصاویری که در لیست باقی‌مانده نیستند
        $keptIds = collect($this->existingImages)
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        $removedImages = $product->images()
            ->whereNotIn('id', $keptIds)
            ->get();

        foreach ($removedImages as $image) {
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
            $image->delete();
        }

        // تعیین sort_orderِ ادامه‌دار بر اساس تصاویر باقی‌مانده به ترتیب موجود
        $baseOrder = $product->images()->whereIn('id', $keptIds)->count();

        // ۲) ذخیره تصاویر جدید
        foreach ($this->photos as $photo) {
            $path = $photo->store('products', 'public');
            $product->images()->create([
                'path' => $path,
                'sort_order' => $baseOrder++,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------|
    |                                 حذف                                 |
    |--------------------------------------------------------------------|
    */
    public function delete(): void
    {
        $this->authorizeAction('products.delete');

        if ($this->deletingId) {
            $product = Product::with('images')->findOrFail($this->deletingId);
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
            }
            $product->delete();
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
        $this->brand_id = '';
        $this->buy_price = 0;
        $this->sell_price = 0;
        $this->stock = 0;
        $this->unit = 'عدد';
        $this->is_active = '1';
        $this->photos = [];
        $this->existingImages = [];
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Product::with(['category', 'brand', 'images']);

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
            'brands' => Brand::all(),
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
