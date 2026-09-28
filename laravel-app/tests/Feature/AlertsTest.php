<?php

namespace Tests\Feature;

use App\Alerts\AlertRegistry;
use App\Alerts\AlertStore;
use App\Jobs\Alerts\DebtDueAlertJob;
use App\Jobs\Alerts\LowStockAlertJob;
use App\Jobs\Alerts\PendingTransferAlertJob;
use App\Livewire\AlertsBell;
use App\Livewire\Settings\SettingsManager;
use App\Models\AlertService;
use App\Models\Debt;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class AlertsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * در AppServiceProvider برای هر مجوز دیتابیس یک Gate همنام ساخته می‌شود؛
     * چون در تست‌ها جدول مجوزها بعد از بوت برنامه پر می‌شود، همان کار
     * اینجا تکرار می‌شود تا میدل‌ور can: روی روت‌ها قابل آزمودن باشد.
     */
    private function registerPermissionGates(): void
    {
        foreach (Permission::pluck('name') as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }
    }

    private function makeRole(string $name, array $permissions = []): Role
    {
        $role = Role::create(['name' => $name, 'display_name' => $name]);
        $group = PermissionGroup::create(['name' => 'هشدارها '.$name]);

        foreach ($permissions as $permission) {
            $role->permissions()->attach(
                Permission::create([
                    'permission_group_id' => $group->id,
                    'name' => $permission,
                    'display_name' => $permission,
                ])->id
            );
        }

        $this->registerPermissionGates();

        return $role;
    }

    private function makeUser(string $username, Role $role): User
    {
        return User::create([
            'name' => 'کاربر '.$username,
            'username' => $username,
            'email' => $username.'@example.com',
            'phone' => '09120000000',
            'password' => 'secret-password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_registry_creates_a_row_for_each_configured_service(): void
    {
        app(AlertRegistry::class)->sync();

        $this->assertDatabaseCount('alert_services', count(config('alerts.services')));

        $service = AlertService::where('key', 'low_stock')->firstOrFail();

        $this->assertTrue($service->enabled);
        $this->assertSame('hourly', $service->frequency);
        $this->assertNotNull($service->next_run_at);
    }

    public function test_low_stock_job_stores_items_and_bell_displays_them(): void
    {
        config(['alerts.bootstrap_on_mount' => false]);

        $product = Product::create([
            'barcode' => '100000000001',
            'name' => 'شیر کم‌چرب',
            'sell_price' => 25000,
            'stock' => 1,
            'unit' => 'عدد',
            'is_active' => true,
        ]);

        (new LowStockAlertJob)->handle(app(AlertStore::class));

        $user = $this->makeUser('cashier1', $this->makeRole('cashier'));

        Livewire::actingAs($user)
            ->test(AlertsBell::class)
            ->assertSet('count', 1)
            ->assertSee($product->name);
    }

    public function test_bell_hides_groups_the_user_has_no_permission_for(): void
    {
        config(['alerts.bootstrap_on_mount' => false]);

        Debt::create([
            'title' => 'فاکتور تامین‌کننده',
            'creditor_name' => 'شرکت الف',
            'amount' => 1000000,
            'paid_amount' => 0,
            'due_date' => now()->addDays(2)->toDateString(),
            'status' => 'unpaid',
        ]);

        (new DebtDueAlertJob)->handle(app(AlertStore::class));

        $withoutPermission = $this->makeUser('cashier2', $this->makeRole('cashier-x'));
        $withPermission = $this->makeUser('accountant1', $this->makeRole('accountant', ['debts.view']));

        Livewire::actingAs($withoutPermission)
            ->test(AlertsBell::class)
            ->assertSet('count', 0)
            ->assertDontSee('سررسید بدهی‌ها');

        Livewire::actingAs($withPermission)
            ->test(AlertsBell::class)
            ->assertSet('count', 1)
            ->assertSee('سررسید بدهی‌ها');
    }

    public function test_refresh_action_rebuilds_alerts_immediately(): void
    {
        config(['alerts.bootstrap_on_mount' => false]);

        Product::create([
            'barcode' => '100000000002',
            'name' => 'ماست',
            'sell_price' => 30000,
            'stock' => 0,
            'unit' => 'عدد',
            'is_active' => true,
        ]);

        $user = $this->makeUser('cashier3', $this->makeRole('cashier-2'));

        Livewire::actingAs($user)
            ->test(AlertsBell::class)
            ->assertSet('count', 0)
            ->call('refreshAlerts')
            ->assertSet('count', 1)
            ->assertSee('ماست');
    }

    public function test_daily_schedule_respects_the_run_at_time(): void
    {
        $service = new AlertService([
            'enabled' => true,
            'frequency' => 'daily',
            'run_at' => '00:00',
        ]);

        $fromMorning = Carbon::parse('2026-09-29 10:30:00');
        $this->assertSame('2026-09-30 00:00:00', $service->computeNextRunAt($fromMorning)->toDateTimeString());

        $fromMidnight = Carbon::parse('2026-09-29 00:00:00');
        $this->assertSame('2026-09-30 00:00:00', $service->computeNextRunAt($fromMidnight)->toDateTimeString());
    }

    public function test_hourly_schedule_adds_one_hour(): void
    {
        $service = new AlertService(['enabled' => true, 'frequency' => 'hourly']);

        $from = Carbon::parse('2026-09-29 10:15:00');

        $this->assertSame('2026-09-29 11:15:00', $service->computeNextRunAt($from)->toDateTimeString());
    }

    public function test_dispatch_command_only_queues_due_services(): void
    {
        Queue::fake();

        $registry = app(AlertRegistry::class);
        $registry->sync();

        AlertService::where('key', 'low_stock')->update(['next_run_at' => now()->subMinute()]);
        AlertService::where('key', '!=', 'low_stock')->update(['next_run_at' => now()->addHour()]);

        $this->artisan('alerts:dispatch')->assertSuccessful();

        Queue::assertPushed(LowStockAlertJob::class, 1);
        Queue::assertNotPushed(PendingTransferAlertJob::class);

        // زمان اجرای بعدی سرویس صف‌گذاری‌شده جلو رفته است.
        $this->assertTrue(AlertService::where('key', 'low_stock')->first()->next_run_at->isFuture());
    }

    public function test_disabled_services_are_not_queued(): void
    {
        Queue::fake();

        $registry = app(AlertRegistry::class);
        $registry->sync();

        AlertService::query()->update(['enabled' => false, 'next_run_at' => now()->subMinute()]);

        $this->artisan('alerts:dispatch')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_settings_tab_can_toggle_and_schedule_a_service(): void
    {
        $admin = $this->makeUser('admin1', $this->makeRole('admin', ['settings.view']));

        Livewire::actingAs($admin)
            ->test(SettingsManager::class)
            ->call('selectTab', 'alerts')
            ->assertSet('activeTab', 'alerts')
            ->assertSee('سرویس‌های هشدار هوشمند')
            ->set('alertServices.low_stock.frequency', 'daily')
            ->set('alertServices.low_stock.run_at', '00:00')
            ->call('saveAlertService', 'low_stock')
            ->assertHasNoErrors();

        $service = AlertService::where('key', 'low_stock')->firstOrFail();

        $this->assertSame('daily', $service->frequency);
        $this->assertSame('00:00', $service->run_at);
        $this->assertTrue($service->enabled);
        $this->assertTrue($service->next_run_at->lessThanOrEqualTo(now()));
    }

    public function test_disabling_a_service_clears_its_cached_alerts(): void
    {
        app(AlertStore::class)->put('low_stock', [
            ['title' => 'کالای تستی', 'meta' => 'موجودی: ۰', 'url' => '/products'],
        ]);

        $admin = $this->makeUser('admin2', $this->makeRole('admin-x', ['settings.view']));

        Livewire::actingAs($admin)
            ->test(SettingsManager::class)
            ->call('selectTab', 'alerts')
            ->set('alertServices.low_stock.enabled', false)
            ->call('saveAlertService', 'low_stock');

        $this->assertFalse(AlertService::where('key', 'low_stock')->firstOrFail()->enabled);
        $this->assertNull(AlertService::where('key', 'low_stock')->firstOrFail()->next_run_at);
        $this->assertFalse(app(AlertStore::class)->has('low_stock'));
    }

    public function test_running_a_service_on_demand_populates_the_store(): void
    {
        config(['alerts.bootstrap_on_mount' => false]);

        Product::create([
            'barcode' => '100000000003',
            'name' => 'نان',
            'sell_price' => 10000,
            'stock' => 2,
            'unit' => 'عدد',
            'is_active' => true,
        ]);

        $admin = $this->makeUser('admin3', $this->makeRole('admin-y', ['settings.view', 'products.view']));

        Livewire::actingAs($admin)
            ->test(SettingsManager::class)
            ->call('selectTab', 'alerts')
            ->call('runAlertServiceNow', 'low_stock');

        $this->assertTrue(app(AlertStore::class)->has('low_stock'));
        $this->assertCount(1, app(AlertStore::class)->items('low_stock'));
        $this->assertNotNull(AlertService::where('key', 'low_stock')->firstOrFail()->last_run_at);
    }
}
