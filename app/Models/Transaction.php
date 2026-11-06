<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    protected $dates = ['date'];

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function accountHeadType() {
        return $this->belongsTo(AccountHeadType::class);
    }

    public function accountHeadSubType() {
        return $this->belongsTo(AccountHeadSubType::class);
    }

    public function bank() {
        return $this->belongsTo(Bank::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function account() {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'id');
    }
}
