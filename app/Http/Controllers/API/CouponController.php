<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{
    public function index()
    {
        return view('ecommerce.coupon.all');
    }

    public function datatable()
    {
        $query = Coupon::select('id', 'code', 'discount', 'type', 'min_purchase', 'start_date', 'end_date', 'is_active')
            ->orderBy('id', 'ASC');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Coupon $coupon) {
                $btn = '';
                // if(auth()->user()->can('coupon_edit')) {
                    $btn .= '<a href="'.route('ecom_coupon.edit',['coupon'=>$coupon->id]).'" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a> ';
                // }
                // if(auth()->user()->can('coupon_delete')) {
                    $btn .= '<button data-id="'.$coupon->id.'" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></button>';
                // }
                return $btn;
            })
            ->editColumn('is_active', function(Coupon $coupon) {
                return '<input type="checkbox" class="coupon-status-switch" data-id="'.$coupon->id.'" '.
                    ($coupon->is_active ? 'checked' : '').' data-bootstrap-switch data-on-color="success" data-off-color="danger">';
            })
            ->rawColumns(['action','is_active'])
            ->toJson();
    }

    public function add()
    {
        return view('ecommerce.coupon.add');
    }

    public function addPost(Request $request)
    {
        $request->validate([
            'code' => ['required', 'max:50', Rule::unique('coupons')],
            'discount' => 'required|numeric',
            'type' => 'required|in:percent,fixed',
            'min_purchase'=> 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $coupon = new Coupon();
            $coupon->code = $request->code;
            $coupon->description = $request->description;
            $coupon->discount = $request->discount;
            $coupon->type = $request->type;
            $coupon->min_purchase = $request->min_purchase;
            $coupon->start_date = date('Y-m-d',strtotime($request->start_date));
            $coupon->end_date = date('Y-m-d',strtotime($request->end_date));
            $coupon->is_active = $request->is_active ?? true;
            $coupon->save();

            return redirect()->route('ecom_coupon')->with('message','Coupon added successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }

    public function edit(Coupon $coupon)
    {
        return view('ecommerce.coupon.edit', compact('coupon'));
    }

    public function editPost(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => ['required', 'max:50', Rule::unique('coupons')->ignore($coupon->id)],
            'discount' => 'required|numeric',
            'type' => 'required|in:percent,fixed',
            'min_purchase'=> 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $coupon->code = $request->code;
            $coupon->description = $request->description;
            $coupon->discount = $request->discount;
            $coupon->type = $request->type;
            $coupon->min_purchase = $request->min_purchase;
            $coupon->start_date = date('Y-m-d',strtotime($request->start_date));
            $coupon->end_date = date('Y-m-d',strtotime($request->end_date));
            $coupon->is_active = $request->is_active ?? true;
            $coupon->save();

            return redirect()->route('ecom_coupon')->with('message','Coupon updated successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }

    // Delete coupon
    public function delete(Coupon $coupon)
    {
        try {
            $coupon->delete();
            return response()->json(['success' => true, 'message' => 'Coupon deleted successfully']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    // Toggle coupon status
    public function toggleStatus(Coupon $coupon)
    {
        try {
            $coupon->is_active = !$coupon->is_active;
            $coupon->save();
            return response()->json(['success' => true, 'status' => $coupon->is_active]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
}
