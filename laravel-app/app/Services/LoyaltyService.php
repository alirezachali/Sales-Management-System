<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\PointTransaction;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    public function __construct(
        protected SettingService $settings,
    ) {}

    public function enabled(): bool
    {
        return in_array((string) $this->settings->get('loyalty_enabled', '1'), ['1', 'true', 'on'], true);
    }

    /**
     * هر X تومان خرید = ۱ امتیاز (پیش‌فرض ۱۰۰۰۰ تومان).
     */
    public function amountPerPoint(): int
    {
        return max(1, (int) $this->settings->get('loyalty_amount_per_point', 10000));
    }

    /**
     * هر امتیاز معادل چند تومان تخفیف است (پیش‌فرض ۱۰۰ تومان).
     */
    public function pointValue(): int
    {
        return max(1, (int) $this->settings->get('loyalty_point_value', 100));
    }

    /**
     * محاسبه‌ی امتیاز قابل کسب برای مبلغ مشخص.
     */
    public function pointsForAmount(float $amount, ?Customer $customer = null): int
    {
        if (! $this->enabled() || $amount <= 0) {
            return 0;
        }

        // ضریب اختصاصی رده‌ی مشتری (اگر تعریف شده باشد)
        $perPoint = $this->amountPerPoint();

        if ($customer?->role && (int) $customer->role->points_per_amount > 0) {
            $perPoint = (int) $customer->role->points_per_amount;
        }

        return (int) floor($amount / $perPoint);
    }

    /**
     * سقف مبلغی که مشتری می‌تواند با امتیازش تخفیف بگیرد.
     */
    public function maxRedeemableAmount(Customer $customer): float
    {
        $available = $this->availablePoints($customer);

        return max(0, $available * $this->pointValue());
    }

    public function availablePoints(Customer $customer): int
    {
        return max(0, (int) $customer->points - (int) $customer->spent_points);
    }

    /**
     * کسب امتیاز بعد از فروش.
     */
    public function earn(Customer $customer, int $points, ?object $reference = null, ?string $description = null): ?PointTransaction
    {
        if ($points <= 0 || ! $this->enabled()) {
            return null;
        }

        return DB::transaction(function () use ($customer, $points, $reference, $description) {
            $customer->increment('points', $points);
            $customer->refresh();

            return PointTransaction::create([
                'customer_id' => $customer->id,
                'type' => 'earn',
                'points' => $points,
                'balance_after' => $this->availablePoints($customer),
                'reference_type' => $reference ? $reference->getMorphClass() : null,
                'reference_id' => $reference?->id,
                'description' => $description ?: 'کسب امتیاز از خرید',
                'user_id' => auth()->id(),
            ]);
        });
    }

    /**
     * استفاده از امتیاز به‌عنوان تخفیف.
     */
    public function redeem(Customer $customer, int $points, ?object $reference = null, ?string $description = null): PointTransaction
    {
        if ($points <= 0) {
            throw new \InvalidArgumentException('تعداد امتیاز نامعتبر است.');
        }

        if ($this->availablePoints($customer) < $points) {
            throw new \InvalidArgumentException('امتیاز کافی موجود نیست.');
        }

        return DB::transaction(function () use ($customer, $points, $reference, $description) {
            $customer->increment('spent_points', $points);
            $customer->refresh();

            return PointTransaction::create([
                'customer_id' => $customer->id,
                'type' => 'redeem',
                'points' => -$points,
                'balance_after' => $this->availablePoints($customer),
                'reference_type' => $reference ? $reference->getMorphClass() : null,
                'reference_id' => $reference?->id,
                'description' => $description ?: 'استفاده از امتیاز به‌عنوان تخفیف',
                'user_id' => auth()->id(),
            ]);
        });
    }

    /**
     * تنظیم دستی امتیاز (مثلاً بعد از لغو فاکتور).
     */
    public function adjust(Customer $customer, int $delta, ?string $description = null): ?PointTransaction
    {
        if ($delta === 0) {
            return null;
        }

        return DB::transaction(function () use ($customer, $delta, $description) {
            if ($delta > 0) {
                $customer->increment('points', $delta);
            } else {
                $customer->increment('spent_points', abs($delta));
            }

            $customer->refresh();

            return PointTransaction::create([
                'customer_id' => $customer->id,
                'type' => 'adjust',
                'points' => $delta,
                'balance_after' => $this->availablePoints($customer),
                'description' => $description ?: 'تنظیم دستی امتیاز',
                'user_id' => auth()->id(),
            ]);
        });
    }
}
