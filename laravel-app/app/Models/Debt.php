<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Debt extends Model
{
    protected $fillable = [
        'title',
        'creditor_name',
        'creditor_type',
        'supplier_id',
        'amount',
        'paid_amount',
        'due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'integer',
        'paid_amount' => 'integer',
    ];

    // Relations
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Accessors
    public function getRemainingAmountAttribute(): int
    {
        return $this->amount - $this->paid_amount;
    }

    public function getDueDateStatusAttribute(): string
    {
        if ($this->status === 'paid') return 'paid';
        if (!$this->due_date) return 'no-date';

        $today = Carbon::today();
        $due = Carbon::parse($this->due_date);

        if ($due->isPast()) return 'overdue';
        if ($due->diffInDays($today) <= 7) return 'warning';
        return 'normal';
    }

    // Scopes
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['unpaid', 'partial']);
    }

    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    // Auto update status on save
    protected static function booted(): void
    {
        static::saving(function (Debt $debt) {
            if ($debt->paid_amount >= $debt->amount) {
                $debt->status = 'paid';
            } elseif ($debt->paid_amount > 0) {
                $debt->status = 'partial';
            } else {
                $debt->status = 'unpaid';
            }
        });
    }
}