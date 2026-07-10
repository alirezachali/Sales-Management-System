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

    public function checkout(array $cart)
    {
        return DB::transaction(function () use ($cart) {

            $total = $this->calculator->total($cart);

            $sale = Sale::create([
                'total_amount' => $total,
            ]);

            foreach ($cart as $item) {

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'total'      => $item['price'] * $item['quantity'],
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