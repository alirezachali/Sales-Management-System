<?php

namespace App\Services\OnlineShop;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OnlineShopClient
{
    public function enabled(): bool
    {
        return config('online_shop.url') !== '' && config('online_shop.token') !== '';
    }

    public function ingest(array $body): void
    {
        $response = $this->http()->post($this->url('/api/catalog/ingest'), $body);

        if (! $response->successful()) {
            throw new RuntimeException('Catalog ingest failed: HTTP '.$response->status().' '.$response->body());
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function pendingOutbox(int $limit = 50): array
    {
        $response = $this->http()->get($this->url('/api/outbox/pending'), ['limit' => $limit]);

        if (! $response->successful()) {
            throw new RuntimeException('Outbox pull failed: HTTP '.$response->status());
        }

        return $response->json('data') ?? [];
    }

    /**
     * @param  array<int, string>  $keys
     */
    /**
     * @return array<int, array<string, mixed>>
     */
    public function customers(): array
    {
        $response = $this->http()->get($this->url('/api/customers'));

        if (! $response->successful()) {
            throw new RuntimeException('Customer directory failed: HTTP '.$response->status());
        }

        return $response->json('data') ?? [];
    }

    public function ack(array $keys): void
    {
        if ($keys === []) {
            return;
        }

        $response = $this->http()->post($this->url('/api/outbox/ack'), [
            'idempotency_keys' => $keys,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Outbox ack failed: HTTP '.$response->status());
        }
    }

    private function http(): PendingRequest
    {
        return Http::timeout(20)
            ->acceptJson()
            ->withToken((string) config('online_shop.token'));
    }

    private function url(string $path): string
    {
        return (string) config('online_shop.url').$path;
    }
}
