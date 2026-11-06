<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryOrderProduct extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'delivery_order_products';
    protected $guarded = [];

    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

    public function sales_order(){
        return $this->belongsTo(SalesOrder::class,'sales_order_id');
    }

    public function department(){
        return $this->belongsTo(Department::class,'department_id');
    }

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }

}
