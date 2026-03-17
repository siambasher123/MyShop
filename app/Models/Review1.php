<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review1 extends Model
{
    use HasFactory;

    protected $table = 'review1';

    protected $fillable = [
        'category','subcategory','code','price','username','rating','review'
    ];
}
