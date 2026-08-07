<?php

namespace App\Exceptions\Business;

use Exception;
use App\Models\Product;

class InsufficientStockException extends Exception
{
    public function __construct(Product $product)
    {
        parent::__construct("موجودی {$product->name} کافی نیست.");
    }
}