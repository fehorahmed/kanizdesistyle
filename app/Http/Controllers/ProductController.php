<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\Ebook;
use App\Models\EbookReview;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\PurchaseInventory;
use App\Models\SubCategory;
use App\Models\Unit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Ramsey\Uuid\Uuid;

class ProductController extends Controller
{
    public function index() {
        $categories = Category::where('status',1)->get();
        $brands = Brand::where('status',1)->get();

        // $products = Product::all();
        // foreach($products as $product){
        //     $product->vat_percent = 0;
        //     $product->save();
        // }
        // dd('done');
        return view('purchase.product.all',compact('categories','brands'));
    }
    public function dataTable(Request $request)
    {
        $query = Product::with('unit','brand','category')->orderBy('id','desc');
        if($request->category != ''){
            $query->where('category_id',$request->category);
        }
        if($request->sub_category != ''){
            $query->where('sub_category_id',$request->sub_category);
        }
        if($request->brand != ''){
            $query->where('brand_id',$request->brand);
        }
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->filter(function ($query) {
                if (request()->has('search') && $search = request('search')['value']) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "{$search}%");
                    });
                }
            })
            ->addColumn('action', function(Product $product) {
                $btn ='<a href="'.route('product.edit', ['product' => $product->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                return $btn;
            })
            ->editColumn('image', function (Product $product) {
                $preview_html = '';
                $preview_html .= '<div class="zoom-gallery">';
                $preview_html .= '<a class="image_preview" title="'. $product->name .'" data-source="'. asset(file_exists($product->image) ? $product->image : 'img/no-image.webp') .'" href="'. asset(file_exists($product->image) ? $product->image : 'img/no-image.webp') .'"><img height="50px" src="'.asset(file_exists($product->image) ? $product->image : 'img/no-image.webp').'"></a>';
                if($product->images){
                    foreach($product->images as $image){
                        $preview_html .= '<a class="image_preview" title="'. $product->name .'" data-source="'. asset(file_exists($image->path) ? $image->path : 'img/no-image.webp') .'" href="'. asset(file_exists($image->path) ? $image->path : 'img/no-image.webp') .'"><img height="0px" src="'.asset(file_exists($image->path) ? $image->path : 'img/no-image.webp').'"></a>';
                    }
                }
                $preview_html .= '</div>';
                return $preview_html;
            })
            ->addColumn('status', function (Product $product) {
                $route = route('product.toggle_status', $product->id);
                $badgeClass = $product->status ? 'badge-success' : 'badge-danger';
                $label = $product->status ? 'Active' : 'Inactive';
                return '<a href="' . $route . '" class="badge ' . $badgeClass . '">' . $label . '</a>';
            })
            ->addColumn('brand', function(Product $product) {
                return $product->brand->name ?? '';
            })
            ->addColumn('category', function(Product $product) {
                return $product->category->name ?? '';
            })
            ->addColumn('unit', function(Product $product) {
                return $product->unit->name ?? '';
            })
            ->rawColumns(['action','image','status'])
            ->toJson();
    }

    public function add() {
        $units = Unit::where('type',1)->where('status',1)->get();
        $brands = Brand::where('status',1)->whereNotIn('id',[0])->get();
        $categories = Category::where('status',1)->get();

        return view('purchase.product.add',compact('units','brands','categories'));
    }

    public function addPost(Request $request) {

        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable',
            'brand' => 'nullable',
            'category' => 'required',
            'sub_category' => 'nullable',
            'unit' => 'required',
            'alert_quantity' => 'required',
            'status' => 'required',
            'image' => [
                'nullable', 'mimes:jpg,png,jpeg,webp'
            ],
        ]);


        // $lastProduct = Product::where('sister_concern_id',1)->where('category_id',$request->category)->latest('id')->first();
        $lastProduct = Product::where('sister_concern_id', 1)
            ->where('category_id', $request->category)
            ->where('code', 'like', '%-%') // নিশ্চিত করুন কোডে হাইফেন আছে
            ->orderByRaw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED) DESC')
            ->first();

        $category = Category::findOrFail($request->category);
        // Generate the next code
        if ($lastProduct && preg_match('/-(\d+)$/', $lastProduct->code, $matches)) {
            $nextCodeNumber = intval($matches[1]) + 1;
        } else {
            $nextCodeNumber = 1;
        }

        // Format the new code as 'p-0001'
        $newCode = $category->prefix . '-' . str_pad($nextCodeNumber, 4, '0', STR_PAD_LEFT);


        $image = null; 
        if ($request->hasFile('image')){
            // Upload Image
            $file = $request->file('image');
            $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
            $destinationPath = 'uploads/product/image';
            // $file->move(public_path($destinationPath), $filename);
            $file->move($destinationPath, $filename);
            $image = 'uploads/product/image/' . $filename;
        }
        DB::beginTransaction();
        try{
            $slug = $request->slug ?: Str::slug($request->name);
            $product = new Product();
            $product->sister_concern_id = 1;
            $product->name = $request->name;
            $product->slug = $slug;
            $product->code = $newCode;
            $product->alert_quantity = $request->alert_quantity;
            //$product->vat_percent = $request->vat_percent;
            $product->unit_id = $request->unit;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->description = $request->description;
            $product->image = $image;
            $product->status = $request->status;
            $product->user_id = Auth::id();;
            $product->save();
            DB::commit();

            return redirect()->route('product')->with('message', 'Product add successfully.');
        }catch(Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function addInstantPost(Request $request) {

        $rules = [
            'name' => 'required|string|max:255|unique:products,name',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable',
            'brand' => 'nullable',
            'category' => 'required',
            'sub_category' => 'nullable',
            'unit' => 'required',
            'alert_quantity' => 'required',
            'status' => 'required',
            'image' => [
                'nullable', 'mimes:jpg,png,jpeg,webp'
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $checkProduct = Product::where('name',$request->name)->first();
        if ($checkProduct){
            return response()->json(['success' => false, 'message' => 'Product already exists!']);
        }
        $lastProduct = Product::where('sister_concern_id',1)->latest('id')->first();

        // Generate the next code
        $nextCodeNumber = !empty($lastProduct) ? intval(substr($lastProduct->code, 4)) + 1 : 1;

        // Format the new code as 'p-000001'
        $newCode = 'P-' . str_pad($nextCodeNumber, 6, '0', STR_PAD_LEFT);

        try{
            $image = null;
            if ($request->hasFile('image')){
                // Upload Image
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
                $destinationPath = 'uploads/product/image';
                // $file->move(public_path($destinationPath), $filename);
                $file->move($destinationPath, $filename);
                $image = 'uploads/product/image/' . $filename;
            }
            $slug = $request->slug ?: Str::slug($request->name);
            $product = new Product();
            $product->sister_concern_id = 1;
            $product->name = $request->name;
            $product->slug = $slug;
            $product->code = $newCode;
            $product->alert_quantity = $request->alert_quantity;
            $product->selling_price = $request->selling_price;
            //$product->vat_percent = $request->vat_percent;
            $product->unit_id = $request->unit;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->description = $request->description;
            $product->image = $image;
            $product->status = $request->status;
            $product->user_id = Auth::id();
            $product->save();

            return response()->json(['success' => true, 'message' => 'added','product'=>$product]);
        }catch(Exception $exception){
            return response()->json(['success'=>false,'message'=>$exception->getMessage()]);
        }
    }

    public function edit(Product $product) {
        $units = Unit::where('type',1)->where('status',1)->get();
        $brands = Brand::where('status',1)->whereNotIn('id',[0])->get();
        $categories = Category::where('status',1)->get();

        return view('purchase.product.edit', compact('product','units','brands','categories'));
    }

    public function editPost(Product $product, Request $request) {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($product->id),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product->id),
            ],
            'description' => 'nullable|string|max:255',
            'brand' => 'nullable',
            'category' => 'required',
            'sub_category' => 'nullable',
            'unit' => 'required',
            'alert_quantity' => 'required',
            'status' => 'required',
            'image' => [
                'nullable', 'mimes:jpg,png,jpeg,webp'
            ],

        ]);

        $image = $product->image;
        if ($request->hasFile('image')){
            if (file_exists($product->image)){
                unlink($product->image);
            }
            // Upload Image
            $file = $request->file('image');
            $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
            $destinationPath = 'uploads/product/image';
            // $file->move(public_path($destinationPath), $filename);
            $file->move($destinationPath, $filename);
            $image = 'uploads/product/image/' . $filename;
        }
        DB::beginTransaction();
        try{
            $slug = $request->slug ?: Str::slug($request->name);
            $product->sister_concern_id = 1;
            $product->name = $request->name;
            $product->slug = $slug;
            $product->unit_id = $request->unit;
            $product->brand_id = $request->brand;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->selling_price = $request->selling_price;
            $product->alert_quantity = $request->alert_quantity;
            //$product->vat_percent = $request->vat_percent;
            $product->description = $request->description;
            $product->image = $image;
            $product->status = $request->status;
            $product->save();

            DB::commit();

            return redirect()->route('product')->with('message', 'Product edit successfully.');
        }catch(Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function productSubCategory(Request $request){
        try{
            $sub_categories = SubCategory::where('category_id',$request->category)->get()->toArray();
            return response()->json(['status'=>true,'sub_categories'=>$sub_categories]);
        }catch(Exception $exception){
            return response()->json(['status'=>false,'sub_categories'=>'']);
        }
    }
    public function getSubCategoryWithProduct(Request $request){
        try{
            $sub_categories = SubCategory::with('products')->where('category_id',$request->category)->get();
            $sub_category_with_product_view = view('purchase.product.subcategory_wise_product_view',compact('sub_categories'))->render();
            return response()->json(['status'=>true,'html_view'=>$sub_category_with_product_view]);
        }catch(Exception $exception){
            return response()->json(['status'=>false,'html_view'=>'']);
        }
    }
    public function discountSettings(){
        $categories = Category::where('status',1)->get();
        return view('purchase.product.discount',compact('categories'));
    }
    public function discountSettingsPost(Request $request){
        $request->validate([
            'discount' => 'required|numeric|min:.1',
            'product' => 'required|array',
        ]);
        
        $productIds = $request->product;
        foreach($productIds as $productId){
            $product = Product::find($productId);
            if($request->discount > 0){
                $discount_amount = floor($product->selling_price * ($request->discount / 100));
                $product->discount_percent = $request->discount;
                $product->discount = $discount_amount;
                $product->selling_price = $product->selling_price - $discount_amount;
            }
            $product->save();
        }
        return redirect()->route('product')->with('message', 'Product discount added successfully.');
    }
    public function productReview(Request $request){
        // dd($request->all());
        $validated = $request->validate([
            'product_id' => 'required',
            'rating' => 'required',
            'comment' => 'required|string|regex:/^(?!.*<script.*>)(?!.*<\/script>).*$/',
        ]);
        DB::beginTransaction();
        try{
            $productReview = new ProductReview();
            $productReview->user_id = auth()->user()->id;
            $productReview->product_id = $request->product_id;
            $productReview->rating = $request->rating;
            $productReview->comment = $request->comment;
            $productReview->save();

            $product = Product::find($request->product_id);
            $product->total_review += 1;
            $product->total_rating += $productReview->rating;
            $product->save();
            $product->rating = $product->total_rating / $product->total_review;
            $product->save();

            $proReview = ProductReview::with(['user:id,name'])->find($productReview->id);

            DB::commit();
            return response()->json(['message' => 'Review Added successful!','productReview'=>$proReview], 201);
        }catch(Exception $exception){
            DB::rollBack();
            return response()->json(['errors' => $exception->getMessage()], 500);
        }
    }
    public function ebookReview(Request $request){
        // dd($request->all());
        $validated = $request->validate([
            'ebook_id' => 'required',
            'rating' => 'required',
            'comment' => 'required|string|regex:/^(?!.*<script.*>)(?!.*<\/script>).*$/',
        ]);
        DB::beginTransaction();
        try{
            $ebookReview = new EbookReview();
            $ebookReview->user_id = auth()->user()->id;
            $ebookReview->ebook_id = $request->ebook_id;
            $ebookReview->rating = $request->rating;
            $ebookReview->comment = $request->comment;
            $ebookReview->save();

            $ebook = Ebook::find($request->ebook_id);
            $ebook->total_review += 1;
            $ebook->total_rating += $ebookReview->rating;
            $ebook->save();
            $ebook->rating = $ebook->total_rating / $ebook->total_review;
            $ebook->save();

            $proReview = ProductReview::with(['user:id,name'])->find($ebookReview->id);

            DB::commit();
            return response()->json(['message' => 'Review Added successful!','productReview'=>$proReview], 201);
        }catch(Exception $exception){
            DB::rollBack();
            return response()->json(['errors' => $exception->getMessage()], 500);
        }
    }
    public function toggleShow(Product $product)
    {
        try {
            if ($product) {
                // Toggle university status
                $newStatus = $product->is_show == 0 ? 1 : 0;
                // dd($newStatus);
                $product->is_show = $newStatus;
                $product->update();

                return redirect()->back()->with('success', 'Product Show Status Updated Successfully!');
            } else {
                return redirect()->back()->with('error', 'Product Not Found!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
    public function toggleStatus(Product $product)
    {
        try {
            if ($product) {
                // Toggle university status
                $newStatus = $product->status == 0 ? 1 : 0;
                $product->status = $newStatus;
                $product->update();

                return redirect()->back()->with('success', 'Product Status Updated Successfully!');
            } else {
                return redirect()->back()->with('error', 'Product Not Found!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
    public function getProducts(Request $request){
        $products = Product::select('id','name','category_id','status')->where('category_id',$request->category)->where('status',1)->get();
        
        return response()->json([
            'status'=>true,
            'products'=>$products
        ]);
    }
    public function getProductBatches(Request $request)
    {
        $productId = $request->product;

        $batches = PurchaseInventory::where('product_id', $productId)->get(['id', 'batch_no']);

        return response()->json([
            'status' => true,
            'batches' => $batches
        ]);
    }
}
