<?php

namespace Tests\Feature;

use App\Livewire\Products\StockManager;
use App\Livewire\Reports\SalesReport;
use App\Livewire\Settings\SettingsManager;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Role;
use App\Support\Progress\ProgressReporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * آزمون «پیشرفت واقعی» لودر:
 *   - مسیر بدون سشن خواندن درصد
 *   - موتور گزارش درصد روی کوئری‌های تکه‌تکه
 *   - اتصال کامپوننت‌های سنگین به گزارشگر (هدر X-Progress-Token)
 *
 * نکته: این آزمون‌ها روی دیتابیس sqlite در حافظه اجرا می‌شوند و به داده‌های
 * برنامه دست نمی‌زنند (به phpunit.xml نگاه کنید).
 */
class ProgressReportingTest extends TestCase
{
    use RefreshDatabase;

    private const HEADER = 'X-Progress-Token';

    private function makeUser(): User
    {
        $role = Role::create(['name' => 'progress-role', 'display_name' => 'مدیر تست']);

        return User::create([
            'name' => 'کاربر تست',
            'username' => 'progress-user',
            'email' => 'progress@example.com',
            'phone' => '09120000001',
            'password' => 'secret-password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    /**
     * توکن پیشرفت را مثل مرورگر روی درخواست جاری می‌گذارد
     * (همان کاری که app-loader.js با هدر X-Progress-Token می‌کند).
     */
    private function bindProgressToken(string $token): void
    {
        $request = Request::create('/livewire/update', 'POST');
        $request->headers->set(self::HEADER, $token);

        $this->app->instance('request', $request);
    }

    /**
     * توکن تازه و پاک؛ اگر اجرای قبلی نیمه‌کاره مانده باشد فایل کهنه حذف می‌شود.
     */
    private function freshToken(string $token): string
    {
        ProgressReporter::forget($token);

        return $token;
    }

    private function makeProduct(): Product
    {
        return Product::create([
            'barcode' => 'BC-PROGRESS',
            'name' => 'کالای پیشرفت',
            'buy_price' => 1000,
            'sell_price' => 2000,
            'stock' => 100,
            'unit' => 'عدد',
            'is_active' => true,
        ]);
    }

    /**
     * ردیف‌های گردش انبار را یک‌جا درج می‌کند (سریع‌تر از create در حلقه).
     */
    private function seedMovements(Product $product, int $count): void
    {
        $now = now();
        $rows = [];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'product_id' => $product->id,
                'type' => 'purchase',
                'quantity' => 1,
                'description' => 'رکورد '.$i,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            StockMovement::insert($chunk);
        }
    }

    /**
     * در حین اجرای عملیات، درصد گزارش‌شده را نمونه‌برداری می‌کند.
     * چون پیمایش تکه‌تکه برای هر تکه یک کوئری می‌زند، درصدها پله‌پله بالا می‌روند.
     *
     * @return array<int, int>
     */
    private function sampleProgressDuring(string $token, callable $operation): array
    {
        $samples = [];

        DB::listen(function () use ($token, &$samples) {
            $data = ProgressReporter::read($token);

            if (is_array($data) && is_int($data['percent'])) {
                $samples[] = $data['percent'];
            }
        });

        $operation();

        return $samples;
    }

    public function test_progress_endpoint_serves_reported_percent(): void
    {
        $token = $this->freshToken('endpointtest00000000000000000000');

        $progress = ProgressReporter::forToken($token);
        $progress->begin('در حال خواندن فاکتورهای فروش…', 10);
        $progress->step(4);

        $this->getJson('/api/app-progress/'.$token)
            ->assertOk()
            ->assertJsonPath('state', 'running')
            ->assertJsonPath('done', 4)
            ->assertJsonPath('total', 10)
            ->assertJsonPath('percent', 40)
            ->assertJsonPath('label', 'در حال خواندن فاکتورهای فروش…');

        // توکن نادرست باید ۴۰۴ بدهد (و راهی برای مسیرگردی نباشد)
        $this->getJson('/api/app-progress/short')->assertNotFound();

        ProgressReporter::forget($token);
    }

    public function test_reporter_abort_removes_progress_file(): void
    {
        $token = $this->freshToken('aborttest00000000000000000000000');

        $progress = ProgressReporter::forToken($token);
        $progress->begin('در حال کار…', 5);
        $progress->step(2);

        $this->assertNotNull(ProgressReporter::read($token));
        $this->assertSame(40, ProgressReporter::read($token)['percent']);

        $progress->abort();

        $this->assertNull(ProgressReporter::read($token));
    }

    public function test_each_row_reports_incremental_percent(): void
    {
        $token = $this->freshToken('eachrowtest000000000000000000000');
        $product = $this->makeProduct();

        $this->seedMovements($product, 620);

        $reporter = ProgressReporter::forToken($token);

        $samples = $this->sampleProgressDuring($token, function () use ($reporter) {
            $reporter->eachRow(
                StockMovement::query()->orderBy('id'),
                500,
                function () {
                    // کار per-row در این آزمون مهم نیست
                },
                'در حال خواندن گردش انبار…',
            );
        });

        $this->assertNotEmpty($samples, 'هیچ درصدی گزارش نشد.');
        $this->assertGreaterThan($samples[0], end($samples), 'درصد پیشرفت افزایش پیدا نکرد.');

        $sorted = $samples;
        sort($sorted);
        $this->assertSame($sorted, $samples, 'درصد پیشرفت باید همیشه صعودی باشد.');

        $final = ProgressReporter::read($token);
        $this->assertSame(620, $final['total'], 'تعداد کل باید تعداد واقعی ردیف‌ها باشد.');
        $this->assertSame(620, $final['done']);

        ProgressReporter::forget($token);
    }

    public function test_sales_report_export_reports_real_progress(): void
    {
        $token = $this->freshToken('salestest00000000000000000000000');
        $user = $this->makeUser();

        $now = now();
        $sales = [];

        for ($i = 1; $i <= 1100; $i++) {
            $sales[] = [
                'invoice_number' => 'INV-'.str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'total_price' => 1000,
                'discount' => 0,
                'final_price' => 1000,
                'payment_type' => 'cash',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($sales, 500) as $chunk) {
            Sale::insert($chunk);
        }

        $this->bindProgressToken($token);
        $this->actingAs($user);

        $component = new SalesReport;

        $samples = $this->sampleProgressDuring($token, function () use ($component) {
            $component->exportExcel();
        });

        $this->assertNotEmpty($samples, 'خروجی گزارش هیچ پیشرفتی گزارش نکرد.');
        $this->assertGreaterThan($samples[0], end($samples));

        $final = ProgressReporter::read($token);
        $this->assertSame(1100, $final['total']);
        $this->assertSame(1100, $final['done']);
        $this->assertSame('در حال خواندن فاکتورهای فروش…', $final['label']);

        ProgressReporter::forget($token);
    }

    public function test_product_stock_export_reports_progress_through_relation(): void
    {
        $token = $this->freshToken('stocktest00000000000000000000000');
        $user = $this->makeUser();
        $product = $this->makeProduct();

        $this->seedMovements($product, 620);

        $this->bindProgressToken($token);
        $this->actingAs($user);

        $component = new StockManager;
        $component->product = $product;

        $samples = $this->sampleProgressDuring($token, function () use ($component) {
            $component->exportExcel();
        });

        $this->assertNotEmpty($samples);
        $this->assertGreaterThan($samples[0], end($samples));

        $final = ProgressReporter::read($token);
        $this->assertSame(620, $final['total']);
        $this->assertSame(620, $final['done']);
        // خواندن ردیف‌ها تمام شده اما خود عملیات (نوشتن فایل خروجی) هنوز تمام نشده؛
        // پس درصد عمداً روی ۹۹ می‌ماند و در پایان درخواست، لودر پنهان می‌شود.
        $this->assertSame(99, $final['percent']);
        $this->assertSame('running', $final['state']);

        ProgressReporter::forget($token);
    }

    public function test_failed_backup_clears_progress_instead_of_leaving_fake_percent(): void
    {
        $token = $this->freshToken('backuptest0000000000000000000000');
        $user = $this->makeUser();

        $this->bindProgressToken($token);
        $this->actingAs($user);

        // روی sqlite دستور SHOW TABLES وجود ندارد، پس ساخت پشتیبان شکست می‌خورد
        (new SettingsManager)->createBackup();

        $this->assertNull(
            ProgressReporter::read($token),
            'پس از خطا، فایل پیشرفت باید پاک شود تا درصد گمراه‌کننده نماند.'
        );
    }
}
