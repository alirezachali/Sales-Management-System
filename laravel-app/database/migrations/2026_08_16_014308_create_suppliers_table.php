<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            // هویتی و شرکتی
            $table->string('code', 30)->unique();               // کد یکتای تامین‌کننده
            $table->string('name', 100);                        // نام (شخص یا شرکت)
            $table->string('company_name', 150)->nullable();    // نام شرکت (اگر حقوقی)
            $table->string('contact_person', 100)->nullable();  // نام شخص رابط
            $table->enum('type', ['individual', 'company'])->default('individual'); // نوع: حقیقی/حقوقی
            $table->string('national_id', 20)->nullable();      // کد ملی
            $table->string('economic_code', 30)->nullable();    // کد اقتصادی
            $table->string('registration_number', 30)->nullable(); // شماره ثبت شرکت

            // تماس
            $table->string('mobile', 15)->unique();
            $table->string('phone', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 150)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code', 15)->nullable();

            // مالی و حسابداری
            $table->decimal('credit_limit', 15, 2)->default(0);     // سقف اعتباری
            $table->decimal('opening_balance', 15, 2)->default(0);  // مونده اولیه حساب
            $table->string('bank_account_number', 30)->nullable();
            $table->string('iban', 34)->nullable();                 // شماره شبا
            $table->string('payment_terms', 100)->nullable();       // شرایط پرداخت

            // عملیاتی
            $table->unsignedTinyInteger('rating')->nullable();      // امتیاز تامین‌کننده (۱ تا ۵)
            $table->string('logo')->nullable();                     // مسیر تصویر/لوگو

            // متادیتا
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};