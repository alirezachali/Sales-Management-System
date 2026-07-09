<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number', 30)->unique();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->decimal('total_price', 12, 2);

            $table->decimal('discount', 12, 2)->default(0);

            $table->decimal('final_price', 12, 2);

            $table->enum('payment_type', [
                'cash',
                'card',
                'mixed'
            ])->default('cash');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
