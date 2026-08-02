<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {

            $table->id();

            // مشخصات

            $table->string('first_name',100);

            $table->string('last_name',100);

            $table->string('mobile',20)->unique();

            $table->string('phone',20)->nullable();

            $table->string('national_code',20)->nullable();

            $table->date('birth_date')->nullable();

            $table->enum('gender',['male','female'])->nullable();


            // آدرس

            $table->string('province',100)->nullable();

            $table->string('city',100)->nullable();

            $table->string('postal_code',20)->nullable();

            $table->text('address')->nullable();


            // باشگاه مشتریان

            $table->foreignId('customer_role_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->unsignedInteger('purchase_count')->default(0);

            $table->decimal('total_purchase_amount',15,2)->default(0);

            $table->timestamp('last_purchase_at')->nullable();


            // سایر

            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
