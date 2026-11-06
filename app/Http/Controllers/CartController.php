<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Fetch cart items
    public function index(Request $request)
    {
        $userId = Auth::check() ? Auth::user()->id : null;
        // dd($userId);
        $cart = Cart::with('items')->where('user_id', $userId)->first();
        if(!$userId){
            $sessinonId = Session::get('session_id') ?? Session::getId();
            $cart = Cart::with('items')->where('session_id', $sessinonId)->first();
        }
        // dd($sessinonId);
        if (!$cart) {
            return response()->json([
                'cart' => [],
                'cartItems' => [],
                'products' => [],
                'message' => 'Cart not found',
            ], 404);
        }

        $cartItems = $cart->items;
        $activeItems = CartItem::where('cart_id',$cart->id)->where('status','active')->get()->toArray();

        // Get the product IDs from cart items
        $productIds = $cartItems->pluck('product_id');

        // Fetch products from the Product model
        $products = Product::whereIn('id', $productIds)->get();

        return response()->json([
            'cart' => $cart,
            'cartItems' => $activeItems,
            'products' => $products
        ]);
    }


    // Add item to cart
    public function store(Request $request)
    {
        // $userId = $request->user_id ?? 'Not Defined';
        $userId = Auth::check() ? Auth::user()->id : null;
        if(!$userId){
            $sessinonId = Session::get('session_id') ?? Session::getId();
            Session::put('session_id',$sessinonId);
            $cart = Cart::firstOrCreate(
                [
                    'session_id' => $sessinonId,
                    'status' => 'active',
                ]
            );
        }else{
            $cart = Cart::firstOrCreate(
                [
                    'user_id' => $userId,
                    'status' => 'active',
                ]
            );
        }
        // dd($sessinonId);

        // Calculate the new total price
        $newTotalPrice = $cart->total_price + $request->price;
        $newTaxableAmount = $newTotalPrice * 0;
        // $newDiscountableAmount = $newTotalPrice * 0;
        $newDiscountableAmount = $cart->discount + $request->discount;

        // Update the cart with the new total price
        $cart->update(['total_price' => $newTotalPrice, 'tax' => $newTaxableAmount, 'discount' => $newDiscountableAmount]);

        // Retrieve the existing cart item for the given product_id
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('status', 'active')
            ->whereNot('status', 'deleted')
            ->whereNot('status', 'p2c')
            ->first();

        if ($cartItem) {
            // If the cart item exists, update the fields by adding the new data
            $cartItem->quantity += $request->quantity;
            // $cartItem->total_price += $request->total_price;
            $cartItem->total_price += $request->price;

            // Merge attributes if they are JSON-encoded objects or arrays
            $existingAttributes = json_decode($cartItem->attributes, true) ?? [];
            $newAttributes = is_array($request->attributes) || is_object($request->attributes)
                ? (array) $request->attributes
                : json_decode($request->attributes, true);

            $mergedAttributes = array_merge($existingAttributes, $newAttributes);
            $cartItem->attributes = json_encode($mergedAttributes);

            $cartItem->save();
        } else {
            // If the cart item does not exist, create it
            $attributes = is_array($request->attributes) || is_object($request->attributes)
                ? json_encode($request->attributes)
                : $request->attributes;

            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'batch_no' => $request->batch_no,
                'status' => 'active',
                'quantity' => $request->quantity,
                'price' => $request->price,
                'discount_percent' => $request->discount_percent,
                'discount' => $request->discount,
                'total_price' => $request->quantity * $request->price,
                'attributes' => $attributes,
            ]);
        }

        return response()->json($cartItem);
    }



    // Remove item from cart
    public function destroy($id)
    {
        $cartItem = CartItem::find($id);
        if ($cartItem) {
            $removeableTotalPrice = $cartItem->total_price;

            $cart = Cart::find($cartItem->cart_id);
            $newTotalPrice = $cart->total_price - $removeableTotalPrice;
            $newTaxableAmount = $newTotalPrice * 0;
            // $newDiscountableAmount = $newTotalPrice * 0;
            $newDiscountableAmount = $cart->discount - ($cartItem->discount*$cartItem->quantity);
            
            $cart->update(['total_price' => $newTotalPrice, 'tax' => $newTaxableAmount, 'discount' => $newDiscountableAmount]);
            $cartItem->delete();
            return response()->json(['message' => 'Item removed from cart']);
        }
        return response()->json(['message' => 'Item not found'], 404);
    }

    public function updateCart(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($id);

        $oldCartItemTotalPrice = $cartItem->total_price;
        $oldCartItemDiscount = $cartItem->discount * $cartItem->quantity;

        $cartItem->quantity = $validated['quantity'];
        $cartItem->total_price = $cartItem->price * $cartItem->quantity; // Assuming product relation exists
        $cartItem->save();

        $cart = Cart::findOrFail($cartItem->cart_id);
        $newTotalPrice = $cart->total_price + ($cartItem->price * $cartItem->quantity) - $oldCartItemTotalPrice;
        $newTaxableAmount = $newTotalPrice * 0;
        $newDiscountableAmount = $cart->discount + ($cartItem->discount * $cartItem->quantity) - $oldCartItemDiscount;

        $cart->update(['total_price' => $newTotalPrice, 'tax' => $newTaxableAmount, 'discount' => $newDiscountableAmount]);


        return response()->json([
            'message' => 'Cart updated successfully.',
            'updatedTotalPrice' => $cartItem->total_price,
        ]);
    }
}
