<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Customer;


class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_number',
        'user_id',
        'total_price',
        'discount',
        'final_price',
        'payment_type',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'final_price' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        // تعریف رابطه این مدل با مدل آیتم فروش
        return $this->hasMany(SaleItem::class);
    }

    public function user(): BelongsTo
    {
        // تعریف رابطه این مدل با مدل کاربران
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        // تعریف رابطه این مدل با مدل مشتری
        return $this->belongsTo(Customer::class);
    }
}