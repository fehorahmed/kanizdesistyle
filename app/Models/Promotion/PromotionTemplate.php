<?php

namespace App\Models\Promotion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionTemplate extends Model
{
    use HasFactory;

    protected $table = 'promotion_templates';

    protected $guarded = [];

    protected $casts = [
        'review_images' => 'array',
        'review_videos' => 'array',
        'faqs' => 'array',
    ];

    public function details()
    {
        return $this->hasMany(PromotionTemplateDetail::class);
    }
}
