<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->unique()->after('name');
            $table->string('phone', 20)->nullable()->after('email');
            $table
                ->boolean('is_active')
                ->default(true)
                ->after('remember_token');

            $table
                ->timestamp('last_login_at')
                ->nullable()
                ->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'phone',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};