<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index() {
        return view('purchase.supplier.all');
    }

    public function dataTable()
    {
        $query = Supplier::whereNotIn('id',[0]);
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Supplier $supplier) {
                $btn ='<a href="'.route('supplier.edit', ['supplier' => $supplier->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                return $btn;
            })
            ->addColumn('status', function (Supplier $supplier) {
                $route = route('supplier.toggle_status', $supplier->id);
                $badgeClass = $supplier->status ? 'badge-success' : 'badge-danger';
                $label = $supplier->status ? 'Active' : 'Inactive';
                return '<a href="' . $route . '" class="badge ' . $badgeClass . '">' . $label . '</a>';
            })
            ->rawColumns(['action','status'])
            ->toJson();
    }

    public function add() {
        return view('purchase.supplier.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'mobile_no' => 'required',
            'email' => 'nullable|email|string|max:255',
            'address' => 'required|string|max:255',
            'opening_due' => 'required|numeric',
            'status' => 'required',
        ]);

        $supplier = new Supplier();
        $supplier->sister_concern_id = 1;
        $supplier->name = $request->name;
        $supplier->mobile = $request->mobile_no;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->opening_due = $request->opening_due;
        $supplier->opening_due_date = Carbon::parse($request->opening_due_date)->format('Y-m-d');
        $supplier->status = $request->status;
        $supplier->user_id = Auth::id();
        $supplier->save();

        return redirect()->route('supplier')->with('message', 'Supplier add successfully.');
    }
    public function addInstantPost(Request $request){
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|max:50',
            'mobile_no' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $checkSupplier = Supplier::where('mobile',$request->mobile_no)->first();
        if ($checkSupplier){
            return response()->json(['success' => false, 'message' => 'Supplier already exists!']);
        }
        DB::beginTransaction();
        try{
            $supplier = new Supplier();
            $supplier->sister_concern_id = 1;
            $supplier->name = $request->name;
            $supplier->email = $request->email;
            $supplier->mobile = $request->mobile_no;
            $supplier->address = $request->address;
            $supplier->user_id = Auth::id();
            $supplier->status = 1;
            $supplier->save();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'added','supplier'=>$supplier]);
        }catch(Exception $exception){
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
    public function edit(Supplier $supplier) {
        return view('purchase.supplier.edit', compact('supplier'));
    }

    public function editPost(Supplier $supplier, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_no' => 'required',
            'email' => 'nullable|email|string|max:255',
            'address' => 'required|string|max:255',
            'opening_due' => 'required|numeric',
            'status' => 'required',
        ]);

        $supplier->sister_concern_id = 1;
        $supplier->name = $request->name;
        $supplier->mobile = $request->mobile_no;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->opening_due = $request->opening_due;
        $supplier->opening_due_date = Carbon::parse($request->opening_due_date)->format('Y-m-d');
        $supplier->status = $request->status;
        $supplier->save();

        return redirect()->route('supplier')->with('message', 'Supplier edit successfully.');
    }
    public function toggleStatus(Supplier $supplier)
    {
        try {
            if ($supplier) {
                // Toggle university status
                $newStatus = $supplier->status == 0 ? 1 : 0;
                $supplier->status = $newStatus;
                $supplier->update();

                return redirect()->route('supplier')->with('success', 'Supplier Status Updated Successfully!');
            } else {
                return redirect()->back()->with('error', 'Supplier Not Found!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
}
