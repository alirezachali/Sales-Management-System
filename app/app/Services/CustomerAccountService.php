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

    public function balance(int $customerId): float
    {
        $transactions = CustomerAccountTransaction::where(
            'customer_id',
            $customerId
        )->get();

        $balance = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->type === 'sale') {
                $balance += (float) $transaction->amount;
            }

            if ($transaction->type === 'payment') {
                $balance -= (float) $transaction->amount;
            }

            if ($transaction->type === 'refund') {
                $balance -= (float) $transaction->amount;
            }

            if ($transaction->type === 'adjustment') {
                $balance += (float) $transaction->amount;
            }
        }

        return $balance;
    }
}