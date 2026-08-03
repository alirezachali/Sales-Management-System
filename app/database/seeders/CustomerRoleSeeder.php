<?php

namespace Database\Seeders;

use App\Models\CustomerRole;
use Illuminate\Database\Seeder;

class CustomerRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [

            [
                'name' => 'مشتری جدید',
                'icon' => 'bi-person-plus',
                'color' => 'secondary',
                'sort_order' => 1,
                'discount_percent' => 0,
                'min_purchase_count' => 0,
                'min_purchase_amount' => 0,
                'is_default' => true,
            ],

            [
                'name' => 'مشتری عادی',
                'icon' => 'bi-person',
                'color' => 'primary',
                'sort_order' => 2,
                'discount_percent' => 0,
                'min_purchase_count' => 5,
                'min_purchase_amount' => 0,
                'is_default' => false,
            ],

            [
                'name' => 'مشتری وفادار',
                'icon' => 'bi-stars',
                'color' => 'success',
                'sort_order' => 3,
                'discount_percent' => 3,
                'min_purchase_count' => 20,
                'min_purchase_amount' => 0,
                'is_default' => false,
            ],

            [
                'name' => 'مشتری ویژه',
                'icon' => 'bi-award',
                'color' => 'warning',
                'sort_order' => 4,
                'discount_percent' => 5,
                'min_purchase_count' => 50,
                'min_purchase_amount' => 0,
                'is_default' => false,
            ],

            [
                'name' => 'مشتری VIP',
                'icon' => 'bi-gem',
                'color' => 'danger',
                'sort_order' => 5,
                'discount_percent' => 10,
                'min_purchase_count' => 100,
                'min_purchase_amount' => 0,
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