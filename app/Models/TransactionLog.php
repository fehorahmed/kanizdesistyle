<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionLog extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $dates = ['date'];

    public function bank() {
        return $this->belongsTo(Bank::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }
    // public function department() {
    //     return $this->belongsTo(Department::class);
    // }

    public function account() {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'id');
    }

    public function accountHead() {
        return $this->belongsTo(AccountHeadType::class, 'account_head_type_id', 'id');
    }

    public function accountSubHead() {
        return $this->belongsTo(AccountHeadSubType::class, 'account_head_sub_type_id', 'id');
    }
}
