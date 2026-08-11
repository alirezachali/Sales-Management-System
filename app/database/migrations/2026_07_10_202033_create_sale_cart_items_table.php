<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sale_cart_items', function (Blueprint $table) {
            $table->id();
            
            $table
                ->foreignId('sale_cart_id')
                ->constrained()
                ->cascadeOnDelete();
                
            $table
                ->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();
                
            $table->decimal('quantity', 12, 3);
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_cart_items');
    }
};