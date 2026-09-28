<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * وضعیت فعال/غیرفعال و زمان‌بندی هر سرویس هشدار.
 *
 * این جدول «منبع حقیقت» زمان‌بندی است: زمان‌بند با یک کوئری سبک روی
 * ایندکس next_run_at سرویس‌های سررسیدشده را پیدا و Job همان‌ها را در
 * صف می‌گذارد. تعریف خود سرویس‌ها (عنوان، آیکن، Job و مجوز) در
 * config/alerts.php نگهداری می‌شود.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_services', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->boolean('enabled')->default(true);
            $table->string('frequency', 30)->default('hourly');
            // ساعت اجرا برای بازه‌ی «روزی یک‌بار» (مثل 00:00 برای پایان شب)
            $table->string('run_at', 5)->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_services');
    }
};
