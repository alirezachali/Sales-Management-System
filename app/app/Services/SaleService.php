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

    public function __construct(
        StockService $stockService,
        SaleCalculator $calculator
    ) {
        $this->stockService = $stockService;
        $this->calculator = $calculator;
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

            $sale = Sale::create([
                'invoice_number' => 'INV-' . now()->format('YmdHis'),
                'user_id' => auth()->id() ?? 1,
                'customer_id' => $customerId,
                'total_price' => $total,
                'discount' => $discount,
                'final_price' => $finalPrice,
                'payment_type' => $paymentType,
            ]);

            Payment::create([
                'sale_id' => $sale->id,
                'payment_type' => $paymentType,
                'amount' => $paidAmount,
            ]);

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