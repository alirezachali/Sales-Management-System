<?php

namespace App\Console\Commands;

use App\Alerts\AlertDispatcher;
use Illuminate\Console\Command;

/**
 * سرویس‌های هشدار سررسیدشده را در صف می‌گذارد.
 * این دستور هر دقیقه توسط زمان‌بند اجرا می‌شود.
 */
class DispatchAlertsCommand extends Command
{
    protected $signature = 'alerts:dispatch';

    protected $description = 'اجرای سرویس‌های هشدار که زمانشان رسیده است';

    public function handle(AlertDispatcher $dispatcher): int
    {
        $count = $dispatcher->dispatchDue();

        $this->info("{$count} سرویس هشدار در صف قرار گرفت.");

        return self::SUCCESS;
    }
}
