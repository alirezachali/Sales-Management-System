<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['supplier_id', 'brand_id']); // جلوگیری از رکورد تکراری
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_supplier');
    }
};
