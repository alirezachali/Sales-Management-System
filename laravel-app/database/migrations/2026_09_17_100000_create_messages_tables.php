<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /* پیام‌های ارسالی مدیر؛ هر پیام یک رکورد دارد و گیرندگان آن
           در جدول message_recipients نگه‌داری می‌شوند تا وضعیت خوانده‌شدن
           هر گیرنده به‌صورت مستقل ثبت شود. */
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject', 255)->nullable();
            $table->text('body');
            /* single: ارسال به یک کاربر خاص | all: ارسال گروهی به همه کاربران فعال */
            $table->enum('audience', ['single', 'all'])->default('single');
            $table->timestamps();

            $table->index(['sender_id', 'created_at']);
        });

        Schema::create('message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['message_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_recipients');
        Schema::dropIfExists('messages');
    }
};
