<?php

namespace App\Services;

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
        string $paymentType = 'cash'
    ){
        return DB::transaction(function () use ($cart, $discount, $paymentType) {

            $productIds = collect($cart)
                ->pluck('id')
                ->unique();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');
        
            $total = $this->calculator->total(
                $cart,
                $products
            );

            $finalPrice = $total - $discount;

            $sale = Sale::create([
                'invoice_number' => 'INV-' . now()->format('YmdHis'),
                'user_id'        => auth()->id() ?? 1,
                'total_price'    => $total,
                'discount'       => $discount,
                'final_price'    => $finalPrice,
                'payment_type'   => $paymentType,
            ]);

            foreach ($cart as $item) {

                $product = $products->get($item['id']);

                $price = $product->sell_price;

                $quantity = (int) $item['quantity'];

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $price,
                    'line_total' => $price * $quantity,
                ]);

                if ($product->stock < $quantity) {
                    throw new DomainException(
                    "موجودی {$product->name} کافی نیست."
                    );
                }

                


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