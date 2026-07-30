<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'نوشیدنی',
                'description' => '',
                'is_active' => '1'
            ],
            [
                'name' => 'تنقلات',
                'description' => '',
                'is_active' => '1'
            ],
            [
                'name' => 'لبنیات',
                'description' => '',
                'is_active' => '1'
            ],
            [
                'name' => 'خشکبار',
                'description' => '',
                'is_active' => '1'
            ],
            [
                'name' => 'مواد پروتئینی',
                'description' => '',
                'is_active' => '1'
            ],
            [
                'name' => 'مواد شوینده',
                'description' => '',
                'is_active' => '1'
            ],
            [
                'name' => 'بهداشتی و آرایشی',
                'description' => '',
                'is_active' => '1'
            ],
            [
                'name' => 'حبوبات',
                'description' => '',
                'is_active' => '1'
            ],
            [
                'name' => 'کنسرو و غذای آماده',
                'description' => '',
                'is_active' => '1'
            ]
            ]);
    }
}
