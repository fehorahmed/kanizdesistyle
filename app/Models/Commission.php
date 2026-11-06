<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
    public function customer()
    {
        return $this->belongsTo(Supplier::class,'customer_id');
    }
}
