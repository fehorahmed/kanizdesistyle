<?php

namespace App\Models\Promotion;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionTemplateDetail extends Model
{
    use HasFactory;

    protected $table = 'promotion_template_details';

    protected $guarded = [];

    public function promotionTemplate()
    {
        return $this->belongsTo(PromotionTemplate::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
