<?php

namespace App\Jobs\Alerts;

use App\Models\Product;

/**
 * کالاهای زیر حد هشدار موجودی.
 */
class LowStockAlertJob extends AlertJob
{
    /** حداکثر تعداد کالای نمایش‌داده‌شده در هر گروه هشدار. */
    private const LIMIT = 6;

    public function key(): string
    {
        return 'low_stock';
    }

    protected function build(): array
    {    // گرفتن تعداد هشدار اتمام موجودی از دیتابیس
        $threshold = (float) setting('stock_alert', 5);

        return Product::query()
            ->where('is_active', true)
            ->where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->orderBy('id')
            ->limit(self::LIMIT)
            ->get(['id', 'name', 'stock', 'unit'])
            ->map(fn (Product $product) => [
                'title' => $product->name,
                'meta' => 'موجودی: '.number_format((float) $product->stock, 0).' '.$product->unit,
                'url' => route('products.stock', $product->id, false),
            ])
            ->all();
    }
}
