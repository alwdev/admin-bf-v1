<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductList extends Model
{
    use HasFactory;
    protected $table = 'product_list';
    protected $fillable = [
        'product_id',
        'product_name',
        'category',
        'active',
        'img',
        'img_mini',
        'order_to',
        'store_id',
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (is_null($product->store_id)) {
                $product->store_id = 0;
            }
            if (is_null($product->active)) {
                $product->active = true;
            }
        });
    }
}
