<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Business\InsufficientStockException;

class StockService
{
    // کم کردن موجودی کالا
    public function remove(
        Product $product,
        float $quantity,
        string $description = 'فروش کالا'
    ): void {

        $this->ensureAvailable($product, $quantity);

        $product->decrement('stock', $quantity);

        // ثبت کاهش موجودی در دیتابیس
        StockMovement::create([
            'product_id'  => $product->id,
            'type'        => 'sale',
            'quantity'    => $quantity,
            'description' => $description,
        ]);
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
    
    // چک کردن موجودی کالا
    public function ensureAvailable(
        Product $product,
        float $quantity
    ): void {

        // اگر موجودی کالا صفر بود
        if ($product->stock < $quantity) {
            throw new InsufficientStockException($product);
        }

    }

}