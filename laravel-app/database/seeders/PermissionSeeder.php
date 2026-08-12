<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'name' => 'داشبورد',
                'icon' => 'bi-speedometer2',
                'sort_order' => 1,
                'permissions' => [
                    [
                        'name' => 'dashboard.view',
                        'display_name' => 'مشاهده داشبورد',
                    ],
                ],
            ],
            [
                'name' => 'کاربران',
                'icon' => 'bi-people',
                'sort_order' => 2,
                'permissions' => [
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
                    [
                        'name' => 'users.activate',
                        'display_name' => 'فعال/غیرفعال کردن کاربر',
                    ],
                ],
            ],
            [
                'name' => 'نقش ها',
                'icon' => 'bi-shield-lock',
                'sort_order' => 3,
                'permissions' => [
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
                    [
                        'name' => 'roles.permissions',
                        'display_name' => 'ویرایش مجوزهای نقش',
                    ],
                ],
            ],
            [
                'name' => 'کالاها',
                'icon' => 'bi-box-seam',
                'sort_order' => 4,
                'permissions' => [
                    [
                        'name' => 'products.view',
                        'display_name' => 'مشاهده کالاها',
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
                    [
                        'name' => 'products.import',
                        'display_name' => 'ورود کالا از فایل',
                    ],
                    [
                        'name' => 'products.export',
                        'display_name' => 'خروجی کالاها',
                    ],
                    [
                        'name' => 'products.print_barcode',
                        'display_name' => 'چاپ بارکد کالا',
                    ],
                ],
            ],
            [
                'name' => 'دسته بندیها',
                'icon' => 'bi-diagram-3',
                'sort_order' => 5,
                'permissions' => [
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
            ],
            [
                'name' => 'فروش',
                'icon' => 'bi-cart-check',
                'sort_order' => 9,
                'permissions' => [
                    [
                        'name' => 'sales.view',
                        'display_name' => 'مشاهده فاکتورهای فروش',
                    ],
                    [
                        'name' => 'sales.create',
                        'display_name' => 'ثبت فاکتور فروش',
                    ],
                    [
                        'name' => 'sales.edit',
                        'display_name' => 'ویرایش فاکتور فروش',
                    ],
                    [
                        'name' => 'sales.cancel',
                        'display_name' => 'لغو فاکتور فروش',
                    ],
                    [
                        'name' => 'sales.return',
                        'display_name' => 'ثبت مرجوعی فروش',
                    ],
                    [
                        'name' => 'sales.discount',
                        'display_name' => 'ثبت تخفیف در فروش',
                    ],
                    [
                        'name' => 'sales.reprint',
                        'display_name' => 'چاپ مجدد فاکتورهای فروش',
                    ],
                ],
            ],
            [
                'name' => 'خرید',
                'icon' => 'bi-cart-plus',
                'sort_order' => 10,
                'permissions' => [
                    [
                        'name' => 'purchases.view',
                        'display_name' => 'مشاهده فاکتورهای خرید',
                    ],
                    [
                        'name' => 'purchases.create',
                        'display_name' => 'ایجاد فاکتور خرید',
                    ],
                    [
                        'name' => 'purchases.edit',
                        'display_name' => 'ویرایش فاکتور خرید',
                    ],
                    [
                        'name' => 'purchases.cancel',
                        'display_name' => 'حذف فاکتور خرید',
                    ],
                    [
                        'name' => 'purchases.return',
                        'display_name' => 'ثبت مرجوعی خرید',
                    ],
                ],
            ],
            [
                'name' => 'مشتریها',
                'icon' => 'bi-person-vcard',
                'sort_order' => 11,
                'permissions' => [
                    [
                        'name' => 'customers.view',
                        'display_name' => 'مشاهده لیست مشتریها',
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
                    [
                        'name' => 'customers.balance',
                        'display_name' => 'بالانس مشتری',
                    ],
                ],
            ],
            [
                'name' => 'تامین کنندگان',
                'icon' => 'bi-truck',
                'sort_order' => 12,
                'permissions' => [
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
                    [
                        'name' => 'suppliers.balance',
                        'display_name' => 'بالانس تامین کننده',
                    ],
                ],
            ],
            [
                'name' => 'انبار',
                'icon' => 'bi-boxes',
                'sort_order' => 8,
                'permissions' => [
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
                    [
                        'name' => 'stocks.history',
                        'display_name' => 'مشاهده سوابق انبار',
                    ],
                ],
            ],
            [
                'name' => 'تنظیمات',
                'icon' => 'bi-gear',
                'sort_order' => 19,
                'permissions' => [
                    [
                        'name' => 'settings.view',
                        'display_name' => 'مشاهده تنظیمات',
                    ],
                    [
                        'name' => 'settings.edit',
                        'display_name' => 'ویرایش تنظیمات',
                    ],
                    [
                        'name' => 'settings.backup',
                        'display_name' => 'تنظیم نسخه پشتیبان',
                    ],
                    [
                        'name' => 'settings.restore',
                        'display_name' => 'تنظیم برگرداندن نسخه پشتیبان',
                    ],
                    [
                        'name' => 'settings.system',
                        'display_name' => 'تنظیمات سیستم',
                    ],
                ],
            ],
            [
                'name' => 'گزارش گیری',
                'icon' => 'bi-bar-chart',
                'sort_order' => 17,
                'permissions' => [
                    [
                        'name' => 'reports.view',
                        'display_name' => 'مشاهده گزارشها',
                    ],
                    [
                        'name' => 'reports.sales',
                        'display_name' => 'گزارش فروش',
                    ],
                    [
                        'name' => 'reports.purchases',
                        'display_name' => 'گزارش خرید',
                    ],
                    [
                        'name' => 'reports.inventory',
                        'display_name' => 'گزارش موجودی',
                    ],
                    [
                        'name' => 'reports.customers',
                        'display_name' => 'گزارش مشتری',
                    ],
                    [
                        'name' => 'reports.suppliers',
                        'display_name' => 'گزارش تامین کننده',
                    ],
                    [
                        'name' => 'reports.financial',
                        'display_name' => 'گزارش مالی',
                    ],
                    [
                        'name' => 'reports.profit',
                        'display_name' => 'گزارش سود',
                    ],
                ],
            ],
            [
                'name' => 'چاپ',
                'icon' => 'bi-printer',
                'sort_order' => 18,
                'permissions' => [
                    [
                        'name' => 'printing.invoice',
                        'display_name' => 'چاپ فاکتور',
                    ],
                    [
                        'name' => 'printing.barcode',
                        'display_name' => 'چاپ بارکد',
                    ],
                    [
                        'name' => 'printing.labels',
                        'display_name' => 'چاپ لیبل',
                    ],
                ],
            ],
            [
                'name' => 'پشتیبان گیری',
                'icon' => 'bi-cloud-arrow-up',
                'sort_order' => 20,
                'permissions' => [
                    [
                        'name' => 'backup.create',
                        'display_name' => 'ساخت نسخه پشتیبان',
                    ],
                    [
                        'name' => 'backup.download',
                        'display_name' => 'دانلود نسخه پشتیبان',
                    ],
                    [
                        'name' => 'backup.restore',
                        'display_name' => 'برگرداندن نسخه پشتیبان',
                    ],
                ],
            ],
            [
                'name' => 'لاگ سیستم',
                'icon' => 'bi-journal-text',
                'sort_order' => 21,
                'permissions' => [
                    [
                        'name' => 'logs.view',
                        'display_name' => 'مشاهده لاگ سیستم',
                    ],
                    [
                        'name' => 'logs.clear',
                        'display_name' => 'پاک کردن لاگ سیستم',
                    ],
                ],
            ],
            [
                'name' => 'اعلان ها',
                'icon' => 'bi-bell',
                'sort_order' => 22,
                'permissions' => [
                    [
                        'name' => 'notifications.view',
                        'display_name' => 'مشاهده اعلان ها',
                    ],
                    [
                        'name' => 'notifications.send',
                        'display_name' => 'فرستادن اعلان',
                    ],
                ],
            ],
            [
                'name' => 'دستگاه ها',
                'icon' => 'bi-pc-display',
                'sort_order' => 23,
                'permissions' => [
                    [
                        'name' => 'devices.scanner',
                        'display_name' => 'دستگاه اسکنر',
                    ],
                    [
                        'name' => 'devices.printer',
                        'display_name' => 'دستگاه پرینتر',
                    ],
                    [
                        'name' => 'devices.scale',
                        'display_name' => 'مقیاس دستگاه',
                    ],
                ],
            ],
            [
                'name' => 'درآمدها',
                'icon' => 'bi-currency-dollar',
                'sort_order' => 16,
                'permissions' => [
                    [
                        'name' => 'incomes.view',
                        'display_name' => 'مشاهده لیست درآمدها',
                    ],
                    [
                        'name' => 'incomes.create',
                        'display_name' => 'ساخت درآمد',
                    ],
                    [
                        'name' => 'incomes.edit',
                        'display_name' => 'ویرایش درآمد',
                    ],
                    [
                        'name' => 'incomes.delete',
                        'display_name' => 'حذف درآمد',
                    ],
                ],
            ],
            [
                'name' => 'هزینه ها',
                'icon' => 'bi-wallet2',
                'sort_order' => 15,
                'permissions' => [
                    [
                        'name' => 'expenses.view',
                        'display_name' => 'مشاهده لیست هزینه ها',
                    ],
                    [
                        'name' => 'expenses.create',
                        'display_name' => 'ساخت هزینه',
                    ],
                    [
                        'name' => 'expenses.edit',
                        'display_name' => 'ویرایش هزینه',
                    ],
                    [
                        'name' => 'expenses.delete',
                        'display_name' => 'حذف هزینه',
                    ],
                ],
            ],
            [
                'name' => 'پرداخت ها',
                'icon' => 'bi-credit-card',
                'sort_order' => 14,
                'permissions' => [
                    [
                        'name' => 'payments.view',
                        'display_name' => 'مشاهده لیست پرداخت ها',
                    ],
                    [
                        'name' => 'payments.create',
                        'display_name' => 'ساخت پرداخت',
                    ],
                    [
                        'name' => 'payments.delete',
                        'display_name' => 'حذف پرداخت',
                    ],
                ],
            ],
            [
                'name' => 'واحدهای اندازه گیری',
                'icon' => 'bi-rulers',
                'sort_order' => 7,
                'permissions' => [
                    [
                        'name' => 'units.view',
                        'display_name' => 'مشاهده واحدهای اندازه گیری',
                    ],
                    [
                        'name' => 'units.create',
                        'display_name' => 'ساخت واحد اندازه گیری جدید',
                    ],
                    [
                        'name' => 'units.edit',
                        'display_name' => 'ویرایش واحد اندازه گیری',
                    ],
                    [
                        'name' => 'units.delete',
                        'display_name' => 'حذف واحد اندازه گیری',
                    ],
                ],
            ],
            [
                'name' => 'برندها',
                'icon' => 'bi-award',
                'sort_order' => 6,
                'permissions' => [
                    [
                        'name' => 'brands.view',
                        'display_name' => 'مشاهده لیست برندها',
                    ],
                    [
                        'name' => 'brands.create',
                        'display_name' => 'ساخت برند جدید',
                    ],
                    [
                        'name' => 'brands.edit',
                        'display_name' => 'ویرایش برند',
                    ],
                    [
                        'name' => 'brands.delete',
                        'display_name' => 'حذف برند',
                    ],
                ],
            ],
            [
                'name' => 'صندوق',
                'icon' => 'bi-cash-stack',
                'sort_order' => 13,
                'permissions' => [
                    [
                        'name' => 'cashbox.view',
                        'display_name' => 'مشاهده صندوق',
                    ],
                    [
                        'name' => 'cashbox.open',
                        'display_name' => 'باز کردن صندوق',
                    ],
                    [
                        'name' => 'cashbox.close',
                        'display_name' => 'بستن صندوق',
                    ],
                    [
                        'name' => 'cashbox.deposit',
                        'display_name' => 'سپرده گذاری در صندوق',
                    ],
                    [
                        'name' => 'cashbox.withdraw',
                        'display_name' => 'برداشت از صندوق',
                    ],
                ],
            ],
        ];

        foreach ($modules as $module) {
            $group = PermissionGroup::updateOrCreate(
                [
                    'name' => $module['name'],
                ],
                [
                    'icon' => $module['icon'],
                    'sort_order' => $module['sort_order'],
                ]
            );

            foreach ($module['permissions'] as $permission) {
                Permission::updateOrCreate(
                    [
                        'name' => $permission['name'],
                    ],
                    [
                        'permission_group_id' => $group->id,
                        'display_name' => $permission['display_name'],
                    ]
                );
            }
        }
    }
}