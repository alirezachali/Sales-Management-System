<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_category_id',
        'employee_id',
        'title',
        'amount',
        'expense_date',
        'payment_method',
        'description',
        'reference_number',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public const PAYMENT_METHODS = [
        'cash' => 'نقدی',
        'card' => 'کارت',
        'transfer' => 'کارت به کارت / حواله',
        'other' => 'سایر',
    ];

    // عنوان فارسی روش پرداخت را برمی‌گرداند
    protected function paymentMethodText(): Attribute
    {
        return Attribute::make(
            get: fn() => self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method,
        );
    }

    // تعریف رابطه میان این مدل و مدل دسته‌بندی هزینه
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    // تعریف رابطه میان این مدل و مدل کارمند (در صورت پرداخت حقوق و مزایا)
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // تعریف رابطه میان این مدل و مدل کاربر ثبت‌کننده
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    // فیلتر هزینه‌های مربوط به یک دوره زمانی مشخص
    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('expense_date', [$start, $end]);
    }
}