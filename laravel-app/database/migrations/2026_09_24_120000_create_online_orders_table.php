<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_orders', function (Blueprint $table) {
            $table->id();
            $table->string('idempotency_key')->unique();
            $table->unsignedBigInteger('shop_order_id')->nullable();
            $table->string('status', 32)->default('received');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 32)->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_orders');
    }
};
