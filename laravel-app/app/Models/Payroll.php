<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'base_salary',
        'overtime_amount',
        'bonus',
        'allowances',
        'deductions',
        'insurance',
        'tax',
        'net_pay',
        'workdays',
        'absent_days',
        'leave_days',
        'status',
        'payment_method',
        'cashbox_id',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'base_salary' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'bonus' => 'decimal:2',
            'allowances' => 'decimal:2',
            'deductions' => 'decimal:2',
            'insurance' => 'decimal:2',
            'tax' => 'decimal:2',
            'net_pay' => 'decimal:2',
            'workdays' => 'integer',
            'absent_days' => 'integer',
            'leave_days' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function cashbox(): BelongsTo
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function grossPay(): float
    {
        return (float) $this->base_salary
            + (float) $this->overtime_amount
            + (float) $this->bonus
            + (float) $this->allowances;
    }

    public function totalDeductions(): float
    {
        return (float) $this->deductions
            + (float) $this->insurance
            + (float) $this->tax;
    }

    public function recalculateNetPay(): float
    {
        $this->net_pay = max(0, $this->grossPay() - $this->totalDeductions());

        return (float) $this->net_pay;
    }

    public function periodLabel(): string
    {
        return $this->year.'/'.$this->month;
    }

    public function statusText(): string
    {
        return match ($this->status) {
            'draft' => 'پیش‌نویس',
            'approved' => 'تأیید شده',
            'paid' => 'پرداخت شده',
            default => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'approved' => 'info',
            'paid' => 'success',
            default => 'secondary',
        };
    }
}
