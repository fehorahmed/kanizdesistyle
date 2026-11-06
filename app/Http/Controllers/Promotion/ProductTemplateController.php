<?php

namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OrderLog;
use App\Models\Product;
use App\Models\Promotion\ProductTemplate;
use App\Models\Promotion\ProductTemplateDetail;
use App\Models\PurchaseInventory;
use App\Models\SalesOrder;
use App\Models\SalesOrderFreeProduct;
use App\Models\SalesOrderProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ProductTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('promotion.product_template.index');
    }
    public function dataTable()
    {
        $query = ProductTemplate::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('products', function(ProductTemplate $productTemplate) {
                $products = $productTemplate->details;
                $productHtml = '<ul>';
                foreach ($products as $detail) {
                     $productHtml .= '<li>' . ($detail->product->name ?? '') . '</li>'; // Load the related product
                }
                $productHtml .= '</ul>';
                return $productHtml;
            })
            ->addColumn('action', function(ProductTemplate $productTemplate) {
                $btn = '';
                $btn .= '<a href="' . route('product-template.edit', ['product_template' => encrypt_value($productTemplate->id)]) . '" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                $btn .= ' <a href="' . route('product-promotion.show', ['slug' => $productTemplate->slug]) . '" target="_blank" class="btn btn-primary bg-gradient-primary btn-sm btn-show"><i class="fa fa-eye"></i></a>';
                $btn .= ' <a role="button" data-id="' . $productTemplate->id . '" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></a>';
                return $btn;
            })
            ->rawColumns(['products','action'])
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
        return view('promotion.product_template.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
            'slug' => 'required|string|max:255|unique:product_templates,slug',
            'promotion_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'main_heading' => 'required|string|max:255',
            'video_url' => 'required|url',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'certificate_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|boolean',
            
            // Validation for repeatable fields (images must be files, videos must be URLs)
            'review_images' => 'nullable|array',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'review_videos' => 'nullable|array',
            'review_videos.*' => 'nullable|url|max:255',
            'buy_quantity' => 'required|array|min:1',
            'buy_quantity.*' => 'required|integer|min:1',
            'free_quantity' => 'required|array|min:0',
            'free_quantity.*' => 'required|integer|min:0',
            'free_product' => 'required|array',
            'free_product.*' => 'nullable',
            'offer_type' => 'required|array',
            'offer_type.*' => 'required|string',
            'discount_percent' => 'required|array|min:1',
            'discount_percent.*' => 'required|numeric|min:0|max:100',
        ]);

        // Start a database transaction
        DB::beginTransaction();

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
            $certificateImagePath = null; 
            if ($request->hasFile('certificate_image')){
                // Upload Image
                $file = $request->file('certificate_image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'uploads/promotion/certificate_image';
                // $file->move(public_path($destinationPath), $filename);
                $file->move($destinationPath, $filename);
                $certificateImagePath = 'uploads/promotion/certificate_image/' . $filename;
            }
            
            $certificateImagePath = null; 
            if ($request->hasFile('certificate_image')){
                // Upload Image
                $file = $request->file('certificate_image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'uploads/promotion/certificate_image';
                // $file->move(public_path($destinationPath), $filename);
                $file->move($destinationPath, $filename);
                $certificateImagePath = 'uploads/promotion/certificate_image/' . $filename;
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

            $benefits = $request->input('benefits', []);
            $benefits = array_filter($benefits);

            $template = ProductTemplate::create([
                'product_combo_name' => $request->product_combo_name,
                'slug' => $request->slug,
                'promotion_image' => $promotionImagePath,
                'main_heading' => $request->main_heading,
                'video_url' => $request->video_url,
                'offer_area_title' => $request->offer_area_title,
                'offer_area_sub_title' => $request->offer_area_sub_title,
                'is_show_certificate' => $request->is_show_certificate,
                'certificate_area_title' => $request->certificate_area_title,
                'certificate_area_subtitle' => $request->certificate_area_subtitle,
                'certificate_image' => $certificateImagePath,
                'certificate_area_bottom_title' => $request->certificate_area_bottom_title,
                'is_free_delivery' => $request->is_free_delivery,
                'status' => $request->status,
                'review_upper_area_title' => $request->review_upper_area_title, 
                'benefits_title' => $request->benefits_title, 
                'benefits' => $benefits, 
                'review_images' => $reviewImagePaths, 
                'review_videos' => $reviewVideoUrls,
                'end_date' => Carbon::parse($request->end_date)->format('Y-m-d'),
            ]);

            $productDetails = [];
            foreach ($request->products as $productId) {
                $productDetails[] = new ProductTemplateDetail(['product_id' => $productId]);
            }
            $template->details()->saveMany($productDetails);

            $newBundles = [];
            for ($i = 0; $i < count($request->buy_quantity); $i++) {
                $newBundles[] = [
                    'buy_quantity' => (int)$request->buy_quantity[$i],
                    'offer_type' => (string)$request->offer_type[$i],
                    'free_quantity' => (int)$request->free_quantity[$i],
                    'free_product_id' => (int)$request->free_product[$i],
                    'discount_percent' => (float)$request->discount_percent[$i],
                ];
            }
            $template->bundles()->createMany($newBundles);

            DB::commit();
            return redirect()->route('product-template.index')->with('success', 'Product Template created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the Product Template: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $template = ProductTemplate::where('slug',$slug)->firstOrFail();
        return view('promotion.product_template.show',compact('template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $template = ProductTemplate::findOrFail(decrypt_value($id));
        $products = Product::with([
            'inventory:id,product_id,batch_no,selling_price,quantity'
        ])
        ->select('id','category_id','brand_id','name','image','discount','sort','rating')
        ->where('status', 1)
        ->whereHas('inventory', function($q) {
            $q->where('quantity', '>', 0);
        })
        ->get();
        return view('promotion.product_template.edit',compact('template','products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $template = ProductTemplate::findOrFail(decrypt_value($id));
        // 1. Validation
        
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
            
            // Allow the slug to be the same as the current template's slug
            'slug' => ['required', 'string', 'max:255', Rule::unique('product_templates', 'slug')->ignore($template->id)], 
            
            // Images are now nullable if the user doesn't upload a new one
            'promotion_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'certificate_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            
            'main_heading' => 'required|string|max:255',
            'video_url' => 'required|url',
            'status' => 'required|boolean',
            
            // Validation for repeatable fields
            'buy_quantity' => 'required|array|min:1',
            'buy_quantity.*' => 'required|integer|min:1',
            'free_quantity' => 'required|array|min:0',
            'free_quantity.*' => 'required|integer|min:0',
            'free_product' => 'required|array',
            'free_product.*' => 'nullable',
            'offer_type' => 'required|array',
            'offer_type.*' => 'required|string',
            'discount_percent' => 'required|array|min:1',
            'discount_percent.*' => 'required|numeric|min:0|max:100',
            'benefits' => 'nullable|array',
            'benefits.*' => 'nullable|string|max:255',
            
            // Review Images: New uploads are validated, existing paths are passed in hidden fields
            'review_images' => 'nullable|array',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'existing_review_images' => 'nullable|array', // Hidden fields from the form
            'existing_review_images.*' => 'nullable|string',

            'review_videos' => 'nullable|array',
            'review_videos.*' => 'nullable|url|max:255',
        ]);
        // dd($template);
        // Check for matching counts on dynamic fields
        if (count($request->buy_quantity) !== count($request->discount_percent)) {
            return back()->withInput()->with('error', 'Quantity and Discount fields must match in number.');
        }

        DB::beginTransaction();

        try {
            // --- 2. Handle Single Image Replacement ---
            

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
            // Certificate Image
            $certificateImagePath = $template->certificate_image;
            if ($request->hasFile('certificate_image')) {
                // Delete old file if it exists
                if (file_exists($certificateImagePath)){
                    unlink($certificateImagePath);
                }
                // Upload new file
                $file = $request->file('certificate_image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'uploads/promotion/certificate_image';
                $file->move($destinationPath, $filename);
                $certificateImagePath = $destinationPath . '/' . $filename;
            }

            // --- 3. Handle Dynamic Review Images (Merge existing and new uploads) ---
            
            // Start with paths of images the user chose to keep (passed in hidden fields)
            $reviewImagePaths = $request->input('existing_review_images', []);

            // Handle new uploads
            if ($request->hasFile('review_images')) {
                foreach ($request->file('review_images') as $imageFile) {
                    if ($imageFile && $imageFile->isValid()) {
                        $file = $imageFile;
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'uploads/promotion/review_images';
                        $file->move($destinationPath, $filename);
                        $path = $destinationPath . '/' . $filename;
                        
                        $reviewImagePaths[] = $path;
                    }
                }
            }
            
            // Identify and delete any images removed by the user (not present in $reviewImagePaths)
            $oldReviewImages = $template->review_images ?? [];
            if(is_string($oldReviewImages)) {
                $oldReviewImages = json_decode($oldReviewImages, true);
            }

            $imagesToDelete = array_diff($oldReviewImages, $reviewImagePaths);
            foreach ($imagesToDelete as $imagePath) {
                if (file_exists($imagePath)){
                    unlink($imagePath);
                }
            }

            // --- 4. Process JSON Fields ---
            
            // Review Videos
            $reviewVideoUrls = array_filter($request->input('review_videos', []));

            // Benefits
            $benefits = array_filter($request->input('benefits', []));

            // --- 5. Update Main ProductTemplate Data ---
            $template->update([
                'product_combo_name' => $request->product_combo_name,
                'slug' => $request->slug,
                'promotion_image' => $promotionImagePath,
                'main_heading' => $request->main_heading,
                'video_url' => $request->video_url,
                'offer_area_title' => $request->offer_area_title,
                'offer_area_sub_title' => $request->offer_area_sub_title,
                'is_show_certificate' => $request->is_show_certificate,
                'certificate_area_title' => $request->certificate_area_title,
                'certificate_area_subtitle' => $request->certificate_area_subtitle,
                'certificate_image' => $certificateImagePath,
                'certificate_area_bottom_title' => $request->certificate_area_bottom_title,
                'is_free_delivery' => $request->is_free_delivery,
                'status' => $request->status,
                'end_date' => Carbon::parse($request->end_date)->format('Y-m-d'),

                // JSON fields
                'review_upper_area_title' => $request->review_upper_area_title, 
                'benefits_title' => $request->benefits_title, 
                'benefits' => $benefits, 
                'review_images' => $reviewImagePaths, 
                'review_videos' => $reviewVideoUrls,
            ]);

            // --- 6. Sync Multiple Selected Products (ProductTemplateDetail) ---
            // Detach all old records and attach the new ones
            $template->details()->delete(); 
            $productDetails = [];
            foreach ($request->products as $productId) {
                $productDetails[] = new ProductTemplateDetail(['product_id' => $productId]);
            }
            $template->details()->saveMany($productDetails);

            $newBundles = [];
            for ($i = 0; $i < count($request->buy_quantity); $i++) {
                $newBundles[] = [
                    'buy_quantity' => (int)$request->buy_quantity[$i],
                    'offer_type' => (string)$request->offer_type[$i],
                    'free_quantity' => (int)$request->free_quantity[$i],
                    'free_product_id' => (int)$request->free_product[$i],
                    'discount_percent' => (float)$request->discount_percent[$i],
                ];
            }
            $template->bundles()->delete();
            $template->bundles()->createMany($newBundles);

            DB::commit();

            return redirect()->route('product-template.index')->with('success', 'Product Template updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage()); // Debugging tool - remove in production
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the Product Template: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $template = ProductTemplate::findOrFail(decrypt_value($id));

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

            // Delete related details and bundles
            $template->details()->delete();
            $template->bundles()->delete();

            // Delete the template itself
            $template->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Product Template deleted successfully.']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the Product Template: '.$e->getMessage()], 500);
        }
    }
    public function orderSubmit(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'product_package' => 'required',
            'delivery_charge' => 'required',
            'package_quantity' => 'required',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'mobile' => 'required|string|max:20',
        ]);
        DB::beginTransaction();
        try {
            $template = ProductTemplate::findOrFail($id);
            
            $bundle = $template->bundles()->findOrFail($request->input('product_package'));
            
            $templateItems = $template->details;
            // dd($templateItems);
            $request['sister_concern_id'] = 1;

            // Handle Customer
            $customer = Customer::where('mobile_no', $request->mobile)->first();
            if ($customer) {
                $request['customer'] = $customer->id;
            }else {
                $customer = new Customer();
            }
            
            $customer->name = $request->name;
            $customer->sister_concern_id = $request->sister_concern_id;
            $customer->mobile_no = $request->mobile;
            $customer->address = $request->address;
            $customer->save();

            $request['customer'] = $customer->id;

            // Generate Order No
            $lastSalesOrder = SalesOrder::where('sister_concern_id', 1)
                ->where('sale_type', 3)
                ->where('is_promotion', 1)
                ->latest('id')->first();

            $nextCodeNumber = !empty($lastSalesOrder) ? intval(substr($lastSalesOrder->order_no, 7)) + 1 : 1;
            $newCodeOrder = 'PRSO-' . str_pad($nextCodeNumber, 10, '0', STR_PAD_LEFT);

            // Create SalesOrder
            $order = new SalesOrder();
            $order->order_no = $newCodeOrder;
            //$order->billing_name = $request->name;
            //$order->billing_mobile_no = $request->mobile;
            //$order->billing_address = $request->address;
            //$order->payment_method = $request->payment_method;
            $order->sister_concern_id = $request->sister_concern_id;
            $order->customer_id = $request->customer;
            $order->sale_type = 3; // Ecommerce
            $order->received_by = '';
            $order->date = date('Y-m-d');
            $order->sub_total = 0;
            $order->discount = 0;
            $order->shipping_charge = $request->delivery_charge ?? 0;
            $order->total = 0;
            $order->paid = 0;
            $order->due = 0;
            $order->opening_sale = 0;
            $order->note = 'Ecommerce Promotion';
            $order->user_id = null;
            $order->status = 0; // Initial
            $order->is_promotion = 1;
            $order->save();

            // Order Log
            $orderLog = new OrderLog();
            $orderLog->order_type = 'SALE';
            $orderLog->order_id  = $order->id;
            $orderLog->created_by  = null;
            $orderLog->status  = 'ORDER CREATE';
            $orderLog->save();

            // Process Cart Items
            $subTotal = 0;
            $totalDiscount = 0;

            foreach ($templateItems as $item) {
                $product = Product::findOrFail($item->product_id);
                $inventory = PurchaseInventory::where('product_id', $product->id)
                    ->where('quantity', '>', 0)
                    ->where('sister_concern_id', 1)
                    ->first();

                $discount_amount = (($bundle->discount_percent / 100) * $inventory->selling_price) * ($bundle->buy_quantity * $request->package_quantity);

                $salesOrderProduct = new SalesOrderProduct();
                $salesOrderProduct->sister_concern_id = 1;
                $salesOrderProduct->product_id = $product->id;
                $salesOrderProduct->sales_order_id = $order->id;
                $salesOrderProduct->batch_no = $inventory->batch_no;
                $salesOrderProduct->name = $product->name;
                $salesOrderProduct->serial = $product->code;
                $salesOrderProduct->sale_type = 3;
                $salesOrderProduct->quantity = ($bundle->buy_quantity * $request->package_quantity);
                $salesOrderProduct->discount_percentage = $bundle->discount_percent;
                $salesOrderProduct->discount_amt = $discount_amount;
                $salesOrderProduct->unit_price = $inventory->selling_price ?? 0;
                $salesOrderProduct->buy_price = $inventory->unit_price ?? 0;
                $salesOrderProduct->date = date('Y-m-d');
                $salesOrderProduct->total = $inventory->selling_price * ($bundle->buy_quantity * $request->package_quantity);
                $salesOrderProduct->user_id = Auth::id();
                $salesOrderProduct->save();

                $subTotal += $inventory->selling_price * ($bundle->buy_quantity * $request->package_quantity);
                $totalDiscount += $discount_amount;
            }
            // dd('jj');
            if($bundle->offer_type == 'buy_x_free_y' && $bundle->free_quantity > 0 && $bundle->free_product_id != 0){
                $freeproduct = Product::findOrFail($bundle->free_product_id);
                $freeinventory = PurchaseInventory::where('product_id', $freeproduct->id)
                    ->where('sister_concern_id', 1)
                    ->where('quantity', '>', 0)
                    ->first();

                $salesOrderProduct = new SalesOrderProduct();
                $salesOrderProduct->sister_concern_id = 1;
                $salesOrderProduct->promotion_product_type = 'free';
                $salesOrderProduct->product_id = $freeproduct->id;
                $salesOrderProduct->sales_order_id = $order->id;
                $salesOrderProduct->batch_no = $freeinventory->batch_no;
                $salesOrderProduct->name = $freeproduct->name;
                $salesOrderProduct->serial = $freeproduct->code;
                $salesOrderProduct->sale_type = 3;
                $salesOrderProduct->quantity = ($bundle->free_quantity * $request->package_quantity);
                $salesOrderProduct->discount_percentage = 0;
                $salesOrderProduct->discount_amt = 0;
                $salesOrderProduct->unit_price = 0;
                $salesOrderProduct->buy_price = 0;
                $salesOrderProduct->selling_free_price = $freeinventory->selling_price ?? 0;
                $salesOrderProduct->date = date('Y-m-d');
                $salesOrderProduct->total = $freeinventory->selling_price * ($bundle->free_quantity * $request->package_quantity);
                $salesOrderProduct->user_id = null;
                $salesOrderProduct->save();
            }

            // Update Order totals
            $order->sub_total = $subTotal;
            $total = $subTotal - floor($totalDiscount);
            $order->discount = floor($totalDiscount);
            $order->total = ceil($total);
            $order->due = ceil($total);
            $order->save();

            DB::commit();
            return redirect()->route('product-template.order.success',['order'=>$order->id])->with('success', 'Order submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while submitting the order: ' . $e->getMessage());
        }
    }
    public function success(SalesOrder $order){
        return view('promotion.order.success',compact('order'));
    }
}
