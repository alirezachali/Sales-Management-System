<?php

namespace App\Alerts;

use App\Models\AlertService;
use Illuminate\Support\Collection;

/**
 * پل میان تعریف سرویس‌های هشدار (config/alerts.php) و رکوردهای دیتابیس.
 *
 * - تعریف‌ها (عنوان، آیکن، Job، مجوز) در کانفیگ می‌مانند تا افزودن سرویس
 *   جدید نیاز به تغییر ساختار دیتابیس نداشته باشد.
 * - وضعیت فعال/غیرفعال و زمان‌بندی در جدول alert_services نگه‌داری می‌شود.
 */
class AlertRegistry
{
    /**
     * همه‌ی تعریف‌های سرویس از کانفیگ.
     *
     * @return array<string, array<string, mixed>>
     */
    public function definitions(): array
    {
        return (array) config('alerts.services', []);
    }

    /**
     * تعریف یک سرویس مشخص.
     *
     * @return array<string, mixed>|null
     */
    public function definition(string $key): ?array
    {
        $definitions = $this->definitions();

        return $definitions[$key] ?? null;
    }

    /** کلاس Job یک سرویس. */
    public function jobClass(string $key): ?string
    {
        $job = $this->definition($key)['job'] ?? null;

        return is_string($job) && class_exists($job) ? $job : null;
    }

    /**
     * اطمینان از وجود رکورد هر سرویس در دیتابیس و حذف سرویس‌های حذف‌شده
     * از کانفیگ. برای سرویس‌های تازه، اولین اجرا کمی بعد زمان‌بندی می‌شود.
     */
    public function sync(): void
    {
        $definitions = $this->definitions();

        // حذف رکورد سرویس‌هایی که از کانفیگ برداشته شده‌اند. اگر کانفیگ
        // خالی/بارگذاری‌نشده باشد، هیچ رکوردی حذف نمی‌شود.
        if ($definitions !== []) {
            AlertService::query()
                ->whereNotIn('key', array_keys($definitions))
                ->delete();
        }

        $existing = AlertService::query()->pluck('key')->all();

        foreach ($definitions as $key => $definition) {
            if (in_array($key, $existing, true)) {
                continue;
            }

            $defaults = $definition['defaults'] ?? [];

            AlertService::query()->create([
                'key' => $key,
                'enabled' => (bool) ($defaults['enabled'] ?? true),
                'frequency' => $defaults['frequency'] ?? 'hourly',
                'run_at' => $defaults['run_at'] ?? null,
                'next_run_at' => now()->addSeconds(30),
            ]);
        }
    }

    /**
     * همه‌ی سرویس‌ها به‌صورت مدل، به‌همراه تعریفشان (برای صفحه‌ی تنظیمات).
     *
     * @return Collection<int, AlertService>
     */
    public function all(): Collection
    {
        $this->sync();

        $definitions = $this->definitions();

        return AlertService::query()
            ->orderBy('id')
            ->get()
            ->each(fn (AlertService $service) => $service->definition = $definitions[$service->key] ?? null);
    }

    /** یک سرویس را برمی‌گرداند و در صورت نبود رکورد، می‌سازد. */
    public function ensure(string $key): ?AlertService
    {
        $definition = $this->definition($key);

        if ($definition === null) {
            return null;
        }

        $defaults = $definition['defaults'] ?? [];

        $service = AlertService::query()->firstOrCreate(
            ['key' => $key],
            [
                'enabled' => (bool) ($defaults['enabled'] ?? true),
                'frequency' => $defaults['frequency'] ?? 'hourly',
                'run_at' => $defaults['run_at'] ?? null,
                'next_run_at' => now()->addSeconds(30),
            ],
        );

        $service->definition = $definition;

        return $service;
    }

    /**
     * سرویس‌های فعالی که زمان اجرایشان رسیده است.
     *
     * @return Collection<int, AlertService>
     */
    public function due(): Collection
    {
        $definitions = $this->definitions();

        return AlertService::query()
            ->where('enabled', true)
            ->where(fn ($query) => $query
                ->whereNull('next_run_at')
                ->orWhere('next_run_at', '<=', now()))
            ->orderBy('id')
            ->get()
            ->filter(fn (AlertService $service) => isset($definitions[$service->key]))
            ->each(fn (AlertService $service) => $service->definition = $definitions[$service->key]);
    }

    /**
     * سرویس‌های فعال (بدون توجه به زمان اجرا).
     *
     * @return Collection<int, AlertService>
     */
    public function enabled(): Collection
    {
        $definitions = $this->definitions();

        return AlertService::query()
            ->where('enabled', true)
            ->orderBy('id')
            ->get()
            ->filter(fn (AlertService $service) => isset($definitions[$service->key]))
            ->each(fn (AlertService $service) => $service->definition = $definitions[$service->key]);
    }
}
