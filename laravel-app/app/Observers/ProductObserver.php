<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\Warehouse;

/**
 * همگام‌ساز خودکار موجودی انبارها با موجودی کل محصول.
 *
 * هر کد قدیمی که مستقیم products.stock را تغییر دهد، مابه‌التفاوت به‌صورت
 * خودکار روی انبار پیش‌فرض اعمال می‌شود تا جمع انبارها همیشه با کل برابر بماند.
 */
class ProductObserver
{
    /**
     * موجودی اولیه محصول جدید روی انبار پیش‌فرض ثبت می‌شود.
     */
    public function created(Product $product): void
    {
        if ((float) $product->stock <= 0) {
            return;
        }

        $warehouseId = Warehouse::getDefaultId();

        if (! $warehouseId) {
            return;
        }

        ProductWarehouseStock::firstOrCreate(
            ['product_id' => $product->id, 'warehouse_id' => $warehouseId],
            ['quantity' => (float) $product->stock],
        );
    }

    public function updated(Product $product): void
    {
        if (! $product->isDirty('stock')) {
            return;
        }

        $warehouseId = Warehouse::getDefaultId();

        if (! $warehouseId) {
            return;
        }

        $sum = (float) ProductWarehouseStock::where('product_id', $product->id)->sum('quantity');
        $delta = (float) $product->stock - $sum;

        if (abs($delta) < 0.0001) {
            return;
        }

        $stock = ProductWarehouseStock::firstOrCreate(
            ['product_id' => $product->id, 'warehouse_id' => $warehouseId],
            ['quantity' => 0],
        );

        $stock->increment('quantity', $delta);
    }
}
