<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('qr_identifier', 64)->unique()
                ->comment('شناسه یکتای QR شامل کد انبار و زمان');
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete()
                ->comment('محصول اصلی (اختیاری)');

            // نوع واحد: جعبه، کارتن، شل، پالت، کیسه، سایر
            $table->enum('unit_type', ['box', 'carton', 'shelf', 'pallet', 'bag', 'other'])
                ->default('box');
            $table->integer('quantity_in_unit')->default(1)
                ->comment('تعداد محصولات درون این واحد');

            // تاریخ‌ها
            $table->date('production_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->date('entry_date')->nullable()->default(now()->toDateString());

            // محل قرارگیری دقیق در انبار
            $table->string('location_section', 100)->nullable()
                ->comment('بخش: مثلاً سردخانه زیر صفر، بخش خشک');
            $table->string('location_shelf', 50)->nullable()
                ->comment('قفسه: مثلاً B یا ۲');
            $table->string('location_row', 50)->nullable()
                ->comment('ردیف: مثلاً ۲ یا ۱۰');
            $table->string('location_position', 50)->nullable()
                ->comment('جایگاه: مثلاً ۱۰ یا A-3');

            // بارکدهای محصولات درون این واحد (JSON)
            $table->json('products_barcode')->nullable()
                ->comment('لیست بارکدهای محصولات درون این واحد');

            // اطلاعات تکمیلی
            $table->text('notes')->nullable();

            // مسیر فایل تصویر QR
            $table->string('qr_image_path')->nullable();

            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamps();

            // ایندکس‌ها
            $table->index('warehouse_id');
            $table->index('expiration_date');
            $table->index('entry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_qr_codes');
    }
};