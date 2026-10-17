<?php

namespace App\Support\Progress;

use Illuminate\Support\Facades\File;

/**
 * گزارش پیشرفت عملیات سنگین برای لودر سراسری.
 *
 * چرا فایل و نه کش؟ چون عملیات سنگین (مثل ساخت نسخه پشتیبان) داخل یک تراکنش
 * دیتابیس یا روی همان کانکشن اجرا می‌شود؛ اگر پیشرفت را در کش دیتابیسی یا
 * ردیس بنویسیم، ممکن است تا پایان تراکنش برای درخواست پولینگ دیده نشود.
 * فایل، از هر درخواستی و بلافاصله قابل خواندن است.
 *
 * توکن پیشرفت توسط مرورگر ساخته می‌شود و در هدر X-Progress-Token همان درخواست
 * می‌آید (به public/js/app-loader.js نگاه کنید). هر درخواستی که این هدر را
 * نداشته باشد، گزارشگر غیرفعال دریافت می‌کند و هیچ فایلی نوشته نمی‌شود.
 */
final class ProgressReporter
{
    /** هدر درخواستی که توکن پیشرفت را حمل می‌کند. */
    public const HEADER = 'X-Progress-Token';

    /** جایگزین هدر برای فرم‌های معمولی (بدون Livewire). */
    public const INPUT = 'progress_token';

    /** عمر فایل پیشرفت‌ها (ثانیه)؛ فایل‌های کهنه خودکار پاک می‌شوند. */
    private const TTL = 900;

    /** حداقل فاصله بین دو نوشتن (ثانیه) تا عملیات سنگین کند نشود. */
    private const MIN_INTERVAL = 0.08;

    private ?string $token = null;

    private ?int $total = null;

    private int $done = 0;

    private ?string $label = null;

    private string $state = 'running';

    private float $startedAt = 0.0;

    private float $lastWrite = 0.0;

    private int $lastWrittenDone = -1;

    private ?int $lastWrittenPercent = null;

    private function __construct(?string $token)
    {
        $this->token = $token;
    }

    /**
     * گزارشگر مخصوص همین درخواست؛ بدون توکن معتبر، غیرفعال است.
     *
     * عمداً کش نمی‌شود: در سرورهای چندکاره (Octane) و اجرای داخلی PHP، متغیر
     * استاتیک بین درخواست‌ها باقی می‌ماند و توکن درخواست بعدی را از دست می‌دهد.
     * ساختن نمونه‌ی تازه فقط یک خواندن هدر است.
     */
    public static function forRequest(): self
    {
        $token = null;

        if (app()->bound('request')) {
            $request = request();
            $token = $request->header(self::HEADER) ?: $request->input(self::INPUT);
        }

        return new self(self::normalize($token));
    }

    /**
     * گزارشگر با توکن مشخص.
     * برای اسکریپت‌های خط فرمان و آزمون‌ها استفاده می‌شود.
     */
    public static function forToken(?string $token): self
    {
        return new self(self::normalize($token));
    }

    public function active(): bool
    {
        return $this->token !== null;
    }

    public function token(): ?string
    {
        return $this->token;
    }

    /**
     * آغاز یک عملیات. اگر تعداد کل معلوم باشد، درصد دقیق محاسبه می‌شود.
     */
    public function begin(string $label, ?int $total = null): void
    {
        if (! $this->active()) {
            return;
        }

        $this->label = $label;
        $this->total = ($total !== null && $total > 0) ? $total : null;
        $this->done = 0;
        $this->state = 'running';
        $this->startedAt = microtime(true);
        $this->lastWrittenDone = -1;
        $this->lastWrittenPercent = null;

        self::sweep();

        $this->write(true);
    }

    /** تغییر متن مرحله بدون تغییر درصد (برای مراحل بدون شمارش دقیق). */
    public function stage(string $label): void
    {
        if (! $this->active()) {
            return;
        }

        $this->label = $label;
        $this->write(true);
    }

    /** ثبت پیشرفت؛ total و label اختیاری‌اند. */
    public function step(int $done, ?int $total = null, ?string $label = null): void
    {
        if (! $this->active()) {
            return;
        }

        if ($total !== null) {
            $this->total = $total > 0 ? $total : null;
        }

        if ($label !== null) {
            $this->label = $label;
        }

        $this->done = max($this->done, $done);
        $this->write();
    }

    /** یک قدم جلو رفتن (مثلاً هر ردیف). */
    public function advance(int $by = 1, ?string $label = null): void
    {
        $this->step($this->done + $by, null, $label);
    }

    /**
     * پایان کار: درصد روی ۱۰۰ قفل می‌شود.
     * فایل تا پایان عمر TTL می‌ماند تا مرورگر ۱۰۰٪ را ببیند و بعد پاک می‌شود.
     */
    public function finish(?string $label = null): void
    {
        if (! $this->active()) {
            return;
        }

        if ($label !== null) {
            $this->label = $label;
        }

        $this->state = 'done';
        $this->done = $this->total ?? $this->done;

        $this->write(true);
    }

    /**
     * نوشتن فوری وضعیت فعلی.
     * برای پایان یک حلقه لازم است؛ چون در آن لحظه ممکن است درصد تغییر نکند
     * و در نتیجه نوشتن معمولی (که بر اساس تغییر درصد محدود می‌شود) انجام نشود.
     */
    public function flush(): void
    {
        $this->write(true);
    }

    /**
     * پایان ناموفق: گزارش پاک می‌شود تا مرورگر درصد گمراه‌کننده نبیند
     * و به حالت نامعین برگردد.
     */
    public function abort(): void
    {
        self::forget($this->token);
    }

    /** کوئری پایه‌ی یک Builder یا Relation (برای شمارش ردیف‌ها). */
    private static function baseQuery(\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation $query): \Illuminate\Database\Query\Builder
    {
        return $query instanceof \Illuminate\Database\Eloquent\Relations\Relation
            ? $query->getBaseQuery()
            : $query->toBase();
    }

    /**
     * خواندن تکه‌تکه‌ی ردیف‌های یک کوئری همراه با گزارش درصد واقعی.
     *
     * تعداد کل با یک کوئری COUNT محاسبه می‌شود و بعد ردیف‌ها با lazy() خوانده
     * می‌شوند؛ بنابراین حافظه هم کمتر مصرف می‌شود. کوئری ورودی باید orderBy
     * داشته باشد (لازمه‌ی lazy)، که همه‌ی خروجی‌های گزارش دارند.
     */
    public function eachRow(
        \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation $query,
        int $chunk,
        callable $handle,
        string $label,
    ): void {
        if (! $this->active()) {
            foreach ($query->lazy($chunk) as $row) {
                $handle($row);
            }

            return;
        }

        // شمارش روی یک کپی انجام می‌شود تا ستون‌های کوئری اصلی (برای lazy) دست‌نخورده بماند
        $counter = clone $query;
        $total = (int) self::baseQuery($counter)->getCountForPagination();

        $this->begin($label, $total);

        $index = 0;

        foreach ($query->lazy($chunk) as $row) {
            $handle($row);
            $this->step(++$index);
        }

        // عدد نهایی (تعداد خوانده‌شده از کل) حتماً روی دیسک نوشته شود
        $this->flush();
    }

    /** خواندن آخرین وضعیت یک توکن (کنترلر پولینگ از این استفاده می‌کند). */
    public static function read(string $token): ?array
    {
        $token = self::normalize($token);

        if ($token === null) {
            return null;
        }

        $path = self::path($token);

        if (! File::exists($path)) {
            return null;
        }

        $raw = File::get($path);
        $data = json_decode($raw, true);

        return is_array($data) ? $data : null;
    }

    /** حذف فایل پیشرفت (مثلاً پس از خطا). */
    public static function forget(?string $token): void
    {
        $token = self::normalize($token);

        if ($token !== null && File::exists(self::path($token))) {
            File::delete(self::path($token));
        }
    }

    /** پوشه‌ی نگهداری فایل‌های پیشرفت. */
    public static function directory(): string
    {
        return storage_path('app/progress');
    }

    private function percent(): ?int
    {
        if ($this->state === 'done') {
            return 100;
        }

        if ($this->total === null || $this->total <= 0) {
            return null;
        }

        // تا وقتی تمام نشده، هرگز ۱۰۰٪ نشان نده
        return min(99, (int) floor($this->done * 100 / $this->total));
    }

    /**
     * نوشتن وضعیت روی دیسک.
     * نوشتن اتمیک است (فایل موقت + rename) تا خواننده هرگز فایل نصفه نبیند.
     */
    private function write(bool $force = false): void
    {
        if (! $this->active() || ! $this->shouldWrite($force)) {
            return;
        }

        $now = microtime(true);
        $payload = [
            'state' => $this->state,
            'label' => $this->label,
            'done' => $this->done,
            'total' => $this->total,
            'percent' => $this->percent(),
            'started_at' => $this->startedAt,
            'updated_at' => $now,
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            return;
        }

        $directory = self::directory();
        File::ensureDirectoryExists($directory);

        $path = self::path($this->token);
        $temp = $path . '.' . getmypid() . '.tmp';

        if (@file_put_contents($temp, $json, LOCK_EX) === false) {
            return;
        }

        @rename($temp, $path);

        $this->lastWrite = $now;
        $this->lastWrittenDone = $this->done;
        $this->lastWrittenPercent = $this->percent();
    }

    /**
     * محدودسازی نوشتن تا ابزارگذاری روی حلقه‌های بزرگ، عملیات را کند نکند:
     * تغییر درصد همیشه نوشته می‌شود (حداکثر ۱۰۰ نوشتن در هر عملیات)، و وقتی
     * درصدی وجود ندارد (مراحل بدون شمارش دقیق) نوشتن با فاصله‌ی زمانی انجام می‌شود.
     */
    private function shouldWrite(bool $force): bool
    {
        if ($force) {
            return true;
        }

        $percent = $this->percent();

        if ($percent !== null && $percent !== $this->lastWrittenPercent) {
            return true;
        }

        if ($this->done === $this->lastWrittenDone) {
            return false;
        }

        return (microtime(true) - $this->lastWrite) >= self::MIN_INTERVAL;
    }

    /** حذف فایل‌های کهنه تا پوشه بی‌نهایت رشد نکند. */
    private static function sweep(): void
    {
        $directory = self::directory();

        if (! File::isDirectory($directory)) {
            return;
        }

        $deadline = time() - self::TTL;

        foreach (File::files($directory) as $file) {
            if ($file->getMTime() < $deadline) {
                File::delete($file->getPathname());
            }
        }
    }

    /** فقط توکن‌های امن (بدون امکان مسیرگردی) پذیرفته می‌شوند. */
    private static function normalize(?string $token): ?string
    {
        if (! is_string($token)) {
            return null;
        }

        $token = trim($token);

        return preg_match('/^[A-Za-z0-9]{8,64}$/', $token) === 1 ? $token : null;
    }

    private static function path(string $token): string
    {
        return self::directory() . DIRECTORY_SEPARATOR . $token . '.json';
    }
}
