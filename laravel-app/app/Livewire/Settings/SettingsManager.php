<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class SettingsManager extends Component
{
    use WithFileUploads;

    /*
    |--------------------------------------------------------------------|
    |            تب فعال (روی کوئری‌استرینگ tab سینک می‌شود)             |
    |--------------------------------------------------------------------|
    */
    #[Url(as: 'tab', except: 'store')]
    public string $activeTab = 'store';

    /*
    |--------------------------------------------------------------------|
    |          همه‌ی تنظیمات به صورت key => value در این آرایه            |
    |--------------------------------------------------------------------|
    */
    public array $data = [];

    /*
    |--------------------------------------------------------------------|
    |                        فایل‌های آپلودی موقت                         |
    |--------------------------------------------------------------------|
    */
    public $store_logo = null;
    public $store_favicon = null;
    public $restoreFile = null;

    /*
    |--------------------------------------------------------------------|
    |     کلیدهایی که مقدارشان بولی (سوییچ روشن/خاموش) در نظر گرفته می‌شود |
    |--------------------------------------------------------------------|
    */
    protected array $booleanKeys = [
        // فروش
        'allow_negative_stock', 'auto_print_invoice', 'barcode_sound', 'confirm_delete_invoice',
        // چاپ
        'print_logo', 'print_address', 'print_phone', 'print_barcode', 'print_qrcode', 'print_datetime',
        // بارکد و لیبل
        'label_show_name', 'label_show_price', 'label_show_barcode', 'label_show_code', 'label_show_unit',
        // سیستم
        'system_log', 'remember_login', 'maintenance_mode', 'developer_mode', 'enable_cache', 'check_update',
        // پشتیبان‌گیری
        'auto_backup', 'backup_before_restore',
    ];

    /*
    |--------------------------------------------------------------------|
    |             لیست تب‌های مجاز (برای اعتبارسنجی مقدار tab)            |
    |--------------------------------------------------------------------|
    */
    protected function tabs(): array
    {
        return ['store', 'sales', 'print', 'barcode', 'system', 'backup'];
    }

    /*
    |--------------------------------------------------------------------|
    |          مقادیر پیش‌فرض هر کلید (وقتی در دیتابیس موجود نباشد)       |
    |--------------------------------------------------------------------|
    */
    protected function defaults(): array
    {
        return [
            // اطلاعات فروشگاه
            'store_name' => '',
            'manager_name' => '',
            'phone' => '',
            'mobile' => '',
            'email' => '',
            'website' => '',
            'address' => '',

            // فروش
            'invoice_prefix' => 'INV',
            'invoice_start' => 1,
            'invoice_digits' => 6,
            'currency' => 'تومان',
            'price_decimal' => 0,
            'tax_percent' => 0,
            'default_discount' => 0,
            'stock_alert' => 5,
            'max_invoice_items' => 100,
            'allow_negative_stock' => false,
            'auto_print_invoice' => false,
            'barcode_sound' => true,
            'confirm_delete_invoice' => true,

            // چاپ
            'paper_size' => '80',
            'print_copies' => 1,
            'auto_print' => '0',
            'print_logo' => true,
            'print_address' => true,
            'print_phone' => true,
            'print_barcode' => false,
            'print_qrcode' => false,
            'print_datetime' => true,
            'receipt_footer' => 'از خرید شما سپاسگزاریم',

            // بارکد و لیبل
            'barcode_prefix' => '200000',
            'barcode_length' => 12,
            'barcode_type' => 'CODE128',
            'label_width' => 50,
            'label_height' => 30,
            'label_default_quantity' => 1,
            'label_show_name' => true,
            'label_show_price' => true,
            'label_show_barcode' => true,
            'label_show_code' => true,
            'label_show_unit' => false,

            // سیستم
            'system_language' => 'fa',
            'timezone' => 'Asia/Tehran',
            'date_format' => 'Y/m/d',
            'system_log' => true,
            'remember_login' => true,
            'maintenance_mode' => false,
            'developer_mode' => false,
            'enable_cache' => true,
            'check_update' => true,
            'session_timeout' => 120,
            'pagination_limit' => 15,

            // پشتیبان‌گیری
            'backup_path' => '',
            'backup_keep' => 20,
            'backup_format' => 'zip',
            'auto_backup' => true,
            'backup_before_restore' => true,
        ];
    }

    public function mount(): void
    {
        $stored = Setting::pluck('value', 'key')->toArray();

        $data = [];
        foreach ($this->defaults() as $key => $default) {
            $value = array_key_exists($key, $stored) ? $stored[$key] : $default;

            if (in_array($key, $this->booleanKeys, true)) {
                $value = in_array((string) $value, ['1', 'true', 'on'], true);
            }

            $data[$key] = $value;
        }

        // مسیر پیش‌فرض پشتیبان‌گیری اگر تنظیم نشده باشد
        if (empty($data['backup_path'])) {
            $data['backup_path'] = storage_path('app/backups');
        }

        $this->data = $data;

        if (! in_array($this->activeTab, $this->tabs(), true)) {
            $this->activeTab = 'store';
        }
    }

    public function selectTab(string $tab): void
    {
        if (in_array($tab, $this->tabs(), true)) {
            $this->activeTab = $tab;
        }
    }

    /*
    |--------------------------------------------------------------------|
    |                            قوانین اعتبارسنجی                        |
    |--------------------------------------------------------------------|
    */
    protected function rules(): array
    {
        return [
            'data.store_name' => ['nullable', 'string', 'max:150'],
            'data.manager_name' => ['nullable', 'string', 'max:150'],
            'data.phone' => ['nullable', 'string', 'max:30'],
            'data.mobile' => ['nullable', 'string', 'max:30'],
            'data.email' => ['nullable', 'email', 'max:150'],
            'data.website' => ['nullable', 'string', 'max:150'],
            'data.address' => ['nullable', 'string', 'max:500'],

            'data.invoice_prefix' => ['nullable', 'string', 'max:10'],
            'data.invoice_start' => ['nullable', 'integer', 'min:0'],
            'data.invoice_digits' => ['nullable', 'integer', 'min:1', 'max:12'],
            'data.currency' => ['nullable', 'string', 'max:20'],
            'data.price_decimal' => ['nullable', 'integer', 'min:0', 'max:4'],
            'data.tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'data.default_discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'data.stock_alert' => ['nullable', 'integer', 'min:0'],
            'data.max_invoice_items' => ['nullable', 'integer', 'min:1'],

            'data.paper_size' => ['nullable', 'in:58,80'],
            'data.print_copies' => ['nullable', 'integer', 'min:1', 'max:10'],
            'data.receipt_footer' => ['nullable', 'string', 'max:500'],

            'data.barcode_prefix' => ['nullable', 'string', 'max:10'],
            'data.barcode_length' => ['nullable', 'integer', 'min:6', 'max:20'],
            'data.label_width' => ['nullable', 'integer', 'min:10', 'max:200'],
            'data.label_height' => ['nullable', 'integer', 'min:10', 'max:200'],
            'data.label_default_quantity' => ['nullable', 'integer', 'min:1', 'max:100'],

            'data.session_timeout' => ['nullable', 'integer', 'min:1'],
            'data.pagination_limit' => ['nullable', 'integer', 'min:1', 'max:200'],

            'data.backup_path' => ['nullable', 'string', 'max:255'],
            'data.backup_keep' => ['nullable', 'integer', 'min:1', 'max:365'],
            'data.backup_format' => ['nullable', 'in:zip,sql'],

            'store_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'store_favicon' => ['nullable', 'mimes:ico,png', 'max:512'],
        ];
    }

    protected function messages(): array
    {
        return [
            'data.email.email' => 'ایمیل وارد شده معتبر نیست.',
            'store_logo.image' => 'فایل لوگو باید یک تصویر باشد.',
            'store_logo.mimes' => 'فرمت مجاز لوگو: jpg, jpeg, png, webp',
            'store_logo.max' => 'حجم لوگو نباید بیشتر از ۲ مگابایت باشد.',
            'store_favicon.mimes' => 'فرمت مجاز فاوآیکن: ico, png',
            'store_favicon.max' => 'حجم فاوآیکن نباید بیشتر از ۵۱۲ کیلوبایت باشد.',
        ];
    }

    /*
    |--------------------------------------------------------------------|
    |                        ذخیره‌ی همه‌ی تنظیمات                        |
    |--------------------------------------------------------------------|
    */
    public function save(): void
    {
        // تبدیل رشته‌های خالی به null تا قوانینی مثل email/integer روی مقدار خالی
        // خطا ندهند (رفتار قبلی کنترلر کاملاً آزاد بود و هر مقداری را ذخیره می‌کرد).
        foreach ($this->data as $key => $value) {
            if (is_string($value) && trim($value) === '') {
                $this->data[$key] = null;
            }
        }

        $this->validate();

        foreach ($this->data as $key => $value) {
            if (in_array($key, $this->booleanKeys, true)) {
                $value = $value ? '1' : '0';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_null($value) ? '' : (string) $value]
            );
        }

        // لوگوی فروشگاه
        if ($this->store_logo) {
            $old = Setting::where('key', 'store_logo')->value('value');

            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $path = $this->store_logo->storeAs(
                'settings',
                'logo.' . $this->store_logo->getClientOriginalExtension(),
                'public'
            );

            Setting::updateOrCreate(['key' => 'store_logo'], ['value' => $path]);
        }

        // فاوآیکن فروشگاه
        if ($this->store_favicon) {
            $old = Setting::where('key', 'store_favicon')->value('value');

            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $path = $this->store_favicon->storeAs(
                'settings',
                'favicon.' . $this->store_favicon->getClientOriginalExtension(),
                'public'
            );

            Setting::updateOrCreate(['key' => 'store_favicon'], ['value' => $path]);
        }

        $this->reset(['store_logo', 'store_favicon']);

        session()->flash('success', 'تنظیمات با موفقیت ذخیره شد.');
    }

    /*
    |--------------------------------------------------------------------|
    |                    مسیر مؤثر ذخیره‌ی نسخه‌های پشتیبان               |
    |--------------------------------------------------------------------|
    */
    protected function backupDirectory(): string
    {
        $path = trim((string) ($this->data['backup_path'] ?? ''));

        if ($path === '') {
            $path = storage_path('app/backups');
        }

        try {
            File::ensureDirectoryExists($path);
        } catch (\Throwable $e) {
            // اگر مسیر تنظیم‌شده قابل ساخت نبود، به مسیر پیش‌فرض برمی‌گردیم
            $path = storage_path('app/backups');
            File::ensureDirectoryExists($path);
        }

        return $path;
    }

    /*
    |--------------------------------------------------------------------|
    |             ساخت خروجی SQL از کل دیتابیس (بدون ابزار خارجی)        |
    |--------------------------------------------------------------------|
    */
    protected function generateSqlDump(): string
    {
        $pdo = DB::getPdo();

        $sql = "-- Backup generated at " . now()->toDateTimeString() . "\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $tableRow) {
            $tableName = array_values((array) $tableRow)[0];

            $createRow = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createSql = array_values((array) $createRow[0])[1] ?? null;

            if (! $createSql) {
                continue;
            }

            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createSql . ";\n\n";

            $rows = DB::table($tableName)->get();

            foreach ($rows as $row) {
                $columns = array_keys((array) $row);
                $values = array_map(function ($value) use ($pdo) {
                    if (is_null($value)) {
                        return 'NULL';
                    }

                    return $pdo->quote((string) $value);
                }, array_values((array) $row));

                $columnList = '`' . implode('`, `', $columns) . '`';
                $valueList = implode(', ', $values);

                $sql .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES ({$valueList});\n";
            }

            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }

    /*
    |--------------------------------------------------------------------|
    |                      ساخت یک نسخه‌ی پشتیبان جدید                    |
    |--------------------------------------------------------------------|
    */
    public function createBackup(): void
    {
        try {
            $directory = $this->backupDirectory();
            $timestamp = now()->format('Ymd_His');
            $format = ($this->data['backup_format'] ?? 'zip') === 'sql' ? 'sql' : 'zip';

            $sqlDump = $this->generateSqlDump();

            if ($format === 'zip' && class_exists(\ZipArchive::class)) {
                $fileName = "backup_{$timestamp}.zip";
                $fullPath = $directory . DIRECTORY_SEPARATOR . $fileName;

                $zip = new \ZipArchive();
                $zip->open($fullPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
                $zip->addFromString("backup_{$timestamp}.sql", $sqlDump);
                $zip->close();
            } else {
                $fileName = "backup_{$timestamp}.sql";
                $fullPath = $directory . DIRECTORY_SEPARATOR . $fileName;
                File::put($fullPath, $sqlDump);
            }

            Setting::updateOrCreate(
                ['key' => 'last_backup'],
                ['value' => now()->toDateTimeString()]
            );

            $this->pruneOldBackups($directory);

            session()->flash('success', "نسخه‌ی پشتیبان با موفقیت ساخته شد: {$fileName}");
        } catch (\Throwable $e) {
            session()->flash('error', 'خطا در ساخت نسخه‌ی پشتیبان: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------|
    |          حذف نسخه‌های قدیمی بر اساس «تعداد قابل نگهداری»           |
    |--------------------------------------------------------------------|
    */
    protected function pruneOldBackups(string $directory): void
    {
        $keep = (int) ($this->data['backup_keep'] ?? 20);

        if ($keep < 1) {
            return;
        }

        $files = collect($this->backupFiles($directory))
            ->sortByDesc('timestamp')
            ->values();

        foreach ($files->slice($keep) as $file) {
            File::delete($directory . DIRECTORY_SEPARATOR . $file['name']);
        }
    }

    /*
    |--------------------------------------------------------------------|
    |                    فهرست فایل‌های پشتیبان موجود                     |
    |--------------------------------------------------------------------|
    */
    protected function backupFiles(?string $directory = null): array
    {
        $directory ??= $this->backupDirectory();

        if (! File::isDirectory($directory)) {
            return [];
        }

        $files = [];

        foreach (File::files($directory) as $file) {
            $extension = strtolower($file->getExtension());

            if (! in_array($extension, ['zip', 'sql'], true)) {
                continue;
            }

            $files[] = [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'timestamp' => $file->getMTime(),
                'date' => date('Y-m-d H:i:s', $file->getMTime()),
            ];
        }

        usort($files, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $files;
    }

    /*
    |--------------------------------------------------------------------|
    |                       دانلود یک نسخه‌ی پشتیبان                      |
    |--------------------------------------------------------------------|
    */
    public function downloadBackup(string $name)
    {
        $directory = $this->backupDirectory();
        $fullPath = $directory . DIRECTORY_SEPARATOR . basename($name);

        if (! File::exists($fullPath)) {
            session()->flash('error', 'فایل پشتیبان مورد نظر یافت نشد.');

            return null;
        }

        return response()->download($fullPath);
    }

    /*
    |--------------------------------------------------------------------|
    |                        حذف یک نسخه‌ی پشتیبان                        |
    |--------------------------------------------------------------------|
    */
    public function deleteBackup(string $name): void
    {
        $directory = $this->backupDirectory();
        $fullPath = $directory . DIRECTORY_SEPARATOR . basename($name);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
            session()->flash('success', 'نسخه‌ی پشتیبان حذف شد.');
        }
    }

    /*
    |--------------------------------------------------------------------|
    |                    بازیابی دیتابیس از فایل پشتیبان                  |
    |--------------------------------------------------------------------|
    */
    public function restoreBackup(): void
    {
        $this->validate([
            'restoreFile' => ['required', 'file', 'mimes:sql,zip,txt', 'max:51200'],
        ], [
            'restoreFile.required' => 'ابتدا فایل پشتیبان را انتخاب کنید.',
            'restoreFile.mimes' => 'فرمت مجاز فایل پشتیبان: sql یا zip',
            'restoreFile.max' => 'حجم فایل پشتیبان نباید بیشتر از ۵۰ مگابایت باشد.',
        ]);

        try {
            // در صورت فعال بودن گزینه، ابتدا از وضعیت فعلی نسخه‌ی پشتیبان بگیر
            if (! empty($this->data['backup_before_restore'])) {
                $this->createBackup();
            }

            $realPath = $this->restoreFile->getRealPath();
            $extension = strtolower($this->restoreFile->getClientOriginalExtension());
            $sql = null;

            if ($extension === 'zip' && class_exists(\ZipArchive::class)) {
                $zip = new \ZipArchive();

                if ($zip->open($realPath) === true) {
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $entry = $zip->getNameIndex($i);

                        if (str_ends_with(strtolower($entry), '.sql')) {
                            $sql = $zip->getFromIndex($i);
                            break;
                        }
                    }

                    $zip->close();
                }
            } else {
                $sql = File::get($realPath);
            }

            if (empty($sql)) {
                session()->flash('error', 'فایل پشتیبان معتبری برای بازیابی یافت نشد.');

                return;
            }

            DB::unprepared($sql);

            $this->reset('restoreFile');

            session()->flash('success', 'اطلاعات با موفقیت از نسخه‌ی پشتیبان بازیابی شد.');
        } catch (\Throwable $e) {
            session()->flash('error', 'خطا در بازیابی اطلاعات: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.settings.settings-manager', [
            'backups' => $this->backupFiles(),
            'lastBackup' => Setting::where('key', 'last_backup')->value('value'),
        ]);
    }
}
