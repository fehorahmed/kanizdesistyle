<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInventory extends Model
{
    use HasFactory;

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sisterConcern() {
        return $this->belongsTo(SisterConcern::class);
    }
    public function purchaseQty(){
        return PurchaseOrderProduct::where('product_id',$this->product_id)->where('batch_no',$this->batch_no)->sum('quantity');
    }
    public function saleQty(){
        return SalesOrderProduct::where('product_id',$this->product_id)->where('batch_no',$this->batch_no)->sum('quantity');
    }
}
