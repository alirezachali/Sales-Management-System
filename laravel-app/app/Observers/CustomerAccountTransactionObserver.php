<?php

namespace App\Observers;

use App\Models\CustomerAccountTransaction;
use Illuminate\Support\Facades\Cache;

/**
 * کش لیست «مشتریان بدهکار» داشبورد صندوقدار را بعد از هر تراکنش باطل
 * می‌کند تا مانده‌ها همیشه تازه باشند.
 */
class CustomerAccountTransactionObserver
{
    public function saved(CustomerAccountTransaction $transaction): void
    {
        Cache::forget('cashier-debtors');
    }

    public function deleted(CustomerAccountTransaction $transaction): void
    {
        Cache::forget('cashier-debtors');
    }
}
