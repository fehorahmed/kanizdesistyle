<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Cart extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Cart items relationship
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Coupon relationship (optional)
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Check if guest cart has expired
     */
    public function isExpired()
    {
        return $this->expires_at && Carbon::now()->gt($this->expires_at);
    }
}
