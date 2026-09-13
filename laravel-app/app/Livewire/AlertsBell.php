<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Debt;
use App\Models\Product;
use App\Models\StockTransfer;
use Hekmatinasser\Verta\Verta;
use Livewire\Component;

/**
 * زنگ هشدار هوشمند در نوار بالا.
 * موارد: کالاهای زیر حد سفارش، بدهی‌های نزدیک سررسید، تولدهای امروز،
 * و انتقالات بین انبار در انتظار تأیید.
 */
class AlertsBell extends Component
{
    public bool $open = false;

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function getAlertsProperty(): array
    {
        $alerts = [];

        // ۱) کالاهای زیر حد هشدار موجودی
        $threshold = (float) setting('Out_of_stock_alert', setting('stock_alert', 5));

        $lowStock = Product::where('is_active', true)
            ->where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->limit(6)
            ->get(['id', 'name', 'stock', 'unit']);

        if ($lowStock->isNotEmpty()) {
            $alerts[] = [
                'group' => 'موجودی کم',
                'icon' => 'bi-exclamation-octagon',
                'color' => 'danger',
                'items' => $lowStock->map(fn ($p) => [
                    'title' => $p->name,
                    'meta' => 'موجودی: '.number_format((float) $p->stock, 1).' '.$p->unit,
                    'route' => route('products.stock', $p->id),
                ])->all(),
            ];
        }

        // ۲) بدهی‌های نزدیک سررسید (۷ روز آینده یا گذشته)
        if (auth()->user()->hasPermission('debts.view')) {
            $soon = Debt::whereIn('status', ['unpaid', 'partial'])
                ->whereNotNull('due_date')
                ->whereBetween('due_date', [now()->subDays(30)->toDateString(), now()->addDays(7)->toDateString()])
                ->orderBy('due_date')
                ->limit(6)
                ->get(['id', 'title', 'creditor_name', 'due_date']);

            if ($soon->isNotEmpty()) {
                $alerts[] = [
                    'group' => 'سررسید بدهی',
                    'icon' => 'bi-calendar-x',
                    'color' => 'warning',
                    'items' => $soon->map(fn ($d) => [
                        'title' => $d->title.' — '.$d->creditor_name,
                        'meta' => 'سررسید: '.jalaliDate($d->due_date),
                        'route' => route('debts.index'),
                    ])->all(),
                ];
            }
        }

        // ۳) تولد مشتریان امروز (بر اساس تقویم شمسی)
        if (auth()->user()->hasPermission('customers.view')) {
            $nowMonth = (int) Verta::now()->format('n');
            $nowDay = (int) Verta::now()->format('j');

            $birthdays = Customer::query()
                ->whereNotNull('birth_date')
                ->where('is_active', true)
                ->limit(500)
                ->get(['id', 'first_name', 'last_name', 'birth_date'])
                ->filter(function (Customer $c) use ($nowMonth, $nowDay) {
                    try {
                        $v = Verta::instance($c->birth_date);

                        return (int) $v->format('n') === $nowMonth && (int) $v->format('j') === $nowDay;
                    } catch (\Throwable) {
                        return false;
                    }
                })
                ->take(6);

            if ($birthdays->isNotEmpty()) {
                $alerts[] = [
                    'group' => 'تولد امروز 🎉',
                    'icon' => 'bi-gift',
                    'color' => 'info',
                    'items' => $birthdays->map(fn ($c) => [
                        'title' => $c->full_name,
                        'meta' => 'برای تبریک و ارسال پیشنهاد مراجعه کنید',
                        'route' => route('customers.index'),
                    ])->all(),
                ];
            }
        }

        // ۴) انتقالات بین انبار در انتظار
        if (auth()->user()->hasPermission('transfers.view')) {
            $pending = StockTransfer::with(['fromWarehouse:id,name', 'toWarehouse:id,name'])
                ->where('status', 'pending')
                ->latest()
                ->limit(6)
                ->get();

            if ($pending->isNotEmpty()) {
                $alerts[] = [
                    'group' => 'انتقال در انتظار',
                    'icon' => 'bi-arrow-left-right',
                    'color' => 'primary',
                    'items' => $pending->map(fn ($t) => [
                        'title' => $t->reference,
                        'meta' => ($t->fromWarehouse?->name ?? '—').' ← '.($t->toWarehouse?->name ?? '—'),
                        'route' => route('transfers.index'),
                    ])->all(),
                ];
            }
        }

        return $alerts;
    }

    public function getCountProperty(): int
    {
        return collect($this->alerts)->sum(fn ($group) => count($group['items']));
    }

    public function render()
    {
        return view('livewire.alerts-bell');
    }
}
