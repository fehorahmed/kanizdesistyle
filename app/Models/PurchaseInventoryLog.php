<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInventoryLog extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $dates = ['date'];

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function order() {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'id');
    }


    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class,'purchase_order_id');
    }
}
