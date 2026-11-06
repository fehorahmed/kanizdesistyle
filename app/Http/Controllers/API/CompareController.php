<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Facades\Compare;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    /**
     * Get all compared products
     */
    public function index()
    {
        try {
            $compare = Compare::all();
            return response()->json([
                'message' => 'Compare list fetched successfully',
                'compare' => $compare
            ]);
        } catch (\Exception $e) {
            \Log::error("Compare fetch error: ".$e->getMessage());
            return response()->json(['message' => 'Failed to fetch compare list'.$e->getMessage()], 500);
        }
    }

    /**
     * Add product to compare
     */
    public function store($productId)
    {
        try {
            Compare::add($productId);
            return response()->json([
                'message' => 'Product added to compare',
                'compare' => Compare::all()
            ]);
        } catch (\Exception $e) {
            \Log::error("Compare add error: ".$e->getMessage());
            return response()->json(['message' => 'Failed to add product to compare'], 500);
        }
    }

    /**
     * Remove product from compare
     */
    public function destroy($productId)
    {
        try {
            Compare::remove($productId);
            return response()->json([
                'message' => 'Product removed from compare',
                'compare' => Compare::all()
            ]);
        } catch (\Exception $e) {
            \Log::error("Compare remove error: ".$e->getMessage());
            return response()->json(['message' => 'Failed to remove product from compare'], 500);
        }
    }

    /**
     * Clear all compare products
     */
    public function clear()
    {
        try {
            Compare::clear();
            return response()->json([
                'message' => 'Compare list cleared',
                'compare' => Compare::all()
            ]);
        } catch (\Exception $e) {
            \Log::error("Compare clear error: ".$e->getMessage());
            return response()->json(['message' => 'Failed to clear compare list'], 500);
        }
    }
}
