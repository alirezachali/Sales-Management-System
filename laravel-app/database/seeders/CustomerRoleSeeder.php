<?php

namespace Database\Seeders;

use App\Models\CustomerRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'مشتری جدید',
                'icon' => 'new_480px.png',
                'color' => 'secondary',
                'sort_order' => 1,
                'discount_percent' => 0,
                'min_purchase_count' => 0,
                'min_purchase_amount' => 0,
                'is_default' => true,
            ],
            [
                'name' => 'مشتری عادی',
                'icon' => 'user_480px.png',
                'color' => 'primary',
                'sort_order' => 2,
                'discount_percent' => 0,
                'min_purchase_count' => 5,
                'min_purchase_amount' => 5000000,
                'is_default' => false,
            ],
            [
                'name' => 'مشتری وفادار',
                'icon' => 'star_480px.png',
                'color' => 'success',
                'sort_order' => 3,
                'discount_percent' => 3,
                'min_purchase_count' => 20,
                'min_purchase_amount' => 20000000,
                'is_default' => false,
            ],
            [
                'name' => 'مشتری ویژه',
                'icon' => 'gold_medal_480px.png',
                'color' => 'warning',
                'sort_order' => 4,
                'discount_percent' => 5,
                'min_purchase_count' => 50,
                'min_purchase_amount' => 50000000,
                'is_default' => false,
            ],
            [
                'name' => 'مشتری VIP',
                'icon' => 'crown_480px.png',
                'color' => 'danger',
                'sort_order' => 5,
                'discount_percent' => 10,
                'min_purchase_count' => 100,
                'min_purchase_amount' => 100000000,
                'is_default' => false,
            ],
        ];

        foreach ($roles as $role) {
            CustomerRole::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}