<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
protected $fillable = [
        'barcode',
        'name',
        'category_id',
        'brand_id',
        'buy_price',
        'sell_price',
        'stock',
        'unit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'buy_price' => 'decimal:2',
            'sell_price' => 'decimal:2',
            'stock' => 'decimal:3',
            'is_active' => 'boolean',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse_stocks')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function warehouseStocks(): HasMany
    {
        return $this->hasMany(ProductWarehouseStock::class);
    }

    public function stockIn(int $warehouseId): float
    {
        return (float) $this->warehouseStocks()->where('warehouse_id', $warehouseId)->value('quantity');
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // متد برای نمایش موجودی کالا در رابط کاربری
    public function getFormattedStockAttribute(): string
    {
        return rtrim(
            rtrim(number_format((float) $this->stock, 3, '.', ','), '0'),
            '.'
        );
    }
}
