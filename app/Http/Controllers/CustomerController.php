<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class CustomerController extends Controller
{
    public function index() {
        return view('sale.customer.all');
    }

    public function add() {
        return view('sale.customer.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|max:50',
            'mobile_no' => 'required|string|min:11|max:11|unique:customers,mobile_no',
            'address' => 'required|string|max:255',
            'opening_due' => 'required',
        ]);

        $checkCustomer = Customer::where('mobile_no',$request->mobile_no)->first();
        if($checkCustomer){
            return redirect()->back()->with('error','Already exists. Please search with mobile no!');
        }

        $customer = new Customer();
        $customer->sister_concern_id = 1;
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->mobile_no = $request->mobile_no;
        $customer->address = $request->address;
        $customer->opening_due = $request->opening_due;
        $customer->status = $request->status;
        $customer->opening_due_date = Carbon::parse($request->opening_due_date)->format('Y-m-d');
        $customer->user_id = Auth::id();
        $customer->save();

        return redirect()->route('customer')->with('message', 'Customer add successfully.');
    }

    public function edit(Customer $customer) {
        return view('sale.customer.edit', compact('customer'));
    }

    public function editPost(Customer $customer, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|max:50',
            'mobile_no' => 'required|string|min:11|max:11|unique:customers,mobile_no,' . $customer->id,
            'address' => 'required|string|max:255',
            'opening_due' => 'required',
        ]);


        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->mobile_no = $request->mobile_no;
        $customer->address = $request->address;
        $customer->opening_due = $request->opening_due;
        $customer->opening_due_date = Carbon::parse($request->opening_due_date)->format('Y-m-d');
        $customer->status = $request->status;
        $customer->save();

        return redirect()->route('customer')->with('message', 'Customer edit successfully.');
    }

    public function datatable() {
        $query = Customer::with(['user']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('opening_paid', function(Customer $customer) {
                return $customer->opening_paid;
            })
            ->editColumn('date_of_birth', function(Customer $customer) {
                if($customer->date_of_birth){
                    return Carbon::parse($customer->date_of_birth)->format('d-M-Y');
                }else{
                    return '';
                }
            })
            ->addColumn('opening_due', function(Customer $customer) {
                return $customer->opening_due - $customer->opening_paid;
            })
            ->addColumn('user', function(Customer $customer) {
                return $customer->user->name??'';
            })
            ->addColumn('status', function (Customer $customer) {
                $route = route('customer.toggle_status', $customer->id);
                $badgeClass = $customer->status ? 'badge-success' : 'badge-danger';
                $label = $customer->status ? 'Active' : 'Inactive';
                return '<a href="' . $route . '" class="badge ' . $badgeClass . '">' . $label . '</a>';
            })
            ->addColumn('action', function(Customer $customer) {
                $btn ='<a href="'.route('customer.edit', ['customer' => $customer->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                return $btn;
            })
            ->rawColumns(['action','status'])
            ->toJson();
    }

    public function addAjaxPost(Request $request){

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|max:50',
            'mobile_no' => 'required|string|min:11|max:11|unique:customers,mobile_no',
            'address' => 'nullable|string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $checkCustomer = Customer::where('mobile_no',$request->mobile_no)->first();
        if ($checkCustomer){
            return response()->json(['success' => false, 'message' => 'Patient/Customer already exists. Please search with mobile no!']);
        }
        $customer = new Customer();
        $customer->sister_concern_id = 1;
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->mobile_no = $request->mobile_no;
        $customer->address = $request->address;
        $customer->user_id = Auth::id();
        $customer->opening_due = 0;
        $customer->status = 1;
        $customer->save();

        return response()->json(['success' => true, 'message' => 'added','customer'=>$customer]);
    }
    public function toggleStatus(Customer $customer)
    {
        try {
            if ($customer) {
                // Toggle university status
                $newStatus = $customer->status == 0 ? 1 : 0;
                $customer->status = $newStatus;
                $customer->update();

                return redirect()->route('customer')->with('success', 'Customer Status Updated Successfully!');
            } else {
                return redirect()->back()->with('error', 'Customer Not Found!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
}
