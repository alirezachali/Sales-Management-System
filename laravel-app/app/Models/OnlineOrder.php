<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineOrder extends Model
{
    protected $fillable = [
        'idempotency_key',
        'shop_order_id',
        'status',
        'customer_name',
        'customer_phone',
        'city',
        'address',
        'total',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'total' => 'decimal:2',
        ];
    }
}
