<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Supplier extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function getDueAttribute() {
        if (Auth::user()->role_id != 1) {
            return PurchaseOrder::where('supplier_id', $this->id)->where('department_id', Auth::user()->id)->sum('due');
        } else {
            return PurchaseOrder::where('supplier_id', $this->id)->sum('due');
        }

    }

    public function getPaidAttribute() {
        if (Auth::user()->role_id != 1) {
            return PurchaseOrder::where('supplier_id', $this->id)->where('department_id', Auth::user()->id)->sum('paid');
        } else {
            return PurchaseOrder::where('supplier_id', $this->id)->sum('paid');
        }

    }

    public function getOpeningPaidAttribute() {
        $purchaseOrderId = PurchaseOrder::where('supplier_id', $this->id)->where('opening_purchase', 1)->first()->id ?? '';
        if (Auth::user()->role_id != 1) {
            return PurchasePayment::where('supplier_id', $this->id)->where('department_id', Auth::user()->id)->where('purchase_order_id', $purchaseOrderId)->sum('amount');
        } else {
            return PurchasePayment::where('supplier_id', $this->id)->where('purchase_order_id', $purchaseOrderId)->sum('amount');
        }
    }

    public function getTotalAttribute() {
        if (Auth::user()->role_id != 1) {
            return PurchaseOrder::where('supplier_id', $this->id)->where('department_id', Auth::user()->id)->sum('total');
        } else {
            return PurchaseOrder::where('supplier_id', $this->id)->sum('total');
        }

    }

    public function getRefundAttribute() {
        return PurchaseOrder::where('supplier_id', $this->id)->sum('refund');
    }
}
