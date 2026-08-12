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
            'store_name' => 'سوپرمارکت نمونه', // نام فروشگاه
            'phone' => '02112345678', // شماره موبایل فروشگاه
            'mobile' => '09120000000', // تلفن ثابت فروشگاه
            'address' => 'تهران', // آدرس فروشگاه
            'website' => '', // آدرس سایت فروشگاه
            'invoice_number_prefix' => 'INV', // پیشوند شماره فاکتور
            'invoice_start_number' => '', // شروع شماره فاکتور از
            'invoice_number_digits' => '6', // تعداد ارقام شماره فاکتور
            'currency' => 'تومان', // واحد پولی
            'tax_rate' => '0',
            'max_item_per_invoice' => '100', // حداکثر تعداد آیتم در هر فاکتور
            'Out_of_stock_alert' => '5', // حداقل موجودی کالا برای نمایش پیغام کمبود موجودی
            'default_discount' => '0',
            'sell_without_inventory' => '0', // آیا فروش کالا با موجودی صفر کجاز است؟
            'barcode_sound' => '1', // آیا هنگام اسکن بارکد صدا بخش شود؟
            'auto_print' => '0', // آیا فاکتور بعد از فروش خودکار چاپ شود؟
            'paper_size' => '80', // سایز فاکتور برای چاپ
            'print_logo' => '1', // آیا لوگو فروشگاه در فاکتور چاپ شود؟
            'print_address' => '1', // آیا آدرس فروشگاه در فاکتور چاپ شود؟
            'print_phone' => '1', // آیا شماره تلفن فروشگاه در فاکتور چاپ شود؟
            'print_barcode' => '',
            'print_datetime' => '1',
            'print_qrcode' => '',
            'receipt_footer' => 'از خرید شما سپاسگزاریم', // متن پایین فاکتور فروش
            'qrcode_in_invoice' => '1', // چاپ کیوآرکد در فاکتور 
            'system_language' => 'fa',
            'timezone' => 'Asia/Tehran',
            'date_format' => 'Y/m/d',
            'system_log' => '1',
            'remember_login' => '1',
            'maintenance_mode' => '0',
            'developer_mode' => '0',
            'enable_cache' => '1',
            'check_update' => '1',
            'session_timeout' => '120',
            'pagination_limit' => '15',
            'backup_path' => 'C:\Users\Ali\Documents\Sales-Management-System\app\storage\app/backups',
            'backup_keep' => '20',
            'backup_format' => 'Zip',
            'auto_backup' => '1',
            'backup_before_restore' => '1',
            'barcode_prefix' => '200000', // پیشوند بارکد داخلی
            'barcode_length' => '12', // طول بارکد داخلی
            'barcode_type' => 'Code128', // نوع بارکد داخلی 
            'allow_negative_stock' => '0',
            'price_decimal' => '0',
            '' => '',
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