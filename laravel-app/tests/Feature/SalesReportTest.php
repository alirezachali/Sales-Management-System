<?php

namespace Tests\Feature;

use App\Livewire\Reports\SalesReport;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'name' => 'مدیر',
            'username' => 'admin_' . uniqid(),
            'email' => uniqid() . '@example.com',
            'password' => 'password',
        ]);
    }

    private function createCustomer(): Customer
    {
        return Customer::create([
            'first_name' => 'علی',
            'last_name' => 'احمدی',
            'mobile' => '09' . random_int(10000000, 99999999),
        ]);
    }

    private function makeSale(User $user, Customer $customer, array $overrides = []): Sale
    {
        $createdAt = $overrides['created_at'] ?? null;
        unset($overrides['created_at']);

        $sale = Sale::create(array_merge([
            'invoice_number' => 'INV-' . uniqid(),
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'total_price' => 1000,
            'discount' => 100,
            'final_price' => 900,
            'payment_type' => 'cash',
        ], $overrides));

        if ($createdAt) {
            $sale->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();
        }

        return $sale;
    }

    public function test_renders_component(): void
    {
        $this->actingAs($this->createUser())
            ->get(route('reports.sales'))
            ->assertOk()
            ->assertSeeLivewire(SalesReport::class);
    }

    public function test_filters_sales_by_date_range_and_shows_totals(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer();

        $inside = $this->makeSale($user, $customer, [
            'invoice_number' => 'INV-INSIDE',
            'created_at' => now()->subDays(5),
        ]);

        $this->makeSale($user, $customer, [
            'invoice_number' => 'INV-OUTSIDE',
            'created_at' => now()->subDays(50),
        ]);

        $component = Livewire::test(SalesReport::class)
            ->set('dateFrom', now()->subDays(10)->toDateString())
            ->set('dateTo', now()->toDateString());

        $component->assertViewHas('sales', function ($sales) {
            return $sales->total() === 1 && $sales->first()->invoice_number === 'INV-INSIDE';
        })
            ->assertViewHas('totals', function ($totals) {
                return (int) $totals->count === 1
                    && (int) $totals->final_price === 900;
            });

        $component->assertSee('INV-INSIDE')->assertDontSee('INV-OUTSIDE');
    }

    public function test_filters_sales_by_payment_type(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer();

        $this->makeSale($user, $customer, [
            'invoice_number' => 'INV-CASH',
            'payment_type' => 'cash',
        ]);

        $this->makeSale($user, $customer, [
            'invoice_number' => 'INV-CREDIT',
            'payment_type' => 'credit',
        ]);

        Livewire::test(SalesReport::class)
            ->set('filterPaymentType', 'credit')
            ->assertViewHas('sales', function ($sales) {
                return $sales->total() === 1 && $sales->first()->invoice_number === 'INV-CREDIT';
            })
            ->assertSee('INV-CREDIT')
            ->assertDontSee('INV-CASH');
    }

    public function test_exports_csv(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer();

        $this->makeSale($user, $customer, ['invoice_number' => 'INV-CSV']);

        $response = Livewire::test(SalesReport::class)
            ->call('exportCsv')
            ->assertFileDownloaded();

        $content = base64_decode($response->effects['download']['content']);

        $this->assertStringContainsString('INV-CSV', $content);
        $this->assertStringContainsString('شماره فاکتور', $content);
    }

    public function test_exports_excel(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer();

        $this->makeSale($user, $customer, ['invoice_number' => 'INV-XLS']);

        $response = Livewire::test(SalesReport::class)
            ->call('exportExcel')
            ->assertFileDownloaded();

        $content = base64_decode($response->effects['download']['content']);

        $this->assertStringContainsString('INV-XLS', $content);
        $this->assertStringContainsString('شماره فاکتور', $content);
    }
}
