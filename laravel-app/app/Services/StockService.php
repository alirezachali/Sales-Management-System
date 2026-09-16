<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Business\InsufficientStockException;

class StockService
{
    public function __construct(
        protected WarehouseService $warehouseService,
    ) {}

    // کم کردن موجودی کالا (پیش‌فرض: از انبار پیش‌فرض)
    public function remove(
        Product $product,
        float $quantity,
        string $description = 'فروش کالا',
        ?int $warehouseId = null,
    ): void {

        $this->ensureAvailable($product, $quantity);

        $this->warehouseService->removeFromWarehouse(
            $product,
            $warehouseId ?: (Warehouse::getDefaultId() ?? 0),
            $quantity,
            'sale',
            $description,
        );
    }

    public function add(
        Product $product,
        float $quantity,
        string $type = 'purchase',
        string $description = 'ورود کالا',
        ?int $warehouseId = null,
    ) {
        $this->warehouseService->addToWarehouse(
            $product,
            $warehouseId ?: (Warehouse::getDefaultId() ?? 0),
            $quantity,
            $type,
            $description,
        );
    }

    // چک کردن موجودی کالا
    public function ensureAvailable(
        Product $product,
        float $quantity
    ): void {

        // اگر موجودی کالا صفر بود
        if ($product->stock < $quantity) {
            throw new InsufficientStockException($product);
        }

    }

}
