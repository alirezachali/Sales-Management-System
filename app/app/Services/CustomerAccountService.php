<?php

namespace App\Services;

use App\Models\CustomerAccountTransaction;
use App\Models\Sale;

class CustomerAccountService
{
    public function addDebt(
        Sale $sale,
        float $amount,
        string $description = 'فروش نسیه'
    ): CustomerAccountTransaction {
        return CustomerAccountTransaction::create([
            'customer_id' => $sale->customer_id,
            'type' => 'sale',
            'amount' => $amount,
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'description' => $description,
        ]);
    }
}