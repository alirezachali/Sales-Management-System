<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function remove(
        Product $product,
        float $quantity,
        string $description = 'فروش کالا'
    )
    {
        DB::transaction(function () use (
            $product,
            $quantity,
            $description
        ) {

            $product->decrement('stock', $quantity);

            StockMovement::create([

                'product_id' => $product->id,

                'type' => 'sale',

                'quantity' => $quantity,

                'description' => $description,

            ]);

        });
    }

    public function add(
        Product $product,
        float $quantity,
        string $type='purchase',
        string $description='ورود کالا'
    )
    {
        DB::transaction(function () use (
            $product,
            $quantity,
            $type,
            $description
        ) {

            $product->increment('stock',$quantity);

            StockMovement::create([

                'product_id'=>$product->id,

                'type'=>$type,

                'quantity'=>$quantity,

                'description'=>$description,

            ]);

        });

    }

}