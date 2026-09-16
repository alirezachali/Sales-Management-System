<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'manager_name',
        'is_default',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductWarehouseStock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'from_warehouse_id');
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'to_warehouse_id');
    }

    public function counts(): HasMany
    {
        return $this->hasMany(StockCount::class);
    }

    public static function getDefaultId(): ?int
    {
        return static::query()->where('is_default', true)->value('id')
            ?? static::query()->where('is_active', true)->orderBy('id')->value('id');
    }

    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_active ? 'فعال' : 'غیرفعال',
        );
    }
}
