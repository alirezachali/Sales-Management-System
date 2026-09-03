<?php

namespace App\Livewire\PurchaseInvoices;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\BarcodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PurchaseInvoiceManager extends Component
{
    /** تاریخ میلادی برای ذخیره در دیتابیس (سمت سرور) */
    public string $purchase_date = '';
    /** تاریخ شمسی برای نمایش و انتخاب توسط کاربر (مثل 1405/06/11) */
    public string $purchase_date_jalali = '';
    public string $supplier_id = '';
    public string $payment_method = 'cash';
    public ?string $notes = null;

    public string $product_barcode = '';
    public string $product_search = '';
    public array $searchResults = [];

    public array $items = [];
    public int $itemCounter = 0;

    public bool $showProductModal = false;
    public bool $showNewProductModal = false;

    public ?int $editingProductId = null;
    public string $new_barcode = '';
    public string $new_name = '';
    public string $new_category_id = '';
    public $new_buy_price = 0;
    public $new_sell_price = 0;
    public $new_stock = 0;
    public string $new_unit = 'عدد';
    public string $new_is_active = '1';

    protected function rules(): array
    {
        return [
            'purchase_date' => ['required', 'date'],
            'purchase_date_jalali' => ['required', 'string'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'transfer', 'credit', 'other'])],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'purchase_date_jalali.required' => 'وارد کردن تاریخ خرید الزامی است.',
            'purchase_date.required' => 'وارد کردن تاریخ خرید الزامی است.',
            'purchase_date.date' => 'تاریخ خرید معتبر نیست.',
            'supplier_id.required' => 'انتخاب تامین‌کننده الزامی است.',
        ];
    }

    protected function productRules(): array
    {
        return [
            'new_barcode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'barcode')->when($this->editingProductId, function ($query) {
                    return $query->ignore($this->editingProductId);
                }),
            ],
            'new_name' => ['required', 'string', 'max:255'],
            'new_category_id' => ['nullable', 'exists:categories,id'],
            'new_buy_price' => ['required', 'numeric', 'min:0'],
            'new_sell_price' => ['required', 'numeric', 'min:0'],
            'new_stock' => ['nullable', 'numeric', 'min:0'],
            'new_unit' => ['required', 'string', 'max:20'],
            'new_is_active' => ['required', 'boolean'],
        ];
    }

    public function mount(): void
    {
        $this->purchase_date = now()->toDateString();
        $this->purchase_date_jalali = gregorianToJalaliInput($this->purchase_date) ?? '';
    }

    /*
    |--------------------------------------------------------------------|
    | همگام‌سازی تاریخ شمسی ورودی کاربر با تاریخ میلادی سمت سرور          |
    |--------------------------------------------------------------------|
    */
    public function updatedPurchaseDateJalali(): void
    {
        $gregorian = jalaliToGregorian($this->purchase_date_jalali);

        if ($gregorian !== null) {
            $this->purchase_date = $gregorian;
            $this->resetErrorBag('purchase_date_jalali');
        }
    }

    /** تبدیل ورودی شمسی به میلادی؛ در صورت نامعتبر بودن خطای فارسی ثبت می‌کند */
    private function syncPurchaseDate(): bool
    {
        $gregorian = jalaliToGregorian($this->purchase_date_jalali);

        if ($gregorian === null) {
            $this->addError('purchase_date_jalali', 'تاریخ خرید معتبر نیست. مثال درست: 1405/06/11');
            return false;
        }

        $this->purchase_date = $gregorian;
        // نرمال‌سازی قالب نمایش (مثل 1405/6/1 به 1405/06/01)
        $this->purchase_date_jalali = gregorianToJalaliInput($gregorian) ?? $this->purchase_date_jalali;

        return true;
    }

    public function render()
    {
        return view('livewire.purchase-invoices.purchase-invoice-manager', [
            'suppliers' => Supplier::active()->orderBy('name')->get(['id', 'name', 'company_name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function updatedProductSearch(): void
    {
        if (strlen($this->product_search) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Product::where('name', 'like', '%' . $this->product_search . '%')
            ->orWhere('barcode', 'like', '%' . $this->product_search . '%')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function selectSearchResult(int $productId): void
    {
        $product = Product::find($productId);
        if ($product) {
            $this->addProductToInvoice($product);
            $this->product_search = '';
            $this->searchResults = [];
        }
    }

    public function processBarcode(): void
    {
        if (empty($this->product_barcode)) {
            return;
        }

        $product = Product::where('barcode', $this->product_barcode)->first();

        if ($product) {
            $this->addProductToInvoice($product);
            $this->product_barcode = '';
        }
    }

    private function addProductToInvoice(Product $product): void
    {
        $exists = collect($this->items)->contains('product_id', $product->id);

        if ($exists) {
            session()->flash('error', 'این محصول قبلاً به لیست اضافه شده است.');
            return;
        }

        $this->items[] = [
            'id' => $this->itemCounter++,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'barcode' => $product->barcode,
            'quantity' => 1,
            'current_buy_price' => $product->buy_price,
            'current_sell_price' => $product->sell_price,
            'buy_price' => '',
            'sell_price' => '',
            'total' => $product->buy_price * 1,
        ];

        session()->flash('success', 'محصول ' . $product->name . ' به لیست اضافه شد.');
    }

    public function openNewProductModal(): void
    {
        $this->resetProductForm();
        $this->showNewProductModal = true;
    }

    // متد ویرایش اطلاعات محصول که فعلا همرا با دکمه ویرایش غیر فعال میشود به خاطر نداشتن کاربرد
    // public function openEditProductModal(int $itemId): void
    // {
    //     $item = collect($this->items)->firstWhere('id', $itemId);
    //     if (!$item) {
    //         return;
    //     }

    //     $product = Product::find($item['product_id']);

    //     $this->editingProductId = $product->id;
    //     $this->new_barcode = $product->barcode;
    //     $this->new_name = $product->name;
    //     $this->new_category_id = (string) $product->category_id;
    //     $this->new_buy_price = $product->buy_price;
    //     $this->new_sell_price = $product->sell_price;
    //     $this->new_stock = 0;
    //     $this->new_unit = $product->unit;
    //     $this->new_is_active = $product->is_active ? '1' : '0';

    //     $this->showProductModal = true;
    // }

    public function resetProductForm(): void
    {
        $this->editingProductId = null;
        $this->new_barcode = '';
        $this->new_name = '';
        $this->new_category_id = '';
        $this->new_buy_price = 0;
        $this->new_sell_price = 0;
        $this->new_stock = 0;
        $this->new_unit = 'عدد';
        $this->new_is_active = '1';
        $this->resetErrorBag();
    }

    public function closeProductModal(): void
    {
        $this->showProductModal = false;
        $this->showNewProductModal = false;
        $this->resetProductForm();
    }

    public function generateBarcode(BarcodeService $barcodeService): void
    {
        $this->new_barcode = $barcodeService->generate();
    }

    public function saveNewProduct(): void
    {
        $this->validate($this->productRules());

        $product = Product::create([
            'barcode' => $this->new_barcode,
            'name' => $this->new_name,
            'category_id' => $this->new_category_id ?: null,
            'buy_price' => $this->new_buy_price,
            'sell_price' => $this->new_sell_price,
            'stock' => $this->new_stock,
            'unit' => $this->new_unit,
            'is_active' => $this->new_is_active == '1',
        ]);

        if ($product->stock > 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'initial',
                'quantity' => $product->stock,
                'description' => 'موجودی اولیه کالا از فاکتور خرید',
            ]);
        }

        $this->addProductToInvoice($product);
        $this->closeProductModal();

        session()->flash('success', 'محصول جدید با موفقیت ایجاد شد.');
    }

    public function updateItemPrice(int $itemId, string $field, $value): void
    {
        foreach ($this->items as $key => $item) {
            if ($item['id'] === $itemId) {
                $this->items[$key][$field] = $value;

                $quantity = (float) ($this->items[$key]['quantity'] ?: 0);
                $buyPrice = (float) ($this->items[$key]['buy_price'] ?: $this->items[$key]['current_buy_price']);
                $this->items[$key]['total'] = $quantity * $buyPrice;
                break;
            }
        }
    }

    public function removeItem(int $itemId): void
    {
        $this->items = collect($this->items)->reject(function ($item) use ($itemId) {
            return $item['id'] === $itemId;
        })->values()->toArray();
    }

    public function getTotalAmountProperty(): float
    {
        return collect($this->items)->sum(function ($item) {
            $quantity = (float) ($item['quantity'] ?: 0);
            $buyPrice = (float) ($item['buy_price'] ?: $item['current_buy_price']);
            return $quantity * $buyPrice;
        });
    }

    public function getTotalItemsCountProperty(): int
    {
        return count($this->items);
    }

    public function getTotalQuantityProperty(): float
    {
        return collect($this->items)->sum(function ($item) {
            return (float) ($item['quantity'] ?: 0);
        });
    }

    public function getNextInvoiceNumberProperty(): string
    {
        return $this->generateInvoiceNumber();
    }

    private function generateInvoiceNumber(): string
    {
        $lastInvoice = PurchaseInvoice::orderBy('id', 'desc')->first();
        $lastNumber = 99;
        if ($lastInvoice && $lastInvoice->invoice_number) {
            $lastNumber = (int) filter_var($lastInvoice->invoice_number, FILTER_SANITIZE_NUMBER_INT);
        }
        return (string) ($lastNumber + 1);
    }

    public function save(): void
    {
        if (! $this->syncPurchaseDate()) {
            return;
        }

        $this->validate();

        if (empty($this->items)) {
            session()->flash('error', 'لطفاً حداقل یک محصول به فاکتور اضافه کنید.');
            return;
        }

        try {
            DB::transaction(function () {
                $totalAmount = 0;
                $invoiceNumber = $this->generateInvoiceNumber();
                $purchaseInvoice = PurchaseInvoice::create([
                    'supplier_id' => $this->supplier_id,
                    'purchase_date' => $this->purchase_date,
                    'invoice_number' => $invoiceNumber,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'payment_method' => $this->payment_method,
                    'status' => 'completed',
                    'notes' => $this->notes,
                    'user_id' => auth()->id(),
                ]);

                foreach ($this->items as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    $buyPrice = (float) ($item['buy_price'] ?: $item['current_buy_price']);
                    $sellPrice = (float) ($item['sell_price'] ?: $item['current_sell_price']);
                    $quantity = (float) $item['quantity'];
                    $itemTotal = $quantity * $buyPrice;

                    $totalAmount += $itemTotal;

                    PurchaseItem::create([
                        'purchase_invoice_id' => $purchaseInvoice->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'buy_price' => $buyPrice,
                        'sell_price' => $sellPrice,
                        'total' => $itemTotal,
                    ]);

                    $product->update([
                        'buy_price' => $buyPrice,
                        'sell_price' => $sellPrice,
                    ]);

                    $product->increment('stock', $quantity);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'purchase',
                        'quantity' => $quantity,
                        'description' => 'خرید از ' . $purchaseInvoice->supplier->name . ' - فاکتور شماره: ' . $invoiceNumber,
                    ]);
                }

                $purchaseInvoice->update(['total_amount' => $totalAmount]);

                $purchaseCategory = ExpenseCategory::firstOrCreate(
                    ['name' => 'خرید'],
                    ['description' => 'هزینه‌های مربوط به خرید کالا', 'is_active' => true]
                );

                Expense::create([
                    'expense_category_id' => $purchaseCategory->id,
                    'title' => 'خرید کالا از ' . $purchaseInvoice->supplier->name . ' - فاکتور شماره: ' . $invoiceNumber,
                    'amount' => $totalAmount,
                    'expense_date' => $this->purchase_date,
                    'payment_method' => $this->payment_method,
                    'description' => 'ثبت خودکار از فاکتور خرید شماره: ' . $invoiceNumber,
                    'reference_number' => $invoiceNumber,
                    'user_id' => auth()->id(),
                    'purchase_invoice_id' => $purchaseInvoice->id,
                ]);
            });

            session()->flash('success', 'فاکتور خرید با موفقیت ثبت شد.');

            $this->resetForm();
            $this->items = [];
            $this->itemCounter = 0;

        } catch (\Exception $e) {
            session()->flash('error', 'خطا در ثبت فاکتور: ' . $e->getMessage());
        }
    }

    private function resetForm(): void
    {
        $this->purchase_date = now()->toDateString();
        $this->purchase_date_jalali = gregorianToJalaliInput($this->purchase_date) ?? '';
        $this->supplier_id = '';
        $this->payment_method = 'cash';
        $this->notes = null;
        $this->product_barcode = '';
        $this->product_search = '';
        $this->searchResults = [];
        $this->resetErrorBag();
    }
}