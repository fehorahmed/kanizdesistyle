<?php

namespace App\Http\Controllers\API;

use App\Facades\Cart;
use App\Facades\Compare;
use App\Facades\Wishlist;
use App\Models\Banner;
use App\Models\Category;
use App\Models\CouponUsage;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Coupon;
use App\Models\Filter;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class FrontendController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'mobile'   => 'required|string|max:20|unique:users,mobile_no',
                'email'    => 'nullable|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
            ]);

            // Create user
            $user = User::create([
                'name'     => $validated['name'],
                'sister_concern_id'     => 1,
                'role_id'     => 4,//Customer
                'mobile_no'   => $validated['mobile'],
                'email'    => $validated['email'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            Auth::login($user);

            try {
                Cart::mergeGuestCartToUser($user->id);
            } catch (\Exception $e) {
                \Log::error("Failed to merge guest cart: ".$e->getMessage());
            }

            try {
                Wishlist::mergeGuestWishlistToUser($user->id);
            } catch (\Exception $e) {
                \Log::error("Failed to merge guest wishlist: ".$e->getMessage());
            }

            return response()->json([
                'message' => 'Registration successful.',
                'user' => $user,
                // 'token' => $token
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function login(Request $request){
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || $user->status !== 1 || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials or inactive account.'
                ], 401);
            }

            Auth::login($user);

            try {
                Cart::mergeGuestCartToUser($user->id);
            } catch (\Exception $e) {
                \Log::error("Failed to merge guest cart: ".$e->getMessage());
            }

            try {
                Wishlist::mergeGuestWishlistToUser($user->id);
            } catch (\Exception $e) {
                \Log::error("Failed to merge guest wishlist: ".$e->getMessage());
            }

            return response()->json([
                'message' => 'Login successful',
                'user' => $request->user()
            ]);

        } catch (\Exception $e) {
            \Log::error("Login error: ".$e->getMessage());
            return response()->json([
                'message' => 'Something went wrong during login.'
            ], 500);
        }
    }
    
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        return response()->json(['message' => 'Logged out']);
    }

    public function changePassword(Request $request)
    {   
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'], // assumes `confirm_password` is sent
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        auth()->user()->update([
            'password' => bcrypt($request->new_password),
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }
    public function forgotPassword(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $email = $request->email;
            $user = User::where('email', $email)->firstOrFail();

            // Generate token
            $token = Hash::make(Str::random(40));

            // Store in password_resets table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );

            // Build SPA reset URL
            $resetUrl = url("/reset-password/{$token}?email=" . urlencode($email));

            // Send email using Notification
            Notification::route('mail', $email)
                ->notify(new ResetPasswordNotification($resetUrl));

            // Return JSON response for Vue SPA
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset link sent to your email.',
                // optional: include for debugging or SPA display
                'reset_url' => $resetUrl,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Forgot Password Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to send reset link. Please try again later.',
            ], 500);
        }
    }
    public function resetPassword(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed', // password_confirmation expected
        ]);

        // Check token in password_reset_tokens table
        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return response()->json([
                'message' => 'Invalid or expired password reset token.'
            ], 422);
        }

        // Optional: Check if token is expired (e.g., older than 60 minutes)
        if (isset($reset->created_at) && Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return response()->json([
                'message' => 'Token has expired. Please request a new password reset.'
            ], 422);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Delete token after successful reset
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Your password has been reset successfully!'
        ]);
    }
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            // Add other fields if needed
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
    public function getSearch(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 5);

        if (empty($query)) {
            return response()->json([]);
        }

        $products = Product::select('id', 'name', 'slug', 'image')
            ->where('name', 'like', '%' . $query . '%')
            ->limit($limit)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->image,
                ];
            });

        return response()->json($products);
    }
    public function getPages(){
        $pages = Page::select('title','slug','status')->where('status', 1)->get();
       
        return response()->json([
            'pages' => $pages,
        ]);
    }
    public function getPage($slug){
        $page = Page::where('slug',$slug)->where('status', 1)->first();
       
        return response()->json([
            'page' => $page,
        ]);
    }
    public function getCategories(){
        $f_categories = Category::with('subCategories:category_id,name,slug')->select('id','name','slug','image','status')->where('status', 1)->get();
        $s_categories = Category::with('subCategories:category_id,name,slug')->select('id','name','slug','image','status')->where('status', 1)->where('is_smart_lifestyle',1)->get();
        $categories = Category::with('subCategories:category_id,name,slug')->select('id','name','slug','image','status')->where('status', 1)->get();
        $brands = Brand::select('id','name','image','status')->where('status', 1)->withCount(['products as product_count'])->get();

        return response()->json([
            'f_categories' => $f_categories,
            's_categories' => $s_categories,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
    public function getTopNicheProducts(){
        $top_sale_products = Product::select('id','name','slug','image','youtube_video_link','selling_price','old_selling_price','discount','discount_percent','status','total_review','rating','is_new_arrival','is_show','sort')->where('is_show',1)->where('status',1)->where('is_best_selling',1)->orderBy('sort','asc')->take(5)->get();
        $top_rating_products = Product::select('id','name','slug','image','youtube_video_link','selling_price','old_selling_price','discount','discount_percent','status','total_review','rating','is_trending','is_show','sort')->where('is_show',1)->where('status',1)->orderBy('rating','desc')->take(5)->get();
        $top_discount_products = Product::select('id','name','slug','image','youtube_video_link','selling_price','old_selling_price','discount','discount_percent','status','total_review','rating','is_featured','is_show','sort')->where('is_show',1)->where('status',1)->orderBy('discount_percent','desc')->take(5)->get();

        return response()->json([
            'top_sale_products' => $top_sale_products,
            'top_rating_products' => $top_rating_products,
            'top_discount_products' => $top_discount_products,
        ]);
    }
    public function getNewProducts(Request $request)
    {
        $limit = $request->input('limit', 10); // default 10
        $offset = $request->input('offset', 0);
        
        $products = Product::select('id','name','slug','image','youtube_video_link','selling_price','old_selling_price','discount','discount_percent','status','total_review','rating','is_new_arrival','sort')
            ->where('status',1)
            ->where('is_new_arrival',1)
            ->orderBy('sort','asc')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->toArray();

        $total = Product::select('id','status','is_new_arrival')->where('status',1)->where('is_new_arrival',1)->count();

        return response()->json([
            'products' => $products,
            'total' => $total,
        ]);
    }
    public function getHomeData(){
        $banners = Banner::select('type','sort','image','status')->where('type', 1)->where('status', 1)->orderBy('sort')->get();
        $new_products = Product::select('id','name','slug','image','youtube_video_link','selling_price','old_selling_price','discount','discount_percent','status','total_review','rating','is_new_arrival','sort')->where('status',1)->where('is_new_arrival',1)->orderBy('sort','asc')->get();
        $trending_products = Product::select('id','name','slug','image','youtube_video_link','selling_price','old_selling_price','discount','discount_percent','status','total_review','rating','is_trending','sort')->where('status',1)->where('is_trending',1)->orderBy('sort','asc')->get();
        $featured_products = Product::select('id','name','slug','image','youtube_video_link','selling_price','old_selling_price','discount','discount_percent','status','total_review','rating','is_featured','sort')->where('status',1)->where('is_featured',1)->orderBy('sort','asc')->get();
        $used_products = Product::select('id','name','slug','image','youtube_video_link','selling_price','old_selling_price','discount','discount_percent','status','total_review','rating','is_used','sort')->where('status',1)->where('is_used',1)->orderBy('sort','asc')->get();

        $setting = Setting::first();

        return response()->json([
            'banners' => $banners,
            'new_products' => $new_products,
            'trending_products' => $trending_products,
            'featured_products' => $featured_products,
            'used_products' => $used_products,
            'setting' => $setting,
        ]);
    }
    // public function getProductData(){
    //     dd(request()->all());
    //     $products = Product::select('id','name','slug','image','youtube_video_link','selling_price','old_selling_price','discount','discount_percent','status','total_review','is_new_arrival','sort')->where('status',1)->orderBy('sort','asc')->get()->toArray();

    //     return response()->json([
    //         'products' => $products,
    //     ]);
    // }
    public function getFilters(Request $request)
    {
        $brands = Brand::select('id','name','status')->where('status', 1)->withCount(['products as product_count'])->get();

        $filters = [];
        
        $slug = $request->get('slug');
        $category = Category::where('slug', $slug)->first();

        // If not found, try subcategory
        if (!$category) {
            $subcategory = SubCategory::where('slug', $slug)->first();

            if ($subcategory) {
                $category = $subcategory->category; // parent category
            }
        }

        if ($category) {
            $filters = $category->filters()
                ->with('options:id,filter_id,value')
                ->get([
                    'filters.id',       // specify table
                    'filters.name',
                    'filters.type'
                ]);
        }
        // dd($filters);
        return response()->json([
            'brands' => $brands,
            'filters' => $filters,
        ]);
    }
    public function getProductData()
    { 
        $query = Product::select('id','name','slug','image','youtube_video_link','selling_price','old_selling_price','discount','discount_percent','status','total_review','rating','is_new_arrival','is_show','sort')
            ->where('is_show',1)->where('status', 1);

        // Filters

        if ($brands = request('brands')) {
            $query->whereIn('brand_id', $brands);
        }

        $slug = request('slug');
        if($slug){
            $query->where(function ($q) use ($slug) {
                // Match products where category slug matches
                $q->whereHas('category', function ($q2) use ($slug) {
                    $q2->where('slug', $slug);
                })
                // OR where subcategory slug matches
                ->orWhereHas('subcategory', function ($q3) use ($slug) {
                    $q3->where('slug', $slug);
                });
            });
        }
        $minPrice = request('minPrice');
        $maxPrice = request('maxPrice');
        if ($minPrice && $maxPrice) {
            $query->whereBetween('selling_price', [$minPrice, $maxPrice]);
        }

        // Sorting
        $sortBy = request('sortBy', 'relevance');
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_low_high':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('selling_price', 'desc');
                break;
            default:
                $query->orderBy('sort', 'asc');
                break;
        }

        if ($selectedFilters = request('filters')) {
            $query->whereHas('filterValues', function($q) use ($selectedFilters) {
                $q->whereIn('filter_option_id', $selectedFilters);
            });
        }

        // Pagination
        $perPage = request('perPage', 10);
        $page = request('page', 1);
        $products = $query->skip(($page - 1) * $perPage)->take($perPage)->get()->toArray();

        return response()->json([
            'products' => $products,
        ]);
    }

    public function getProductDetailsData($slug){
        $product = Product::with('brand','images')->where('slug',$slug)->where('status',1)->first();
        $relatedProducts = Product::select('id','name','slug','image','youtube_video_link','selling_price','old_selling_price','discount','discount_percent','status','total_review','rating','is_show','is_new_arrival','sort')->where('category_id', $product->category_id)->where('id', '!=', $product->id)->where('is_show',1)->where('status', 1)->get();
        return response()->json([
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
    public function checkCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found']);
        }

        // check active
        if (!$coupon->is_active) {
            return response()->json(['success' => false, 'message' => 'Coupon is inactive']);
        }

        // check date validity
        $today = now();
        if ($coupon->start_date && $today->lt($coupon->start_date)) {
            return response()->json(['success' => false, 'message' => 'Coupon not started yet']);
        }
        if ($coupon->end_date && $today->gt($coupon->end_date)) {
            return response()->json(['success' => false, 'message' => 'Coupon expired']);
        }

        // check minimum purchase
        if ($coupon->min_purchase && $request->subtotal < $coupon->min_purchase) {
            return response()->json([
                'success' => false,
                'message' => "Minimum purchase required ৳{$coupon->min_purchase}"
            ]);
        }

        $customerId = auth()->user()->customer_id ?? null;
        if ($customerId) {
            $alreadyUsed = CouponUsage::where('coupon_id', $coupon->id)
                ->where('customer_id', $customerId)
                ->exists();

            if ($alreadyUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used this coupon'
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'coupon' => [
                'code'=>$coupon->code,
                'type'=>$coupon->type,
                'discount'=>$coupon->discount,
            ],
            'message' => 'Coupon applied successfully'
        ]);
    }

}
