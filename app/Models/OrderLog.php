<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderLog extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function purchaseOrder(){
        return $this->belongsTo(PurchaseOrder::class,'order_id')->withTrashed();
    }
    public function saleOrder(){
        return $this->belongsTo(SalesOrder::class,'order_id')->withTrashed();
    }
    public function acupunctureService(){
        return $this->belongsTo(AcupunctureSaleService::class,'order_id')->withTrashed();
    }
    public function pathologyService(){
        return $this->belongsTo(PathologySaleService::class,'order_id')->withTrashed();
    }
    public function therapyService(){
        return $this->belongsTo(TherapySaleService::class,'order_id')->withTrashed();
    }
    public function homeoService(){
        return $this->belongsTo(HomeoSaleService::class,'order_id')->withTrashed();
    }
    public function clientPayment(){
        return $this->belongsTo(SalePayment::class,'order_id')->withTrashed();
    }
    public function supplierPayment(){
        return $this->belongsTo(PurchasePayment::class,'order_id')->withTrashed();
    }
}
