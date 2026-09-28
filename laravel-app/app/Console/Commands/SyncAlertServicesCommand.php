<?php

namespace App\Console\Commands;

use App\Alerts\AlertRegistry;
use App\Models\AlertService;
use Illuminate\Console\Command;

/**
 * رکورد سرویس‌های هشدار را با config/alerts.php همگام می‌کند.
 * پس از افزودن سرویس جدید کافی است یک‌بار اجرا شود.
 */
class SyncAlertServicesCommand extends Command
{
    protected $signature = 'alerts:sync';

    protected $description = 'همگام‌سازی سرویس‌های هشدار با فایل کانفیگ';

    public function handle(AlertRegistry $registry): int
    {
        $registry->sync();

        foreach (AlertService::query()->orderBy('id')->get() as $service) {
            $state = $service->enabled ? 'فعال' : 'غیرفعال';
            $this->line("- {$service->key} [{$state}] ({$service->frequency})");
        }

        $this->info('سرویس‌های هشدار همگام شدند.');

        return self::SUCCESS;
    }
}
