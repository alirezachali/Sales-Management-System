<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_account_transactions', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->string('type', 30);
            $table->decimal('amount', 15, 2);
            $table->nullableMorphs('reference');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_account_transactions');
    }
};