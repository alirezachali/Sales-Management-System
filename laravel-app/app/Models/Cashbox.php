<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cashbox extends Model
{
    protected $fillable = [
        'name',
        'type',
        'account_number',
        'iban',
        'bank_name',
        'opening_balance',
        'balance',
        'is_default',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'balance' => 'decimal:2',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashboxTransaction::class);
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(CashboxTransaction::class, 'to_cashbox_id');
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getDefaultId(): ?int
    {
        return static::query()->where('is_default', true)->value('id')
            ?? static::query()->where('is_active', true)->orderBy('id')->value('id');
    }

    public function typeText(): string
    {
        return match ($this->type) {
            'cash' => 'نقدی',
            'bank' => 'بانکی',
            'wallet' => 'کیف پول الکترونیکی',
            'other' => 'سایر',
            default => $this->type,
        };
    }
}
