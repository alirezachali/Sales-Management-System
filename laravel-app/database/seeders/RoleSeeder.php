<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super-admin',
                'display_name' => 'مدیر کل',
                'description' => 'دسترسی کامل به تمام بخش‌های سیستم',
                'color' => 'danger',
                'icon' => 'trophy_480px.png',
            ],
            [
                'name' => 'admin',
                'display_name' => 'مدیر',
                'description' => 'مدیریت بخش‌های عملیاتی فروشگاه',
                'color' => 'warning',
                'icon' => 'gold_medal_480px.png',
            ],
            [
                'name' => 'cashier',
                'display_name' => 'صندوقدار',
                'description' => 'ثبت فروش و مدیریت صندوق',
                'color' => 'success',
                'icon' => 'cash_register_480px.png',
            ],
            [
                'name' => 'warehouse',
                'display_name' => 'انباردار',
                'description' => 'مدیریت کالا و موجودی انبار',
                'color' => 'primary',
                'icon' => 'box_480px.png',
            ],
            [
                'name' => 'accountant',
                'display_name' => 'حسابدار',
                'description' => 'مدیریت امور مالی و گزارش‌ها',
                'color' => 'info',
                'icon' => 'calculator_480px.png',
            ],
            [
                'name' => 'courier',
                'display_name' => 'پیک',
                'description' => 'رساندن خریدهای غیر حضوری به مشتری',
                'color' => 'warning',
                'icon' => 'motorcycle_480px.png',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}