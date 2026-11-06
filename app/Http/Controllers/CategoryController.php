<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Filter;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('purchase.category.index');
    }
    public function dataTable()
    {
        $query = Category::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Category $category) {
                $btn = '';
                $btn .= '<a href="' . route('category.edit', ['category' => $category->id]) . '" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                $btn .= ' <a href="' . route('category.add_edit_filter', ['category' => $category->id]) . '" class="btn btn-primary bg-gradient-primary btn-sm btn-edit">Add/Edit Filter</a>';
                $btn .= ' <a role="button" data-id="' . $category->id . '" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></a>';
                return $btn;
            })
            ->addColumn('image',function(Category $category){
                if($category->image){
                    return '<img src="'. asset($category->image) .'" width="35"/>';
                }
            })
            ->addColumn('filters', function(Category $category) {
                return $category->filters ? $category->filters->pluck('name')->implode(', ') : '-';
            })
            ->addColumn('status', function (Category $category) {
                $route = route('category.toggle_status', $category->id);
                $badgeClass = $category->status ? 'badge-success' : 'badge-danger';
                $label = $category->status ? 'Active' : 'Inactive';
                return '<a href="' . $route . '" class="badge ' . $badgeClass . '">' . $label . '</a>';
            })
            ->addColumn('filters', function(Category $category) {
                return $category->filters ? $category->filters->pluck('name')->implode(', ') : '-';
            })
            ->rawColumns(['action','image','status'])
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('purchase.category.create');
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
                Rule::unique('categories')
            ],
            'image' => [
                'nullable', 'mimes:jpg,png,jpeg,webp'
            ],
            'prefix' =>[
                'required','max:255',
                Rule::unique('categories')
            ],
            'status' => 'required|boolean', // Ensure 'status' is boolean
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            $image = null;
            if ($request->hasFile('image')){
                // Upload Image
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
                $destinationPath = 'uploads/category/image';
                // $file->move(public_path($destinationPath), $filename);
                $file->move($destinationPath, $filename);
                $image = 'uploads/category/image/' . $filename;
            }
            $validatedData['image'] = $image;
            $validatedData['sister_concern_id'] = 1;
            $validatedData['slug'] = Str::slug($validatedData['name']);

            Category::create($validatedData);

            // Commit the transaction
            DB::commit();

            // Redirect to the index page with a success message
            return redirect()->route('category.index')->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollback();

            // Handle the error and redirect with an error message
            return redirect()->route('category.create')->with('error', 'An error occurred while creating the Category: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        try {
            // If the Product exists, display the edit view
            return view('purchase.category.edit', compact('category'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle the case where the Product is not found
            return redirect()->route('category.index')->with('error', 'Category not found: '.$e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' =>[
                'required','max:255',
                Rule::unique('categories')
                    ->ignore($category)
            ],
            'image' => [
                'nullable', 'mimes:jpg,png,jpeg,webp'
            ],
            'prefix' =>[
                'required','max:255',
                Rule::unique('categories')
                    ->ignore($category)
            ],
            'status' => 'required|boolean', // Ensure 'status' is boolean
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            $image = $category->image;
            if ($request->hasFile('image')){
                if (file_exists($category->image)){
                    unlink($category->image);
                }
                // Upload Image
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
                $destinationPath = 'uploads/category/image';
                // $file->move(public_path($destinationPath), $filename);
                $file->move($destinationPath, $filename);
                $image = 'uploads/category/image/' . $filename;
            }
            $validatedData['image'] = $image;
            $validatedData['sister_concern_id'] = 1;
            $validatedData['slug'] = Str::slug($validatedData['name']);
            // Update the Product record in the database
            $category->update($validatedData);

            // Commit the transaction
            DB::commit();

            // Redirect to the index page with a success message
            return redirect()->route('category.index')->with('success', 'Category updated successfully');
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollback();

            // Handle the error and redirect with an error message
            return redirect()->route('category.edit',['category'=>$category->id])->with('error', 'An error occurred while updating the Category: '.$e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {

            $product = Product::where('category_id',$category->id)->first();
            if ($product) {
                // If a related Supplier exists, return an error message
                return response()->json(['success' => false, 'message' => 'It has product logs. Cannot delete.'], Response::HTTP_OK);
            }
            // Delete the Product record
            $category->delete();
            // Return a JSON success response
            return response()->json(['success'=>true,'message' => 'Category deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any errors, such as record not found
            return response()->json(['success'=>false,'message' => 'Category not found: '.$e->getMessage()], Response::HTTP_OK);

        }
    }
    public function addEditFilter(Category $category)
    {
        try {
            $filters = Filter::all();
            // selected filters for this category
            $selectedFilters = $category->filters->pluck('id')->toArray();
            return view('purchase.category.add_edit_filter', compact('category', 'filters', 'selectedFilters'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle the case where the Product is not found
            return redirect()->route('category.index')->with('error', 'Category not found: '.$e->getMessage());
        }
    }
    public function addEditFilterPost(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'filters' => 'nullable|array',
            'filters.*' => 'exists:filters,id',
        ]);

        // Sync the selected filters with the category
        $category->filters()->sync($validatedData['filters'] ?? []);

        return redirect()->route('category.index')->with('success', 'Category filters updated successfully');
    }
    public function toggleStatus(Category $category)
    {
        try {
            if ($category) {
                // Toggle university status
                $newStatus = $category->status == 0 ? 1 : 0;
                $category->status = $newStatus;
                $category->update();

                return redirect()->route('category.index')->with('success', 'Category Status Updated Successfully!');
            } else {
                return redirect()->back()->with('error', 'Category Not Found!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
}
