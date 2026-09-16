<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedInteger('points')->default(0)->after('total_purchase_amount');
            $table->unsignedInteger('spent_points')->default(0)->after('points');
        });

        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['earn', 'redeem', 'expire', 'adjust'])->default('earn');
            $table->integer('points');
            $table->integer('balance_after')->default(0);
            $table->nullableMorphs('reference');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // شماره تلفن همراه متولدین این ماه، برای تبریک تولد در داشبورد
        Schema::table('customer_roles', function (Blueprint $table) {
            $table->unsignedInteger('points_per_amount')->default(0)->after('discount_percent');
            $table->unsignedInteger('points_expiry_days')->default(0)->after('points_per_amount');
        });
    }

    public function down(): void
    {
        Schema::table('customer_roles', function (Blueprint $table) {
            $table->dropColumn(['points_per_amount', 'points_expiry_days']);
        });

        Schema::dropIfExists('point_transactions');

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['points', 'spent_points']);
        });
    }
};
