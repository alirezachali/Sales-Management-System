<?php

namespace App\Alerts;

/**
 * صف‌گذاری Jobهای سرویس‌های هشدار.
 *
 * زمان‌بند هر دقیقه dispatchDue() را صدا می‌زند و فقط سرویس‌هایی که
 * زمانشان رسیده در صف قرار می‌گیرند؛ بقیه بدون هیچ هزینه‌ای رد می‌شوند.
 */
class AlertDispatcher
{
    public function __construct(
        private AlertRegistry $registry,
        private AlertStore $store,
    ) {}

    /**
     * سرویس‌های سررسیدشده را در صف می‌گذارد و تعدادشان را برمی‌گرداند.
     */
    public function dispatchDue(): int
    {
        $this->registry->sync();

        $dispatched = 0;

        foreach ($this->registry->due() as $service) {
            $job = $this->registry->jobClass($service->key);

            if ($job === null) {
                continue;
            }

            // قبل از صف‌گذاری، زمان را جلو می‌بریم تا اجرای بعدی درست
            // محاسبه شود و اجرای هم‌زمان/تکراری رخ ندهد.
            $service->markScheduled();

            $job::dispatch();

            $dispatched++;
        }

        return $dispatched;
    }

    /**
     * اجرای فوری یک سرویس (خروجی: تعداد هشدارهای ساخته‌شده).
     * برای دکمه‌ی «اجرای اکنون» در صفحه‌ی تنظیمات استفاده می‌شود.
     */
    public function run(string $key): int
    {
        $service = $this->registry->ensure($key);
        $job = $this->registry->jobClass($key);

        if ($service === null || $job === null) {
            return 0;
        }

        $job::dispatchSync();
        $service->markScheduled();

        return count($this->store->items($key));
    }

    /**
     * اجرای همه‌ی سرویس‌های فعال. با $sync = true به‌صورت فوری (برای
     * دکمه‌ی «به‌روزرسانی» زنگ هشدار) و در غیر این صورت در صف.
     */
    public function runAll(bool $sync = false): int
    {
        $this->registry->sync();

        $executed = 0;

        foreach ($this->registry->enabled() as $service) {
            $job = $this->registry->jobClass($service->key);

            if ($job === null) {
                continue;
            }

            $sync ? $job::dispatchSync() : $job::dispatch();

            $service->markScheduled();

            $executed++;
        }

        return $executed;
    }
}
