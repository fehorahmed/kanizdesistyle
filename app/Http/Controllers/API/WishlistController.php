<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Facades\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'wishlist'   => Wishlist::all(),
        ], 200);
    }

    public function store($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            Wishlist::add($product->id);

            return response()->json([
                'status'  => 'success',
                'message' => 'Product added to wishlist',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy($productId)
    {
        try {
            Wishlist::remove($productId);

            return response()->json([
                'status'  => 'success',
                'message' => 'Product removed from wishlist',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
