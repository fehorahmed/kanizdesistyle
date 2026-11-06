<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Filter;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Validation\Rule;
use App\Models\Unit;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Ramsey\Uuid\Uuid;

class EcomProductController extends Controller
{
    public function index() {
        return view('ecommerce.product.all');
    }

    public function dataTable()
    {
        $query = Product::with('unit','brand','category');
        if(request('offer') == 1){
            $query->where('discount','>',0);
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
                $btn = '';
                if(auth()->user()->can('product_edit')) {
                    $btn .='<a href="'.route('ecom_product.edit', ['product' => $product->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>&nbsp;';
                }
                if(auth()->user()->can('product_images')) {
                    $btn .='<a href="'.route('ecom_product.images', ['product' => $product->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit">Images</a>';
                }
                if(auth()->user()->can('product_images')) {
                    $btn .=' <a href="'.route('ecom_product.filters', ['product' => $product->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit">Filters</a>';
                }
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
             ->addColumn('is_show', function (Product $product) {
                $route = route('product.toggle_show', $product->id);
                $badgeClass = $product->is_show ? 'badge-success' : 'badge-danger';
                $label = $product->is_show ? 'Show' : 'Hide';
                return '<a href="' . $route . '" class="badge ' . $badgeClass . '">' . $label . '</a>';
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
            ->addColumn('pack_size', function(Product $product) {
                return $product->pack_size->name ?? '';
            })
            ->rawColumns(['action','is_show','status','image'])
            ->toJson();
    }

    public function add() {
        $units = Unit::where('status',1)->get();
        $brands = Brand::where('status',1)->whereNotIn('id',[0])->get();
        $categories = Category::where('status',1)->get();
        $maxSort = (Product::max('sort') ?? 0) + 1;
        // dd((Product::max('sort') ?? 0) + 1);

        return view('ecommerce.product.add',compact('units','brands','categories','maxSort'));
    }

    public function addPost(Request $request) {

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable',
            'sister_concern_id' => 'required',
            'brand' => 'nullable',
            'category' => 'nullable',
            'sub_category' => 'nullable',
            'unit' => 'required',
            'alert_quantity' => 'required',
            'selling_price' => 'required|min:0',
            'discount' => 'required|min:0',
            'is_featured' => 'nullable',
            'is_trending' => 'nullable',
            'is_best_selling' => 'nullable',
            'is_new_arrival' => 'nullable',
            'status' => 'required',
            'image' => [
                'nullable', 'mimes:jpg,png,jpeg,webp'
            ],
        ]);


        $lastProduct = Product::where('sister_concern_id',$request->sister_concern_id)->latest('id')->first();

        // Generate the next code
        $nextCodeNumber = !empty($lastProduct) ? intval(substr($lastProduct->code, 4)) + 1 : 1;

        // Format the new code as 'p-000001'
        if($request->sister_concern_id == 1){
            $newCode = 'P-' . str_pad($nextCodeNumber, 6, '0', STR_PAD_LEFT);
        }else{
            $newCode = 'P-' . str_pad($nextCodeNumber, 6, '0', STR_PAD_LEFT);
        }


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
            $product->name = $request->name;
            $product->slug = $slug;
            $product->code = $newCode;
            $product->alert_quantity = $request->alert_quantity;
            $product->sister_concern_id = $request->sister_concern_id;
            $product->unit_id = $request->unit;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->image = $image;
            $product->old_selling_price = $request->selling_price; 
            if($request->discount > 0){
                $discount_amount = floor($request->selling_price * ($request->discount / 100));
                $product->discount_percent = $request->discount;
                $product->discount = $discount_amount;
                $product->selling_price = $request->selling_price - $discount_amount;
            }else{
                $product->selling_price = $request->selling_price;
            }
            $product->youtube_video_link = $request->youtube_video_link;
            $product->is_featured = $request->is_featured;
            $product->is_trending = $request->is_trending;
            $product->is_best_selling = $request->is_best_selling;
            $product->is_new_arrival = $request->is_new_arrival;
            $product->is_show = $request->is_show;
            $product->status = $request->status;
            $product->sort = $request->sort;
            $product->user_id = Auth::id();
            $product->save();
            DB::commit();

            return redirect()->route('ecom_product')->with('message', 'Product add successfully.');
        }catch(Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function edit(Product $product) {
        $units = Unit::where('status',1)->get();
        $brands = Brand::where('status',1)->whereNotIn('id',[0])->get();
        $categories = Category::where('status',1)->get();

        return view('ecommerce.product.edit', compact('product','units','brands','categories'));
    }

    public function editPost(Product $product, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product->id),
            ],
            'description' => 'nullable',
            'short_description' => 'nullable',
            'sister_concern_id' => 'required',
            'brand' => 'nullable',
            'category' => 'nullable',
            'sub_category' => 'nullable',
            'unit' => 'required',
            'alert_quantity' => 'required',
            'selling_price' => 'required|min:0',
            'discount' => 'required|min:0',
            'is_featured' => 'nullable',
            'is_trending' => 'nullable',
            'is_best_selling' => 'nullable',
            'is_new_arrival' => 'nullable',
            'sort' => 'required',
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
            $product->name = $request->name;
            $product->slug = $slug;
            $product->sister_concern_id = $request->sister_concern_id;
            $product->unit_id = $request->unit;
            $product->brand_id = $request->brand;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->alert_quantity = $request->alert_quantity;
            $product->short_description = $request->short_description;
            $product->description = $request->description;
            $product->image = $image;
            $product->old_selling_price = $request->selling_price;
            if($request->discount > 0){
                // dd('dd');
                $discount_amount = floor($request->selling_price * ($request->discount / 100));
                $product->discount_percent = $request->discount;
                $product->discount = $discount_amount;
                $product->selling_price = $request->selling_price - $discount_amount;
            }else{
                $product->discount_percent = 0;
                $product->discount = 0;
                $product->selling_price = $request->selling_price;
            }
            $product->youtube_video_link = $request->youtube_video_link;
            $product->is_featured = $request->is_featured;
            $product->is_trending = $request->is_trending;
            $product->is_best_selling = $request->is_best_selling;
            $product->is_new_arrival = $request->is_new_arrival;
            $product->sort = $request->sort;
            $product->is_show = $request->is_show;
            $product->status = $request->status;
            $product->save();

            DB::commit();

            return redirect()->route('ecom_product')->with('message', 'Product edit successfully.');
        }catch(Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function productImages(Product $product) {
        $units = Unit::where('status',1)->get();
        $brands = Brand::where('status',1)->whereNotIn('id',[0])->get();
        $categories = Category::where('status',1)->get();

        return view('ecommerce.product.images', compact('product','units','brands','categories'));
    }
    public function productImagesUpload(Request $request){
        $request->validate([
            'file' => 'required|image'
        ]);
        //dd($request->all());
        try{
            $file = $request->file('file');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/product_images';
            $file->move($destinationPath, $filename);

            $path = 'uploads/product_images/'.$filename;

            $image = ProductImage::create([
                'product_id' => $request->product_id,
                'path' => $path
            ]);

            $image->fullPath = asset($path);

            return response()->json(['success' => true, 'data' => $image->toArray()]);
        }catch(Exception $exception){
            return response()->json(['success' => false, 'errors' => $exception->getMessage()]);
        }
    }
    public function productImagesPost(Product $product, Request $request) {
        $request->validate([
            'imagesId.*' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try{
            if ($request->imagesId) {
                for ($i = 0; $i < sizeof($request->imagesId); $i++) {
                    $image = ProductImage::where('id', $request->imagesId[$i])->first();
    
                    if ($image->product_id == null || $image->thumbs == null) {
                        $filename = Uuid::uuid1()->toString();
                        $ext = pathinfo($image->path, PATHINFO_EXTENSION);
    
                        $thumbsSavePath = 'uploads/product_images/thumbs/' . $filename . '.' . $ext;
    
                        // Thumbs Image
                        //$thumb = Image::make(public_path($image->path))->resize(650, 650);
                        //$thumb->save(public_path($thumbsSavePath), 50);
    
                        //$image->thumbs = $thumbsSavePath;
                    }
    
                    $image->product_id = $product->id;
                    $image->sort = $i + 1;
                    $image->save();
                }
    
                // Delete Images
                $images = ProductImage::where('product_id', $product->id)
                    ->whereNotIn('id', $request->imagesId)
                    ->get();
    
                foreach ($images as $image) {
                    if ($image->path != null){
                        if (file_exists($image->path)){
                            unlink($image->path);
                        }
                    }
                    // unlink(public_path($image->path));
    
                    if ($image->thumbs != null){
                        if (file_exists($image->path)){
                            unlink($image->thumbs);
                        }
                    }
                    // unlink(public_path($image->thumbs));
    
                    $image->delete();
                }
            }

            DB::commit();

            return redirect()->route('ecom_product')->with('message', 'Product Image Uploaded successfully.');
        }catch(Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function productFilters(Product $product)
    {
        // Get category filters with options
        $categoryFilters = $product->category
            ->filters()
            ->with('options')
            ->get();
            
        // Get existing selected filters for product
        $productFilterValues = $product->filterValues()->get(); // product_filter_values table

        return view('ecommerce.product.filters', compact('product', 'categoryFilters', 'productFilterValues'));
    }

    public function productFiltersPost(Product $product, Request $request)
    {
        DB::transaction(function () use ($product, $request) {

            $filters = $request->input('filters', []);
            
            foreach($filters as $filterId => $value) {
                $filter = Filter::find($filterId);
                if (!$filter) continue;

                if (in_array($filter->type, ['select','radio','checkbox'])) {
                    $optionId = $value;
                    $valueText = $filter->options()->find($optionId)->value ?? null;
                } else {
                    $optionId = null;
                    $valueText = $value;
                }
                // dd($optionId);
                // Update or create
                $product->filterValues()->updateOrCreate(
                    ['filter_id' => $filterId],
                    ['filter_option_id' => $optionId, 'value_text' => $valueText]
                );
            }
        });

        return redirect()->route('ecom_product')->with('success', 'Product filters updated successfully!');
    }
    public function productDeleteImage(Product $product)
    {
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image)); // ফাইলটি ফোল্ডার থেকে ডিলিট করা
        }

        $product->image = null; // ডাটাবেস থেকে ইমেজ কলাম খালি করা
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'ছবিটি সফলভাবে ডিলিট করা হয়েছে।'
        ]);
    }
}
