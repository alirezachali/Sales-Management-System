<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'name' => 'super-admin',
                'display_name' => 'مدیر کل',
                'description' => 'دسترسی کامل به تمام بخش‌های سیستم',
            ],
            [
                'name' => 'admin',
                'display_name' => 'مدیر',
                'description' => 'مدیریت بخش‌های عملیاتی فروشگاه',
            ],
            [
                'name' => 'cashier',
                'display_name' => 'صندوقدار',
                'description' => 'ثبت فروش و مدیریت صندوق',
            ],
            [
                'name' => 'warehouse',
                'display_name' => 'انباردار',
                'description' => 'مدیریت کالا و موجودی انبار',
            ],
            [
                'name' => 'accountant',
                'display_name' => 'حسابدار',
                'description' => 'مدیریت امور مالی و گزارش‌ها',
            ],
        ]);
    }
}
