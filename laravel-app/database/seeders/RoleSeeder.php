<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            [
                'name' => 'super-admin',
                'display_name' => 'مدیر کل',
                'description' => 'دسترسی کامل به تمام بخش‌های سیستم',
                'color' => 'danger',
                'icon' => 'bi bi-award-fill',
            ],
            [
                'name' => 'admin',
                'display_name' => 'مدیر',
                'description' => 'مدیریت بخش‌های عملیاتی فروشگاه',
                'color' => 'warning',
                'icon' => 'bi bi-award',
            ],
            [
                'name' => 'cashier',
                'display_name' => 'صندوقدار',
                'description' => 'ثبت فروش و مدیریت صندوق',
                'color' => 'success',
                'icon' => 'bi bi-cash-stack',
            ],
            [
                'name' => 'warehouse',
                'display_name' => 'انباردار',
                'description' => 'مدیریت کالا و موجودی انبار',
                'color' => 'primary',
                'icon' => 'bi bi-box-seam-fill',
            ],
            [
                'name' => 'accountant',
                'display_name' => 'حسابدار',
                'description' => 'مدیریت امور مالی و گزارش‌ها',
                'color' => 'info',
                'icon' => 'bi bi-calculator',
            ],
        ]);
    }
}