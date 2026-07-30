<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $settings = [

            'store_name'     => 'سوپرمارکت نمونه',

            'phone'          => '02112345678',

            'mobile'         => '09120000000',

            'address'        => 'تهران',

            'website'        => '',

            'invoice_number_prefix' => 'INV',

            'invoice_start_number' => '',

            'invoice_number_digits' => '6',

            'currency_unit' => 'ریال',

            'tax_rate' => '0',

            'max_item_per_invoice' => '100',

            'Out_of_stock_alert' => '5',

            'default_discount' => '0',

            'sell_without_inventory' => '0', // 0(false) or 1(true)

            'barcode_scann_sound' => '1', // 0(false) or 1(true)

            'auto_invoice_print' => '0', // 0(false) or 1(true)

            'confirm_delete_invoice' => '1', // 0(false) or 1(true)

            'paper_size' => '80',

            'store_logo_in_invoice' => '1', // 0(false) or 1(true)

            'store_address_in_invoice' => '1', // 0(false) or 1(true)

            'store_phone_in_invoice' => '1', // 0(false) or 1(true)

            'invoice_footer_text' => 'از خرید شما سپاسگزاریم',

            'qrcode_in_invoice'    => '1', // 0(false) or 1(true)

        ];

        foreach ($settings as $key => $value) {

            Setting::updateOrCreate(

                ['key' => $key],

                ['value' => $value]
 
            );

        }

        Setting::updateOrCreate(
            ['key' => 'store_logo'],
            ['value' => 'settings/logo.png']
        );

        Setting::updateOrCreate(
            ['key' => 'store_favicon'],
            ['value' => 'settings/favicon.ico']
        );

    }
}
