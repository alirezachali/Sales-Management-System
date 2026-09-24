<?php

namespace App\Services\OnlineShop;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class PublishCatalogToOnlineShop
{
    public function __construct(private OnlineShopClient $client) {}

    public function handle(): void
    {
        if (! $this->client->enabled()) {
            return;
        }

        $this->client->ingest([
            'categories' => Category::query()->get()->map(fn (Category $c) => [
                'source_id' => $c->id,
                'name' => $c->name,
                'is_active' => (bool) $c->is_active,
            ])->all(),
            'products' => Product::query()->get()->map(fn (Product $p) => [
                'source_id' => $p->id,
                'name' => $p->name,
                'barcode' => $p->barcode,
                'category_source_id' => $p->category_id,
                'sell_price' => $p->sell_price,
                'stock' => $p->stock,
                'unit' => $p->unit,
                'is_active' => (bool) $p->is_active,
            ])->all(),
            'settings' => $this->settingsPayload(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function settingsPayload(): array
    {
        $payload = [
            'store_name' => setting('store_name'),
            'phone' => setting('phone'),
            'mobile' => setting('mobile'),
            'address' => setting('address'),
            'website' => setting('website'),
            'currency' => setting('currency'),
            'receipt_footer' => setting('receipt_footer'),
            'tax_rate' => setting('tax_rate'),
            'city' => config('online_shop.city') ?: setting('address'),
        ];

        $logo = setting('store_logo');
        if ($logo && Storage::disk('public')->exists($logo)) {
            $mime = Storage::disk('public')->mimeType($logo) ?: 'image/png';
            $payload['store_logo_base64'] = 'data:'.$mime.';base64,'.base64_encode(
                Storage::disk('public')->get($logo)
            );
        }

        return $payload;
    }
}
