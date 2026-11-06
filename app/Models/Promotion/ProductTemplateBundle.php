<?php

namespace App\Models\Promotion;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTemplateBundle extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function template()
    {
        return $this->belongsTo(ProductTemplate::class);
    }
    public function freeProduct(){
        return $this->belongsTo(Product::class, 'free_product_id');
    }
}
