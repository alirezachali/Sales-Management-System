<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashboxTransaction extends Model
{
    protected $fillable = [
        'cashbox_id',
        'to_cashbox_id',
        'type',
        'amount',
        'reference_type',
        'reference_id',
        'description',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function cashbox(): BelongsTo
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function toCashbox(): BelongsTo
    {
        return $this->belongsTo(Cashbox::class, 'to_cashbox_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public static function typesWithLabels(): array
    {
        return [
            'deposit' => 'واریز',
            'withdraw' => 'برداشت',
            'transfer_in' => 'انتقال ورودی',
            'transfer_out' => 'انتقال خروجی',
            'sale' => 'فروش',
            'refund' => 'مرجوعی',
            'expense' => 'هزینه',
            'adjustment' => 'اصلاح',
        ];
    }

    public function typeText(): string
    {
        return static::typesWithLabels()[$this->type] ?? $this->type;
    }

    public function isPositive(): bool
    {
        return in_array($this->type, ['deposit', 'transfer_in', 'sale', 'adjustment'], true)
            && (float) $this->amount >= 0;
    }
}
