<?php

namespace App\Alerts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * ذخیره‌گاه نتیجه‌ی سرویس‌های هشدار در کش.
 *
 * Jobها نتیجه‌ی محاسبه‌شده‌شان را این‌جا می‌نویسند و زنگ هشدار (که در
 * فوتر همه‌ی صفحات رندر می‌شود) فقط این کش را می‌خواند؛ بنابراین نمایش
 * هشدارها هیچ کوئری‌ای روی دیتابیس نمی‌زند.
 */
class AlertStore
{
    private const PREFIX = 'alerts:service:';

    /**
     * نتیجه‌ی یک سرویس را ذخیره می‌کند.
     *
     * @param  array<int, array{title:string, meta:string, url:string}>  $items
     */
    public function put(string $key, array $items): void
    {
        Cache::put($this->cacheKey($key), [
            'items' => array_values($items),
            'generated_at' => now()->toIso8601String(),
        ], now()->addDays($this->ttlDays()));
    }

    /**
     * آیتم‌های هشدار یک سرویس (فهرست خالی در صورت نبود/انقضای کش).
     *
     * @return array<int, array{title:string, meta:string, url:string}>
     */
    public function items(string $key): array
    {
        $payload = Cache::get($this->cacheKey($key));

        return is_array($payload['items'] ?? null) ? $payload['items'] : [];
    }

    /** آیا برای این سرویس نتیجه‌ای (حتی خالی) ذخیره شده است؟ */
    public function has(string $key): bool
    {
        return Cache::has($this->cacheKey($key));
    }

    /** زمان آخرین تولید نتیجه‌ی این سرویس. */
    public function generatedAt(string $key): ?Carbon
    {
        $payload = Cache::get($this->cacheKey($key));
        $value = $payload['generated_at'] ?? null;

        return is_string($value) ? Carbon::parse($value) : null;
    }

    /** پاک‌کردن نتیجه‌ی یک سرویس (هنگام غیرفعال‌کردن آن). */
    public function forget(string $key): void
    {
        Cache::forget($this->cacheKey($key));
    }

    private function cacheKey(string $key): string
    {
        return self::PREFIX.$key;
    }

    private function ttlDays(): int
    {
        return max(1, (int) config('alerts.cache_ttl_days', 2));
    }
}
