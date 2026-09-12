<?php

namespace App\Livewire\Sales;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
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

    // مودال‌ها
    public bool $showCheckoutModal = false;

    public bool $showInvoiceModal = false;

    public ?int $lastSaleId = null;

    public ?Sale $lastSale = null;

    protected array $messages = [
        'cart.required' => 'سبد فروش خالی است.',
    ];

    public function mount(): void
    {
        $this->resetCart();
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
            return;
        }

        $this->customerId = $customer->id;
        $this->customerName = $customer->full_name;
        $this->customerQuery = '';
    }

    public function clearCustomer(): void
    {
        $this->customerId = null;
        $this->customerName = null;
        $this->customerQuery = '';

        // نسیه فقط برای مشتری ثبت‌شده معتبر است
        if ($this->paymentType === 'credit') {
            $this->paymentType = 'cash';
        }
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

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
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
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        }
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

    public function getFinalPriceProperty(): float
    {
        return max(0, $this->subtotal - $this->discount);
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
     * اختلاف پرداخت ترکیبی تا تسویه
     */
    public function getMixedDiffProperty(): float
    {
        return $this->finalPrice - ($this->cashAmount + $this->cardAmount);
    }

    public function openCheckoutModal(): void
    {
        if (empty($this->cart)) {
            session()->flash('error', 'سبد فروش خالی است.');

            return;
        }

        $this->paidAmount = $this->finalPrice;
        $this->cashAmount = round($this->finalPrice / 2);
        $this->cardAmount = $this->finalPrice - $this->cashAmount;
        $this->creditPayMethod = 'cash';
        $this->showCheckoutModal = true;
    }

    /**
     * ثبت نهایی فروش (Checkout) با استفاده از SaleService موجود
     */
    public function checkout(SaleService $saleService): void
    {
        $this->validate([
            'paymentType' => 'required|in:cash,card,mixed,credit',
            'discount' => 'nullable|numeric|min:0',
        ]);

        if (empty($this->cart)) {
            session()->flash('error', 'سبد فروش خالی است.');

            return;
        }

        $final = $this->finalPrice;

        // اعتبارسنجی مبالغ بر اساس روش پرداخت
        match ($this->paymentType) {
            'cash' => $this->validate([
                'paidAmount' => 'required|numeric|min:'.$final,
            ], [
                'paidAmount.min' => 'مبلغ نقدی دریافتی نمی‌تواند کمتر از مبلغ قابل پرداخت باشد.',
            ]),
            'card' => $this->validate([
                'paidAmount' => 'required|numeric|eq:'.$final,
            ], [
                'paidAmount.eq' => 'مبلغ کارتخوان باید دقیقاً برابر مبلغ قابل پرداخت باشد.',
            ]),
            'mixed' => $this->validate([
                'cashAmount' => 'required|numeric|min:1|max:'.$final,
                'cardAmount' => 'required|numeric|min:1|max:'.$final,
            ], [
                'cashAmount.required' => 'مبلغ نقدی را وارد کنید.',
                'cardAmount.required' => 'مبلغ کارتخوان را وارد کنید.',
                'cashAmount.min' => 'مبلغ نقدی باید بزرگ‌تر از صفر باشد.',
                'cardAmount.min' => 'مبلغ کارتخوان باید بزرگ‌تر از صفر باشد.',
            ]),
            'credit' => $this->validate([
                'paidAmount' => 'nullable|numeric|min:0|max:'.$final,
            ], [
                'paidAmount.max' => 'مبلغ پیش‌پرداخت نمی‌تواند بیشتر از مبلغ قابل پرداخت باشد.',
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
                        ? 'مجموع مبالغ '.number_format($diff).' تومان کمتر از فاکتور است.'
                        : 'مجموع مبالغ '.number_format(abs($diff)).' تومان بیشتر از فاکتور است.'
                );

                return;
            }
        }

        $payments = match ($this->paymentType) {
            'cash' => [['type' => 'cash', 'amount' => $this->paidAmount]],
            'card' => [['type' => 'card', 'amount' => $this->finalPrice]],
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
            );

            session()->flash('success', 'فاکتور فروش با موفقیت ثبت شد.');

            $this->lastSaleId = $sale->id;
            $this->lastSale = $sale->fresh(['customer', 'payments']);
            $this->showCheckoutModal = false;
            $this->showInvoiceModal = true;
            $this->resetCart();
        } catch (\InvalidArgumentException $e) {
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
