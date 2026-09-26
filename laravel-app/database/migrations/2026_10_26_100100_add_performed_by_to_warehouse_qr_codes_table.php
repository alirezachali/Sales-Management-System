<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_qr_codes', function (Blueprint $table) {
            $table->foreignId('performed_by')
                ->nullable()
                ->after('created_by')
                ->constrained('users')
                ->nullOnDelete()
                ->comment('کاربری که عملیات (ورود/ساخت) را انجام داده');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_qr_codes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('performed_by');
        });
    }
};
