<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // این متد لیست دسته‌بندی‌های فعال را برمی‌گرداند
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // تعریف رابطه میان این مدل و مدل هزینه‌ها
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'expense_category_id');
    }
}
