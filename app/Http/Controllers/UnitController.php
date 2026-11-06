<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('purchase.unit.index');
    }
    public function dataTable()
    {
        $query = Unit::where('type',1);
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Unit $unit) {
                $btn = '';
                $btn .= '<a href="' . route('unit.edit', ['unit' => $unit->id]) . '" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                $btn .= ' <a role="button" data-id="' . $unit->id . '" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></a>';
                return $btn;
            })
            ->addColumn('status', function (Unit $unit) {
                $route = route('unit.toggle_status', $unit->id);
                $badgeClass = $unit->status ? 'badge-success' : 'badge-danger';
                $label = $unit->status ? 'Active' : 'Inactive';
                return '<a href="' . $route . '" class="badge ' . $badgeClass . '">' . $label . '</a>';
            })
            ->rawColumns(['action','status'])
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('purchase.unit.create');
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
                Rule::unique('units')
            ],
            'status' => 'required|boolean', // Ensure 'status' is boolean
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {

            // Create a new Product record in the database
            $validatedData['sister_concern_id'] = 1;
            Unit::create($validatedData);

            // Commit the transaction
            DB::commit();

            // Redirect to the index page with a success message
            return redirect()->route('unit.index')->with('success', 'Unit created successfully');
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollback();

            // Handle the error and redirect with an error message
            return redirect()->route('unit.create')->with('error', 'An error occurred while creating the Unit: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        try {
            // If the Product exists, display the edit view
            return view('purchase.unit.edit', compact('unit'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle the case where the Product is not found
            return redirect()->route('unit.index')->with('error', 'Unit not found: '.$e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' =>[
                'required','max:255',
                Rule::unique('units')
                    ->ignore($unit)
            ],
            'status' => 'required|boolean', // Ensure 'status' is boolean
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update the Product record in the database
            $validatedData['sister_concern_id'] = 1;
            $unit->update($validatedData);

            // Commit the transaction
            DB::commit();

            // Redirect to the index page with a success message
            return redirect()->route('unit.index')->with('success', 'Unit updated successfully');
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollback();

            // Handle the error and redirect with an error message
            return redirect()->route('unit.edit',['unit'=>$unit->id])->with('error', 'An error occurred while updating the Unit: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        try {
            $product = Product::where('unit_id',$unit->id)->first();
            if ($product) {
                // If a related Supplier exists, return an error message
                return response()->json(['success' => false, 'message' => 'It has product logs. Cannot delete.'], Response::HTTP_OK);
            }
            // Delete the Product record
            $unit->delete();
            // Return a JSON success response
            return response()->json(['success'=>true,'message' => 'Unit deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any errors, such as record not found
            return response()->json(['success'=>false,'message' => 'Unit not found: '.$e->getMessage()], Response::HTTP_OK);

        }
    }
    public function toggleStatus(Unit $unit)
    {
        try {
            if ($unit) {
                // Toggle university status
                $newStatus = $unit->status == 0 ? 1 : 0;
                $unit->status = $newStatus;
                $unit->update();

                return redirect()->route('unit.index')->with('success', 'Unit Status Updated Successfully!');
            } else {
                return redirect()->back()->with('error', 'Unit Not Found!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
}
