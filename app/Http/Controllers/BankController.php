<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bank;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BankController extends Controller
{
    public function index() {
        return view('bank_n_account.bank.all');
    }

    public function dataTable()
    {
        $query = Bank::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Bank $bank) {
                $btn ='<a href="'.route('bank.edit', ['bank' => $bank->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function add() {
        return view('bank_n_account.bank.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $bank = new Bank();
        $bank->name = $request->name;
        $bank->status = $request->status;
        $bank->user_id = Auth::id();
        $bank->save();

        return redirect()->route('bank')->with('message', 'Bank add successfully.');
    }

    public function edit(Bank $bank) {
        return view('bank_n_account.bank.edit', compact('bank'));
    }

    public function editPost(Bank $bank, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $bank->name = $request->name;
        $bank->status = $request->status;
        $bank->save();

        return redirect()->route('bank')->with('message', 'Bank edit successfully.');
    }
}

