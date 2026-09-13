<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockCount extends Model
{
    protected $fillable = [
        'reference',
        'warehouse_id',
        'status',
        'notes',
        'created_by',
        'finalized_by',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'finalized_at' => 'datetime',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockCountItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function finalizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'CN-'.now()->format('ymdHis').str_pad((string) random_int(0, 99), 2, '0', STR_PAD_LEFT);
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    public function statusText(): string
    {
        return match ($this->status) {
            'draft' => 'در حال شمارش',
            'finalized' => 'نهایی شده',
            'cancelled' => 'لغو شده',
            default => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'draft' => 'warning',
            'finalized' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }
}
