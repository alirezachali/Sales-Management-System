<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_id',
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

    public function customer(): BelongsTo
    {
        // تعریف رابطه این مدل با مدل مشتری
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        // تعریف رابطه میان این مدل با مدل پرداخت
        return $this->hasMany(Payment::class);
    }
}