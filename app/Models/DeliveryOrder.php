<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryOrder extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    protected $dates = ['date', 'next_payment','deleted_at'];

    // public function products() {
    //     return $this->belongsToMany(Product::class)
    //         ->withPivot('id', 'name', 'serial', 'warranty', 'quantity', 'exchange_qty', 'unit_price', 'total');
    // }

    public function products() {
        return $this->hasMany(SalesOrderProduct::class);
    }
    public function orderInfo(){
        return $this->belongsTo(EcomOrderInfo::class,'id','order_id');
    }
    public function allProducts() {
        return $this->hasMany(SalesOrderProduct::class);
    }

    public function minimum_price()
    {
        $salesOrderProduct=SalesOrderProduct::where('sales_order_id',$this->id)->get();
         $minimumPrice= 0;
        foreach ($salesOrderProduct as  $product) {
           $mini_p= Product::where('id', $product->product_id)->value('min_price');

           $minimumPrice += $mini_p* $product->quantity;
        }
        return $minimumPrice;
    }
    public function stock_price()
    {
        $products= SalesOrderProduct::where('sales_order_id',$this->id)->get();

        $total=0;
        foreach ($products as $product) {
            $inventory_unit_price= PurchaseInventory::where('department_id', $product->department_id)
             ->where('product_id', $product->product_id)
             ->value('unit_price');

        $total += $inventory_unit_price* $product->quantity;

        }

        return $total;

    }

    public function payments() {
        return $this->hasMany(SalePayment::class);
    }
    public function payment() {
        return $this->hasOne(SalePayment::class,'sales_order_id','id');
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function sisterConcern() {
        return $this->belongsTo(SisterConcern::class);
    }

    public function services() {
        return $this->hasMany(Service::class);
    }
}
