<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
            [
                'barcode' => '0552291362370',
                'name' => 'نوشابه پپسی 1.5لیتری',
                'category_id' => '7',
                'buy_price' => '90000',
                'sell_price' => '110000',
                'stock' => '12',
                'unit' => 'بطری',
                'is_active' => '1'
            ],
            [
                'barcode' => '0552291362371',
                'name' => 'نوشابه پپسی 300سی سی',
                'category_id' => '7',
                'buy_price' => '90000',
                'sell_price' => '110000',
                'stock' => '12',
                'unit' => 'بطری',
                'is_active' => '1'
            ],
            [
                'barcode' => '0552291362372',
                'name' => 'نوشابه میرندا 1.5لیتری',
                'category_id' => '7',
                'buy_price' => '90000',
                'sell_price' => '110000',
                'stock' => '12',
                'unit' => 'بطری',
                'is_active' => '1'
            ],
            [
                'barcode' => '0552291362373',
                'name' => 'نوشابه میرندا 300سی سی',
                'category_id' => '7',
                'buy_price' => '90000',
                'sell_price' => '110000',
                'stock' => '12',
                'unit' => 'بطری',
                'is_active' => '1'
            ],
            [
                'barcode' => '0552291362374',
                'name' => 'نوشابه شاه توت عالیس 1.5لیتری',
                'category_id' => '7',
                'buy_price' => '90000',
                'sell_price' => '110000',
                'stock' => '12',
                'unit' => 'بطری',
                'is_active' => '1'
            ],
            [
                'barcode' => '0552291362375',
                'name' => 'نوشابه شاه توت عالیس 300سی سی',
                'category_id' => '7',
                'buy_price' => '90000',
                'sell_price' => '110000',
                'stock' => '12',
                'unit' => 'بطری',
                'is_active' => '1'
            ],
            [
                'barcode' => '0552291362376',
                'name' => 'دوغ عالیس 1.5لیتری',
                'category_id' => '7',
                'buy_price' => '90000',
                'sell_price' => '110000',
                'stock' => '12',
                'unit' => 'بطری',
                'is_active' => '1'
            ]

        ]);
    }
}
