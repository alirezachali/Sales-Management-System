<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'company_name',
        'contact_person',
        'type',
        'national_id',
        'economic_code',
        'registration_number',
        'mobile',
        'phone',
        'email',
        'website',
        'province',
        'city',
        'address',
        'postal_code',
        'credit_limit',
        'opening_balance',
        'bank_account_number',
        'iban',
        'payment_terms',
        'rating',
        'logo',
        'notes',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'credit_limit'    => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'rating'          => 'integer',
        'is_active'       => 'boolean',
    ];

    /**
     * تامین‌کننده‌ای که ثبت کرده (کاربر)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * برندهایی که این تامین‌کننده باهاشون همکاری می‌کنه (رابطه چند به چند)
     */
    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'brand_supplier');
    }
}