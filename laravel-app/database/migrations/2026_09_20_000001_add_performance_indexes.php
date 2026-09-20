<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ایندکس‌های عملکردی برای کوئری‌های پرتکرار (poll داشبورد، زنگ هشدار و کارت‌ها).
 *
 * ستون‌های کلید خارجی (مثل sale_items.product_id، sales.user_id) در MySQL
 * به‌صورت خودکار ایندکس دارند؛ این‌جا فقط ستون‌هایی ایندکس می‌شوند که
 * ایندکس خودکار ندارند اما در کوئری‌های پراستعلام used هستند.
 */
return new class extends Migration {
    public function up(): void
    {
        // فروش: جستجوی بر اساس تاریخ و کاربر (داشبوردها + گزارش‌ها)
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasIndex('sales', 'sales_created_at_index')) {
                $table->index('created_at');
            }
            // ترکیبی برای داشبورد صندوقدار: فروش‌های یک کاربر در یک روز
            if (! Schema::hasIndex('sales', 'sales_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at']);
            }
        });

        // کالاها: موجودی کم (زنگ هشدار + داشبورد انباردار + لیست انبار)
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasIndex('products', 'products_stock_index')) {
                $table->index('stock');
            }
            if (! Schema::hasIndex('products', 'products_is_active_stock_index')) {
                $table->index(['is_active', 'stock']);
            }
        });

        // کارها: لیست کارهای هر کاربر (کارت کارها + داشبورد)
        Schema::table('todos', function (Blueprint $table) {
            if (! Schema::hasIndex('todos', 'todos_assigned_to_status_index')) {
                $table->index(['assigned_to', 'status']);
            }
        });

        // بدهی‌ها: هشدار سررسید + صفحه بدهی‌ها
        Schema::table('debts', function (Blueprint $table) {
            if (! Schema::hasIndex('debts', 'debts_status_due_date_index')) {
                $table->index(['status', 'due_date']);
            }
        });

        // مشتریان: تولد امروز (زنگ هشدار)
        Schema::table('customers', function (Blueprint $table) {
            if (! Schema::hasIndex('customers', 'customers_is_active_birth_date_index')) {
                $table->index(['is_active', 'birth_date']);
            }
        });

        // انتقالات انبار: در انتظار تأیید (زنگ هشدار + صفحه انتقالات)
        Schema::table('stock_transfers', function (Blueprint $table) {
            if (! Schema::hasIndex('stock_transfers', 'stock_transfers_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasIndex('sales', 'sales_created_at_index')) {
                $table->dropIndex(['created_at']);
            }
            if (Schema::hasIndex('sales', 'sales_user_id_created_at_index')) {
                $table->dropIndex(['user_id', 'created_at']);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasIndex('products', 'products_stock_index')) {
                $table->dropIndex(['stock']);
            }
            if (Schema::hasIndex('products', 'products_is_active_stock_index')) {
                $table->dropIndex(['is_active', 'stock']);
            }
        });

        Schema::table('todos', function (Blueprint $table) {
            if (Schema::hasIndex('todos', 'todos_assigned_to_status_index')) {
                $table->dropIndex(['assigned_to', 'status']);
            }
        });

        Schema::table('debts', function (Blueprint $table) {
            if (Schema::hasIndex('debts', 'debts_status_due_date_index')) {
                $table->dropIndex(['status', 'due_date']);
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasIndex('customers', 'customers_is_active_birth_date_index')) {
                $table->dropIndex(['is_active', 'birth_date']);
            }
        });

        Schema::table('stock_transfers', function (Blueprint $table) {
            if (Schema::hasIndex('stock_transfers', 'stock_transfers_status_created_at_index')) {
                $table->dropIndex(['status', 'created_at']);
            }
        });
    }
};
