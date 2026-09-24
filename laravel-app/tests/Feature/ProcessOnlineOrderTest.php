<?php

namespace Tests\Feature;

use App\Models\Cashbox;
use App\Models\OnlineOrder;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use App\Services\OnlineShop\ProcessOnlineOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessOnlineOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_fulfill_creates_sale_and_decrements_stock(): void
    {
        $role = Role::create(['name' => Role::SUPER_ADMIN, 'display_name' => 'مدیر']);
        $user = User::create([
            'name' => 'مدیر',
            'username' => 'admin',
            'email' => 'a@example.com',
            'phone' => '09120000000',
            'password' => 'secret-password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
        $this->actingAs($user);

        Cashbox::create([
            'name' => 'کارتخوان',
            'type' => 'bank',
            'balance' => 0,
            'opening_balance' => 0,
            'is_default' => true,
            'is_active' => true,
        ]);

        $product = Product::create([
            'barcode' => '222',
            'name' => 'نان',
            'buy_price' => 5000,
            'sell_price' => 20000,
            'stock' => 10,
            'unit' => 'عدد',
            'is_active' => true,
        ]);

        $order = OnlineOrder::create([
            'idempotency_key' => 'k-process',
            'shop_order_id' => 1,
            'status' => 'received',
            'customer_name' => 'علی',
            'total' => 40000,
            'payload' => [
                'order_id' => 1,
                'items' => [
                    ['source_product_id' => $product->id, 'quantity' => 2, 'unit_price' => 20000],
                ],
            ],
        ]);

        $sale = app(ProcessOnlineOrder::class)->fulfill($order);

        $this->assertInstanceOf(Sale::class, $sale);
        $this->assertSame('packing', $order->fresh()->status);
        $this->assertSame($sale->id, $order->fresh()->sale_id);
        $this->assertEquals(8, (float) $product->fresh()->stock);
    }

    public function test_reject_received_order(): void
    {
        $order = OnlineOrder::create([
            'idempotency_key' => 'k-rej',
            'status' => 'received',
            'total' => 0,
            'payload' => [],
        ]);

        app(ProcessOnlineOrder::class)->reject($order, 'ناموجود');

        $this->assertSame('rejected', $order->fresh()->status);
    }
}
