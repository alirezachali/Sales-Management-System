<?php

namespace Tests\Feature;

use App\Livewire\Sales\SaleManager;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SaleCheckoutRulesTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        $role = Role::create(['name' => 'cashier-role', 'display_name' => 'صندوق‌دار']);

        return User::create([
            'name' => 'صندوق‌دار تست',
            'username' => 'cashier',
            'email' => 'cashier@example.com',
            'phone' => '09120000000',
            'password' => 'secret-password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    private function makeProduct(int $stock = 10, float $price = 2000): Product
    {
        return Product::create([
            'barcode' => 'BC-'.uniqid(),
            'name' => 'کالای تست',
            'buy_price' => 1000,
            'sell_price' => $price,
            'stock' => $stock,
            'unit' => 'عدد',
            'is_active' => true,
        ]);
    }

    private function makeCustomer(): Customer
    {
        return Customer::create([
            'first_name' => 'مشتری',
            'last_name' => 'تست',
            'mobile' => '0912'.random_int(1000000, 9999999),
            'is_active' => true,
        ]);
    }

    public function test_cash_shortfall_shows_user_friendly_message(): void
    {
        $product = $this->makeProduct();

        Livewire::actingAs($this->makeUser())
            ->test(SaleManager::class)
            ->call('addProduct', $product->id)
            ->call('setPaymentType', 'cash')
            ->set('paidAmount', 500)
            ->call('checkout')
            ->assertHasErrors(['paidAmount']);

        $this->assertSame(0, Sale::count());
    }

    public function test_mixed_payment_less_than_total_shows_message(): void
    {
        $product = $this->makeProduct(price: 10000);

        Livewire::actingAs($this->makeUser())
            ->test(SaleManager::class)
            ->call('addProduct', $product->id)
            ->call('setPaymentType', 'mixed')
            ->set('cashAmount', 3000)
            ->set('cardAmount', 2000)
            ->call('checkout')
            ->assertHasErrors(['cardAmount']);

        $this->assertSame(0, Sale::count());
    }

    public function test_mixed_payment_more_than_total_shows_message(): void
    {
        $product = $this->makeProduct(price: 10000);

        Livewire::actingAs($this->makeUser())
            ->test(SaleManager::class)
            ->call('addProduct', $product->id)
            ->call('setPaymentType', 'mixed')
            ->set('cashAmount', 7000)
            ->set('cardAmount', 5000)
            ->call('checkout')
            ->assertHasErrors(['cardAmount']);

        $this->assertSame(0, Sale::count());
    }

    public function test_credit_without_customer_is_blocked(): void
    {
        $product = $this->makeProduct();

        Livewire::actingAs($this->makeUser())
            ->test(SaleManager::class)
            ->call('addProduct', $product->id)
            ->call('setPaymentType', 'credit')
            ->assertHasErrors(['paymentType'])
            ->assertSet('paymentType', 'cash');
    }

    public function test_discount_greater_than_subtotal_is_blocked(): void
    {
        $product = $this->makeProduct(price: 2000);

        Livewire::actingAs($this->makeUser())
            ->test(SaleManager::class)
            ->call('addProduct', $product->id)
            ->set('discount', 5000)
            ->call('openCheckoutModal')
            ->assertSet('showCheckoutModal', false);

        $this->assertSame(0, Sale::count());
    }

    public function test_sale_service_rejects_cash_underpayment_with_amounts(): void
    {
        $this->actingAs($this->makeUser());
        $product = $this->makeProduct(price: 5000);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('کمتر از مبلغ سبد خرید');

        app(SaleService::class)->checkout(
            [['id' => $product->id, 'quantity' => 1]],
            0,
            'cash',
            null,
            [['type' => 'cash', 'amount' => 1000]],
        );
    }

    public function test_sale_service_rejects_card_overpayment_with_amounts(): void
    {
        $this->actingAs($this->makeUser());
        $product = $this->makeProduct(price: 5000);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('بیشتر از مبلغ سبد خرید');

        app(SaleService::class)->checkout(
            [['id' => $product->id, 'quantity' => 1]],
            0,
            'card',
            null,
            [['type' => 'card', 'amount' => 6000]],
        );
    }

    public function test_successful_cash_checkout_with_change(): void
    {
        $this->actingAs($this->makeUser());
        $product = $this->makeProduct(price: 2000);

        $sale = app(SaleService::class)->checkout(
            [['id' => $product->id, 'quantity' => 1]],
            0,
            'cash',
            null,
            [['type' => 'cash', 'amount' => 2500]],
        );

        $this->assertSame(2000.0, (float) $sale->final_price);
        $this->assertSame(500.0, (float) $sale->change_amount);
        $this->assertSame(9.0, (float) $product->fresh()->stock);
    }

    public function test_insufficient_stock_is_rejected(): void
    {
        $this->actingAs($this->makeUser());
        $product = $this->makeProduct(stock: 1, price: 2000);

        $this->expectException(\App\Exceptions\Business\InsufficientStockException::class);

        app(SaleService::class)->checkout(
            [['id' => $product->id, 'quantity' => 5]],
            0,
            'cash',
            null,
            [['type' => 'cash', 'amount' => 10000]],
        );
    }

    public function test_credit_prepaid_cannot_exceed_total(): void
    {
        $product = $this->makeProduct(price: 2000);
        $customer = $this->makeCustomer();

        Livewire::actingAs($this->makeUser())
            ->test(SaleManager::class)
            ->call('addProduct', $product->id)
            ->call('selectCustomer', $customer->id)
            ->call('setPaymentType', 'credit')
            ->set('paidAmount', 5000)
            ->call('checkout')
            ->assertHasErrors(['paidAmount']);

        $this->assertSame(0, Sale::count());
    }
}
