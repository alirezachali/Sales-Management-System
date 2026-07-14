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
    )
    {
        return DB::transaction(function () use ($cart) {

            $total = $this->calculator->total($cart);

            $discount = 0;
            $finalPrice = $total - $discount;

            $sale = Sale::create([
                'invoice_number' => 'INV-' . now()->format('YmdHis'),
                'user_id'        => auth()->id() ?? 1,
                'total_price'    => $total,
                'discount'       => $discount,
                'final_price'    => $finalPrice,
                'payment_type'   => 'cash',
            ]);

            foreach ($cart as $item) {

                SaleItem::create([
                    'sale_id'     => $sale->id,
                    'product_id'  => $item['id'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['price'],
                    'line_total'  => $item['price'] * $item['quantity'],
                ]);

                $product = Product::findOrFail($item['id']);

                $this->stockService->remove(
                    $product,
                    $item['quantity'],
                    'فروش کالا'
                );
            }

            return $sale;
        });
    }
}