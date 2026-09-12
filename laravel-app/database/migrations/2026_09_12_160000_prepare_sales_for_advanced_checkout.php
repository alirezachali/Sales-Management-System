<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0);
            }

            if (! Schema::hasColumn('sales', 'change_amount')) {
                $table->decimal('change_amount', 12, 2)->default(0);
            }
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE sales
                MODIFY payment_type
                ENUM('cash', 'card', 'mixed', 'credit')
                NOT NULL
                DEFAULT 'cash'
            ");
        }
    }

    public function down(): void
    {
        //
    }
};
