<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * حذف تنظیم unused از جدول settings:
     * - default_discount: دیگر استفاده نمی‌شود (تخفیف دستی از UI صندوق حذف شد).
     *
     * بازگشت‌پذیر: تنظیم با اجرای مجدد seeder بازسازی نمی‌شود، ولی
     * `php artisan db:seed --class=SettingSeeder` آن را برنمی‌گرداند.
     */
    public function up(): void
    {
        DB::table('settings')->where('key', 'default_discount')->delete();
    }

    public function down(): void
    {
        DB::table('settings')->insert([
            'key' => 'default_discount',
            'value' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
