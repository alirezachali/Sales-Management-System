<?php

namespace App\Services\OnlineShop;

use App\Models\OnlineOrder;

class PullOnlineOrders
{
    public function __construct(private OnlineShopClient $client) {}

    public function handle(): int
    {
        if (! $this->client->enabled()) {
            return 0;
        }

        $messages = $this->client->pendingOutbox();
        $acked = [];

        foreach ($messages as $message) {
            if (($message['type'] ?? '') !== 'order.placed') {
                continue;
            }

            $payload = $message['payload'] ?? [];
            $key = $message['idempotency_key'] ?? null;
            if (! $key) {
                continue;
            }

            OnlineOrder::query()->updateOrCreate(
                ['idempotency_key' => $key],
                [
                    'shop_order_id' => $payload['order_id'] ?? null,
                    'status' => 'received',
                    'customer_name' => data_get($payload, 'customer.name'),
                    'customer_phone' => data_get($payload, 'customer.phone'),
                    'city' => data_get($payload, 'customer.city'),
                    'address' => data_get($payload, 'customer.address'),
                    'total' => $payload['total'] ?? 0,
                    'payload' => $payload,
                ],
            );

            $acked[] = $key;
        }

        $this->client->ack($acked);

        return count($acked);
    }
}
