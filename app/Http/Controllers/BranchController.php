<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    public function index() {
        $branches = Branch::with('bank')->get();

        return view('bank_n_account.branch.all', compact('branches'));
    }

    public function dataTable()
    {
        $query = Branch::with('bank')->where('status',1);
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Branch $branch) {
                $btn = '<a href="' . route('branch.edit', ['branch' => $branch->id]) . '" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                return $btn;
            })
            ->addColumn('bank_name', function(Branch $branch) {
                return $branch->bank->name ?? '';
             })
            ->addColumn('status', function(Branch $branch) {
              if ($branch->status == 1)
               return '<span class="badge badge-success">Active</span>';
              else
                  return '<span class="badge badge-danger">Inactive</span>';

            })
            ->rawColumns(['action','status'])
            ->toJson();
    }

    public function add() {
        $banks = Bank::orderBy('name')->get();

        return view('bank_n_account.branch.add', compact('banks'));
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank' => 'required',
            'status' => 'required'
        ]);

        $branch = new Branch();
        $branch->bank_id = $request->bank;
        $branch->name = $request->name;
        $branch->status = $request->status;
        $branch->user_id = Auth::id();
        $branch->save();

        return redirect()->route('branch')->with('message', 'Branch add successfully.');
    }

    public function edit(Branch $branch) {
        $banks = Bank::orderBy('name')->get();

        return view('bank_n_account.branch.edit', compact('banks', 'branch'));
    }

    public function editPost(Branch $branch, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank' => 'required',
            'status' => 'required'
        ]);

        $branch->bank_id = $request->bank;
        $branch->name = $request->name;
        $branch->status = $request->status;
        $branch->save();

        return redirect()->route('branch')->with('message', 'Branch edit successfully.');
    }
}
