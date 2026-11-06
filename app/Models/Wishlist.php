<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class)->with('category:id,name', 'subcategory:id,name', 'brand:id,name')->select('id', 'name', 'image', 'selling_price','category_id', 'sub_category_id', 'brand_id');
    }
}
