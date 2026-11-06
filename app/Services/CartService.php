<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CartService
{
    protected $cartLifetimeDays = 30; // Guest cart valid for 30 days

    /** Get current cart (guest or logged-in) */
    protected function getCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        // Guest cart
        $sessionId = Session::get('cart_session');

        if (!$sessionId) {
            $sessionId = (string) Str::uuid();
            Session::put('cart_session', $sessionId);
        }

        $expiresAt = Carbon::now()->addDays($this->cartLifetimeDays);

        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['expires_at' => $expiresAt]
        );
    }

    /** Get all items */
    public function all()
    {
        return $this->getCart()->items()->with('product')->get();
    }

    /** Add product to cart */
    public function add($productId, $quantity = 1, $discount = 0)
    {
        $cart = $this->getCart();
        $product = Product::findOrFail($productId);
        // dd($cart);
        $item = CartItem::firstOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $productId],
            ['old_price' => $product->old_selling_price, 'price' => $product->selling_price, 'discount' => $discount, 'discount_percent'=>$product->discount_percent]
        );

        $item->quantity += $quantity;
        $item->save();

        // Extend guest cart expiration
        if (!$cart->user_id) {
            $cart->expires_at = Carbon::now()->addDays($this->cartLifetimeDays);
            $cart->save();
        }
    }

    /** Update quantity */
    public function update($productId, $quantity)
    {
        $item = $this->getCart()->items()->where('product_id', $productId)->first();
        if ($item) {
            $item->quantity = $quantity;
            $item->save();
        }
    }

    /** Remove item */
    public function remove($productId)
    {
        $this->getCart()->items()->where('product_id', $productId)->delete();
    }

    /** Clear cart */
    public function clear()
    {
        $this->getCart()->items()->delete();
        Session::forget('cart_coupon');
    }

    /** Count items */
    public function count()
    {
        return $this->getCart()->items()->sum('quantity');
    }

    /** Subtotal */
    public function subtotal()
    {
        $items = $this->getCart()->items()->get(); // fetch as Collection
        return $items->sum(fn($item) => $item->old_price * $item->quantity);
    }
    public function productDiscount()
    {
        $items = $this->getCart()->items()->get(); // fetch as Collection
        $cartDiscount = $items->sum(fn($item) => $item->discount * $item->quantity);

        return $cartDiscount;
    }

    /** Coupon discount only */
    public function couponDiscount()
    {
        $cartDiscount = $this->productDiscount();
        $couponDiscount = 0;
        if ($coupon = $this->getCoupon()) {
            $subtotalAfterCartDiscount = $this->subtotal() - $cartDiscount;
            if ($coupon['type'] === 'percent') {
                $couponDiscount = round($subtotalAfterCartDiscount * ($coupon['discount'] / 100));
            } elseif ($coupon['type'] === 'fixed') {
                $couponDiscount = $coupon['discount'];
            }
        }

        return $couponDiscount;
    }
    /** Discount */
    public function discount()
    {
        return $this->productDiscount() + $this->couponDiscount();
    }

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

    /** Coupon */
    public function applyCoupon($coupon)
    {
        Session::put('cart_coupon', $coupon);
    }

    public function getCoupon()
    {
        return Session::get('cart_coupon');
    }

    public function removeCoupon()
    {
        Session::forget('cart_coupon');
    }

    /** Merge guest cart into user cart on login */
    public function mergeGuestCartToUser($userId)
    {
        $sessionId = Session::get('cart_session');
        if (!$sessionId) return;

        $guestCart = Cart::where('session_id', $sessionId)->first();
        if (!$guestCart) return;

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()->where('product_id', $item->product_id)->first();
            if ($existing) {
                $existing->quantity += $item->quantity;
                $existing->save();
            } else {
                $item->cart_id = $userCart->id;
                $item->save();
            }
        }

        $guestCart->delete();
        Session::forget('cart_session');
    }
}
