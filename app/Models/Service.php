<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class,'department_id','id');
    }
    public function pathologyDepartment()
    {
        return $this->belongsTo(PathologySetting::class,'pathology_department_id','id');
    }
    public function testTemplate(){
        return $this->belongsTo(PathologyTestTemplate::class,'id','test_id');
    }
}
