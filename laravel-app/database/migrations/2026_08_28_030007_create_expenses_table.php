<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('expense_category_id')
                ->constrained('expense_categories')
                ->cascadeOnDelete();

            $table
                ->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->string('title');
            $table->decimal('amount', 15, 2);

            $table->date('expense_date');

            $table->string('payment_method', 20)->default('cash');

            $table->text('description')->nullable();

            $table->string('reference_number', 50)->nullable();

            $table
                ->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('expense_date');
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
