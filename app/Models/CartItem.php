<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CartItem extends Model
{
    // protected $guarded = [];
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'old_price',
        'price',
        'discount',
        'discount_percent',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // Link to the Product
    public function product()
    {
        return $this->belongsTo(Product::class)->select('id','name','image');
    }
}
