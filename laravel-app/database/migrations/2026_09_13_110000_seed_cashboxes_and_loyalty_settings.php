<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // صندوق کارتخوان (برای تفکیک واریزی کارت از نقدی)
        $exists = DB::table('cashboxes')->where('type', 'bank')->exists();

        if (! $exists) {
            DB::table('cashboxes')->insert([
                'name' => 'کارتخوان اصلی',
                'type' => 'bank',
                'is_default' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $settings = [
            'loyalty_enabled' => '1',
            'loyalty_amount_per_point' => '10000',
            'loyalty_point_value' => '100',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    public function down(): void
    {
        DB::table('cashboxes')->where('type', 'bank')->delete();
        Setting::whereIn('key', ['loyalty_enabled', 'loyalty_amount_per_point', 'loyalty_point_value'])->delete();
    }
};
