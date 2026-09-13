<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE stock_movements
                MODIFY type
                ENUM('initial','purchase','sale','return','adjust','transfer','count')
                NOT NULL
            ");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE stock_movements
                MODIFY type
                ENUM('initial','purchase','sale','return','adjust')
                NOT NULL
            ");
        }
    }
};
