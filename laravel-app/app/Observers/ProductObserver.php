<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Cache;

/**
 * همگام‌ساز خودکار موجودی انبارها با موجودی کل محصول.
 *
 * هر کد قدیمی که مستقیم products.stock را تغییر دهد، مابه‌التفاوت به‌صورت
 * خودکار روی انبار پیش‌فرض اعمال می‌شود تا جمع انبارها همیشه با کل برابر بماند.
 */
class ProductObserver
{
    /**
     * بعد از هر تغییر روی محصول (ایجاد/ویرایش موجودی)، کش آمار داشبورد
     * مربوط به موجودی باطل می‌شود تا poll بعدی مقادیر تازه نشان دهد.
     * معرف Threshold مناسب نیست چون مقدارش به تنظیم بستگی دارد؛ کلیدهای
     * کش با پیشوند مشترک را با حذف همه‌ی موارد ممکن می‌سنجیم.
     */
    protected function forgetStockCaches(): void
    {
        $thresholds = [0, 1, 2, 3, 5, 10];

        foreach ($thresholds as $t) {
            Cache::forget('warehouse-running-out-'.$t);
            Cache::forget('warehouse-running-out-count-'.$t);
            Cache::forget('alerts-low-stock-'.$t);
        }

        Cache::forget('warehouse-products-count');
        Cache::forget('warehouse-ranking');
        Cache::forget('dashboard-low-stock-count');
        Cache::forget('dashboard-low-stock-list');
        Cache::forget('dashboard-products-count');
    }

    /**
     * موجودی اولیه محصول جدید روی انبار پیش‌فرض ثبت می‌شود.
     */
    public function created(Product $product): void
    {
        $this->forgetStockCaches();

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
        if ($product->isDirty('stock') || $product->isDirty('is_active')) {
            $this->forgetStockCaches();
        }

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
