<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function getDueAttribute() {
        if (Auth::user()->role_id != 1) {
            return SalesOrder::where('customer_id', $this->id)->where('department_id', Auth::user()->id)->sum('due');
        }else {
            return SalesOrder::where('customer_id', $this->id)->sum('due');
        }

    }

    public function getPaidAttribute() {
        if (Auth::user()->role_id != 1) {
            return SalesOrder::where('customer_id', $this->id)->where('department_id', Auth::user()->id)->sum('paid');
        } else {
            return SalesOrder::where('customer_id', $this->id)->sum('paid');
        }

    }

    public function getOpeningPaidAttribute() {
        $salesOrderId = SalesOrder::where('customer_id', $this->id)->where('opening_sale', 1)->first()->id??'';
        if (Auth::user()->role_id != 1) {
            return SalePayment::where('customer_id', $this->id)->where('department_id', Auth::user()->id)->where('sales_order_id', $salesOrderId)->sum('amount');
        } else {
            return SalePayment::where('customer_id', $this->id)->where('sales_order_id', $salesOrderId)->sum('amount');
        }

    }

    public function getTotalAttribute() {
        if (Auth::user()->role_id != 1) {
            return SalesOrder::where('customer_id', $this->id)->where('department_id', Auth::user()->id)->sum('total');
        } else {
            return SalesOrder::where('customer_id', $this->id)->sum('total');
        }

    }

    public function getRefundAttribute() {
        if (Auth::user()->role_id != 1) {
            return SalesOrder::where('customer_id', $this->id)->where('department_id', Auth::user()->id)->sum('refund');
        } else {
            return SalesOrder::where('customer_id', $this->id)->sum('refund');
        }

    }
}
