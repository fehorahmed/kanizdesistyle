<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suggestion extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];

    public function doctor(){
        return $this->belongsTo(Doctor::class);
    }
    public function parent(){
        return $this->belongsTo(Suggestion::class);
    }
}
