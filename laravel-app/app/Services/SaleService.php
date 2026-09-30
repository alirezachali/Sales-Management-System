<?php

namespace App\Services;

use App\Exceptions\Business\InsufficientStockException;
use App\Exceptions\Business\ProductNotFoundException;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    private StockService $stockService;

    private SaleCalculator $calculator;

    private CustomerAccountService $customerAccountService;

    private CashboxService $cashboxService;

    private LoyaltyService $loyaltyService;

    public function __construct(
        StockService $stockService,
        SaleCalculator $calculator,
        CustomerAccountService $customerAccountService,
        CashboxService $cashboxService,
        LoyaltyService $loyaltyService,
    ) {
        $this->stockService = $stockService;
        $this->calculator = $calculator;
        $this->customerAccountService = $customerAccountService;
        $this->cashboxService = $cashboxService;
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * ثبت نهایی فروش
     *
     * @param  array  $cart  آرایه‌ی آیتم‌های سبد [['id' => .., 'quantity' => ..], ...]
     * @param  array  $payments  تقسیم‌بندی پرداخت‌ها [['type' => 'cash|card', 'amount' => ..], ...]
     * @param  int  $pointsToRedeem  تعداد امتیاز قابل تبدیل به تخفیف (اختیاری)
     */
    public function checkout(
        array $cart,
        float $discount = 0,
        string $paymentType = 'cash',
        ?int $customerId = null,
        array $payments = [],
        int $pointsToRedeem = 0,
    ): Sale {
        if ($cart === []) {
            throw new \InvalidArgumentException('سبد فروش خالی است.');
        }

        if ($discount < 0) {
            throw new \InvalidArgumentException('مبلغ تخفیف نمی‌تواند منفی باشد.');
        }

        if ($pointsToRedeem < 0) {
            throw new \InvalidArgumentException('تعداد امتیاز مصرفی نامعتبر است.');
        }

        return DB::transaction(function () use (
            $cart,
            $discount,
            $paymentType,
            $customerId,
            $payments,
            $pointsToRedeem,
        ) {
            $cart = $this->normalizeCart($cart);

            $productIds = collect($cart)->pluck('id')->unique()->values();

            /** @var \Illuminate\Support\Collection<int, Product> $products */
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // اعتبارسنجی کالاها و موجودی قبل از ایجاد فاکتور (کاهش کارهای بیهوده در rollback)
            $this->assertCartProducts($cart, $products);

            $total = $this->calculator->total($cart, $products);

            if ($discount > $total) {
                throw new \InvalidArgumentException(
                    'تخفیف ('.$this->money($discount).') نمی‌تواند بیشتر از جمع سبد خرید ('.$this->money($total).') باشد.'
                );
            }

            $customer = null;
            $pointsValue = 0.0;

            if ($pointsToRedeem > 0 || $customerId) {
                if ($customerId) {
                    $customer = Customer::query()
                        ->whereKey($customerId)
                        ->lockForUpdate()
                        ->first();

                    if (! $customer) {
                        throw new \InvalidArgumentException('مشتری یافت نشد.');
                    }
                }
            }

            if ($pointsToRedeem > 0) {
                if (! $customer) {
                    throw new \InvalidArgumentException(
                        'برای استفاده از امتیاز باید مشتری انتخاب شده باشد.'
                    );
                }

                $available = $this->loyaltyService->availablePoints($customer);

                if ($pointsToRedeem > $available) {
                    throw new \InvalidArgumentException(
                        'امتیاز درخواستی ('.number_format($pointsToRedeem).') بیشتر از امتیاز موجود مشتری ('.number_format($available).') است.'
                    );
                }

                $pointsValue = $pointsToRedeem * $this->loyaltyService->pointValue();
                $payableBeforePoints = $total - $discount;

                if ($pointsValue > $payableBeforePoints) {
                    throw new \InvalidArgumentException(
                        'مبلغ تخفیف امتیازی ('.$this->money($pointsValue).') بیشتر از مبلغ قابل پرداخت فاکتور ('.$this->money($payableBeforePoints).') است.'
                    );
                }

                $this->loyaltyService->redeem(
                    $customer,
                    $pointsToRedeem,
                    null,
                    'تخفیف امتیازی در لحظه فروش',
                );

                $discount += $pointsValue;
            }

            $finalPrice = round($total - $discount, 2);

            if ($finalPrice < 0) {
                throw new \InvalidArgumentException('مبلغ نهایی فروش نمی‌تواند منفی باشد.');
            }

            $payments = $this->normalizePayments($payments);
            $paidAmount = round(array_sum($payments), 2);

            [$paidAmount, $changeAmount, $debtAmount] = $this->assertPayments(
                $paymentType,
                $payments,
                $paidAmount,
                $finalPrice,
                $customerId,
            );

            $cashbox = $this->cashboxService->resolveCashboxFor($paymentType);

            $sale = Sale::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'user_id' => auth()->id() ?? 1,
                'customer_id' => $customerId,
                'total_price' => $total,
                'discount' => $discount,
                'final_price' => $finalPrice,
                'payment_type' => $paymentType,
                'cashbox_id' => $cashbox?->id,
                'paid_amount' => min($paidAmount, $finalPrice),
                'change_amount' => $changeAmount,
                'status' => 'completed',
            ]);

            $this->storePayments($sale, $payments);
            $this->cashboxService->recordSale($sale);

            if ($paymentType === 'credit') {
                $this->customerAccountService->addDebt(
                    $sale,
                    $finalPrice,
                    'فروش نسیه'
                );

                if ($paidAmount > 0) {
                    $this->customerAccountService->addPayment(
                        $sale,
                        $paidAmount,
                        'پرداخت بخشی از فاکتور نسیه'
                    );
                }
            }

            $this->storeSaleItemsAndReduceStock($sale, $cart, $products);

            if ($sale->customer_id) {
                $customer ??= $sale->customer;

                if ($customer) {
                    $this->loyaltyService->earn(
                        $customer,
                        $this->loyaltyService->pointsForAmount((float) $sale->final_price, $customer),
                        $sale,
                        'کسب امتیاز از فاکتور '.$sale->invoice_number,
                    );
                }
            }

            return $sale;
        });
    }

    /**
     * تجمیع تعداد کالاهای تکراری و حذف مقادیر نامعتبر
     *
     * @return array<int, array{id: int, quantity: int}>
     */
    private function normalizeCart(array $cart): array
    {
        $merged = [];

        foreach ($cart as $item) {
            $id = (int) ($item['id'] ?? 0);
            $qty = (int) ($item['quantity'] ?? 0);

            if ($id <= 0 || $qty <= 0) {
                throw new \InvalidArgumentException(
                    'تعداد کالا در سبد خرید باید حداقل ۱ باشد.'
                );
            }

            $merged[$id] = ($merged[$id] ?? 0) + $qty;
        }

        if ($merged === []) {
            throw new \InvalidArgumentException('سبد فروش خالی است.');
        }

        return collect($merged)
            ->map(fn (int $quantity, int $id) => ['id' => $id, 'quantity' => $quantity])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{id: int, quantity: int}>  $cart
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     */
    private function assertCartProducts(array $cart, $products): void
    {
        foreach ($cart as $item) {
            $product = $products->get($item['id']);

            if (! $product) {
                throw new ProductNotFoundException;
            }

            if (! $product->is_active) {
                throw new \InvalidArgumentException(
                    'کالای «'.$product->name.'» غیرفعال است و قابل فروش نیست.'
                );
            }

            try {
                $this->stockService->ensureAvailable($product, $item['quantity']);
            } catch (InsufficientStockException $e) {
                throw $e;
            }
        }
    }

    /**
     * @param  array<string, float>  $payments
     * @return array{0: float, 1: float, 2: float} [paidAmount, changeAmount, debtAmount]
     */
    private function assertPayments(
        string $paymentType,
        array $payments,
        float $paidAmount,
        float $finalPrice,
        ?int $customerId,
    ): array {
        $changeAmount = 0.0;
        $debtAmount = 0.0;

        switch ($paymentType) {
            case 'cash':
                $this->assertOnlyPaymentType($payments, 'cash');

                if ($payments === []) {
                    throw new \InvalidArgumentException(
                        'مبلغ نقدی دریافتی را وارد کنید. مبلغ قابل پرداخت: '.$this->money($finalPrice).'.'
                    );
                }

                if ($paidAmount < $finalPrice) {
                    $shortage = round($finalPrice - $paidAmount, 2);

                    throw new \InvalidArgumentException(
                        'مبلغ پرداختی ('.$this->money($paidAmount).') کمتر از مبلغ سبد خرید ('.$this->money($finalPrice).') است. کمبود: '.$this->money($shortage).'.'
                    );
                }

                $changeAmount = round($paidAmount - $finalPrice, 2);
                break;

            case 'card':
                $this->assertOnlyPaymentType($payments, 'card');

                if ($payments === []) {
                    throw new \InvalidArgumentException(
                        'مبلغ کارتخوان را وارد کنید. مبلغ قابل پرداخت: '.$this->money($finalPrice).'.'
                    );
                }

                if (abs($paidAmount - $finalPrice) > 0.001) {
                    throw new \InvalidArgumentException(
                        $this->exactPaymentMismatchMessage('کارتخوان', $paidAmount, $finalPrice)
                    );
                }
                break;

            case 'mixed':
                $this->assertOnlyPaymentType($payments, ['cash', 'card']);

                if (($payments['cash'] ?? 0) <= 0 || ($payments['card'] ?? 0) <= 0) {
                    throw new \InvalidArgumentException(
                        'در پرداخت ترکیبی باید هر دو مبلغ نقدی و کارتخوان بزرگ‌تر از صفر باشد.'
                    );
                }

                if (abs($paidAmount - $finalPrice) > 0.001) {
                    throw new \InvalidArgumentException(
                        $this->exactPaymentMismatchMessage('ترکیبی', $paidAmount, $finalPrice)
                    );
                }
                break;

            case 'credit':
                if ($customerId === null) {
                    throw new \InvalidArgumentException(
                        'برای فروش نسیه باید مشتری انتخاب شده باشد.'
                    );
                }

                if ($paidAmount > $finalPrice) {
                    $extra = round($paidAmount - $finalPrice, 2);

                    throw new \InvalidArgumentException(
                        'مبلغ پیش‌پرداخت ('.$this->money($paidAmount).') بیشتر از مبلغ سبد خرید ('.$this->money($finalPrice).') است. اضافه: '.$this->money($extra).'.'
                    );
                }

                $debtAmount = round($finalPrice - $paidAmount, 2);
                break;

            default:
                throw new \InvalidArgumentException('روش پرداخت نامعتبر است.');
        }

        return [$paidAmount, $changeAmount, $debtAmount];
    }

    private function exactPaymentMismatchMessage(string $label, float $paidAmount, float $finalPrice): string
    {
        $diff = round($paidAmount - $finalPrice, 2);

        if ($diff < 0) {
            return 'مجموع پرداخت '.$label.' ('.$this->money($paidAmount).') کمتر از مبلغ سبد خرید ('.$this->money($finalPrice).') است. کمبود: '.$this->money(abs($diff)).'.';
        }

        return 'مجموع پرداخت '.$label.' ('.$this->money($paidAmount).') بیشتر از مبلغ سبد خرید ('.$this->money($finalPrice).') است. اضافه: '.$this->money($diff).'.';
    }

    /**
     * @param  array<string, float>  $payments
     */
    private function storePayments(Sale $sale, array $payments): void
    {
        if ($payments === []) {
            return;
        }

        $now = now();
        $rows = [];

        foreach ($payments as $type => $amount) {
            if ($amount <= 0) {
                continue;
            }

            $rows[] = [
                'sale_id' => $sale->id,
                'payment_type' => $type,
                'amount' => $amount,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            Payment::insert($rows);
            $sale->unsetRelation('payments');
        }
    }

    /**
     * @param  array<int, array{id: int, quantity: int}>  $cart
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     */
    private function storeSaleItemsAndReduceStock(Sale $sale, array $cart, $products): void
    {
        $now = now();
        $rows = [];

        foreach ($cart as $item) {
            $product = $products->get($item['id']);

            if (! $product) {
                throw new ProductNotFoundException;
            }

            $quantity = (int) $item['quantity'];
            $price = (float) $product->sell_price;

            $rows[] = [
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $price,
                'cost_price' => (float) $product->buy_price,
                'line_total' => $price * $quantity,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // موجودی قبلاً در assertCartProducts بررسی شده؛ remove دوباره ensure می‌کند
            $this->stockService->remove(
                $product,
                $quantity,
                'فروش کالا'
            );
        }

        SaleItem::insert($rows);
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
    }

    private function money(float $amount): string
    {
        return number_format($amount).' تومان';
    }

    /**
     * تبدیل لیست پرداخت‌ها به دیکشنری [نوع => مبلغ] با حذف صفرها
     *
     * @return array<string, float>
     */
    private function normalizePayments(array $payments): array
    {
        $result = [];

        foreach ($payments as $payment) {
            $type = $payment['type'] ?? null;
            $amount = round((float) ($payment['amount'] ?? 0), 2);

            if (! in_array($type, ['cash', 'card'], true) || $amount <= 0) {
                continue;
            }

            $result[$type] = ($result[$type] ?? 0) + $amount;
        }

        return $result;
    }

    /**
     * مطمئن شو فقط نوع/انواع پرداخت مجاز در لیست وجود دارد
     *
     * @param  array<string, float>  $payments
     */
    private function assertOnlyPaymentType(array $payments, string|array $allowed): void
    {
        $allowed = (array) $allowed;

        foreach (array_keys($payments) as $type) {
            if (! in_array($type, $allowed, true)) {
                throw new \InvalidArgumentException(
                    'نوع پرداخت «'.$type.'» با روش انتخاب‌شده همخوانی ندارد.'
                );
            }
        }
    }
}
