<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\OnlineOrder;
use App\Models\Product;
use App\Models\Setting;
use App\Services\OnlineShop\PublishCatalogToOnlineShop;
use App\Services\OnlineShop\PullOnlineOrders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OnlineShopSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_sends_catalog_and_public_settings(): void
    {
        config([
            'online_shop.url' => 'https://shop.test',
            'online_shop.token' => 'secret',
            'online_shop.city' => 'تهران',
        ]);

        Setting::updateOrCreate(['key' => 'store_name'], ['value' => 'سوپرمارکت نمونه']);

        $category = Category::create(['name' => 'نوشیدنی', 'is_active' => true]);
        Product::create([
            'barcode' => '111',
            'name' => 'آب',
            'category_id' => $category->id,
            'buy_price' => 1000,
            'sell_price' => 15000,
            'stock' => 5,
            'unit' => 'عدد',
            'is_active' => true,
        ]);

        Http::fake([
            'https://shop.test/api/catalog/ingest' => Http::response(['ok' => true], 200),
        ]);

        app(PublishCatalogToOnlineShop::class)->handle();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://shop.test/api/catalog/ingest'
                && $request['settings']['store_name'] === 'سوپرمارکت نمونه'
                && $request['products'][0]['name'] === 'آب';
        });
    }

    public function test_pull_stores_orders_and_acks(): void
    {
        config([
            'online_shop.url' => 'https://shop.test',
            'online_shop.token' => 'secret',
        ]);

        Http::fake([
            'https://shop.test/api/outbox/pending*' => Http::response([
                'data' => [[
                    'type' => 'order.placed',
                    'idempotency_key' => 'k-1',
                    'payload' => [
                        'order_id' => 9,
                        'total' => 40000,
                        'customer' => ['name' => 'علی', 'phone' => '0912'],
                    ],
                ]],
            ], 200),
            'https://shop.test/api/outbox/ack' => Http::response(['acked' => 1], 200),
        ]);

        $count = app(PullOnlineOrders::class)->handle();

        $this->assertSame(1, $count);
        $this->assertTrue(OnlineOrder::query()->where('idempotency_key', 'k-1')->exists());
    }
}
