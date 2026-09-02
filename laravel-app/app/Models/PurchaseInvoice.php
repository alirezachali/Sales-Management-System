<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseInvoice extends Model
{
    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'invoice_number',
        'total_amount',
        'paid_amount',
        'payment_method',
        'status',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public const PAYMENT_METHODS = [
        'cash' => 'نقدی',
        'card' => 'کارت',
        'transfer' => 'کارت به کارت / حواله',
        'credit' => 'نسیه',
        'other' => 'سایر',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function expense(): HasOne
    {
        return $this->hasOne(Expense::class);
    }
}