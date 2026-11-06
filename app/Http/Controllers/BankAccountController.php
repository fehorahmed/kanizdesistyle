<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BankAccountController extends Controller
{
    public function index() {
        $accounts = BankAccount::with('bank', 'branch')->get();

        return view('bank_n_account.account.all', compact('accounts'));
    }
    public function dataTable()
    {
        $query = BankAccount::with('bank','branch');
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(BankAccount $bankAccount) {
                $btn = '<a href="' . route('bank_account.edit', ['account' => $bankAccount->id]) . '" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                return $btn;
            })
            ->addColumn('bank_name', function(BankAccount $bankAccount) {
                return $bankAccount->bank->name ?? '';
             })
            ->addColumn('branch_name', function(BankAccount $bankAccount) {
                return $bankAccount->branch->name ?? '';
             })
            ->editColumn('balance', function(BankAccount $bankAccount) {
                return number_format($bankAccount->balance ?? 0 ,2);
             })
            ->addColumn('status', function(BankAccount $bankAccount) {
              if ($bankAccount->status == 1)
               return '<span class="badge badge-success">Active</span>';
              else
                  return '<span class="badge badge-danger">Inactive</span>';

            })
            ->rawColumns(['action','status'])
            ->toJson();
    }
    public function add() {
        $banks = Bank::orderBy('name')->get();

        return view('bank_n_account.account.add', compact('banks'));
    }

    public function addPost(Request $request) {
        $request->validate([
            'bank' => 'required',
            'branch' => 'required',
            'account_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
            'account_code' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'opening_balance' => 'required|numeric|min:0',
            'status' => 'required'
        ]);

        $account = new BankAccount();
        $account->bank_id = $request->bank;
        $account->branch_id = $request->branch;
        $account->account_name = $request->account_name;
        $account->account_no = $request->account_no;
        $account->account_code = $request->account_code;
        $account->description = $request->description;
        $account->opening_balance = $request->opening_balance;
        $account->balance = $request->opening_balance;
        $account->status = $request->status;
        $account->user_id = Auth::id();
        $account->save();

        return redirect()->route('bank_account')->with('message', 'Bank account add successfully.');
    }

    public function edit(BankAccount $account) {
        $banks = Bank::orderBy('name')->get();

        return view('bank_n_account.account.edit', compact('banks', 'account'));
    }

    public function editPost(BankAccount $account, Request $request) {
        $request->validate([
            'bank' => 'required',
            'branch' => 'required',
            'account_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
            'account_code' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'required'
        ]);

        $account->bank_id = $request->bank;
        $account->branch_id = $request->branch;
        $account->account_name = $request->account_name;
        $account->account_no = $request->account_no;
        $account->account_code = $request->account_code;
        $account->description = $request->description;
        $account->status = $request->status;
        $account->save();

        return redirect()->route('bank_account')->with('message', 'Bank account edit successfully.');
    }

    public function getBranches(Request $request) {
        $branches = Branch::where('bank_id', $request->bankId)
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($branches);
    }
}
