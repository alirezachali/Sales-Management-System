<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\PermissionGroup;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [


            'کاربران' => [

    [
        'name' => 'users.view',
        'display_name' => 'مشاهده لیست کاربران',
    ],

    [
        'name' => 'users.create',
        'display_name' => 'ایجاد کاربر جدید',
    ],

    [
        'name' => 'users.edit',
        'display_name' => 'ویرایش کاربر',
    ],

    [
        'name' => 'users.delete',
        'display_name' => 'حذف کاربر',
    ],

],

            'کالاها' => [

    [
        'name' => 'products.view',
        'display_name' => 'مشاهده لیست کالاها',
    ],

    [
        'name' => 'products.create',
        'display_name' => 'ایجاد کالا',
    ],

    [
        'name' => 'products.edit',
        'display_name' => 'ویرایش کالا',
    ],

    [
        'name' => 'products.delete',
        'display_name' => 'حذف کالا',
    ],

],
            'نقشها' => [

    [
        'name' => 'roles.view',
        'display_name' => 'مشاهده لیست نقشها',
    ],

    [
        'name' => 'roles.create',
        'display_name' => 'ایجاد نقش جدید',
    ],

    [
        'name' => 'roles.edit',
        'display_name' => 'ویرایش نقش',
    ],

    [
        'name' => 'roles.delete',
        'display_name' => 'حذف نقش',
    ],

],
            'دسته بندیها' => [

    [
        'name' => 'categories.view',
        'display_name' => 'مشاهده لیست دسته بندیها',
    ],

    [
        'name' => 'categories.create',
        'display_name' => 'ایجاد دسته بندی جدید',
    ],

    [
        'name' => 'categories.edit',
        'display_name' => 'ویرایش دسته بندی',
    ],

    [
        'name' => 'categories.delete',
        'display_name' => 'حذف دسته بندی',
    ],

],
            'فروش' => [

    [
        'name' => 'sales.view',
        'display_name' => 'مشاهده فروش',
    ],

    [
        'name' => 'sales.create',
        'display_name' => 'ثبت فروش',
    ],

    [
        'name' => 'sales.return',
        'display_name' => 'ثبت مرجوعی فروش',
    ],

    [
        'name' => 'sales.cancel',
        'display_name' => 'لغو فاکتور فروش',
    ],

],
            'مشتریان' => [

    [
        'name' => 'customers.view',
        'display_name' => 'مشاهده لیست مشتریان',
    ],

    [
        'name' => 'customers.create',
        'display_name' => 'ایجاد مشتری جدید',
    ],

    [
        'name' => 'customers.edit',
        'display_name' => 'ویرایش مشتری',
    ],

    [
        'name' => 'customers.delete',
        'display_name' => 'حذف مشتری',
    ],

],
            'تامین کنندگان' => [

    [
        'name' => 'suppliers.view',
        'display_name' => 'مشاهده تامین کنندگان',
    ],

    [
        'name' => 'suppliers.create',
        'display_name' => 'ایجاد تامین کننده جدید',
    ],

    [
        'name' => 'suppliers.edit',
        'display_name' => 'ویرایش تامین کننده',
    ],

    [
        'name' => 'suppliers.delete',
        'display_name' => 'حذف تامین کننده',
    ],

],
            'انبار' => [

    [
        'name' => 'stocks.view',
        'display_name' => 'مشاهده موجودی انبار',
    ],

    [
        'name' => 'stocks.adjust',
        'display_name' => 'اصلاح موجودی انبار',
    ],

    [
        'name' => 'stocks.transfer',
        'display_name' => 'انتقال موجودی انبار',
    ],
],
            'داشبورد' => [

    [
        'name' => 'dashboard.view',
        'display_name' => 'مشاهده داشبورد',
    ],
],
            'تنظیمات' => [

    [
        'name' => 'settings.view',
        'display_name' => 'مشاهده تنظیمات',
    ],

    [
        'name' => 'settings.edit',
        'display_name' => 'ویرایش تنظیمات',
    ],
],
            'گزارشها' => [

    [
        'name' => 'reports.view',
        'display_name' => 'مشاهده گزارشها',
    ],
],            
        ];

        foreach ($modules as $groupName => $permissions) {

            $group = PermissionGroup::create([

                'name' => $groupName,

            ]);

            foreach ($permissions as $permission) {

                Permission::updateOrCreate(

                    [
                        'name' => $permission['name'],
                    ],

                    [
                        'permission_group_id' => $group->id,

                        'display_name' => $permission['display_name'],

                        'description' => null,
                    ]
                );

            }

        }
    }
}