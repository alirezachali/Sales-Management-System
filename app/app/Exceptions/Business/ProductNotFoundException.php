<?php

namespace App\Exceptions\Business;

use Exception;

class ProductNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('کالا یافت نشد.');
    }
}