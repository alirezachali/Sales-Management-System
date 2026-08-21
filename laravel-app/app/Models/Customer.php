<?php

namespace App\Models;

use App\Models\CustomerAccountTransaction;
use App\Models\CustomerRole;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'mobile',
        'phone',
        'national_code',
        'birth_date',
        'gender',
        'province',
        'city',
        'postal_code',
        'address',
        'customer_role_id',
        'purchase_count',
        'total_purchase_amount',
        'last_purchase_at',
        'notes',
        'is_active',
    ];

    protected static function booted(): void
    {
        // هنگام ثبت مشتری جدید، اگر رده‌ای انتخاب نشده باشد، رده‌ی پیش‌فرض باشگاه
        // مشتریان به‌صورت خودکار به او اختصاص داده می‌شود.
        static::creating(function (Customer $customer) {
            if (! $customer->customer_role_id) {
                $defaultRole = CustomerRole::where('is_default', true)
                    ->where('is_active', true)
                    ->first();

                if ($defaultRole) {
                    $customer->customer_role_id = $defaultRole->id;
                }
            }
        });
    }

    // Start Accessor methhoods >>

    // (نام + نام خانوادگی = نام کامل) خروجی این متد نام کامل مشتری است
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => trim($this->first_name . ' ' . $this->last_name),
        );
    }

    // این متد بارای زمانی که مشتری تصویر پروفایلی ندارد جهت حرف اول نام و نام خانوادگی را برمیگرداند
    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn() => mb_substr($this->first_name, 0, 1) . mb_substr($this->last_name, 0, 1),
        );
    }

    // این متد لیستی مشتریان را فعال را برمیگرداند
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // این متد هم برای جستجوی مشتریان ها استفاده می‌شود
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%");
        });
    }

    // این متد آدرس کامل مشتری را با بربرگرداند
    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: function () {
                return collect([
                    $this->province,
                    $this->city,
                    $this->address,
                    $this->postal_code,
                ])
                    ->filter()
                    ->implode(' - ');
            }
        );
    }

    // این متد نام مشتری به همراه شماره تماس مشتری را با بربرگرداند
    protected function nameWithMobile(): Attribute
    {
        return Attribute::make(
            get: fn() =>
                $this->full_name
                . ' ('
                . $this->mobile
                . ')'
        );
    }

    // این متد وضعیت فعلی یک مشتری را با بربرگرداند
    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: fn() =>
                $this->is_active
                    ? 'فعال'
                    : 'غیرفعال'
        );
    }

    // این متد رنگ قرمز یا سبز را با نسبت به وضعیت مشتری با بربرگرداند
    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn() =>
                $this->is_active
                    ? 'success'
                    : 'danger'
        );
    }

    // این متد سن را با اساس تاریخ تولد محاسبه و برمیگرداند
    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn() =>
                $this->birth_date
                    ? Carbon::parse($this->birth_date)->age
                    : null
        );
    }

    // End Accessor methhoods //

    // این متد هر باره اگر آن روز روز تولد مشتری بود مقدار (درست) را برمیگرداند
    public function hasBirthdayToday(): bool
    {
        if (! $this->birth_date) {
            return false;
        }

        return Carbon::parse($this->birth_date)->isBirthday();
    }

    // تعریف رابطه میان این مدل و مدل نقش مشتری
    public function role()
    {
        return $this->belongsTo(CustomerRole::class, 'customer_role_id');
    }

    // تعریف رابطه میان این مدل و مدل فروش
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // تعریف رابطه میان این مدل و مدل تراکنش‌های حساب مشتری
    public function accountTransactions(): HasMany
    {
        return $this->hasMany(CustomerAccountTransaction::class);
    }

    /*
    |--------------------------------------------------------------------|
    |                     منطق باشگاه مشتریان (رده‌بندی)                  |
    |--------------------------------------------------------------------|
    */

    /**
     * بر اساس تعداد کل خریدها و مبلغ کل خریدهای این مشتری، مناسب‌ترین رده‌ی
     * فعال (بالاترین sort_order که شرایطش برقرار است) را پیدا کرده و در صورت
     * تفاوت با رده‌ی فعلی، آن را به‌روزرسانی می‌کند. اگر هیچ رده‌ای شرایطش را
     * نداشت، رده‌ی پیش‌فرض اعمال می‌شود.
     */
    public function recalculateRole(): void
    {
        $matchedRole = CustomerRole::query()
            ->where('is_active', true)
            ->where('min_purchase_count', '<=', $this->purchase_count)
            ->where('min_purchase_amount', '<=', $this->total_purchase_amount)
            ->orderByDesc('sort_order')
            ->first();

        if (! $matchedRole) {
            $matchedRole = CustomerRole::where('is_default', true)
                ->where('is_active', true)
                ->first();
        }

        if ($matchedRole && $matchedRole->id !== $this->customer_role_id) {
            $this->update(['customer_role_id' => $matchedRole->id]);
        }
    }

    /**
     * وقتی یک فروش برای این مشتری ثبت می‌شود، آمار خریدش را به‌روزرسانی و
     * رده‌ی باشگاه مشتریانش را بازمحاسبه می‌کند.
     */
    public function registerPurchase(float $amount): void
    {
        $this->increment('purchase_count');
        $this->increment('total_purchase_amount', $amount);
        $this->update(['last_purchase_at' => now()]);
        $this->refresh();
        $this->recalculateRole();
    }
}