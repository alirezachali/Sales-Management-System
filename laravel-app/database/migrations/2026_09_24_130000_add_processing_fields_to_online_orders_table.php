<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('online_orders', function (Blueprint $table) {
            $table->foreignId('sale_id')->nullable()->after('shop_order_id')->constrained('sales')->nullOnDelete();
            $table->string('courier_name')->nullable()->after('address');
            $table->string('courier_phone', 32)->nullable()->after('courier_name');
            $table->text('reject_reason')->nullable();
            $table->timestamp('packed_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('online_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sale_id');
            $table->dropColumn([
                'courier_name',
                'courier_phone',
                'reject_reason',
                'packed_at',
                'dispatched_at',
                'delivered_at',
            ]);
        });
    }
};
