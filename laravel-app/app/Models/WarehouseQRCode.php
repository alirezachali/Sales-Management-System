<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseQRCode extends Model
{
    protected $table = 'warehouse_qr_codes';

    protected $fillable = [
        'qr_identifier',
        'warehouse_id',
        'product_id',
        'unit_type',
        'quantity_in_unit',
        'production_date',
        'expiration_date',
        'entry_date',
        'location_section',
        'location_shelf',
        'location_row',
        'location_position',
        'products_barcode',
        'notes',
        'qr_image_path',
        'created_by',
        'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'production_date' => 'date',
            'expiration_date' => 'date',
            'entry_date' => 'date',
            'products_barcode' => 'array',
        ];
    }

    // نوع‌های واحد قابل استفاده
    public static function unitTypes(): array
    {
        return [
            'box' => 'جعبه',
            'carton' => 'کارتن',
            'shelf' => 'شل',
            'pallet' => 'پالت',
            'bag' => 'کیسه',
            'other' => 'سایر',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * ساخت شناسه QR یکتا
     */
    public static function generateIdentifier(Warehouse $warehouse): string
    {
        $prefix = strtoupper(substr($warehouse->code ?? 'W', 0, 4));
        $timestamp = now()->format('ymdHis');
        $random = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$timestamp}-{$random}";
    }

    /**
     * نام کامل محل قرارگیری
     */
    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([
            $this->location_section,
            $this->location_shelf ? "قفسه {$this->location_shelf}" : null,
            $this->location_row ? "ردیف {$this->location_row}" : null,
            $this->location_position ? "جایگاه {$this->location_position}" : null,
        ]);

        return $parts ? implode(' - ', $parts) : 'نامشخص';
    }

    /**
     * وضعیت انقضا
     */
    public function getExpirationStatusAttribute(): string
    {
        if (! $this->expiration_date) {
            return 'بدون تاریخ';
        }

        $now = now()->startOfDay();
        $expiry = $this->expiration_date->startOfDay();
        $daysUntil = $now->diffInDays($expiry, false);

        if ($daysUntil < 0) {
            return 'منقضی شده';
        } elseif ($daysUntil <= 30) {
            return "{$daysUntil} روز مانده";
        } else {
            return $this->expiration_date->format('Y/m/d');
        }
    }

    /**
     * آیا محصول منقضی شده؟
     */
    public function getIsExpiredAttribute(): bool
    {
        if (! $this->expiration_date) {
            return false;
        }

        return $this->expiration_date->isPast();
    }

    /**
     * آیا نزدیک انقضا است؟ (۳۰ روز یا کمتر)
     */
    public function getIsNearExpirationAttribute(): bool
    {
        if (! $this->expiration_date) {
            return false;
        }

        $days = now()->diffInDays($this->expiration_date, false);

        return $days >= 0 && $days <= 30;
    }
}
