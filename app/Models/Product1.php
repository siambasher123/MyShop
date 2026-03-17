<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product1 extends Model
{
    use HasFactory;

    protected $table = 'products1';

    protected $fillable = [
        'category',
        'subcategory',
        'code',
        'price',
        'stock',
        'description',
        'image',
    ];
}
