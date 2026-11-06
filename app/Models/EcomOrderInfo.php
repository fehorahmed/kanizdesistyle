<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcomOrderInfo extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'ordered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->hasMany(SalesOrder::class);
    }
    public function user(){
        return $this->belongsTo(User::class,'id','employee_id');
    }
}
