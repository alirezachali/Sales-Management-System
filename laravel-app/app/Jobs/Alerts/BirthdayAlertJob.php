<?php

namespace App\Jobs\Alerts;

use App\Models\Customer;
use Hekmatinasser\Verta\Verta;

/**
 * تولد مشتریان امروز (بر اساس تقویم شمسی).
 */
class BirthdayAlertJob extends AlertJob
{
    private const LIMIT = 6;

    public function key(): string
    {
        return 'birthdays';
    }

    protected function build(): array
    {
        $now = Verta::now();
        $nowMonth = (int) $now->format('n');
        $nowDay = (int) $now->format('j');
        $jalaliYear = (int) $now->format('Y');

        $daysInMonth = (int) Verta::createJalaliDate($jalaliYear, $nowMonth, 1)->daysInMonth;

        // فقط تولدهای ماه جاری شمسی از دیتابیس خوانده می‌شوند و سپس در PHP
        // فیلتر می‌شوند؛ به‌این‌ترتیب به‌جای لود کل مشتریان، کوئری کوچک می‌زنیم.
        $monthStart = Verta::createJalaliDate($jalaliYear, $nowMonth, 1)->toCarbon()->toDateString();
        $monthEnd = Verta::createJalaliDate($jalaliYear, $nowMonth, $daysInMonth)->toCarbon()->toDateString();

        return Customer::query()
            ->whereNotNull('birth_date')
            ->where('is_active', true)
            ->whereBetween('birth_date', [$monthStart, $monthEnd])
            ->orderBy('birth_date')
            ->get(['id', 'first_name', 'last_name', 'birth_date'])
            ->filter(function (Customer $customer) use ($nowMonth, $nowDay) {
                try {
                    $birth = Verta::instance($customer->birth_date);

                    return (int) $birth->format('n') === $nowMonth
                        && (int) $birth->format('j') === $nowDay;
                } catch (\Throwable) {
                    return false;
                }
            })
            ->take(self::LIMIT)
            ->map(fn (Customer $customer) => [
                'title' => $customer->full_name,
                'meta' => 'برای تبریک و ارسال پیشنهاد مراجعه کنید',
                'url' => route('customers.index', absolute: false),
            ])
            ->values()
            ->all();
    }
}
