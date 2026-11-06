<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalePayment extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    protected $dates = ['date'];

    public function sisterConcern() {
        return $this->belongsTo(SisterConcern::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function salesOrder() {
        return $this->belongsTo(SalesOrder::class);
    }

    public function bank() {
        return $this->belongsTo(Bank::class);
    }
    public function mobileBanking() {
        return $this->belongsTo(MobileBanking::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function account() {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'id');
    }
}
