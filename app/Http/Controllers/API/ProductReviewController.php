<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductReviewController extends Controller
{
    // Get all reviews for a product
    public function index(Product $product)
    {
        // Get reviews with pagination, e.g., 5 per page
        $perPage = 5;
        $reviews = $product->reviews()
            ->with('user:id,name')
            ->latest()
            ->paginate($perPage);

        // Transform the reviews
        $reviewsTransformed = $reviews->getCollection()->map(function ($review) {
            return [
                'id' => $review->id,
                'user' => $review->user->name ?? 'Guest',
                'comment' => $review->comment,
                'rating' => $review->rating,
                'created_at' => $review->created_at->format('Y-m-d H:i'),
            ];
        });

        // Return paginated data
        return response()->json([
            'reviews' => $reviewsTransformed,
            'current_page' => $reviews->currentPage(),
            'last_page' => $reviews->lastPage(),
            'per_page' => $reviews->perPage(),
            'total' => $reviews->total(),
        ]);
    }
    public function summaryReviews(Product $product){
        $reviews = $product->reviews()
            ->with('user:id,name')
            ->get();

        // Return paginated data
        return response()->json([
            'reviews' => $reviews,
        ]);
    }
    // Submit a review (must be logged in)
    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Create review
            $review = $product->reviews()->create([
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            // Update product ratings
            $product->total_review += 1;
            $product->total_rating += $request->rating;
            $product->save();
            $product->rating = $product->total_review ? $product->total_rating / $product->total_review : 0;
            $product->save();

            // Load user data for response
            $review->load('user:id,name');

            DB::commit();

            return response()->json([
                'message' => 'Review added successfully!',
                'review' => [
                    'id' => $review->id,
                    'user' => $review->user->name,
                    'comment' => $review->comment,
                    'rating' => (int) $review->rating,
                    'created_at' => $review->created_at->format('Y-m-d H:i'),
                ],
                'product' => [
                    'id' => $product->id,
                    'total_review' => $product->total_review,
                    'total_rating' => $product->total_rating,
                    'rating' => $product->rating,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
}
