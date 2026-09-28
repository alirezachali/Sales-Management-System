<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * وضعیت و زمان‌بندی یک سرویس هشدار.
 *
 * هر رکورد به یک سرویس در config/alerts.php گره خورده است. محاسبه‌ی
 * «زمان اجرای بعدی» این‌جا انجام می‌شود تا زمان‌بند و صفحه‌ی تنظیمات
 * از یک منطق مشترک استفاده کنند.
 */
class AlertService extends Model
{
    protected $fillable = [
        'key',
        'enabled',
        'frequency',
        'run_at',
        'last_run_at',
        'next_run_at',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'last_run_at' => 'datetime',
            'next_run_at' => 'datetime',
        ];
    }

    /**
     * تعریف این سرویس از فایل کانفیگ (در زمان اجرا پر می‌شود و ذخیره نمی‌شود).
     *
     * @var array<string, mixed>|null
     */
    public ?array $definition = null;

    /** آیا زمان اجرای این سرویس رسیده است؟ */
    public function isDue(): bool
    {
        if (! $this->enabled) {
            return false;
        }

        return $this->next_run_at === null || $this->next_run_at->isPast();
    }

    /**
     * زمان اجرای بعدی بر اساس بازه‌ی انتخابی.
     * برای بازه‌ی «روزی یک‌بار»، ساعت run_at در نظر گرفته می‌شود.
     */
    public function computeNextRunAt(?Carbon $from = null): Carbon
    {
        $from ??= now();

        return match ($this->frequency) {
            'every_5_minutes' => $from->copy()->addMinutes(5),
            'every_15_minutes' => $from->copy()->addMinutes(15),
            'hourly' => $from->copy()->addHour(),
            'every_6_hours' => $from->copy()->addHours(6),
            'daily' => $this->nextDailyAt($from),
            default => $from->copy()->addHour(),
        };
    }

    /**
     * نزدیک‌ترین رخداد ساعتِ run_at (پیش‌فرض نیمه‌شب) در امروز یا فردا.
     */
    protected function nextDailyAt(Carbon $from): Carbon
    {
        [$hour, $minute] = $this->runAtParts();

        $candidate = $from->copy()->setTime($hour, $minute, 0);

        if ($candidate->lessThanOrEqualTo($from)) {
            $candidate->addDay();
        }

        return $candidate;
    }

    /**
     * ساعت اجرا به‌صورت [ساعت, دقیقه]؛ پیش‌فرض 00:00 (پایان شب).
     *
     * @return array{0:int, 1:int}
     */
    public function runAtParts(): array
    {
        $runAt = is_string($this->run_at) ? trim($this->run_at) : '';

        if (preg_match('/^(\d{1,2}):(\d{2})/', $runAt, $matches) !== 1) {
            return [0, 0];
        }

        return [
            min(23, max(0, (int) $matches[1])),
            min(59, max(0, (int) $matches[2])),
        ];
    }

    /**
     * ثبت اجرا: آخرین زمان اجرا و زمان اجرای بعدی به‌روز می‌شوند.
     * در زمان «صف‌گذاری» صدا زده می‌شود تا حتی اگر Job شکست بخورد،
     * زمان‌بند در حلقه‌ی اجرای پی‌درپی نیفتد.
     */
    public function markScheduled(): void
    {
        $this->forceFill([
            'last_run_at' => now(),
            'next_run_at' => $this->enabled ? $this->computeNextRunAt(now()) : null,
        ])->save();
    }
}
