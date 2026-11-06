<?php

namespace App\Http\Controllers\API;

use App\Facades\Cart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /** Add item to cart */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'discount'   => 'nullable|numeric|min:0',
        ]);

        Cart::add($request->product_id, $request->quantity, $request->discount ?? 0);

        return response()->json([
            'message'   => 'Product added to cart.',
            'cart'      => Cart::all(),
            'subtotal'  => Cart::subtotal(),
            'discount'  => Cart::discount(),
            'total'     => Cart::total(0),
        ]);
    }

    /** Update item quantity */
    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        Cart::update($productId, $request->quantity);

        return response()->json([
            'message'   => 'Cart updated.',
            'cart'      => Cart::all(),
            'subtotal'  => Cart::subtotal(),
            'discount'  => Cart::discount(),
            'total'     => Cart::total(0),
        ]);
    }

    /** Remove item from cart */
    public function remove($productId)
    {
        Cart::remove($productId);

        return response()->json([
            'message'   => 'Product removed from cart.',
            'cart'      => Cart::all(),
            'subtotal'  => Cart::subtotal(),
            'discount'  => Cart::discount(),
            'total'     => Cart::total(0),
        ]);
    }

    /** Apply coupon */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code'     => 'required|string',
            'type'     => 'required|in:percent,fixed',
            'discount' => 'required|numeric|min:0',
        ]);

        $coupon = [
            'code'     => $request->code,
            'type'     => $request->type,
            'discount' => $request->discount,
        ];

        Cart::applyCoupon($coupon);

        return response()->json([
            'message' => 'Coupon applied.',
            'coupon'  => Cart::getCoupon(),
            'total'   => Cart::total(0),
        ]);
    }

    /** Remove coupon */
    public function removeCoupon()
    {
        Cart::removeCoupon();

        return response()->json([
            'message' => 'Coupon removed.',
            'total'   => Cart::total(0),
        ]);
    }

    /** Get cart summary */
    public function summary()
    {
        // Cart::clear();
        return response()->json([
            'items'     => Cart::all(),
            'subtotal'  => Cart::subtotal(),
            'discount'  => Cart::discount(),
            'tax'       => Cart::tax(0),
            'total'     => Cart::total(0),
            'coupon'    => Cart::getCoupon(),
            'count'     => Cart::count(),
            'productDiscount'  => Cart::productDiscount(),
            'couponDiscount'  => Cart::couponDiscount(),
        ]);
    }
    public function clear()
    {
        Cart::clear();

        return response()->json([
            'message' => 'Cart cleared successfully.',
            'cart'    => Cart::all(),
            'subtotal'=> Cart::subtotal(),
            'discount'=> Cart::discount(),
            'total'   => Cart::total(0),
        ]);
    }
}
