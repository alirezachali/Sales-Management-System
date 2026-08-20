<?php

namespace App\Livewire\Sales;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Livewire\Component;
use Livewire\WithPagination;

class SaleManager extends Component
{
    use WithPagination;

    // جستجوی کالا / بارکد
    public string $barcode = '';
    public string $search = '';

    // سبد فروش (Cart)
    public array $cart = [];

    // اطلاعات فاکتور
    public ?int $customerId = null;
    public string $paymentType = 'cash';
    public float $discount = 0;
    public float $paidAmount = 0;

    // مودال‌ها
    public bool $showCheckoutModal = false;
    public bool $showInvoiceModal = false;
    public ?int $lastSaleId = null;

    protected array $messages = [
        'cart.required' => 'سبد فروش خالی است.',
    ];

    public function mount(): void
    {
        $this->resetCart();
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

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    public function getFinalPriceProperty(): float
    {
        return max(0, $this->subtotal - $this->discount);
    }

    public function openCheckoutModal(): void
    {
        if (empty($this->cart)) {
            session()->flash('error', 'سبد فروش خالی است.');
            return;
        }

        $this->paidAmount = $this->finalPrice;
        $this->showCheckoutModal = true;
    }

    /**
     * ثبت نهایی فروش (Checkout) با استفاده از SaleService موجود
     */
    public function checkout(SaleService $saleService): void
    {
        $this->validate([
            'paymentType' => 'required|in:cash,card,credit',
            'discount' => 'nullable|numeric|min:0',
            'paidAmount' => 'nullable|numeric|min:0',
            'customerId' => 'nullable|exists:customers,id',
        ]);

        if (empty($this->cart)) {
            session()->flash('error', 'سبد فروش خالی است.');
            return;
        }

        try {
            $cartPayload = collect($this->cart)->map(fn ($item) => [
                'id' => $item['id'],
                'quantity' => $item['quantity'],
            ])->values()->all();

            $sale = $saleService->checkout(
                $cartPayload,
                $this->discount ?? 0,
                $this->paymentType,
                $this->customerId,
                $this->paidAmount ?? 0,
            );

            session()->flash('success', 'فاکتور فروش با موفقیت ثبت شد.');

            $this->lastSaleId = $sale->id;
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
        $this->paymentType = 'cash';
        $this->customerId = null;
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
            'customers' => Customer::orderBy('first_name')->orderBy('last_name')->get(),
            'recentSales' => Sale::latest()->with('customer')->paginate(10),
        ]);
    }
}