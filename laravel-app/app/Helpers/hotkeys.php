<?php

use App\Models\Setting;

if (! function_exists('hotkeyActions')) {

    /**
     * فهرست اقدام‌هایی که می‌توان برایشان کلید میانبر تعریف کرد.
     * کلید تنظیماتی هر اقدام به شکل hotkey_<id> در جدول settings ذخیره می‌شود.
     */
    function hotkeyActions(): array
    {
        return [
            'products_add' => [
                'label' => 'افزودن محصول',
                'page' => 'صفحه محصولات',
                'default' => 'F2',
                'description' => 'باز کردن مودال افزودن محصول جدید در صفحه محصولات',
            ],
            'products_search' => [
                'label' => 'جستجوی کالا',
                'page' => 'صفحه محصولات',
                'default' => 'Ctrl+Alt+F',
                'description' => 'برنده کردن کادر جستجو در صفحه محصولات',
            ],
            'form_save' => [
                'label' => 'ذخیره فرم مودال',
                'page' => 'همه فرم‌های مودال',
                'default' => 'Enter',
                'description' => 'فعال کردن دکمه ذخیره در مودال باز شده',
            ],
            'form_cancel' => [
                'label' => 'بستن / انصراف مودال',
                'page' => 'همه فرم‌های مودال',
                'default' => 'Esc',
                'description' => 'فعال کردن دکمه انصراف و بستن مودال باز شده',
            ],
            'customer_add' => [
                'label' => 'افزودن مشتری جدید',
                'page' => 'صفحه باشگاه مشتریان',
                'default' => 'F3',
                'description' => 'باز کردن مودال افزودن مشتری جدید در صفحه باشگاه مشتریان',
            ],
            'todo_add' => [
                'label' => 'افزودن کار جدید',
                'page' => 'صفحه لیست کارها',
                'default' => 'F4',
                'description' => 'باز کردن مودال افزودن کار جدید در صفحه لیست کارها',
            ],
        ];
    }

}

if (! function_exists('hotkeyDefaults')) {

    /**
     * مقادیر پیش‌فرض کلید میانبر هر اقدام به شکل hotkey_<id> => combo
     */
    function hotkeyDefaults(): array
    {
        $defaults = [];

        foreach (hotkeyActions() as $id => $action) {
            $defaults['hotkey_' . $id] = $action['default'];
        }

        return $defaults;
    }

}

if (! function_exists('hotkeyHint')) {

    /**
     * متن راهنمای کلید میانبر برای tooltip دکمه‌ها؛ در صورت ست نبودن کلید رشته خالی.
     */
    function hotkeyHint($action)
    {
        $combo = hotkeyData()[$action] ?? null;

        return $combo ? ' (کلید میانبر: ' . $combo . ')' : '';
    }

}

if (! function_exists('hotkeyData')) {

    /**
     * نقشه action_id => combo که در تنظیمات ذخیره شده (یا پیش‌فرض).
     * کلیدهای خالی حذف و ترکیب‌های تکراری رد می‌شوند.
     */
    function hotkeyData(): array
    {
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        $stored = Setting::pluck('value', 'key')->toArray();

        $result = [];
        $used = [];

        foreach (hotkeyActions() as $id => $action) {
            $key = 'hotkey_' . $id;
            $combo = trim((string) ($stored[$key] ?? $action['default']));

            if ($combo === '' || in_array(mb_strtolower($combo), $used, true)) {
                continue;
            }

            $used[] = mb_strtolower($combo);
            $result[$id] = $combo;
        }

        return $cache = $result;
    }

}
