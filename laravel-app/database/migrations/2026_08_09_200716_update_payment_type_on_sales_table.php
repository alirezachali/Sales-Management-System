<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
        ALTER TABLE sales
        MODIFY payment_type
        ENUM('cash', 'card', 'mixed', 'credit')
        NOT NULL
        DEFAULT 'cash'
    ");
    }

    public function down(): void
    {
        DB::statement("
        ALTER TABLE sales
        MODIFY payment_type
        ENUM('cash', 'card', 'mixed')
        NOT NULL
        DEFAULT 'cash'
    ");
    }
};