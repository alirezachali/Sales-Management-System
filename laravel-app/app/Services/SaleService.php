<?php

namespace App\Services;

use App\Exceptions\Business\ProductNotFoundException;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class SaleService
{
    private StockService $stockService;

    private SaleCalculator $calculator;

    private CustomerAccountService $customerAccountService;

    public function __construct(
        StockService $stockService,
        SaleCalculator $calculator,
        CustomerAccountService $customerAccountService,
    ) {
        $this->stockService = $stockService;
        $this->calculator = $calculator;
        $this->customerAccountService = $customerAccountService;
    }

    /**
     * ثبت نهایی فروش
     *
     * @param  array  $cart  آرایه‌ی آیتم‌های سبد [['id' => .., 'quantity' => ..], ...]
     * @param  array  $payments  تقسیم‌بندی پرداخت‌ها [['type' => 'cash|card', 'amount' => ..], ...]
     */
    public function checkout(
        array $cart,
        float $discount = 0,
        string $paymentType = 'cash',
        ?int $customerId = null,
        array $payments = [],
    ): Sale {
        return DB::transaction(function () use (
            $cart,
            $discount,
            $paymentType,
            $customerId,
            $payments,
        ) {
            $productIds = collect($cart)
                ->pluck('id')
                ->unique()
                ->values();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = $this->calculator->total($cart, $products);

            $finalPrice = $total - $discount;

            if ($finalPrice < 0) {
                throw new \InvalidArgumentException(
                    'مبلغ نهایی فروش نمی‌تواند منفی باشد.'
                );
            }

            // نرمال‌سازی پرداخت‌ها: حذف مقادیر صفر/منفی و تجمیع هر نوع پرداخت
            $payments = $this->normalizePayments($payments);

            $paidAmount = array_sum($payments);

            $changeAmount = 0.0;
            $debtAmount = 0.0;

            switch ($paymentType) {
                case 'cash':
                    $this->assertOnlyPaymentType($payments, 'cash');

                    if ($paidAmount < $finalPrice) {
                        throw new \InvalidArgumentException(
                            'مبلغ پرداختی کمتر از مبلغ نهایی فروش است.'
                        );
                    }

                    // در پرداخت نقدی امکان دریافت بیش از مبلغ (برای بازگرداندن باقی) وجود دارد
                    $changeAmount = $paidAmount - $finalPrice;
                    $paidAmount = $finalPrice + $changeAmount;
                    break;

                case 'card':
                    $this->assertOnlyPaymentType($payments, 'card');

                    if (abs($paidAmount - $finalPrice) > 0.001) {
                        throw new \InvalidArgumentException(
                            'مبلغ پرداختی با کارتخوان باید دقیقاً برابر مبلغ نهایی فروش باشد.'
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
                            'مجموع مبالغ نقدی و کارتخوان باید دقیقاً برابر مبلغ نهایی فروش باشد.'
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
                        throw new \InvalidArgumentException(
                            'مبلغ پرداختی نمی‌تواند بیشتر از مبلغ نهایی فروش باشد.'
                        );
                    }

                    // باقی‌مانده به حساب نسیه‌ی مشتری ثبت می‌شود
                    $debtAmount = $finalPrice - $paidAmount;
                    break;

                default:
                    throw new \InvalidArgumentException(
                        'روش پرداخت نامعتبر است.'
                    );
            }

            $sale = Sale::create([
                'invoice_number' => 'INV-'.now()->format('YmdHis'),
                'user_id' => auth()->id() ?? 1,
                'customer_id' => $customerId,
                'total_price' => $total,
                'discount' => $discount,
                'final_price' => $finalPrice,
                'payment_type' => $paymentType,
                'paid_amount' => min($paidAmount, $finalPrice),
                'change_amount' => $changeAmount,
            ]);

            // ثبت تیکه‌های پرداخت (نقدی / کارتخوان)
            foreach ($payments as $type => $amount) {
                if ($amount <= 0) {
                    continue;
                }

                Payment::create([
                    'sale_id' => $sale->id,
                    'payment_type' => $type,
                    'amount' => $amount,
                ]);
            }

            if ($paymentType === 'credit') {
                $this->customerAccountService->addDebt(
                    $sale,
                    $finalPrice,
                    'فروش نسیه'
                );

                // اگر مشتری بخشی از مبلغ را نقد/کارت پرداخت کرده، در حسابش تهاتر می‌شود
                if ($paidAmount > 0) {
                    $this->customerAccountService->addPayment(
                        $sale,
                        $paidAmount,
                        'پرداخت بخشی از فاکتور نسیه'
                    );
                }
            }

            foreach ($cart as $item) {
                $product = $products->get($item['id']);

                // اکر کالا وجود نداشته باشد
                if (! $product) {
                    throw new ProductNotFoundException;
                }

                // گرفتن قیمت فروش کالا از دیتابیس
                $price = $product->sell_price;

                $quantity = (int) $item['quantity'];

                $lineTotal = $price * $quantity;

                // چک کردن موجودی کالا
                $this->stockService->ensureAvailable(
                    $product,
                    $quantity
                );

                // ساخت آیتم فروش
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'line_total' => $lineTotal,
                ]);

                // ثبت فروش کالا و کم کردن موجودی کالا
                $this->stockService->remove(
                    $product,
                    $quantity,
                    'فروش کالا'
                );
            }

            return $sale;
        });
    }

    /**
     * تبدیل لیست پرداخت‌ها به دیکشنری [نوع => مبلغ] با حذف صفرها
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
