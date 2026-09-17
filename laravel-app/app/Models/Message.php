<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * پیامی که مدیر برای یک کاربر یا همه کاربران می‌فرستد.
 * وضعیت خوانده‌شدن به‌ازای هر گیرنده در MessageRecipient ثبت می‌شود.
 */
class Message extends Model
{
    use HasFactory;

    /** ارسال به یک کاربر مشخص */
    public const AUDIENCE_SINGLE = 'single';

    /** ارسال گروهی به همه کاربران فعال (به‌جز فرستنده) */
    public const AUDIENCE_ALL = 'all';

    protected $fillable = [
        'sender_id',
        'subject',
        'body',
        'audience',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(MessageRecipient::class);
    }

    public function getAudienceLabelAttribute(): string
    {
        return match ($this->audience) {
            self::AUDIENCE_ALL => 'گروهی (همه کاربران)',
            default => 'شخصی',
        };
    }

    /** تعداد گیرندگان پیام */
    public function getRecipientsCountAttribute(): int
    {
        return $this->recipients->count();
    }

    /** تعداد گیرندگانی که پیام را خوانده‌اند */
    public function getReadCountAttribute(): int
    {
        return $this->recipients->whereNotNull('read_at')->count();
    }

    /** تعداد گیرندگانی که پیام را هنوز نخوانده‌اند */
    public function getUnreadCountAttribute(): int
    {
        return $this->recipients->whereNull('read_at')->count();
    }

    /** آیا همه‌ی گیرندگان پیام را خوانده‌اند؟ */
    public function getIsFullyReadAttribute(): bool
    {
        return $this->recipients->isNotEmpty() && $this->recipients->every(fn ($r) => $r->read_at !== null);
    }

    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('subject', 'like', "%{$term}%")
                ->orWhere('body', 'like', "%{$term}%");
        });
    }
}
