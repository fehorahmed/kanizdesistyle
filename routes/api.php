<?php

use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CompareController;
use App\Http\Controllers\API\FrontendController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductReviewController;
use App\Http\Controllers\API\WishlistController;
use App\Http\Controllers\GalleryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/user', function (Request $request) {
//         return $request->user();
//     });
// });

//Public Routes
Route::post('login', [FrontendController::class, 'login']);
Route::post('register', [FrontendController::class, 'register']);
Route::post('forgot-password', [FrontendController::class, 'forgotPassword']);
Route::post('reset-password', [FrontendController::class, 'resetPassword']);

Route::get('get-search', [FrontendController::class, 'getSearch']);
Route::get('get-pages', [FrontendController::class, 'getPages']);
Route::get('get-page/{slug}', [FrontendController::class, 'getPage']);
Route::get('get-galleries', [GalleryController::class, 'getGalleries']);
Route::get('get-categories', [FrontendController::class, 'getCategories']);
Route::get('get-top-niche-products', [FrontendController::class, 'getTopNicheProducts']);
Route::get('get-new-products', [FrontendController::class, 'getNewProducts']);
Route::get('get-home-data', [FrontendController::class, 'getHomeData']);
Route::get('get-filters', [FrontendController::class, 'getFilters']);
Route::get('get-product-data', [FrontendController::class, 'getProductData']);
Route::get('get-product-details-data/{slug}', [FrontendController::class, 'getProductDetailsData']);
Route::post('check-coupon', [FrontendController::class, 'checkCoupon']);

Route::prefix('cart')->group(function() {
    Route::post('add', [CartController::class,'add']);
    Route::post('update/{productId}', [CartController::class,'update']);
    Route::delete('remove/{productId}', [CartController::class,'remove']);
    Route::post('coupon', [CartController::class,'applyCoupon']);
    Route::delete('coupon', [CartController::class,'removeCoupon']);
    Route::get('summary', [CartController::class,'summary']);
    Route::post('clear', [CartController::class,'clear']);
});

Route::prefix('compare')->group(function () {
    Route::get('/', [CompareController::class, 'index']); // list items
    Route::post('/{productId}', [CompareController::class, 'store']); // add item
    Route::delete('/{productId}', [CompareController::class, 'destroy']); // remove item
});

//Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    });
    Route::put('user', [FrontendController::class, 'update']);
    Route::post('logout', [FrontendController::class, 'logout']);
    Route::post('change-password', [FrontendController::class, 'changePassword']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('order', [OrderController::class, 'store']);
    Route::get('order/{order_no}', [OrderController::class, 'show']);

    //Wishlist
    Route::get('wishlist', [WishlistController::class, 'index']);
    Route::post('wishlist/{productId}', [WishlistController::class, 'store']);
    Route::delete('wishlist/{productId}', [WishlistController::class, 'destroy']);

    Route::post('products/{product}/reviews', [ProductReviewController::class, 'store']);
});

Route::post('buy-now', [OrderController::class, 'buyNow']);
Route::get('buy-now/{order_no}/{mobile}', [OrderController::class, 'buyNowShow']);

Route::get('order/{order_no}/{mobile}/receipt', [OrderController::class, 'downloadReceipt'])->name('ecommerce_receipt.download');

Route::get('products/{product}/reviews', [ProductReviewController::class, 'index']);
Route::get('products/{product}/summary-reviews', [ProductReviewController::class, 'summaryReviews']);

Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});
