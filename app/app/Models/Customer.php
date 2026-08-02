<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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

    // Start Accessor methoods >>

    // (نام + نام خانوادگی = نام کامل) خروجی این متد نام کامل مشتری است
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->first_name . ' ' . $this->last_name),
        );
    }

    // این متد برای زمانی که مشتری تصویر پروفایل ندارد حروف اول نام و نام خانوادگی را برمیگرداند
    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn () => mb_substr($this->first_name,0,1). mb_substr($this->last_name,0,1),
        );
    }

    // این متد لیست مشتری های فعال را برمیگرداند
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // این متد هم برای جستجوی مشتری ها استفاده میشود
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {

            $q->where('first_name','like',"%{$search}%")
              ->orWhere('last_name','like',"%{$search}%")
              ->orWhere('mobile','like',"%{$search}%");
        });
    }

    // این متد آدرس کامل مشتری را برمیگرداند
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

    // این متد نام مشتری به همراه شماره تماس مشتری را برمیگرداند
    protected function nameWithMobile(): Attribute
    {
        return Attribute::make(

            get: fn () =>

                $this->full_name

                .' ('

                .$this->mobile

                .')'

        );
    }

    // این متد وضعیت فعلی یک مشتری را برمیگرداند
    protected function statusText(): Attribute
    {
        return Attribute::make(

            get: fn () =>

                $this->is_active

                    ? 'فعال'

                    : 'غیرفعال'

        );
    }

    // این متد رنگ قرمز یا سبز را نسبت به وضعیت مشتری برمیگرداند
    protected function statusColor(): Attribute
    {
        return Attribute::make(

            get: fn () =>

                $this->is_active

                    ? 'success'

                    : 'danger'

        );
    }

    // این متد سن مشتری را با استفاده از تاریخ تولد محاسبه و برمیگرداند 
    protected function age(): Attribute
    {
        return Attribute::make(

            get: fn () =>

                $this->birth_date

                    ? Carbon::parse($this->birth_date)->age

                    : null
        );
    }

    // End Accessor methoods //



    // این متد هر بار که اجرا میشود اگر آن روز تولد مشتری باشد مقدار (درست) را برمیگرداند
    public function hasBirthdayToday(): bool
    {
        if (!$this->birth_date) {

            return false;

        }

        return Carbon::parse($this->birth_date)->isBirthday();
    }

    
    // تعریف رابطه میان مدل مشتری با مدل نقش_مشتری
    public function role()
    {
        return $this->belongsTo(CustomerRole::class,'customer_role_id');
    }


}
