<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ثبت قیمت تمام‌شده در لحظه‌ی فروش، برای محاسبه‌ی دقیق سود و زیان
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 2)->default(0)->after('unit_price');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->enum('status', ['completed', 'cancelled'])->default('completed')->after('change_amount');
            $table->text('cancel_reason')->nullable()->after('status');
        });

        // پر کردن قیمت تمام‌شده‌ی آیتم‌های قبلی از روی خرید فعلی محصول
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('
                UPDATE sale_items si
                INNER JOIN products p ON p.id = si.product_id
                SET si.cost_price = p.buy_price
                WHERE si.cost_price = 0
            ');
        } else {
            DB::table('sale_items')
                ->where('cost_price', 0)
                ->chunkById(200, function ($items) {
                    foreach ($items as $item) {
                        $buy = DB::table('products')->where('id', $item->product_id)->value('buy_price');
                        DB::table('sale_items')->where('id', $item->id)->update(['cost_price' => $buy ?? 0]);
                    }
                });
        }

        // بک‌فیل کردن فاکتورهای فروش قبلی به صندوق پیش‌فرض
        $cashboxId = DB::table('cashboxes')->where('is_default', true)->value('id');

        if ($cashboxId) {
            DB::table('sales')
                ->whereNull('cashbox_id')
                ->update(['cashbox_id' => $cashboxId]);
        }
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['status', 'cancel_reason']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });
    }
};
