<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction1 extends Model
{
    use HasFactory;

    protected $table = 'transactions1'; // ✅ matches migration

    protected $fillable = [
        'order_id',
        'user_id',
        'full_name',
        'email',
        'mobile_number',
        'delivery_area',
        'address',
        'product_codes',
        'products',
        'quantity',
        'total',
        'status',
        'payment_note',
    ];
}
