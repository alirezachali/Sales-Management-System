<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashboxes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->enum('type', ['cash', 'bank', 'wallet', 'other'])->default('cash');
            $table->string('account_number', 50)->nullable();
            $table->string('iban', 40)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        DB::table('cashboxes')->insert([
            'name' => 'صندوق نقدی اصلی',
            'type' => 'cash',
            'is_default' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::create('cashbox_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashbox_id')->constrained()->cascadeOnDelete();
            $table->foreignId('to_cashbox_id')->nullable()->constrained('cashboxes')->nullOnDelete();
            $table->enum('type', ['deposit', 'withdraw', 'transfer_in', 'transfer_out', 'sale', 'refund', 'expense', 'adjustment'])->default('deposit');
            $table->decimal('amount', 15, 2);
            $table->nullableMorphs('reference');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('type');
        });

        // اتصال فروش و هزینه‌ها به صندوق
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('cashbox_id')
                ->nullable()
                ->after('payment_type')
                ->constrained('cashboxes')
                ->nullOnDelete();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('cashbox_id')
                ->nullable()
                ->after('payment_method')
                ->constrained('cashboxes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['cashbox_id']);
            $table->dropColumn('cashbox_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['cashbox_id']);
            $table->dropColumn('cashbox_id');
        });

        Schema::dropIfExists('cashbox_transactions');
        Schema::dropIfExists('cashboxes');
    }
};
