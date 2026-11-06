<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('purchase.sub_category.index');
    }
    public function dataTable()
    {
        $query = SubCategory::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('category',function(SubCategory $subCategory){
                return $subCategory->category->name??'';
            })
            ->addColumn('action', function(SubCategory $subCategory) {
                $btn = '';
                $btn .= '<a href="' . route('sub-category.edit', ['sub_category' => $subCategory->id]) . '" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                $btn .= ' <a role="button" data-id="' . $subCategory->id . '" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></a>';
                return $btn;
            })
            ->addColumn('status', function (SubCategory $subCategory) {
                $route = route('sub-category.toggle_status', $subCategory->id);
                $badgeClass = $subCategory->status ? 'badge-success' : 'badge-danger';
                $label = $subCategory->status ? 'Active' : 'Inactive';
                return '<a href="' . $route . '" class="badge ' . $badgeClass . '">' . $label . '</a>';
            })
            ->rawColumns(['action','status'])
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $categories = Category::where('status',1)->get();
        return view('purchase.sub_category.create',compact('categories'));
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
                Rule::unique('sub_categories')
            ],
            'category' => 'required',
            'status' => 'required|boolean', // Ensure 'status' is boolean
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {

            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = Str::slug($request->name);
            $subCategory->category_id = $request->category;
            $subCategory->sister_concern_id = 1;
            $subCategory->status = 1;
            $subCategory->save();

            // Commit the transaction
            DB::commit();

            // Redirect to the index page with a success message
            return redirect()->route('sub-category.index')->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollback();

            // Handle the error and redirect with an error message
            return redirect()->route('sub-category.create')->with('error', 'An error occurred while creating the Category: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubCategory $subCategory)
    {
        try {
            $categories = Category::where('status',1)->get();
            return view('purchase.sub_category.edit', compact('subCategory','categories'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle the case where the Product is not found
            return redirect()->route('sub-category.index')->with('error', 'SubCategory not found: '.$e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubCategory $subCategory)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' =>[
                'required','max:255',
                Rule::unique('sub_categories')
                    ->ignore($subCategory)
            ],
            'category' => 'required',
            'status' => 'required|boolean', // Ensure 'status' is boolean
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            $subCategory->sister_concern_id = 1;
            $subCategory->name = $request->name;
            $subCategory->slug = Str::slug($request->name);
            $subCategory->category_id = $request->category;
            $subCategory->status = 1;
            $subCategory->save();

            DB::commit();

            // Redirect to the index page with a success message
            return redirect()->route('sub-category.index')->with('success', 'SubCategory updated successfully');
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollback();

            // Handle the error and redirect with an error message
            return redirect()->route('sub-category.edit',['sub_category'=>$subCategory->id])->with('error', 'An error occurred while updating the Category: '.$e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subCategory)
    {
        try {

            $product = Product::where('sub_category_id',$subCategory->id)->first();
            if ($product) {
                // If a related Supplier exists, return an error message
                return response()->json(['success' => false, 'message' => 'It has product logs. Cannot delete.'], Response::HTTP_OK);
            }
            // Delete the Product record
            $subCategory->delete();
            // Return a JSON success response
            return response()->json(['success'=>true,'message' => 'SubCategory deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any errors, such as record not found
            return response()->json(['success'=>false,'message' => 'SubCategory not found: '.$e->getMessage()], Response::HTTP_OK);

        }
    }
    public function toggleStatus(SubCategory $subCategory)
    {
        try {
            if ($subCategory) {
                // Toggle university status
                $newStatus = $subCategory->status == 0 ? 1 : 0;
                $subCategory->status = $newStatus;
                $subCategory->update();

                return redirect()->route('sub-category.index')->with('success', 'SubCategory Status Updated Successfully!');
            } else {
                return redirect()->back()->with('error', 'SubCategory Not Found!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
}
