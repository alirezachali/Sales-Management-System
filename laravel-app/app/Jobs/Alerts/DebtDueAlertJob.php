<?php

namespace App\Jobs\Alerts;

use App\Models\Debt;

/**
 * بدهی‌های نزدیک سررسید یا گذشته از سررسید.
 */
class DebtDueAlertJob extends AlertJob
{
    private const LIMIT = 6;

    public function key(): string
    {
        return 'debt_due';
    }

    protected function build(): array
    {
        return Debt::query()
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [
                now()->subDays(30)->toDateString(),
                now()->addDays(7)->toDateString(),
            ])
            ->orderBy('due_date')
            ->orderBy('id')
            ->limit(self::LIMIT)
            ->get(['id', 'title', 'creditor_name', 'due_date'])
            ->map(fn (Debt $debt) => [
                'title' => $debt->title.' — '.$debt->creditor_name,
                'meta' => 'سررسید: '.jalaliDate($debt->due_date),
                'url' => route('debts.index', absolute: false),
            ])
            ->all();
    }
}
