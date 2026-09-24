<?php

namespace App\Console\Commands;

use App\Services\OnlineShop\PublishCatalogToOnlineShop;
use Illuminate\Console\Command;

class PublishCatalogToOnlineShopCommand extends Command
{
    protected $signature = 'online-shop:publish-catalog';

    protected $description = 'Push products, categories, and public store settings to the online shop';

    public function handle(PublishCatalogToOnlineShop $publisher): int
    {
        $publisher->handle();
        $this->info('Catalog published.');

        return self::SUCCESS;
    }
}
