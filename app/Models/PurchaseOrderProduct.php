<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderProduct extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function purchase_order(){
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
