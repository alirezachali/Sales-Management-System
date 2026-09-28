<?php

namespace App\Jobs\Alerts;

use App\Models\StockTransfer;

/**
 * انتقالات بین انبار که در انتظار تأیید هستند.
 */
class PendingTransferAlertJob extends AlertJob
{
    private const LIMIT = 6;

    public function key(): string
    {
        return 'pending_transfers';
    }

    protected function build(): array
    {
        return StockTransfer::query()
            ->with(['fromWarehouse:id,name', 'toWarehouse:id,name'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (StockTransfer $transfer) => [
                'title' => $transfer->reference,
                'meta' => ($transfer->fromWarehouse?->name ?? '—').' ← '.($transfer->toWarehouse?->name ?? '—'),
                'url' => route('transfers.index', absolute: false),
            ])
            ->all();
    }
}
