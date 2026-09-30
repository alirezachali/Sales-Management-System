<?php

namespace App\Livewire\Sales;

use App\Exceptions\Business\InsufficientStockException;
use App\Exceptions\Business\ProductNotFoundException;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\LoyaltyService;
use App\Services\SaleService;
use DomainException;
use Livewire\Component;

class SaleManager extends Component
{
    // جستجوی کالا / بارکد
    public string $barcode = '';

    public string $search = '';

    // سبد فروش (Cart)
    public array $cart = [];

    // اطلاعات فاکتور
    public float $discount = 0;

    // انتخاب مشتری (داخل مودال پرداخت)
    public string $customerQuery = '';

    public ?int $customerId = null;

    public ?string $customerName = null;

    // پرداخت
    public string $paymentType = 'cash';

    public float $paidAmount = 0;      // نقدی / نسیه

    public float $cashAmount = 0;      // ترکیبی: نقدی

    public float $cardAmount = 0;      // ترکیبی: کارتخوان

    public string $creditPayMethod = 'cash'; // نسیه: روش پرداخت مبلغ پیش‌پرداخت

    // امتیاز و وفاداری
    // float است تا ورودی اعشاری هم خطای سرور ندهد؛ در updatedPointsToRedeem به عدد صحیح تبدیل می‌شود.
    public float $pointsToRedeem = 0;

    public int $customerAvailablePoints = 0;

    public int $pointValue = 100;

    /**
     * فیلدهای عددی که کاربر می‌تواند خالی بگذارند.
     * مقدار پیش‌فرض هر فیلد وقتی خالی شد.
     */
    protected const NUMERIC_INPUTS = [
        'discount' => 0.0,
        'paidAmount' => 0.0,
        'cashAmount' => 0.0,
        'cardAmount' => 0.0,
        'pointsToRedeem' => 0.0,
    ];

    // مودال‌ها
    public bool $showCheckoutModal = false;

    public bool $showInvoiceModal = false;

    public ?int $lastSaleId = null;

    public ?Sale $lastSale = null;

    // خطای موجودی کالا
    public ?string $stockError = null;

    protected array $messages = [
        'cart.required' => 'سبد فروش خالی است.',
        'paymentType.required' => 'روش پرداخت را انتخاب کنید.',
        'paymentType.in' => 'روش پرداخت نامعتبر است.',
        'discount.numeric' => 'تخفیف باید عدد باشد.',
        'discount.min' => 'تخفیف نمی‌تواند منفی باشد.',
        'discount.max' => 'تخفیف نمی‌تواند بیشتر از جمع سبد خرید باشد.',
        'paidAmount.required' => 'مبلغ پرداختی را وارد کنید.',
        'paidAmount.numeric' => 'مبلغ پرداختی باید عدد باشد.',
        'paidAmount.min' => 'مبلغ پرداختی کمتر از مبلغ سبد خرید است.',
        'paidAmount.max' => 'مبلغ پیش‌پرداخت نمی‌تواند بیشتر از مبلغ سبد خرید باشد.',
        'cashAmount.required' => 'مبلغ نقدی را وارد کنید.',
        'cardAmount.required' => 'مبلغ کارتخوان را وارد کنید.',
        'cashAmount.min' => 'مبلغ نقدی باید بزرگ‌تر از صفر باشد.',
        'cardAmount.min' => 'مبلغ کارتخوان باید بزرگ‌تر از صفر باشد.',
        'pointsToRedeem.max' => 'امتیاز واردشده بیشتر از امتیاز موجود مشتری است.',
    ];

    public function mount(): void
    {
        $this->resetCart();
    }

    /**
     * وقتی کاربر یک فیلد عددی را کاملاً خالی می‌کند، Livewire مقدار خالی را
     * نمی‌تواند در پراپرتی‌های typed بگذارد، پس آن‌ها را unset می‌کند و هر خواندن
     * بعدی از پراپرتی به خطای PropertyNotFoundException منجر می‌شود.
     * اینجا هر فیلد عددیِ خالی به صفر برمی‌گردد تا خالی گذاشتن فیلد خطای سرور ندهد.
     */
    public function hydrate(): void
    {
        $this->normalizeNumericInputs();
    }

    public function updated(string $property, mixed $value): void
    {
        if (array_key_exists($property, self::NUMERIC_INPUTS)) {
            $this->normalizeNumericInputs();
        }
    }

    private function normalizeNumericInputs(): void
    {
        foreach (self::NUMERIC_INPUTS as $property => $default) {
            // پراپرتیِ unset شده با isset قابل تشخیص است (null هم به صفر تبدیل می‌شود)
            if (! isset($this->{$property})) {
                $this->{$property} = $default;
            }
        }
    }

    /**
     * جستجوی لایو مشتری‌ها بر اساس نام یا موبایل
     */
    public function getCustomerResultsProperty(): array
    {
        if (mb_strlen(trim($this->customerQuery)) < 1) {
            return [];
        }

        return Customer::query()
            ->active()
            ->search(trim($this->customerQuery))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->limit(8)
            ->get()
            ->map(fn (Customer $customer) => [
                'id' => $customer->id,
                'name' => $customer->full_name,
                'mobile' => $customer->mobile,
            ])
            ->all();
    }

    public function selectCustomer(int $customerId): void
    {
        $customer = Customer::find($customerId);

        if (! $customer) {
            session()->flash('error', 'مشتری یافت نشد.');

            return;
        }

        if (! $customer->is_active) {
            session()->flash('error', 'این مشتری غیرفعال است و قابل انتخاب نیست.');

            return;
        }

        $this->customerId = $customer->id;
        $this->customerName = $customer->full_name;
        $this->customerQuery = '';

        $this->refreshCustomerPoints();
    }

    /**
     * بارگذاری دوباره اطلاعات امتیاز مشتری انتخاب‌شده
     */
    public function refreshCustomerPoints(): void
    {
        $this->pointsToRedeem = 0;
        $this->customerAvailablePoints = 0;

        if (! $this->customerId) {
            return;
        }

        $loyalty = app(LoyaltyService::class);
        $customer = Customer::find($this->customerId);

        if ($customer && $loyalty->enabled()) {
            $this->customerAvailablePoints = $loyalty->availablePoints($customer);
            $this->pointValue = $loyalty->pointValue();
        }
    }

    public function updatedPointsToRedeem(): void
    {
        $this->pointsToRedeem = max(0, (int) $this->pointsToRedeem);

        if ($this->pointsToRedeem > $this->customerAvailablePoints) {
            $this->pointsToRedeem = $this->customerAvailablePoints;
        }

        $maxByInvoice = (int) floor(max(0, $this->subtotal - $this->discount) / max(1, $this->pointValue));
        if ($this->pointsToRedeem > $maxByInvoice) {
            $this->pointsToRedeem = $maxByInvoice;
        }

        $this->syncAmountsWithFinalPrice();
    }

    public function updatedDiscount(): void
    {
        if ($this->discount < 0) {
            $this->discount = 0;
        }

        if ($this->discount > $this->subtotal) {
            // اجازه ورود می‌دهیم ولی در checkout اعتبارسنجی می‌شود؛ هشدار در UI نشان داده می‌شود
        }

        $this->syncAmountsWithFinalPrice();
    }

    public function applyAllPoints(): void
    {
        $this->refreshCustomerPoints();

        $maxPoints = (int) floor(max(0, $this->subtotal - $this->discount) / max(1, $this->pointValue));
        $this->pointsToRedeem = min($this->customerAvailablePoints, $maxPoints);
        $this->syncAmountsWithFinalPrice();
    }

    public function clearCustomer(): void
    {
        $this->customerId = null;
        $this->customerName = null;
        $this->customerQuery = '';
        $this->pointsToRedeem = 0;
        $this->customerAvailablePoints = 0;

        // نسیه فقط برای مشتری ثبت‌شده معتبر است
        if ($this->paymentType === 'credit') {
            $this->paymentType = 'cash';

            // به کاربر نشان بده که با حذف مشتری، گزینه نسیه غیرفعال شد
            $this->dispatch('credit-blocked');
        }

        $this->syncAmountsWithFinalPrice();
    }

    public function setPaymentType(string $type): void
    {
        if (! in_array($type, ['cash', 'card', 'mixed', 'credit'], true)) {
            return;
        }

        // نسیه فقط برای مشتری ثبت‌شده
        if ($type === 'credit' && ! $this->customerId) {
            $this->addError('paymentType', 'برای فروش نسیه ابتدا یک مشتری انتخاب کنید.');

            return;
        }

        $this->paymentType = $type;
        $this->resetValidation('paymentType');
        $this->syncAmountsWithFinalPrice();
    }

    /**
     * هم‌تراز کردن مبالغ پیش‌فرض با مبلغ قابل پرداخت فعلی
     */
    private function syncAmountsWithFinalPrice(): void
    {
        $final = $this->finalPrice;

        if ($this->paymentType === 'card') {
            $this->paidAmount = $final;
        }

        if ($this->paymentType === 'mixed' && abs($this->mixedDiff) < 0.001) {
            // اگر قبلاً تسویه بوده، نسبت را حفظ نکن؛ فقط وقتی مودال تازه باز می‌شود مقداردهی می‌شود
        }
    }

    /**
     * جستجوی کالا با بارکد و افزودن به سبد
     */
    public function addByBarcode(): void
    {
        if (blank($this->barcode)) {
            return;
        }

        $product = Product::where('barcode', $this->barcode)
            ->where('is_active', true)
            ->first();

        if (! $product) {
            session()->flash('error', 'کالای مورد نظر با این بارکد یافت نشد.');
            $this->barcode = '';

            return;
        }

        $this->addProduct($product->id);
        $this->barcode = '';
    }

    /**
     * افزودن کالا به سبد بر اساس شناسه
     */
    public function addProduct(int $productId): void
    {
        $product = Product::find($productId);

        if (! $product) {
            session()->flash('error', 'کالا یافت نشد.');

            return;
        }

        if (! $product->is_active) {
            session()->flash('error', 'کالای «'.$product->name.'» غیرفعال است و قابل فروش نیست.');

            return;
        }

        $currentInCart = isset($this->cart[$productId]) ? $this->cart[$productId]['quantity'] : 0;
        $requestedQty = $currentInCart + 1;

        // بررسی موجودی
        if ($requestedQty > $product->stock) {
            $this->stockError = $product->name;

            return;
        }

        $this->stockError = null;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['stock'] = $product->stock;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'price' => (float) $product->sell_price,
                'quantity' => 1,
                'stock' => $product->stock,
            ];
        }
    }

    public function incrementQty(int $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        $currentQty = $this->cart[$productId]['quantity'];
        $product = Product::find($productId);

        if (! $product) {
            session()->flash('error', 'کالا یافت نشد و از سبد حذف شد.');
            unset($this->cart[$productId]);

            return;
        }

        // بررسی موجودی
        if (($currentQty + 1) > $product->stock) {
            $this->stockError = $product->name;

            return;
        }

        $this->stockError = null;
        $this->cart[$productId]['quantity']++;
        $this->cart[$productId]['stock'] = $product->stock;
    }

    public function decrementQty(int $productId): void
    {
        if (isset($this->cart[$productId]) && $this->cart[$productId]['quantity'] > 1) {
            $this->cart[$productId]['quantity']--;
        }
    }

    public function updatePrice(int $productId, float $price): void
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['price'] = max(0, $price);
        }
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    public function clearCart(): void
    {
        $this->cart = [];
        session()->flash('success', 'سبد خرید پاک شد.');
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    /**
     * مبلغ تخفیف ناشی از امتیاز مصرف‌شده
     */
    public function getPointsDiscountProperty(): float
    {
        return min($this->pointsToRedeem * $this->pointValue, max(0, $this->subtotal - $this->discount));
    }

    public function getFinalPriceProperty(): float
    {
        return max(0, $this->subtotal - $this->discount - $this->pointsDiscount);
    }

    /**
     * مبلغ باقی‌مانده نسیه (پیش‌پرداخت کسر شود)
     */
    public function getCreditRemainProperty(): float
    {
        return max(0, $this->finalPrice - $this->paidAmount);
    }

    /**
     * باقیمانده وجه نقد (در پرداخت نقدی بیش از مبلغ)
     */
    public function getChangeProperty(): float
    {
        return max(0, $this->paidAmount - $this->finalPrice);
    }

    /**
     * کمبود مبلغ نقدی نسبت به فاکتور
     */
    public function getCashShortfallProperty(): float
    {
        return max(0, $this->finalPrice - $this->paidAmount);
    }

    /**
     * اختلاف پرداخت ترکیبی تا تسویه
     */
    public function getMixedDiffProperty(): float
    {
        return $this->finalPrice - ($this->cashAmount + $this->cardAmount);
    }

    /**
     * آیا ثبت نهایی به‌خاطر اختلاف مبلغ باید مسدود شود؟
     */
    public function getCheckoutBlockedProperty(): bool
    {
        return match ($this->paymentType) {
            'cash' => $this->cashShortfall > 0.001,
            'mixed' => abs($this->mixedDiff) > 0.001,
            'credit' => $this->paidAmount > $this->finalPrice + 0.001,
            default => false,
        };
    }

    public function openCheckoutModal(): void
    {
        if (empty($this->cart)) {
            session()->flash('error', 'سبد فروش خالی است.');

            return;
        }

        if ($this->discount > $this->subtotal) {
            session()->flash(
                'error',
                'تخفیف ('.number_format($this->discount).' تومان) بیشتر از جمع سبد خرید ('.number_format($this->subtotal).' تومان) است.'
            );

            return;
        }

        $this->paidAmount = $this->finalPrice;
        $this->cashAmount = round($this->finalPrice / 2);
        $this->cardAmount = $this->finalPrice - $this->cashAmount;
        $this->creditPayMethod = 'cash';
        $this->resetErrorBag();
        $this->showCheckoutModal = true;
    }

    /**
     * ثبت نهایی فروش (Checkout) با استفاده از SaleService موجود
     */
    public function checkout(SaleService $saleService): void
    {
        $this->normalizeNumericInputs();

        if (empty($this->cart)) {
            session()->flash('error', 'سبد فروش خالی است.');

            return;
        }

        $subtotal = $this->subtotal;
        $final = $this->finalPrice;

        $this->validate([
            'paymentType' => 'required|in:cash,card,mixed,credit',
            'discount' => 'nullable|numeric|min:0|max:'.$subtotal,
            'pointsToRedeem' => 'nullable|numeric|min:0|max:'.$this->customerAvailablePoints,
        ]);

        // برای کارتخوان مبلغ همیشه برابر فاکتور است
        if ($this->paymentType === 'card') {
            $this->paidAmount = $final;
        }

        // اعتبارسنجی مبالغ بر اساس روش پرداخت
        match ($this->paymentType) {
            'cash' => $this->validate([
                'paidAmount' => 'required|numeric|min:'.$final,
            ], [
                'paidAmount.required' => 'مبلغ نقدی دریافتی را وارد کنید.',
                'paidAmount.min' => 'مبلغ پرداختی ('.number_format((float) $this->paidAmount).' تومان) کمتر از مبلغ سبد خرید ('.number_format($final).' تومان) است. کمبود: '.number_format(max(0, $final - (float) $this->paidAmount)).' تومان.',
            ]),
            'card' => $this->validate([
                'paidAmount' => 'required|numeric',
            ]),
            'mixed' => $this->validate([
                'cashAmount' => 'required|numeric|min:1|max:'.$final,
                'cardAmount' => 'required|numeric|min:1|max:'.$final,
            ], [
                'cashAmount.required' => 'مبلغ نقدی را وارد کنید.',
                'cardAmount.required' => 'مبلغ کارتخوان را وارد کنید.',
                'cashAmount.min' => 'مبلغ نقدی باید بزرگ‌تر از صفر باشد.',
                'cardAmount.min' => 'مبلغ کارتخوان باید بزرگ‌تر از صفر باشد.',
                'cashAmount.max' => 'مبلغ نقدی نمی‌تواند بیشتر از مبلغ سبد خرید باشد.',
                'cardAmount.max' => 'مبلغ کارتخوان نمی‌تواند بیشتر از مبلغ سبد خرید باشد.',
            ]),
            'credit' => $this->validate([
                'paidAmount' => 'nullable|numeric|min:0|max:'.$final,
            ], [
                'paidAmount.max' => 'مبلغ پیش‌پرداخت ('.number_format((float) $this->paidAmount).' تومان) بیشتر از مبلغ سبد خرید ('.number_format($final).' تومان) است.',
            ]),
            default => null,
        };

        if ($this->paymentType === 'credit' && ! $this->customerId) {
            $this->addError('paymentType', 'برای فروش نسیه ابتدا یک مشتری انتخاب کنید.');

            return;
        }

        if ($this->paymentType === 'mixed') {
            $diff = round($this->mixedDiff, 2);

            if (abs($diff) > 0.001) {
                $this->addError(
                    'cardAmount',
                    $diff > 0
                        ? 'مجموع مبالغ پرداختی ('.number_format($this->cashAmount + $this->cardAmount).' تومان) کمتر از مبلغ سبد خرید ('.number_format($final).' تومان) است. کمبود: '.number_format($diff).' تومان.'
                        : 'مجموع مبالغ پرداختی ('.number_format($this->cashAmount + $this->cardAmount).' تومان) بیشتر از مبلغ سبد خرید ('.number_format($final).' تومان) است. اضافه: '.number_format(abs($diff)).' تومان.'
                );

                return;
            }
        }

        if ($this->paymentType === 'card' && abs((float) $this->paidAmount - $final) > 0.001) {
            $diff = round((float) $this->paidAmount - $final, 2);
            $this->addError(
                'paidAmount',
                $diff < 0
                    ? 'مبلغ کارتخوان کمتر از مبلغ سبد خرید است. کمبود: '.number_format(abs($diff)).' تومان.'
                    : 'مبلغ کارتخوان بیشتر از مبلغ سبد خرید است. اضافه: '.number_format($diff).' تومان.'
            );

            return;
        }

        $payments = match ($this->paymentType) {
            'cash' => [['type' => 'cash', 'amount' => $this->paidAmount]],
            'card' => [['type' => 'card', 'amount' => $this->paidAmount]],
            'mixed' => [
                ['type' => 'cash', 'amount' => $this->cashAmount],
                ['type' => 'card', 'amount' => $this->cardAmount],
            ],
            'credit' => $this->paidAmount > 0
                ? [['type' => $this->creditPayMethod, 'amount' => $this->paidAmount]]
                : [],
            default => [],
        };

        try {
            $cartPayload = collect($this->cart)->map(fn ($item) => [
                'id' => $item['id'],
                'quantity' => $item['quantity'],
            ])->values()->all();

            $sale = $saleService->checkout(
                $cartPayload,
                $this->discount,
                $this->paymentType,
                $this->customerId,
                $payments,
                (int) $this->pointsToRedeem,
            );

            session()->flash('success', 'فاکتور فروش با موفقیت ثبت شد.');

            $this->lastSaleId = $sale->id;
            $this->lastSale = $sale->fresh(['customer', 'payments']);
            $this->showCheckoutModal = false;
            $this->showInvoiceModal = true;
            $this->resetCart();
        } catch (InsufficientStockException $e) {
            $this->addError('checkout', $e->getMessage());
            $this->stockError = trim(str_replace(['موجودی ', ' کافی نیست.'], '', $e->getMessage())) ?: null;
        } catch (ProductNotFoundException|DomainException|\InvalidArgumentException $e) {
            $this->addError('checkout', $e->getMessage());
        }
    }

    /**
     * چاپ فاکتور فروش (باز شدن در تب جدید از طریق روت invoice)
     */
    public function printInvoice(?int $saleId = null): void
    {
        $id = $saleId ?? $this->lastSaleId;

        if ($id) {
            $this->dispatch('open-invoice', url: route('invoice', $id));
        }
    }

    public function closeModals(): void
    {
        $this->showCheckoutModal = false;
        $this->showInvoiceModal = false;
    }

    private function resetCart(): void
    {
        $this->cart = [];
        $this->discount = 0;
        $this->paidAmount = 0;
        $this->cashAmount = 0;
        $this->cardAmount = 0;
        $this->paymentType = 'cash';
        $this->creditPayMethod = 'cash';
        $this->customerId = null;
        $this->customerName = null;
        $this->customerQuery = '';
        $this->pointsToRedeem = 0;
        $this->customerAvailablePoints = 0;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.sales.sale-manager', [
            'products' => blank($this->search)
                ? collect()
                : Product::where('is_active', true)
                    ->where(function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                            ->orWhere('barcode', 'like', "%{$this->search}%");
                    })
                    ->limit(10)
                    ->get(),
        ]);
    }
}
