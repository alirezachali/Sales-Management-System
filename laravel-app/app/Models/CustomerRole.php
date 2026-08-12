<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRole extends Model
{
    protected $fillable = [

        'name',

        'icon',

        'color',

        'sort_order',

        'discount_percent',

        'min_purchase_count',

        'min_purchase_amount',

        'description',

        'is_default',

        'is_active',

    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}