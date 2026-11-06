<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    protected $dates = ['date'];

    // public function products() {
    //     return $this->belongsToMany(Product::class)
    //         ->withPivot('id', 'name', 'type', 'serial_no', 'warranty', 'quantity',
    //             'unit_price', 'selling_price', 'total');
    // }

    public function products() {
        return $this->hasMany(PurchaseOrderProduct::class);
    }
    public function purchaseOrder() {
        return $this->belongsTo(PurchaseOrderProduct::class,'id','purchase_order_id');
    }
    public function inventoryLogs() {
        return $this->hasMany(PurchaseInventoryLog::class);
    }

    public function sisterConcern() {
        return $this->belongsTo(SisterConcern::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function payments() {
        return $this->hasMany(PurchasePayment::class);
    }
    public function payment() {
        return $this->hasOne(PurchasePayment::class);
    }
}
