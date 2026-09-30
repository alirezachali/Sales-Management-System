<?php

namespace Tests\Feature;

use App\Livewire\Sales\SaleManager;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * خالی گذاشتن فیلدهای عددی مودال تسویه نباید خطای سرور بدهد.
 */
class SaleManagerNumericInputTest extends TestCase
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

    private function makeProduct(): Product
    {
        return Product::create([
            'barcode' => '123456789',
            'name' => 'کالای تست',
            'buy_price' => 1000,
            'sell_price' => 2000,
            'stock' => 10,
            'unit' => 'عدد',
            'is_active' => true,
        ]);
    }

    private function makeCustomer(): Customer
    {
        return Customer::create([
            'first_name' => 'مشتری',
            'last_name' => 'تست',
            'mobile' => '09121112233',
            'is_active' => true,
        ]);
    }

    public function test_clearing_money_fields_falls_back_to_zero(): void
    {
        Livewire::actingAs($this->makeUser())
            ->test(SaleManager::class)
            ->set('discount', '')
            ->set('paidAmount', '')
            ->set('cashAmount', '')
            ->set('cardAmount', '')
            ->assertSet('discount', 0)
            ->assertSet('paidAmount', 0)
            ->assertSet('cashAmount', 0)
            ->assertSet('cardAmount', 0);
    }

    public function test_clearing_points_field_falls_back_to_zero(): void
    {
        Livewire::actingAs($this->makeUser())
            ->test(SaleManager::class)
            ->set('pointsToRedeem', '')
            ->assertSet('pointsToRedeem', 0);
    }

    public function test_credit_checkout_with_empty_prepayment_registers_full_debt(): void
    {
        $product = $this->makeProduct();
        $customer = $this->makeCustomer();

        Livewire::actingAs($this->makeUser())
            ->test(SaleManager::class)
            ->call('addProduct', $product->id)
            ->call('selectCustomer', $customer->id)
            ->call('setPaymentType', 'credit')
            ->set('paidAmount', '')
            ->call('checkout')
            ->assertHasNoErrors();

        $sale = Sale::latest('id')->first();

        $this->assertNotNull($sale, 'فاکتور نسیه ثبت نشد.');
        $this->assertSame('credit', $sale->payment_type);
        $this->assertSame(0.0, (float) $sale->paid_amount);
        $this->assertSame(2000.0, (float) $sale->final_price);
    }

    public function test_cash_checkout_with_empty_amount_shows_validation_error(): void
    {
        $product = $this->makeProduct();

        Livewire::actingAs($this->makeUser())
            ->test(SaleManager::class)
            ->call('addProduct', $product->id)
            ->call('setPaymentType', 'cash')
            ->set('paidAmount', '')
            ->call('checkout')
            ->assertHasErrors(['paidAmount']);

        $this->assertSame(0, Sale::count());
    }
}
