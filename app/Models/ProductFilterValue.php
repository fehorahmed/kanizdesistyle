<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFilterValue extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'filter_id', 'filter_option_id', 'value_text', 'value_number'];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function filter() {
        return $this->belongsTo(Filter::class);
    }

    public function option() {
        return $this->belongsTo(FilterOption::class, 'filter_option_id');
    }
}
