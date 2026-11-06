<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('purchase.brand.index');
    }
    public function dataTable()
    {
        $query = Brand::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Brand $brand) {
                $btn = '';
                $btn .= '<a href="' . route('brand.edit', ['brand' => $brand->id]) . '" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                $btn .= ' <a role="button" data-id="' . $brand->id . '" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></a>';
                return $btn;
            })
            ->addColumn('image',function(Brand $brand){
                if($brand->image){
                    return '<img src="'. asset($brand->image) .'" width="35"/>';
                }
            })
            ->addColumn('status', function (Brand $brand) {
                $route = route('brand.toggle_status', $brand->id);
                $badgeClass = $brand->status ? 'badge-success' : 'badge-danger';
                $label = $brand->status ? 'Active' : 'Inactive';
                return '<a href="' . $route . '" class="badge ' . $badgeClass . '">' . $label . '</a>';
            })
            ->rawColumns(['action','image','status'])
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        return view('purchase.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' =>[
                'required','max:255',
                Rule::unique('brands')
            ],
            'image' => [
                'nullable', 'mimes:jpg,png,jpeg,webp'
            ],
            'status' => 'required|boolean', // Ensure 'status' is boolean
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {

            // Create a new Product record in the database
            $image = null;
            if ($request->hasFile('image')){
                // Upload Image
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
                $destinationPath = 'uploads/brand/image';
                // $file->move(public_path($destinationPath), $filename);
                $file->move($destinationPath, $filename);
                $image = 'uploads/brand/image/' . $filename;
            }
            $validatedData['image'] = $image;
            $validatedData['sister_concern_id'] = 1;
            Brand::create($validatedData);

            // Commit the transaction
            DB::commit();

            // Redirect to the index page with a success message
            return redirect()->route('brand.index')->with('success', 'Brand created successfully');
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollback();

            // Handle the error and redirect with an error message
            return redirect()->route('brand.create')->with('error', 'An error occurred while creating the Brand: '.$e->getMessage());
        }
    }
    public function instantAddPost(Request $request){
        $rules = [
            'name' =>[
                'required','max:255',
                Rule::unique('brands')
            ],
            'image' => [
                'nullable', 'mimes:jpg,png,jpeg,webp'
            ],
            'status' => 'required|boolean', // Ensure 'status' is boolean
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $checkBrand = Brand::where('name',$request->name)->first();
        if ($checkBrand){
            return response()->json(['success' => false, 'message' => 'Brand already exists!']);
        }
        DB::beginTransaction();

        try {
            
            $brand = new Brand();
            $brand->sister_concern_id = 1;
            $brand->name = $request->name;
            $brand->status = $request->status;
            $brand->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'added','brand'=>$brand]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        try {
            // If the Product exists, display the edit view
            return view('purchase.brand.edit', compact('brand'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle the case where the Product is not found
            return redirect()->route('brand.index')->with('error', 'Brand not found: '.$e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' =>[
                'required','max:255',
                Rule::unique('brands')
                    ->ignore($brand)
            ],
            'image' => [
                'nullable', 'mimes:jpg,png,jpeg,webp'
            ],
            'status' => 'required|boolean', // Ensure 'status' is boolean
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update the Product record in the database
            $image = $brand->image;
            if ($request->hasFile('image')){
                if (file_exists($brand->image)){
                    unlink($brand->image);
                }
                // Upload Image
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
                $destinationPath = 'uploads/brand/image';
                // $file->move(public_path($destinationPath), $filename);
                $file->move($destinationPath, $filename);
                $image = 'uploads/brand/image/' . $filename;
            }
            $validatedData['image'] = $image;
            $validatedData['sister_concern_id'] = 1;
            $brand->update($validatedData);

            // Commit the transaction
            DB::commit();

            // Redirect to the index page with a success message
            return redirect()->route('brand.index')->with('success', 'Brand updated successfully');
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollback();

            // Handle the error and redirect with an error message
            return redirect()->route('brand.edit',['brand'=>$brand->id])->with('error', 'An error occurred while updating the Brand: '.$e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        try {
           
            $product = Product::where('brand_id',$brand->id)->first();
            if ($product) {
                // If a related Supplier exists, return an error message
                return response()->json(['success' => false, 'message' => 'It has product logs. Cannot delete.'], Response::HTTP_OK);
            }
            // Delete the Product record
            $brand->delete();
            // Return a JSON success response
            return response()->json(['success'=>true,'message' => 'Brand deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any errors, such as record not found
            return response()->json(['success'=>false,'message' => 'Brand not found: '.$e->getMessage()], Response::HTTP_OK);

        }
    }
    public function toggleStatus(Brand $brand)
    {
        try {
            if ($brand) {
                // Toggle university status
                $newStatus = $brand->status == 0 ? 1 : 0;
                $brand->status = $newStatus;
                $brand->update();

                return redirect()->route('brand.index')->with('success', 'Brand Status Updated Successfully!');
            } else {
                return redirect()->back()->with('error', 'Brand Not Found!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
}
