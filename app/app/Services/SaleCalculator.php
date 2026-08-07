<?php

namespace App\Services;

use Illuminate\Support\Collection;
use DomainException;


class SaleCalculator
{
    public function total(array $cart, Collection $products): float
    {
        return collect($cart)->sum(function ($item) use ($products) {

            $product = $products->get($item['id']);

            if (!$product) {
                throw new DomainException("کالا یافت نشد.");
            }

            return $product->sell_price * $item['quantity'];
        });
    }
}