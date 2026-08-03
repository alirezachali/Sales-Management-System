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
        Schema::create('customer_roles', function (Blueprint $table) {

            $table->id();

            $table->string('name',100);

            $table->string('icon',50)->nullable();

            $table->string('color',30)->default('secondary');

            $table->unsignedInteger('sort_order')->default(0);

            $table->decimal('discount_percent',5,2)->default(0);

            $table->unsignedInteger('min_purchase_count')->default(0);

            $table->decimal('min_purchase_amount',15,2)->default(0);

            $table->text('description')->nullable();

            $table->boolean('is_default')->default(false);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_roles');
    }
};
