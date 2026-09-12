<?php

namespace Tests\Feature;

use App\Livewire\Customers\DebtorManager;
use App\Models\Customer;
use App\Models\CustomerAccountTransaction;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DebtorManagerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $role = Role::create([
            'name' => 'super-admin',
            'display_name' => 'Super Admin',
        ]);

        return User::create([
            'name' => 'Test Admin',
            'username' => 'admin_' . uniqid(),
            'email' => uniqid() . '@example.com',
            'password' => 'password',
            'role_id' => $role->id,
        ]);
    }

    private function createDebtor(float $debtAmount = 100000): Customer
    {
        $customer = Customer::create([
            'first_name' => 'علی',
            'last_name' => 'رضایی',
            'mobile' => '0912' . rand(1000000, 9999999),
            'is_active' => true,
        ]);

        $sale = Sale::create([
            'invoice_number' => 'INV-' . uniqid(),
            'user_id' => auth()->id() ?? 1,
            'customer_id' => $customer->id,
            'total_price' => $debtAmount,
            'discount' => 0,
            'final_price' => $debtAmount,
            'payment_type' => 'credit',
            'paid_amount' => 0,
            'change_amount' => 0,
        ]);

        CustomerAccountTransaction::create([
            'customer_id' => $customer->id,
            'type' => 'sale',
            'amount' => $debtAmount,
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'description' => 'فروش نسیه',
        ]);

        return $customer;
    }

    public function test_debtor_page_loads(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        Livewire::test(DebtorManager::class)
            ->assertStatus(200)
            ->assertSee('مشتریان بدهکار')
            ->assertSee('تعداد مشتریان بدهکار');
    }

    public function test_debtor_list_shows_debtor_customers(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $debtor = $this->createDebtor(150000);
        $nonDebtor = Customer::create([
            'first_name' => 'محمد',
            'last_name' => 'احمدی',
            'mobile' => '0935' . rand(1000000, 9999999),
            'is_active' => true,
        ]);

        Livewire::test(DebtorManager::class)
            ->assertSee($debtor->full_name)
            ->assertSee(number_format(150000))
            ->assertDontSee($nonDebtor->full_name);
    }

    public function test_search_filter_works(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $debtor = $this->createDebtor(50000);

        Livewire::test(DebtorManager::class)
            ->set('search', $debtor->mobile)
            ->assertSee($debtor->full_name)
            ->assertSee(number_format(50000));
    }

    public function test_stats_cards_show_correct_values(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $this->createDebtor(100000);
        $this->createDebtor(200000);

        Livewire::test(DebtorManager::class)
            ->assertSee(number_format(2))      // debtor count
            ->assertSee(number_format(300000)); // total debt
    }

    public function test_cash_payment_records_correctly(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $debtor = $this->createDebtor(100000);

        Livewire::test(DebtorManager::class)
            ->call('openPayModal', $debtor->id)
            ->assertSet('showPayModal', true)
            ->set('pay_method', 'cash')
            ->set('pay_amount', '50000')
            ->call('pay')
            ->assertSet('showPayModal', false);

        $this->assertDatabaseHas('customer_account_transactions', [
            'customer_id' => $debtor->id,
            'type' => 'payment',
            'amount' => 50000,
        ]);

        $this->assertDatabaseHas('payments', [
            'payment_type' => 'cash',
            'amount' => 50000,
        ]);
    }

    public function test_mixed_payment_records_correctly(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $debtor = $this->createDebtor(200000);

        Livewire::test(DebtorManager::class)
            ->call('openPayModal', $debtor->id)
            ->set('pay_method', 'mixed')
            ->set('pay_cash', '80000')
            ->set('pay_card', '120000')
            ->call('pay')
            ->assertSet('showPayModal', false);

        $this->assertDatabaseHas('customer_account_transactions', [
            'customer_id' => $debtor->id,
            'type' => 'payment',
            'amount' => 80000,
        ]);

        $this->assertDatabaseHas('customer_account_transactions', [
            'customer_id' => $debtor->id,
            'type' => 'payment',
            'amount' => 120000,
        ]);
    }

    public function test_payment_exceeding_debt_fails(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $debtor = $this->createDebtor(50000);

        Livewire::test(DebtorManager::class)
            ->call('openPayModal', $debtor->id)
            ->set('pay_method', 'cash')
            ->set('pay_amount', '60000')
            ->call('pay')
            ->assertHasErrors();
    }

    public function test_full_payment_removes_customer_from_debtor_list(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $debtor = $this->createDebtor(80000);

        Livewire::test(DebtorManager::class)
            ->assertSee($debtor->full_name)
            ->call('openPayModal', $debtor->id)
            ->set('pay_method', 'cash')
            ->set('pay_amount', '80000')
            ->call('pay');

        Livewire::test(DebtorManager::class)
            ->assertDontSee($debtor->full_name);
    }

    public function test_history_modal_shows_transactions(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $debtor = $this->createDebtor(100000);

        Livewire::test(DebtorManager::class)
            ->call('openHistory', $debtor->id)
            ->assertSet('showHistoryModal', true)
            ->assertSee('سوابق خرید و بدهی و پرداخت')
            ->assertSee('فروش نسیه')
            ->assertSee(number_format(100000));
    }
}
