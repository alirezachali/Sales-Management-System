<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile',
        'national_code',
        'job_title',
        'hired_at',
        'base_salary',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'hired_at' => 'date',
            'base_salary' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // (نام + نام خانوادگی = نام کامل) خروجی این متد نام کامل کارمند است
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => trim($this->first_name . ' ' . $this->last_name),
        );
    }

    // برای زمانی که کارمند تصویری ندارد، حرف اول نام و نام خانوادگی را برمی‌گرداند
    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn() => mb_substr($this->first_name, 0, 1) . mb_substr($this->last_name, 0, 1),
        );
    }

    // این متد لیست کارمندان فعال را برمی‌گرداند
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // این متد برای جستجوی کارمندان استفاده می‌شود
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%")
                ->orWhere('national_code', 'like', "%{$search}%")
                ->orWhere('job_title', 'like', "%{$search}%");
        });
    }

    // این متد وضعیت فعلی یک کارمند را برمی‌گرداند
    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_active ? 'فعال' : 'غیرفعال',
        );
    }

    // این متد رنگ قرمز یا سبز را نسبت به وضعیت کارمند برمی‌گرداند
    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_active ? 'success' : 'danger',
        );
    }

    // این متد سابقه‌ی کاری (تعداد سال) کارمند را بر اساس تاریخ استخدام برمی‌گرداند
    protected function yearsOfService(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->hired_at
                ? max(0, Carbon::parse($this->hired_at)->diffInYears(now()))
                : null,
        );
    }
}