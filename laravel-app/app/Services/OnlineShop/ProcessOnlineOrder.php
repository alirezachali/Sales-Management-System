<?php

namespace App\Services\OnlineShop;

use App\Models\OnlineOrder;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleCalculator;
use App\Services\SaleService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProcessOnlineOrder
{
    public function __construct(private SaleService $saleService) {}

    public function fulfill(OnlineOrder $order): Sale
    {
        return DB::transaction(function () use ($order) {
            /** @var OnlineOrder $order */
            $order = OnlineOrder::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->sale_id) {
                return Sale::query()->findOrFail($order->sale_id);
            }

            if (! in_array($order->status, ['received', 'pending'], true)) {
                throw new InvalidArgumentException('این سفارش در وضعیت قابل پردازش نیست.');
            }

            $items = data_get($order->payload, 'items', []);
            if (! is_array($items) || $items === []) {
                throw new InvalidArgumentException('سفارش آیتم ندارد.');
            }

            $cart = [];
            foreach ($items as $item) {
                $sourceId = (int) ($item['source_product_id'] ?? 0);
                $qty = (int) ($item['quantity'] ?? 0);
                if ($sourceId < 1 || $qty < 1) {
                    throw new InvalidArgumentException('آیتم سفارش نامعتبر است.');
                }

                if (! Product::query()->whereKey($sourceId)->exists()) {
                    throw new InvalidArgumentException("کالای #{$sourceId} در سیستم مدیریت یافت نشد.");
                }

                $cart[] = ['id' => $sourceId, 'quantity' => $qty];
            }

            $products = Product::query()->whereIn('id', collect($cart)->pluck('id'))->get()->keyBy('id');
            $payable = app(SaleCalculator::class)->total($cart, $products);

            $customerId = $this->resolveCustomerId($order);

            $sale = $this->saleService->checkout(
                cart: $cart,
                discount: 0,
                paymentType: 'card',
                customerId: $customerId,
                payments: [['type' => 'card', 'amount' => $payable]],
            );

            $sale->forceFill(['source' => 'online'])->save();

            $order->forceFill([
                'sale_id' => $sale->id,
                'status' => 'packing',
                'packed_at' => now(),
            ])->save();

            return $sale;
        });
    }

    public function resolveCustomerId(OnlineOrder $order): ?int
    {
        $phone = preg_replace('/\D+/', '', (string) data_get($order->payload, 'customer.phone', ''));

        if ($phone !== '') {
            return \App\Models\Customer::query()->where('mobile', $phone)->value('id');
        }

        if ($order->customer_phone) {
            return \App\Models\Customer::query()
                ->where('mobile', preg_replace('/\D+/', '', $order->customer_phone))
                ->value('id');
        }

        return null;
    }

    public function dispatch(OnlineOrder $order, string $courierName, string $courierPhone): void
    {
        if ($order->status !== 'packing' || ! $order->sale_id) {
            throw new InvalidArgumentException('ابتدا سفارش باید پردازش و فاکتور شود.');
        }

        $order->forceFill([
            'status' => 'out_for_delivery',
            'courier_name' => $courierName,
            'courier_phone' => $courierPhone,
            'dispatched_at' => now(),
        ])->save();
    }

    public function deliver(OnlineOrder $order): void
    {
        if ($order->status !== 'out_for_delivery') {
            throw new InvalidArgumentException('سفارش هنوز برای پیک ارسال نشده است.');
        }

        $order->forceFill([
            'status' => 'delivered',
            'delivered_at' => now(),
        ])->save();
    }

    public function reject(OnlineOrder $order, string $reason): void
    {
        if ($order->sale_id) {
            throw new InvalidArgumentException('سفارش فاکتور شده را از این صفحه لغو نکنید.');
        }

        $order->forceFill([
            'status' => 'rejected',
            'reject_reason' => $reason,
        ])->save();
    }
}
