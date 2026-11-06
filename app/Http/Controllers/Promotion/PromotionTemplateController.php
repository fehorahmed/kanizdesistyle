<?php

namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OrderLog;
use App\Models\Product;
use App\Models\Promotion\PromotionTemplate;
use App\Models\Promotion\PromotionTemplateDetail;
use App\Models\Promotion\PromotionTemplateBundle;
use App\Models\PurchaseInventory;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class PromotionTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('promotion.promotion_template.index');
    }
    public function dataTable()
    {
        $query = PromotionTemplate::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('products', function (PromotionTemplate $promotionTemplate) {
                $details = $promotionTemplate->details;
                $productHtml = '<ul>';
                foreach ($details as $detail) {
                    $productHtml .= '<li>' . ($detail->product->name ?? '') . '</li>';
                }
                $productHtml .= '</ul>';
                return $productHtml;
            })
            ->addColumn('action', function (PromotionTemplate $promotionTemplate) {
                $btn = '';
                $btn .= '<a href="' . route('promotion-template.edit', ['promotion_template' => encrypt_value($promotionTemplate->id)]) . '" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                $btn .= ' <a href="' . route('promotion-template.show.slug', ['slug' => $promotionTemplate->slug]) . '" target="_blank" class="btn btn-primary bg-gradient-primary btn-sm btn-show"><i class="fa fa-eye"></i></a>';
                $btn .= ' <a role="button" data-id="' . encrypt_value($promotionTemplate->id) . '" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></a>';
                return $btn;
            })
            ->rawColumns(['products', 'action'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with([
            'inventory:id,product_id,batch_no,selling_price,quantity'
        ])
        ->select('id','category_id','brand_id','name','image','discount','sort','rating')
        ->where('status', 1)
        ->whereHas('inventory', function($q) {
            $q->where('quantity', '>', 0);
        })
        ->get();
        return view('promotion.promotion_template.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'video_url' => 'nullable|url',
            'video_file' => 'nullable|mimes:mp4,mov,ogg,qt|max:51200',
            'detail_product_id' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (count($value) !== count(array_unique($value))) {
                        $fail('একই প্রডাক্ট একাধিক রোতে সিলেক্ট করা যাবে না। প্রতিটি প্রডাক্ট শুধু একবার সিলেক্ট করুন।');
                    }
                },
            ],
            'detail_product_id.*' => 'required|exists:products,id',
            'detail_image' => 'nullable|array',
            'detail_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'discount_type' => 'required|array|min:1',
            'discount_type.*' => 'required|in:percentage,fixed',
            'discount' => 'required|array|min:1',
            'discount.*' => 'required|numeric|min:0',
            'slug' => 'required|string|max:255|unique:promotion_templates,slug',
            'promotion_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'main_heading' => 'required|string|max:255',
            'video_url' => 'required|url',
            'status' => 'required|boolean',
            'review_images' => 'nullable|array',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'review_videos' => 'nullable|array',
            'review_videos.*' => 'nullable|url|max:255',
        ]);

        // Start a database transaction
        DB::beginTransaction();
        // dd($request->all());
        try {
            $promotionImagePath = null; 
            if ($request->hasFile('promotion_image')){
                // Upload Image
                $file = $request->file('promotion_image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'uploads/promotion/promotion_image';
                // $file->move(public_path($destinationPath), $filename);
                $file->move($destinationPath, $filename);
                $promotionImagePath = 'uploads/promotion/promotion_image/' . $filename;
            }

            $reviewImagePaths = [];
            if ($request->hasFile('review_images')) {
                foreach ($request->file('review_images') as $imageFile) {
                    if ($imageFile && $imageFile->isValid()) {
                        $file = $imageFile;
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'uploads/promotion/review_images';
                        // $file->move(public_path($destinationPath), $filename);
                        $file->move($destinationPath, $filename);
                        $path = 'uploads/promotion/review_images/' . $filename;
                        
                        $reviewImagePaths[] = $path;
                    }
                }
            }
            
            $reviewVideoUrls = $request->input('review_videos', []);
            $reviewVideoUrls = array_filter($reviewVideoUrls);

            $questions = $request->faq_questions;
            $answers = $request->faq_answers;
            
            $faqs = [];
            if($questions) {
                foreach($questions as $key => $question) {
                    if(!empty($question)) { 
                        $faqs[] = [
                            'question' => $question,
                            'answer' => $answers[$key] ?? ''
                        ];
                    }
                }
            }

            $finalVideoUrl = $request->video_url;

            if ($request->hasFile('video_file')) {
                $file = $request->file('video_file');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/videos'), $filename);
                $finalVideoUrl = asset('uploads/videos/' . $filename);
            }

            $template = PromotionTemplate::create([
                'slug' => $request->slug,
                'promotion_image' => $promotionImagePath,
                'main_heading' => $request->main_heading,
                'video_url' => $finalVideoUrl,
                'offer_area_title' => $request->offer_area_title,
                'offer_area_sub_title' => $request->offer_area_sub_title,
                'is_free_delivery' => $request->is_free_delivery,
                'status' => $request->status,
                'review_upper_area_title' => $request->review_upper_area_title, 
                'is_faq_show' => $request->is_faq_show, 
                'faqs' => $faqs, 
                'review_images' => $reviewImagePaths, 
                'review_videos' => $reviewVideoUrls,
                'description' => $request->description,
                'end_date' => Carbon::parse($request->end_date)->format('Y-m-d'),
            ]);

            $detailImages = $request->file('detail_image', []);
            $productDetails = [];
            foreach ($request->detail_product_id as $i => $productId) {
                $imagePath = null;
                if (!empty($detailImages[$i]) && $detailImages[$i]->isValid()) {
                    $file = $detailImages[$i];
                    $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                    $destinationPath = 'uploads/promotion/detail_images';
                    if (!is_dir($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    $file->move($destinationPath, $filename);
                    $imagePath = $destinationPath . '/' . $filename;
                }
                $productDetails[] = new PromotionTemplateDetail([
                    'product_id' => $productId,
                    'discount_type' => $request->discount_type[$i] ?? 'percentage',
                    'discount' => (float)($request->discount[$i] ?? 0),
                    'image' => $imagePath,
                ]);
            }
            $template->details()->saveMany($productDetails);

            DB::commit();
            return redirect()->route('promotion-template.index')->with('success', 'Promotion Template created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the Promotion Template: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $template = PromotionTemplate::where('slug', $slug)->firstOrFail();
        return view('promotion.promotion_template.show', compact('template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $template = PromotionTemplate::findOrFail(decrypt_value($id));
        $products = Product::with([
            'inventory:id,product_id,batch_no,selling_price,quantity'
        ])
            ->select('id', 'category_id', 'brand_id', 'name', 'image', 'discount', 'sort', 'rating')
            ->where('status', 1)
            ->whereHas('inventory', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->get();
        return view('promotion.promotion_template.edit', compact('template', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $template = PromotionTemplate::findOrFail(decrypt_value($id));

        $request->validate([
            'detail_product_id' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (count($value) !== count(array_unique($value))) {
                        $fail('একই প্রডাক্ট একাধিক রোতে সিলেক্ট করা যাবে না। প্রতিটি প্রডাক্ট শুধু একবার সিলেক্ট করুন।');
                    }
                },
            ],
            'detail_product_id.*' => 'required|exists:products,id',
            'detail_image' => 'nullable|array',
            'detail_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'discount_type' => 'required|array|min:1',
            'discount_type.*' => 'required|in:percentage,fixed',
            'discount' => 'required|array|min:1',
            'discount.*' => 'required|numeric|min:0',
            'slug' => ['required', 'string', 'max:255', Rule::unique('promotion_templates', 'slug')->ignore($template->id)],
            'promotion_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'main_heading' => 'required|string|max:255',
            'video_url' => 'required|url',
            'status' => 'required|boolean',
            'review_images' => 'nullable|array',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'existing_review_images' => 'nullable|array',
            'existing_review_images.*' => 'nullable|string',
            'review_videos' => 'nullable|array',
            'review_videos.*' => 'nullable|url|max:255',
        ]);

        DB::beginTransaction();

        try {
            // --- 2. Handle Single Image Replacement ---
            // dd($request->all());

            // Promotion Image
            $promotionImagePath = $template->promotion_image;
            if ($request->hasFile('promotion_image')) {
                // Delete old file if it exists
                if (file_exists($promotionImagePath)){
                    unlink($promotionImagePath);
                }
                // Upload new file
                $file = $request->file('promotion_image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'uploads/promotion/promotion_image';
                $file->move($destinationPath, $filename);
                $promotionImagePath = $destinationPath . '/' . $filename;
            }

            // --- 3. Handle Dynamic Review Images (Merge existing and new uploads) ---
            
            // ১. ইউজার যে পুরনো ইমেজগুলো 'রাখতে' চায় (যা ডিলিট করেনি)
            $existingImages = $request->input('existing_review_images', []);

            // এটি নিশ্চিত করে যে এরেতে কোনো নাল (null) বা খালি ভ্যালু নেই
            $reviewImagePaths = array_values(array_filter($existingImages));

            // ২. নতুন আপলোড করা ইমেজগুলো প্রসেস করা (Replacement বা Add More যাই হোক)
            if ($request->hasFile('review_images')) {
                foreach ($request->file('review_images') as $imageFile) {
                    if ($imageFile && $imageFile->isValid()) {
                        $filename = Uuid::uuid1()->toString() . '.' . $imageFile->getClientOriginalExtension();
                        $destinationPath = 'uploads/promotion/review_images';
                        
                        // public_path ব্যবহার করা ভালো
                        $imageFile->move(public_path($destinationPath), $filename);
                        $path = $destinationPath . '/' . $filename;
                        
                        // এরেতে নতুন পাথ যোগ করা
                        $reviewImagePaths[] = $path;
                    }
                }
            }

            // ৩. ডাটাবেস থেকে পুরনো ইমেজগুলো সংগ্রহ করা (ডিলিট করার জন্য চেক করা)
            $oldReviewImages = $template->review_images ?? [];
            if(is_string($oldReviewImages)) {
                $oldReviewImages = json_decode($oldReviewImages, true) ?: [];
            }

            // ৪. যেসব ইমেজ $reviewImagePaths-এ নেই সেগুলো সার্ভার থেকে ডিলিট করা
            $imagesToDelete = array_diff($oldReviewImages, $reviewImagePaths);
            foreach ($imagesToDelete as $imagePath) {
                if ($imagePath && file_exists(public_path($imagePath))) {
                    unlink(public_path($imagePath));
                }
            }
            // --- 4. Process JSON Fields ---
            
            // Review Videos
            $reviewVideoUrls = array_filter($request->input('review_videos', []));

            $questions = $request->faq_questions;
            $answers = $request->faq_answers;
            
            $faqs = [];
            if($questions) {
                foreach($questions as $key => $question) {
                    if(!empty($question)) { // শুধুমাত্র খালি না থাকলে সেভ হবে
                        $faqs[] = [
                            'question' => $question,
                            'answer' => $answers[$key] ?? ''
                        ];
                    }
                }
            }

            $finalVideoUrl = $request->video_url;
            if ($request->video_source == 'link') {
                // যদি ইউটিউব লিঙ্ক দেয়, তবে পুরনো ফাইল থাকলে সেটি ডিলিট করে দিন
                if ($template->video_url && file_exists(public_path($template->video_url))) {
                    unlink(public_path($template->video_url));
                }
            } else {
                // যদি নতুন ভিডিও ফাইল আপলোড করে
                if ($request->hasFile('video_file')) {
                    // পুরনো ফাইল ডিলিট করা
                    if ($template->video_url && file_exists(public_path($template->video_url))) {
                        unlink(public_path($template->video_url));
                    }
        
                    $file = $request->file('video_file');
                    $filename = time() . '.' . $file->getClientOriginalExtension();
                    $destinationPath = 'uploads/videos';
                    $file->move(public_path($destinationPath), $filename);
                    
                    $finalVideoUrl = $destinationPath . '/' . $filename;
                }
            }
            // --- 5. Update Main PromotionTemplate Data ---
            $template->update([
                'slug' => $request->slug,
                'promotion_image' => $promotionImagePath,
                'main_heading' => $request->main_heading,
                'video_url' => $finalVideoUrl,
                'offer_area_title' => $request->offer_area_title,
                'offer_area_sub_title' => $request->offer_area_sub_title,
                'is_free_delivery' => $request->is_free_delivery,
                'status' => $request->status,
                'description' => $request->description,
                'end_date' => Carbon::parse($request->end_date)->format('Y-m-d'),

                // JSON fields
                'review_upper_area_title' => $request->review_upper_area_title, 
                'is_faq_show' => $request->is_faq_show, 
                'faqs' => $faqs, 
                'review_images' => $reviewImagePaths, 
                'review_videos' => $reviewVideoUrls,
            ]);

            // --- 6. Sync PromotionTemplateDetail (product + discount_type + discount + image) ---
            $existingDetailImages = $request->input('existing_detail_image', []);
            $detailImages = $request->file('detail_image', []);
            $template->details()->delete();
            $productDetails = [];
            foreach ($request->detail_product_id as $i => $productId) {
                $imagePath = $existingDetailImages[$i] ?? null;
                if (!empty($detailImages[$i]) && $detailImages[$i]->isValid()) {
                    $file = $detailImages[$i];
                    $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                    $destinationPath = 'uploads/promotion/detail_images';
                    if (!is_dir($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    $file->move($destinationPath, $filename);
                    $imagePath = $destinationPath . '/' . $filename;
                }
                $productDetails[] = new PromotionTemplateDetail([
                    'product_id' => $productId,
                    'discount_type' => $request->discount_type[$i] ?? 'percentage',
                    'discount' => (float)($request->discount[$i] ?? 0),
                    'image' => $imagePath,
                ]);
            }
            $template->details()->saveMany($productDetails);

            DB::commit();

            return redirect()->route('promotion-template.index')->with('success', 'Promotion Template updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the Promotion Template: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $template = PromotionTemplate::findOrFail(decrypt_value($id));

        DB::beginTransaction();

        try {
            // Delete associated images
            if (file_exists($template->promotion_image)){
                unlink($template->promotion_image);
            }

            $reviewImages = $template->review_images ?? [];
            if(is_string($reviewImages)) {
                $reviewImages = json_decode($reviewImages, true);
            }
            foreach ($reviewImages as $imagePath) {
                if (file_exists($imagePath)){
                    unlink($imagePath);
                }
            }

            // Delete related details
            $template->details()->delete();

            // Delete the template itself
            $template->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Promotion Template deleted successfully.']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the Promotion Template: ' . $e->getMessage()], 500);
        }
    }
    public function orderSubmit(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'product_ids' => 'required|array|min:1',
            'qty' => 'required|array',
            'delivery_charge' => 'required|numeric',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'mobile' => 'required|string|max:20',
        ]);

        // Transaction শুরু করার আগে ডাটা প্রস্তুত করে নেওয়া
        DB::beginTransaction();
        try {
            $template = PromotionTemplate::findOrFail($id);
            $sisterConcernId = 1;

            // ১. কাস্টমার হ্যান্ডেল করা
            $customer = Customer::where('mobile_no', $request->mobile)->first();
            if (!$customer) {
                $customer = new Customer();
            }
            $customer->name = $request->name;
            $customer->sister_concern_id = $sisterConcernId;
            $customer->mobile_no = $request->mobile;
            $customer->address = $request->address;
            $customer->save();

            $user = User::where('mobile_no', $request->mobile)->first();
            if(!$user){
                $user = new User();
            }
            $user->name = $request->name;
            $user->mobile_no = $request->mobile;
            $user->username = $request->mobile;
            $user->role_id = 4;
            $user->email = null;
            $user->password = Hash::make($request->mobile);
            $user->customer_id = $customer->id;
            $user->sister_concern_id = 1;
            $user->save();

            // ২. ডুপ্লিকেট অর্ডার নম্বর এড়াতে লজিক (Race Condition Fix)
            // এখানে sharedLock বা lockForUpdate ব্যবহার করা যেতে পারে অথবা সিম্পল ইউনিক কোড
            $lastSalesOrder = SalesOrder::where('sister_concern_id', $sisterConcernId)
                ->where('sale_type', 3)
                ->where('is_promotion', 1)
                ->lockForUpdate() // ডাটাবেস লেভেলে রো লক করবে যতক্ষণ ট্রানজেকশন শেষ না হয়
                ->latest('id')->first();

            $nextCodeNumber = !empty($lastSalesOrder) ? intval(substr($lastSalesOrder->order_no, 7)) + 1 : 1;
            $newCodeOrder = 'PRSO-' . str_pad($nextCodeNumber, 10, '0', STR_PAD_LEFT);

            // ৩. SalesOrder তৈরি
            $order = new SalesOrder();
            $order->order_no = $newCodeOrder;
            $order->billing_name = $request->name;
            $order->billing_mobile_no = $request->mobile;
            $order->billing_address = $request->address;
            $order->sister_concern_id = $sisterConcernId;
            $order->customer_id = $customer->id;
            $order->sale_type = 3;
            $order->date = date('Y-m-d');
            $order->shipping_charge = $request->delivery_charge;
            $order->sub_total = 0;
            $order->discount = 0;
            $order->total = 0;
            $order->status = 0;
            $order->is_promotion = 1;
            $order->save();

            $subTotal = 0;
            $totalDiscount = 0;

            // ৪. সিলেক্ট করা প্রোডাক্ট প্রসেস করা
            foreach ($request->product_ids as $productId) {
                $batch_no = null;
                $detail = $template->details()->where('product_id', $productId)->first();
                if (!$detail) continue;

                $product = Product::findOrFail($productId);
                $inventory = PurchaseInventory::where('product_id', $productId)
                    ->where('quantity', '>', 0)
                    ->where('sister_concern_id', $sisterConcernId)
                    ->first();

                $userQty = $request->qty[$productId] ?? 1;
                $unitSellingPrice = $product->selling_price;
                
                // ৫. ডিসকাউন্ট ক্যালকুলেশন (Percentage এবং Amount দুইটাই সেভ করা)
                $discountPercentage = 0;
                $discountAmtPerUnit = 0;

                if ($detail->discount_type == 'percentage') {
                    $discountPercentage = $detail->discount;
                    $discountAmtPerUnit = ($unitSellingPrice * ($discountPercentage / 100));
                } else {
                    $discountAmtPerUnit = $detail->discount;
                    $discountPercentage = ($unitSellingPrice > 0) ? ($discountAmtPerUnit / $unitSellingPrice) * 100 : 0;
                }

                $totalDiscountForThisItem = $discountAmtPerUnit * $userQty;
                $lineTotal = $unitSellingPrice * $userQty;

                $salesOrderProduct = new SalesOrderProduct();
                $salesOrderProduct->sales_order_id = $order->id;
                $salesOrderProduct->product_id = $product->id;
                $salesOrderProduct->sister_concern_id = $sisterConcernId;
                $salesOrderProduct->name = $product->name;
                $salesOrderProduct->serial = $product->code;
                $salesOrderProduct->quantity = $userQty;
                $salesOrderProduct->unit_price = $unitSellingPrice;
                $salesOrderProduct->buy_price = $inventory->unit_price ?? 0;
                $salesOrderProduct->batch_no = $batch_no;
                
                // আপনার রিকোয়েস্ট অনুযায়ী দুই কলাম পূরণ
                $salesOrderProduct->discount_percentage = $discountPercentage;
                $salesOrderProduct->discount_amt = $totalDiscountForThisItem;
                
                $salesOrderProduct->total = $lineTotal;
                $salesOrderProduct->sale_type = 3;
                $salesOrderProduct->date = date('Y-m-d');
                $salesOrderProduct->user_id = $user->id;
                $salesOrderProduct->save();

                $subTotal += $lineTotal;
                $totalDiscount += $totalDiscountForThisItem;
            }

            // ৬. অর্ডারের ফাইনাল ক্যালকুলেশন
            $order->sub_total = $subTotal;
            $order->discount = floor($totalDiscount);
            $finalTotal = ($subTotal - floor($totalDiscount));
            $order->total = ceil($finalTotal);
            $order->due = ceil($finalTotal);
            $order->save();

            // লগ সেভ করা
            OrderLog::create([
                'order_type' => 'SALE',
                'order_id'  => $order->id,
                'created_by'  => $user->id,
                'status'  => 'ORDER CREATE',
            ]);

            DB::commit();
            return redirect()->route('promotion-template.order.success', ['order' => encrypt_value($order->id)]);

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
    public function success($orderId){
        $order = SalesOrder::find(decrypt_value($orderId));
        return view('promotion.order.success',compact('order'));
    }
}
