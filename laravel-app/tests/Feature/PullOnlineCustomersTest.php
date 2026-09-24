<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Services\OnlineShop\PullOnlineCustomers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PullOnlineCustomersTest extends TestCase
{
    use RefreshDatabase;

    public function test_upserts_customer_by_mobile(): void
    {
        config([
            'online_shop.url' => 'https://shop.test',
            'online_shop.token' => 'secret',
        ]);

        Customer::create([
            'first_name' => 'علی',
            'last_name' => 'قدیمی',
            'mobile' => '09120000000',
            'is_active' => true,
        ]);

        Http::fake([
            'https://shop.test/api/customers' => Http::response([
                'data' => [[
                    'id' => 7,
                    'name' => 'علی مشتری',
                    'phone' => '09120000000',
                    'city' => 'تهران',
                    'address' => 'خیابان ۱',
                    'created_at' => now()->toIso8601String(),
                ]],
            ], 200),
        ]);

        $count = app(PullOnlineCustomers::class)->handle();

        $this->assertSame(1, $count);
        $customer = Customer::query()->where('mobile', '09120000000')->first();
        $this->assertSame(7, $customer->online_user_id);
        $this->assertSame('علی', $customer->first_name);
        $this->assertSame('قدیمی', $customer->last_name);
    }
}
