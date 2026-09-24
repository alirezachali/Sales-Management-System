<?php

namespace App\Console\Commands;

use App\Services\OnlineShop\PullOnlineOrders;
use Illuminate\Console\Command;

class PullOnlineOrdersCommand extends Command
{
    protected $signature = 'online-shop:pull-orders';

    protected $description = 'Pull pending online orders from the shop outbox';

    public function handle(PullOnlineOrders $puller): int
    {
        $count = $puller->handle();
        $this->info("Pulled {$count} order(s).");

        return self::SUCCESS;
    }
}
