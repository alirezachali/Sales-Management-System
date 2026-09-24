<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('online_user_id')->nullable()->unique()->after('id');
            $table->timestamp('registered_online_at')->nullable()->after('last_purchase_at');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['online_user_id']);
            $table->dropColumn(['online_user_id', 'registered_online_at']);
        });
    }
};
