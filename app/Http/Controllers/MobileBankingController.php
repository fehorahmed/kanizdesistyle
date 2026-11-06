<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\MobileBanking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MobileBankingController extends Controller
{
    public function index() {
        return view('mobile_banking.all');
    }
    public function dataTable()
    {
        $query = MobileBanking::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(MobileBanking $mobileBanking) {
                $btn = '<a href="' . route('mobile_banking.edit', ['mobileBanking' => $mobileBanking->id]) . '" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                return $btn;
            })
            ->addColumn('status', function(MobileBanking $mobileBanking) {
              if ($mobileBanking->status == 1)
               return '<span class="badge badge-success">Active</span>';
              else
                  return '<span class="badge badge-danger">Inactive</span>';

            })
            ->rawColumns(['action','status'])
            ->toJson();
    }
    public function add() {
        return view('mobile_banking.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
            'opening_balance' => 'required|numeric|min:0',
            'status' => 'required'
        ]);

        $mobileBanking = new MobileBanking();
        $mobileBanking->account_name = $request->account_name;
        $mobileBanking->account_no = $request->account_no;
        $mobileBanking->opening_balance = $request->opening_balance;
        $mobileBanking->amount = $request->opening_balance;
        $mobileBanking->status = $request->status;
        $mobileBanking->user_id = Auth::id();
        $mobileBanking->save();

        return redirect()->route('mobile_banking')->with('message', 'Mobile Banking add successfully.');
    }

    public function edit(MobileBanking $mobileBanking) {
        return view('mobile_banking.edit', compact( 'mobileBanking'));
    }

    public function editPost(MobileBanking $mobileBanking, Request $request) {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $mobileBanking->account_name = $request->account_name;
        $mobileBanking->account_no = $request->account_no;
        $mobileBanking->status = $request->status;
        $mobileBanking->save();

        return redirect()->route('mobile_banking')->with('message', 'Mobile Banking edit successfully.');
    }
}
