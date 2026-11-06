<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function inventory_qty($product_id,$sister_concern_id,$batch){
        $in = PurchaseInventoryLog::where('product_id', $product_id)
            ->where('sister_concern_id', $sister_concern_id)
            ->where('batch_no', $batch)
            ->where('type', 1)
            ->sum('quantity');
        $out = PurchaseInventoryLog::where('product_id', $product_id)
            ->where('sister_concern_id', $sister_concern_id)
            ->where('batch_no', $batch)
            ->where('type', 2)
            ->sum('quantity');

        return $in-$out;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id','id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id','id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }
    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class,'sub_category_id','id');
    }
    public function inventory(){
        return $this->belongsTo(PurchaseInventory::class,'id','product_id')->orderBy('batch_no','desc')->where('quantity','>',0);
    }
    public function images(){
        return $this->hasMany(ProductImage::class,'product_id');
    }
    public function reviews(){
        return $this->hasMany(ProductReview::class,'product_id');
    }
    public function getStockAttribute(){
        return PurchaseInventory::where('product_id',$this->id)->sum('pack_quantity');
    }
    public function filterValues() {
        return $this->hasMany(ProductFilterValue::class);
    }
}
