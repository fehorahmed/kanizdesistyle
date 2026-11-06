<?php

namespace App\Models\Promotion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTemplate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'review_images' => 'array',
        'review_videos' => 'array',
        'benefits' => 'array',
    ];
    
    public function details()
    {
        return $this->hasMany(ProductTemplateDetail::class);
    }
    public function bundles()
    {
        return $this->hasMany(ProductTemplateBundle::class);
    }
}
