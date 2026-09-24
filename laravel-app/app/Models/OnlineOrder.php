<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineOrder extends Model
{
    protected $fillable = [
        'idempotency_key',
        'shop_order_id',
        'sale_id',
        'status',
        'customer_name',
        'customer_phone',
        'city',
        'address',
        'courier_name',
        'courier_phone',
        'reject_reason',
        'total',
        'payload',
        'packed_at',
        'dispatched_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'total' => 'decimal:2',
            'packed_at' => 'datetime',
            'dispatched_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function sale(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'received', 'pending' => 'جدید',
            'packing' => 'در حال آماده‌سازی',
            'out_for_delivery' => 'ارسال با پیک',
            'delivered' => 'تحویل شد',
            'rejected' => 'رد شده',
            default => $this->status,
        };
    }
}
