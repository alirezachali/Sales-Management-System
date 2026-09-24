<?php

namespace App\Services\OnlineShop;

use App\Models\Customer;
use Illuminate\Support\Carbon;

class PullOnlineCustomers
{
    public function __construct(private OnlineShopClient $client) {}

    public function handle(): int
    {
        if (! $this->client->enabled()) {
            return 0;
        }

        $synced = 0;

        foreach ($this->client->customers() as $row) {
            $mobile = $this->normalizePhone((string) ($row['phone'] ?? ''));
            if ($mobile === '') {
                continue;
            }

            $onlineId = (int) ($row['id'] ?? 0);
            [$first, $last] = $this->splitName((string) ($row['name'] ?? 'مشتری'));

            $customer = Customer::query()->where('online_user_id', $onlineId)->first()
                ?? Customer::query()->where('mobile', $mobile)->first();

            if (! $customer) {
                Customer::query()->create([
                    'online_user_id' => $onlineId ?: null,
                    'first_name' => $first,
                    'last_name' => $last,
                    'mobile' => $mobile,
                    'city' => $row['city'] ?? null,
                    'address' => $row['address'] ?? null,
                    'registered_online_at' => $this->parseTime($row['created_at'] ?? null),
                    'is_active' => true,
                ]);
                $synced++;

                continue;
            }

            $customer->forceFill([
                'online_user_id' => $onlineId ?: $customer->online_user_id,
                'registered_online_at' => $customer->registered_online_at ?? $this->parseTime($row['created_at'] ?? null),
                'city' => $customer->city ?: ($row['city'] ?? null),
                'address' => $customer->address ?: ($row['address'] ?? null),
            ])->save();

            $synced++;
        }

        return $synced;
    }

    private function parseTime(mixed $value): Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        try {
            return Carbon::parse((string) $value);
        } catch (\Throwable) {
            return now();
        }
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            $digits = '0'.$digits;
        }

        return $digits;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitName(string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return ['مشتری', 'آنلاین'];
        }

        $parts = preg_split('/\s+/u', $name) ?: [$name];
        $first = array_shift($parts);

        return [$first, $parts === [] ? 'آنلاین' : implode(' ', $parts)];
    }
}
