<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'points',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'balance_after' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public static function typeLabels(): array
    {
        return [
            'earn' => 'کسب امتیاز',
            'redeem' => 'استفاده از امتیاز',
            'expire' => 'انقضای امتیاز',
            'adjust' => 'تنظیم دستی',
        ];
    }

    public function typeText(): string
    {
        return static::typeLabels()[$this->type] ?? $this->type;
    }
}
