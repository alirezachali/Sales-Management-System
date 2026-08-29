<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    protected $fillable = [
        'user_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', self::PRIORITY_HIGH);
    }

    public function scopeDueSoon($query, int $days = 3)
    {
        return $query->whereDate('due_date', '<=', now()->addDays($days))
            ->where('status', '!=', self::STATUS_COMPLETED);
    }

    public function scopeMine($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function toggleComplete(): void
    {
        if ($this->isCompleted()) {
            $this->update([
                'status' => self::STATUS_PENDING,
                'completed_at' => null,
            ]);
        } else {
            $this->update([
                'status' => self::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
        }
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'در انتظار',
            self::STATUS_IN_PROGRESS => 'در حال انجام',
            self::STATUS_COMPLETED => 'تکمیل شده',
            default => $this->status,
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'کم',
            self::PRIORITY_MEDIUM => 'متوسط',
            self::PRIORITY_HIGH => 'زیاد',
            default => $this->priority,
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'secondary',
            self::PRIORITY_MEDIUM => 'warning',
            self::PRIORITY_HIGH => 'danger',
            default => 'secondary',
        };
    }
}