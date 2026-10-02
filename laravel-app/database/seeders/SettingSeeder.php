<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
             // تنظیمات اطلاعات فروشگاه
            'store_name' => '', // نام فروشگاه
            'manager_name' => '', // نام مدیر فروشگاه
            'phone' => '', // تلفن فروشگاه
            'mobile' => '', // موبایل مدیر
            'address' => '', // آدرس فروشگاه
            'website' => '', // آدرس سایت فروشگاه
            'email' => '', // ایمیل فروشگاه
             // تنظیمات فروش
            'invoice_prefix' => 'INV', // پیشوند شماره فاکتور
            'invoice_start' => '', // شروع شماره فاکتور از
            'invoice_digits' => '6', // تعداد ارقام شماره فاکتور
            'currency' => 'تومان', // واحد پول
            'price_decimal' => '0', // تعداد اعشار قیمت
            'tax_percent' => '0', // نرخ مالیات
            'default_discount' => '0', // تخفیف پیشفرض
            'stock_alert' => '5', // هشدار اتمام موجودی
            'max_invoice_items' => '100', // حداکثر تعداد آیتم در هر فاکتور
            'allow_negative_stock' => '0', // اجازه فروش با موجودی منفی
            'barcode_sound' => '1', // آیا هنگام اسکن بارکد صدا بخش شود؟
            'confirm_delete_invoice' => '1', // تایید قبل از حذف فاکتور
             // تنظیمات چاپ
            'paper_size' => '80', // سایز فاکتور برای چاپ
            'print_copies' => '1', // تعداد نسخه چاپ
            'auto_print' => '0', // چاپ خودکار
            'print_logo' => '1', // آیا لوگو فروشگاه در فاکتور چاپ شود؟
            'print_address' => '1', // آیا آدرس فروشگاه در فاکتور چاپ شود؟
            'print_phone' => '1', // آیا شماره تلفن فروشگاه در فاکتور چاپ شود؟
            'print_qrcode' => '', // چاپ کیوآر کد در فاکتور فروش
            'print_datetime' => '1', // چاپ تاریخ و ساعت در فاکتور
            'receipt_footer' => 'از خرید شما سپاسگزاریم', // متن پایین فاکتور فروش
             // تنظیمات چاپ بارکد و لیبل
            'barcode_prefix' => '200000', // پیشوند بارکد داخلی
            'barcode_length' => '12', // طول بارکد داخلی
            'barcode_type' => 'Code128', // نوع بارکد داخلی
            'label_width' => '', // عرض لیبل
            'label_height' => '',  // ارتفاع لیبل
            'label_default_quantity' => '', // تعداد چاپ پیشفرض
            'label_show_name' => '', // نمایش نام کالا در لیبل
            'label_show_price' => '', // نمایش قیمت کالا در لیبل
            'label_show_barcode' => '', // نمایش بارکد کالا در لیبل
            'label_show_code' => '', // نمایش شماره بارکد کالا در لیبل
            'label_show_unit' => '', // نمایش واحد کالا در لیبل
             // تنظیمات سیستم
            'system_language' => 'fa', // زبان سیستم
            'timezone' => 'Asia/Tehran', // منطقه زمانی
            'date_format' => 'Y/m/d', // فرمت تاریخ
            'system_log' => '1', // ثبت گزارش فعالیت کاربران
            'remember_login' => '1', // فعال بودن ورود خودکار
            'maintenance_mode' => '0', // حالت تعمیر و نگهداری
            'developer_mode' => '0', // حالت توسعه دهنده
            'enable_cache' => '1', // فعال بودن کش سیستم
            'check_update' => '1', // بررسی بروزرسانی هنگام اجرا
            'session_timeout' => '120', // مدت زمان انقضای نشست (دقیقه)
            'pagination_limit' => '15', // تعداد رکورد در هر صفحه
            'notify_sound' => '1', // پخش صدا در هنگام نمایش هشدارها
             // تنظیمات کلیدهای میانبر
            'hotkeys_enabled' => '1', // فعال بودن کلیدهای میانبر
             // تنظیمات پشتیبان گیری
            'backup_path' => 'C:\Users\Ali\Documents\Sales-Management-System\app\storage\app/backups', // مسیر ذخیره نسخه های پشتیبان
            'backup_keep' => '20', // تعداد نسخه قابل نگهداری
            'backup_format' => 'Zip', // فرمت فایل پشتیبان
            'auto_backup' => '1', // تهیه نسخه پشتیبان خودکار
            'backup_before_restore' => '1', // قبل از بازیابی نسخه پشتیبان تهیه شود
             // سیستم امتیازدهی
            'loyalty_amount_per_point' => '', // هر امتیاز به ازای خرید (تومان)
            'loyalty_point_value' => '', // ارزش هر امتیاز (تومان)
            'loyalty_enabled' => '', // فعالسازی سیستم امتیازدهی
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        // آدرس تصویر لوگو فروشگاه
        Setting::updateOrCreate(
            ['key' => 'store_logo'],
            ['value' => 'settings/logo.png']
        );
        // آدرس تصویر فاوآیکن فروشگاه
        Setting::updateOrCreate(
            ['key' => 'store_favicon'],
            ['value' => 'settings/favicon.ico']
        );
    }
}
