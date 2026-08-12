<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('sale_id')
                ->constrained('sales')
                ->cascadeOnDelete();

            $table->string('payment_type', 30);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->index('payment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};