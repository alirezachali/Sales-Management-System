<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    /** نام نقش مدیر کل که به تمام بخش‌ها دسترسی دارد */
    public const SUPER_ADMIN = 'super-admin';

    public const ADMIN = 'admin';

    public const CASHIER = 'cashier';

    public const WAREHOUSE = 'warehouse';

    public const ACCOUNTANT = 'accountant';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'color',
        'icon',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
}