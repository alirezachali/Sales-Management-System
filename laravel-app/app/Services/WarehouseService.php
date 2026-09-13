<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

/**
 * سرویس موجودی چند-انباره.
 *
 * جمع موجودی انبارها همیشه با products.stock همگام نگه داشته می‌شود،
 * پس کدهای قدیمی که مستقیم از products.stock استفاده می‌کنند نمی‌شکنند.
 */
class WarehouseService
{
    /**
     * ورود مقدار به موجودی یک انبار (ثبت گردش + همگام‌سازی موجودی کل).
     */
    public function addToWarehouse(
        Product $product,
        int $warehouseId,
        float $quantity,
        string $type = 'purchase',
        string $description = 'ورود کالا',
        ?int $userId = null,
    ): void {
        DB::transaction(function () use ($product, $warehouseId, $quantity, $type, $description, $userId) {
            $stock = ProductWarehouseStock::firstOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => $warehouseId],
                ['quantity' => 0],
            );

            $stock->increment('quantity', $quantity);

            $product->increment('stock', $quantity);

            $this->logMovement($product, $warehouseId, $type, $quantity, $description, $userId);
        });
    }

    /**
     * خروج مقدار از موجودی یک انبار (ثبت گردش + همگام‌سازی موجودی کل).
     */
    public function removeFromWarehouse(
        Product $product,
        int $warehouseId,
        float $quantity,
        string $type = 'sale',
        string $description = 'خروج کالا',
        ?int $userId = null,
    ): void {
        DB::transaction(function () use ($product, $warehouseId, $quantity, $type, $description, $userId) {
            $stock = ProductWarehouseStock::firstOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => $warehouseId],
                ['quantity' => 0],
            );

            $stock->decrement('quantity', $quantity);

            $product->decrement('stock', $quantity);

            $this->logMovement($product, $warehouseId, $type, $quantity, $description, $userId);
        });
    }

    /**
     * تنظیم دقیق موجودی محصول در یک انبار (برای انبارگردانی و اصلاح).
     * مابه‌التفاوت را با نوع adjust ثبت می‌کند و آن را برمی‌گرداند.
     */
    public function setWarehouseQuantity(
        Product $product,
        int $warehouseId,
        float $newQuantity,
        string $description = 'اصلاح موجودی',
        ?int $userId = null,
    ): float {
        return DB::transaction(function () use ($product, $warehouseId, $newQuantity, $description, $userId) {
            $stock = ProductWarehouseStock::firstOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => $warehouseId],
                ['quantity' => 0],
            );

            $difference = $newQuantity - (float) $stock->quantity;

            if (abs($difference) > 0.0001) {
                $stock->update(['quantity' => $newQuantity]);
                $product->increment('stock', $difference);

                $this->logMovement(
                    $product,
                    $warehouseId,
                    'adjust',
                    abs($difference),
                    $description.' ('.($difference > 0 ? 'اضافه' : 'کسر').')',
                    $userId,
                );
            }

            return $difference;
        });
    }

    /**
     * انتقال کالا بین دو انبار.
     */
    public function transfer(
        Product $product,
        int $fromWarehouseId,
        int $toWarehouseId,
        float $quantity,
        string $description = 'انتقال بین انبار',
        ?int $userId = null,
    ): void {
        DB::transaction(function () use ($product, $fromWarehouseId, $toWarehouseId, $quantity, $description, $userId) {
            $this->removeFromWarehouse(
                $product,
                $fromWarehouseId,
                $quantity,
                'transfer',
                $description,
                $userId,
            );

            $this->addToWarehouse(
                $product,
                $toWarehouseId,
                $quantity,
                'transfer',
                $description,
                $userId,
            );
        });
    }

    /**
     * یکسان‌سازی موجودی کل محصول با جمع انبارها.
     */
    public function syncTotalStock(Product $product): void
    {
        $sum = (float) ProductWarehouseStock::where('product_id', $product->id)->sum('quantity');

        if (abs($sum - (float) $product->stock) > 0.0001) {
            $product->update(['stock' => $sum]);
        }
    }

    public function resolveWarehouseId(?int $warehouseId): int
    {
        return $warehouseId ?: (Warehouse::getDefaultId() ?? 0);
    }

    private function logMovement(
        Product $product,
        int $warehouseId,
        string $type,
        float $quantity,
        string $description,
        ?int $userId,
    ): void {
        StockMovement::create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouseId,
            'user_id' => $userId ?? auth()->id(),
            'type' => $type,
            'quantity' => $quantity,
            'description' => $description,
        ]);
    }
}
