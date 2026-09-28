<?php

namespace App\Jobs\Alerts;

use App\Alerts\AlertStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * پایه‌ی همه‌ی Jobهای سرویس هشدار.
 *
 * هر سرویس هشدار یک Job مستقل است: محاسبه‌ی سبک خودش را انجام می‌دهد و
 * نتیجه را در AlertStore می‌نویسد. این ساختار باعث می‌شود هر سرویس جدا
 * در صف اجرا، retry و پایش شود و اضافه‌کردن سرویس جدید فقط یک کلاس
 * کوچک و یک ورودی در config/alerts.php باشد.
 */
abstract class AlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** تعداد تلاش‌ها در صورت خطا. */
    public int $tries = 3;

    /** حداکثر زمان اجرا (ثانیه). */
    public int $timeout = 60;

    /** کلید سرویس در config/alerts.php. */
    abstract public function key(): string;

    /**
     * ساخت آیتم‌های هشدار این سرویس.
     *
     * خروجی هر آیتم: ['title' => ..., 'meta' => ..., 'url' => ...].
     * آدرس‌ها به‌صورت نسبی ساخته می‌شوند (route(..., false)) تا مستقل از
     * هاست/پورت درخواست و APP_URL درست کار کنند.
     *
     * @return array<int, array{title:string, meta:string, url:string}>
     */
    abstract protected function build(): array;

    public function handle(AlertStore $store): void
    {
        $store->put($this->key(), $this->build());
    }
}
