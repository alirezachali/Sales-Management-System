<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function get(string $key, $default = null)
    {
        return Setting::where('key', $key)->value('value') ?? $default;
    }

    public function set(string $key, $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}