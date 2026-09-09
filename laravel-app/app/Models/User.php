<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'avatar',
        'password',
        'role_id',
        'is_active',
        'last_login_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * آیا کاربر مجوز (permission) مشخصی دارد؟
     * مجوزها به‌ازای هر نقش در کش نگه‌داری می‌شوند تا برای هر درخواست
     * کوئری اضافه‌ای به دیتابیس زده نشود. کش هنگام ذخیره‌ی مجوزهای
     * نقش باطل می‌شود (RolePermissionManager و RoleController).
     */
    public function hasPermission(string $name): bool
    {
        if ($this->role?->name === 'super-admin') {
            return true;
        }

        if (! $this->role) {
            return false;
        }

        $permissions = cache()->rememberForever(
            "role-permissions-{$this->role_id}",
            fn () => $this->role->permissions()->pluck('name')->all()
        );

        return in_array($name, $permissions, true);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * آدرس کامل تصویر پروفایل کاربر؛ در صورت نبود تصویر، null برمی‌گرداند
     * تا در رابط کاربری آیکن پیش‌فرض نمایش داده شود.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        return null;
    }

    /**
     * آیا این کاربر هم‌اکنون در سیستم آنلاین است؟
     * وضعیت با میدل‌ور TrackUserOnline در کش نگه‌داری می‌شود و
     * چند دقیقه پس از آخرین درخواست کاربر، به‌صورت خودکار منقضی می‌شود.
     */
    public function isOnline(): bool
    {
        return Cache::has('user-online-' . $this->id);
    }
}