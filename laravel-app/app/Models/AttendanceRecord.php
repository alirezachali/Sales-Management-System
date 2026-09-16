<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'late_minutes',
        'overtime_hours',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'late_minutes' => 'decimal:1',
            'overtime_hours' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public static function statusLabels(): array
    {
        return [
            'present' => 'حاضر',
            'absent' => 'غایب',
            'leave' => 'مرخصی',
            'half' => 'نیمه‌وقت',
            'holiday' => 'تعطیل',
        ];
    }

    public function statusText(): string
    {
        return static::statusLabels()[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'present' => 'success',
            'absent' => 'danger',
            'leave' => 'warning',
            'half' => 'info',
            'holiday' => 'secondary',
            default => 'secondary',
        };
    }
}
