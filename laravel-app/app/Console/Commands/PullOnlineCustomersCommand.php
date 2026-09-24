<?php

namespace App\Console\Commands;

use App\Services\OnlineShop\PullOnlineCustomers;
use Illuminate\Console\Command;

class PullOnlineCustomersCommand extends Command
{
    protected $signature = 'online-shop:pull-customers';

    protected $description = 'Upsert POS customers from the online shop directory, matched by mobile';

    public function handle(PullOnlineCustomers $puller): int
    {
        $count = $puller->handle();
        $this->info("Synced {$count} customer(s).");

        return self::SUCCESS;
    }
}
