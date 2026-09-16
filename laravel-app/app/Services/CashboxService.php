<?php

namespace App\Services;

use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CashboxService
{
    /**
     * انواعی که موجودی صندوق را افزایش می‌دهند.
     */
    protected const INCREASE_TYPES = ['deposit', 'transfer_in', 'sale', 'refund'];

    /**
     * انواعی که موجودی صندوق را کاهش می‌دهند.
     */
    protected const DECREASE_TYPES = ['withdraw', 'transfer_out', 'expense'];

    /**
     * صندوق مناسب برای یک روش پرداخت (نقدی → صندوق نقدی، کارت → کارتخوان).
     */
    public function resolveCashboxFor(string $paymentType): ?Cashbox
    {
        $boxType = $paymentType === 'card' ? 'bank' : 'cash';

        return Cashbox::query()
            ->where('type', $boxType)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first()
            ?? Cashbox::active()->orderBy('id')->first();
    }

    /**
     * ثبت یک گردش و به‌روزرسانی موجودی صندوق.
     * برای adjustment می‌تواند مبلغ منفی باشد.
     */
    public function transaction(
        Cashbox|int $cashbox,
        string $type,
        float $amount,
        ?string $description = null,
        ?object $reference = null,
        ?int $userId = null,
    ): CashboxTransaction {
        $cashbox = is_int($cashbox) ? Cashbox::findOrFail($cashbox) : $cashbox;

        if ($type !== 'adjustment' && $amount <= 0) {
            throw new InvalidArgumentException('مبلغ تراکنش باید بزرگ‌تر از صفر باشد.');
        }

        return DB::transaction(function () use ($cashbox, $type, $amount, $description, $reference, $userId) {
            $delta = match (true) {
                $type === 'adjustment' => $amount,
                in_array($type, self::INCREASE_TYPES, true) => abs($amount),
                in_array($type, self::DECREASE_TYPES, true) => -abs($amount),
                default => throw new InvalidArgumentException('نوع تراکنش نامعتبر است.'),
            };

            if ($cashbox->balance + $delta < 0 && $type === 'withdraw') {
                throw new InvalidArgumentException('موجودی صندوق کافی نیست.');
            }

            $cashbox->increment('balance', $delta);

            return CashboxTransaction::create([
                'cashbox_id' => $cashbox->id,
                'type' => $type,
                'amount' => $amount,
                'reference_type' => $reference ? $reference->getMorphClass() : null,
                'reference_id' => $reference?->id,
                'description' => $description,
                'user_id' => $userId ?? auth()->id(),
            ]);
        });
    }

    /**
     * انتقال وجه بین دو صندوق.
     */
    public function transfer(
        Cashbox $from,
        Cashbox $to,
        float $amount,
        ?string $description = null,
        ?int $userId = null,
    ): void {
        if ($from->is($to)) {
            throw new InvalidArgumentException('صندوق مبدأ و مقصد نمی‌توانند یکی باشند.');
        }

        if ($amount <= 0) {
            throw new InvalidArgumentException('مبلغ انتقال باید بزرگ‌تر از صفر باشد.');
        }

        DB::transaction(function () use ($from, $to, $amount, $description, $userId) {
            $this->transaction($from, 'transfer_out', $amount, $description ?: 'انتقال به '.$to->name, userId: $userId);
            $this->transaction($to, 'transfer_in', $amount, 'انتقال از '.$from->name, userId: $userId);
        });
    }

    /**
     * ثبت گردش‌های فروش در صندوق‌ها (به ازای هر روش پرداخت).
     * وجهِ بدهی (change) وارد صندوق نمی‌شود چون به مشتری پس داده می‌شود.
     */
    public function recordSale(object $sale): void
    {
        $sale->loadMissing('payments');

        $change = (float) ($sale->change_amount ?? 0);

        foreach ($sale->payments as $payment) {
            $cashbox = $this->resolveCashboxFor($payment->payment_type);

            if (! $cashbox) {
                continue;
            }

            $amount = (float) $payment->amount;

            if ($payment->payment_type === 'cash' && $sale->payment_type === 'cash') {
                $amount -= $change;
            }

            if ($amount <= 0) {
                continue;
            }

            $this->transaction(
                cashbox: $cashbox,
                type: 'sale',
                amount: $amount,
                description: 'فروش فاکتور '.$sale->invoice_number,
                reference: $sale,
            );
        }
    }
}
