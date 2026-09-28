<?php

namespace App\Livewire;

use App\Alerts\AlertDispatcher;
use App\Alerts\AlertRegistry;
use App\Alerts\AlertStore;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

/**
 * زنگ هشدار هوشمند در نوار پایین.
 *
 * محاسبه‌ی هشدارها به Jobهای مستقل سپرده شده است (app/Jobs/Alerts) و
 * نتیجه‌شان در AlertStore (کش) نگه‌داری می‌شود. این کامپوننت فقط کش را
 * می‌خواند و گروه‌های مجاز برای کاربر جاری را نمایش می‌دهد؛ بنابراین در
 * هر بارگذاری صفحه هیچ کوئری سنگینی اجرا نمی‌شود.
 */
class AlertsBell extends Component
{
    public bool $open = false;

    public function mount(AlertStore $store): void
    {
        if (! config('alerts.bootstrap_on_mount', true)) {
            return;
        }

        // اگر سرویسی هنوز نتیجه‌ای در کش ندارد (نصب تازه یا خاموش بودن
        // زمان‌بند)، یک‌بار در پس‌زمینه در صف قرار می‌گیرد. قفل کش مانع
        // از صف‌گذاری تکراری در بازدیدهای پشت‌سرهم می‌شود.
        foreach (app(AlertRegistry::class)->definitions() as $key => $definition) {
            if (! empty($definition['job']) && ! $store->has($key)) {
                if (Cache::add('alerts:bootstrapped:'.$key, 1, now()->addMinutes(5))) {
                    try {
                        $definition['job']::dispatch();
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }
        }
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    /** اجرای فوری همه‌ی سرویس‌های فعال (دکمه‌ی به‌روزرسانی). */
    public function refreshAlerts(AlertDispatcher $dispatcher): void
    {
        try {
            $dispatcher->runAll(sync: true);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * گروه‌های هشدار قابل نمایش برای کاربر جاری.
     *
     * @return array<int, array{group:string, icon:string, color:string, items:array}>
     */
    public function getAlertsProperty(): array
    {
        $user = auth()->user();

        if ($user === null) {
            return [];
        }

        $store = app(AlertStore::class);
        $alerts = [];

        foreach (app(AlertRegistry::class)->definitions() as $key => $definition) {
            $permission = $definition['permission'] ?? null;

            if ($permission !== null && ! $user->hasPermission($permission)) {
                continue;
            }

            $items = $store->items($key);

            if ($items === []) {
                continue;
            }

            $alerts[] = [
                'group' => $definition['label'] ?? $key,
                'icon' => $definition['icon'] ?? 'bi-bell',
                'color' => $definition['color'] ?? 'primary',
                'items' => $items,
            ];
        }

        return $alerts;
    }

    public function getCountProperty(): int
    {
        return collect($this->alerts)->sum(fn ($group) => count($group['items']));
    }

    /** تازه‌ترین زمان تولید هشدارها؛ برای نمایش «آخرین به‌روزرسانی». */
    public function getGeneratedAtProperty(): ?Carbon
    {
        $user = auth()->user();

        if ($user === null) {
            return null;
        }

        $store = app(AlertStore::class);
        $latest = null;

        foreach (app(AlertRegistry::class)->definitions() as $key => $definition) {
            $permission = $definition['permission'] ?? null;

            if ($permission !== null && ! $user->hasPermission($permission)) {
                continue;
            }

            $generatedAt = $store->generatedAt($key);

            if ($generatedAt !== null && ($latest === null || $generatedAt->greaterThan($latest))) {
                $latest = $generatedAt;
            }
        }

        return $latest;
    }

    public function render()
    {
        return view('livewire.alerts-bell');
    }
}
