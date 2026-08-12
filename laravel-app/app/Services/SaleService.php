<?php

namespace App\Services;

use App\Exceptions\Business\ProductNotFoundException;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\CustomerAccountService;
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

    public function checkout(
        array $cart,
        float $discount = 0,
        string $paymentType = 'cash',
        ?int $customerId = null,
        float $paidAmount = 0,
    ): Sale {
        return DB::transaction(function () use (
            $cart,
            $discount,
            $paymentType,
            $customerId,
            $paidAmount,
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

            if ($paymentType === 'credit') {
                if ($customerId === null) {
                    throw new \InvalidArgumentException(
                        'برای فروش نسیه باید مشتری انتخاب شده باشد.'
                    );
                }

                if ($paidAmount != 0) {
                    throw new \InvalidArgumentException(
                        'در فروش نسیه مبلغ پرداختی باید صفر باشد.'
                    );
                }
            } else {
                if ($paidAmount < $finalPrice) {
                    throw new \InvalidArgumentException(
                        'مبلغ پرداختی کمتر از مبلغ نهایی فروش است.'
                    );
                }
            }

            $sale = Sale::create([
                'invoice_number' => 'INV-' . now()->format('YmdHis'),
                'user_id' => auth()->id() ?? 1,
                'customer_id' => $customerId,
                'total_price' => $total,
                'discount' => $discount,
                'final_price' => $finalPrice,
                'payment_type' => $paymentType,
            ]);

            if ($paymentType !== 'credit') {
                Payment::create([
                    'sale_id' => $sale->id,
                    'payment_type' => $paymentType,
                    'amount' => $paidAmount,
                ]);
            }

            if ($paymentType === 'credit') {
                $this->customerAccountService->addDebt(
                    $sale,
                    $finalPrice
                );
            }

            foreach ($cart as $item) {
                $product = $products->get($item['id']);

                // اکر کالا وجود نداشته باشد
                if (!$product) {
                    throw new ProductNotFoundException();
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
}