<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Courier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'vehicle_type',
        'plate_number',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function onlineOrders(): HasMany
    {
        return $this->hasMany(OnlineOrder::class, 'courier_name', 'name');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function vehicleLabel(): string
    {
        return match ($this->vehicle_type) {
            'motorcycle' => 'موتورسیکلت',
            'bicycle' => 'دوچرخه',
            'car' => 'خودرو',
            'walking' => 'پیاده',
            'other' => 'سایر',
            default => $this->vehicle_type,
        };
    }
}