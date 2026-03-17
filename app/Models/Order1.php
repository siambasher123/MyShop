<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order1 extends Model
{
    // Explicitly define your table name
    protected $table = 'orders1';

    // Allow mass assignment for these fields
    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'mobile_number',
        'delivery_area',
        'address',
        'products',
        'product_codes', // ✅ add this line
        'subtotal',
        'shipping_charge',
        'discount',
        'total',
        'status',
    ];

    // Optional: automatically decode JSON columns when accessing
    protected $casts = [
        'products' => 'array',
        'product_codes' => 'array',
    ];
}
