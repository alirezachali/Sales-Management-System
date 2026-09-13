<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('code', 30)->unique();
            $table->string('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('manager_name', 120)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('product_warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 12, 3)->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'warehouse_id']);
        });

        // ساخت انبار پیش‌فرض و منتقل کردن موجودی فعلی محصولات به آن
        $defaultId = DB::table('warehouses')->insertGetId([
            'name' => 'انبار مرکزی',
            'code' => 'WH-01',
            'is_default' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $rows = DB::table('products')
            ->where('stock', '!=', 0)
            ->pluck('stock', 'id');

        $now = now();
        $payload = $rows->map(fn ($qty, $productId) => [
            'product_id' => $productId,
            'warehouse_id' => $defaultId,
            'quantity' => $qty,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        if ($payload) {
            DB::table('product_warehouse_stocks')->insert($payload);
        }

        // افزودن انبار به گردش کالا
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('warehouse_id')
                ->nullable()
                ->after('product_id')
                ->constrained('warehouses')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
        });

        Schema::dropIfExists('product_warehouse_stocks');
        Schema::dropIfExists('warehouses');
    }
};
