<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected $cartKey = 'cart';
    protected $couponKey = 'cart_coupon';

    /** Get all cart items */
    public function all()
    {
        return Session::get($this->cartKey, []);
    }

    /** Add item to cart with per-product discount */
    public function add($productId, $quantity = 1, $discount = 0)
    {
        $product = Product::findOrFail($productId);
        $cart = Session::get($this->cartKey, []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
            $cart[$product->id]['discount'] = $discount; // update discount if provided
        } else {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'image'      => $product->image,
                'old_price'  => $product->old_selling_price,
                'price'      => $product->selling_price,
                'quantity'   => $quantity,
                'discount'   => $discount,
                'discount_percent'=> $product->discount_percent,
            ];
        }

        Session::put($this->cartKey, $cart);
    }

    /** Remove item */
    public function remove($productId)
    {
        $cart = Session::get($this->cartKey, []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }
        Session::put($this->cartKey, $cart);
    }

    /** Update quantity */
    public function update($productId, $quantity)
    {
        $cart = Session::get($this->cartKey, []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
        }
        Session::put($this->cartKey, $cart);
    }

    /** Clear cart */
    public function clear()
    {
        Session::forget($this->cartKey);
        Session::forget($this->couponKey);
        Session::save();
    }

    /** Count items */
    public function count()
    {
        return array_sum(array_column($this->all(), 'quantity'));
    }

    /** Subtotal before any discount */
    public function subtotal()
    {
        $total = 0;
        foreach ($this->all() as $item) {
            $total += $item['old_price'] * $item['quantity'];
        }
        return $total;
    }

    public function productDiscount()
    {
        $cartDiscount = 0;
        foreach ($this->all() as $item) {
            $cartDiscount += ($item['discount'] ?? 0) * $item['quantity'];
        }
        return $cartDiscount;
    }

    /** Coupon discount only */
    public function couponDiscount()
    {
        $coupon = $this->getCoupon();
        $discount = 0;

        if ($coupon) {
            if ($coupon['type'] === 'percent') {
                $discount = ($this->subtotal() - $this->productDiscount()) * ($coupon['discount'] / 100);
            } elseif ($coupon['type'] === 'fixed') {
                $discount = $coupon['discount'];
            }
        }

        return $discount;
    }

    /** Total discount (product + coupon) */
    public function discount()
    {
        return $this->productDiscount() + $this->couponDiscount();
    }
    /** Total discount including per-product + coupon */
    // public function discount()
    // {
    //     $cartDiscount = 0;
    //     foreach ($this->all() as $item) {
    //         $cartDiscount += ($item['discount'] ?? 0) * $item['quantity'];
    //     }

    //     $coupon = $this->getCoupon();
    //     $couponDiscount = 0;

    //     if ($coupon) {
    //         if ($coupon['type'] === 'percent') {
    //             $couponDiscount = ($this->subtotal() - $cartDiscount) * ($coupon['discount'] / 100);
    //         } elseif ($coupon['type'] === 'fixed') {
    //             $couponDiscount = $coupon['discount'];
    //         }
    //     }

    //     return $cartDiscount + $couponDiscount;
    // }

    /** Tax */
    public function tax($taxPercent = 0)
    {
        return ($this->subtotal() - $this->discount()) * ($taxPercent / 100);
    }

    /** Total */
    public function total($taxPercent = 0)
    {
        return $this->subtotal() - $this->discount() + $this->tax($taxPercent);
    }

    /** Apply coupon */
    public function applyCoupon($coupon)
    {
        Session::put($this->couponKey, $coupon);
    }

    public function getCoupon()
    {
        return Session::get($this->couponKey, null);
    }

    public function removeCoupon()
    {
        Session::forget($this->couponKey);
    }

    /** Get single item */
    public function get($productId)
    {
        return $this->all()[$productId] ?? null;
    }

    /** Check item exists */
    public function has($productId)
    {
        return isset($this->all()[$productId]);
    }
}
