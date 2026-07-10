<?php

namespace App\Services;

class SaleCalculator
{
    public function total(array $cart): float
    {
        return collect($cart)->sum(function ($item) {

            return $item['price'] * $item['quantity'];

        });
    }
}