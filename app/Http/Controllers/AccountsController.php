<?php

namespace App\Http\Controllers;

use App\Models\AccountHeadSubType;
use App\Models\AccountHeadType;
use App\Models\BalanceTransfer;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Department;
use App\Models\MobileBanking;
use App\Models\Transaction;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;
use Yajra\DataTables\Facades\DataTables;

class AccountsController extends Controller
{
    public function accountHeadType() {
        $types = AccountHeadType::whereNotIn('id', [1,2])->get();

        return view('accounts.account_head_type.all', compact('types'));
    }

    public function accountHeadTypeAdd() {
        return view('accounts.account_head_type.add');
    }

    public function accountHeadTypeAddPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|min:1|max:2',
            'status' => 'required'
        ]);

        $type = new AccountHeadType();
        $type->name = $request->name;
        $type->transaction_type = $request->type;
        $type->status = $request->status;
        $type->user_id = Auth::id();
        $type->save();

        return redirect()->route('account_head.type')->with('message', 'Account head type add successfully.');
    }

    public function accountHeadTypeEdit(AccountHeadType $type) {
        return view('accounts.account_head_type.edit', compact('type'));
    }

    public function accountHeadTypeEditPost(AccountHeadType $type, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|min:1|max:2',
            'status' => 'required'
        ]);

        $type->name = $request->name;
        $type->transaction_type = $request->type;
        $type->status = $request->status;
        $type->save();

        return redirect()->route('account_head.type')->with('message', 'Account head type edit successfully.');
    }

    public function accountHeadSubType() {
        $subTypes = AccountHeadSubType::whereNotIn('id', [1,2])->get();

        return view('accounts.account_head_sub_type.all', compact('subTypes'));
    }

    public function accountHeadSubTypeAdd() {
        return view('accounts.account_head_sub_type.add');
    }

    public function accountHeadSubTypeAddPost(Request $request) {
        $request->validate([
            'type' => 'required',
            'name' => 'required|string|max:255',
            'account_head_type' => 'required',
            'status' => 'required'
        ]);

        $subType = new AccountHeadSubType();
        $subType->account_head_type_id = $request->account_head_type;
        $subType->name = $request->name;
        $subType->status = $request->status;
        $subType->user_id = Auth::id();
        $subType->save();

        return redirect()->route('account_head.sub_type')->with('message', 'Account head sub type add successfully.');
    }

    public function accountHeadSubTypeEdit(AccountHeadSubType $subType) {
        return view('accounts.account_head_sub_type.edit', compact('subType'));
    }

    public function accountHeadSubTypeEditPost(AccountHeadSubType $subType, Request $request) {
        $request->validate([
            'type' => 'required',
            'name' => 'required|string|max:255',
            'account_head_type' => 'required',
            'status' => 'required'
        ]);

        $subType->account_head_type_id = $request->account_head_type;
        $subType->name = $request->name;
        $subType->status = $request->status;
        $subType->save();

        return redirect()->route('account_head.sub_type')->with('message', 'Account head sub type edit successfully.');
    }

    public function transactionIndex() {
        return view('accounts.transaction.all');
    }

    public function transactionAdd() {
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();

        return view('accounts.transaction.add', compact('banks', 'mobileBankings'));
    }

    public function transactionAddPost(Request $request) {
        $messages = [
            'bank.required_if' => 'The bank field is required.',
            'branch.required_if' => 'The branch field is required.',
            'account.required_if' => 'The account field is required.',
        ];

        $validator = Validator::make($request->all(), [
            'department_id' => 'required|integer|min:1|max:2',
            'type' => 'required|integer|min:1|max:2',
            'account_head_type' => 'required',
            'account_head_sub_type' => 'required',
            'payment_type' => 'required|integer|min:1|max:3',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
            'bank' => 'required_if:payment_type,==,2',
            'branch' => 'required_if:payment_type,==,2',
            'account' => 'required_if:payment_type,==,2',
            'cheque_no' => 'nullable|string|max:255',
            'cheque_image' => 'nullable|image',
        ], $messages);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validator->after(function ($validator) use ($request) {
            if ($request->type == 2) {
                if ($request->payment_type == 1) {
                    $cash = Cash::first();

                    if ($request->amount > $cash->amount)
                        $validator->errors()->add('amount', 'Insufficient balance.');
                } elseif ($request->payment_type == 3) {
                    $mobileBanking = MobileBanking::find($request->mobile_banking);

                    if ($request->amount > $mobileBanking->amount)
                        $validator->errors()->add('amount', 'Insufficient balance.');
                } else {
                    $bankAccount = BankAccount::find($request->account);

                    if ($request->amount > $bankAccount->balance)
                        $validator->errors()->add('amount', 'Insufficient balance.');
                }

            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        DB::beginTransaction();
        try {
            $image = null;
            if ($request->payment_type == 2) {
                $image = 'img/no_image.png';

                if ($request->cheque_image) {
                    // Upload Image
                    $file = $request->file('cheque_image');
                    $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                    $destinationPath = 'public/uploads/transaction_cheque';
                    $file->move($destinationPath, $filename);

                    $image = 'uploads/transaction_cheque/'.$filename;
                }
            }

            $transaction = new Transaction();
            $transaction->sister_concern_id = $request->sister_concern_id;
            $transaction->transaction_type = $request->type;
            $transaction->account_head_type_id = $request->account_head_type;
            $transaction->account_head_sub_type_id = $request->account_head_sub_type;
            $transaction->transaction_method = $request->payment_type;
            $transaction->bank_id = $request->payment_type == 2 ? $request->bank : null;
            $transaction->branch_id = $request->payment_type == 2 ? $request->branch : null;
            $transaction->bank_account_id = $request->payment_type == 2 ? $request->account : null;
            $transaction->cheque_no = $request->payment_type == 2 ? $request->cheque_no : null;
            $transaction->cheque_image = $image;
            $transaction->amount = $request->amount;
            $transaction->date = $request->date;
            $transaction->note = $request->note;
            if ($request->payment_type == 3) {
                $transaction->mobile_banking_id = $request->mobile_banking;
            }
            $transaction->user_id = Auth::id();
            $transaction->save();

            if ($request->type == 1) {
                // Income
                if ($request->payment_type == 1) {
                    // Cash
                    Cash::first()->increment('amount', $request->amount);
                } elseif ($request->payment_type == 3) {
                    // Mobile Banking
                    MobileBanking::find($request->mobile_banking)->increment('amount', $request->amount);
                } else {
                    // Bank
                    BankAccount::find($request->account)->increment('balance', $request->amount);
                }
            } else {
                // Expense
                if ($request->payment_type == 1) {
                    // Cash
                    Cash::first()->decrement('amount', $request->amount);
                } elseif ($request->payment_type == 3) {
                    // Mobile Banking
                    MobileBanking::find($request->mobile_banking)->decrement('amount', $request->amount);
                } else {
                    // Bank
                    BankAccount::find($request->account)->decrement('balance', $request->amount);
                }
            }

            $accountHeadSubType = AccountHeadSubType::find($request->account_head_sub_type);

            $log = new TransactionLog();
            $log->sister_concern_id = 1;
            $log->date = $request->date;
            $log->particular = $accountHeadSubType->name;
            $log->transaction_type = $request->type;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = $request->account_head_type;
            $log->account_head_sub_type_id = $request->account_head_sub_type;
            $log->bank_id = $request->payment_type == 2 ? $request->bank : null;
            $log->branch_id = $request->payment_type == 2 ? $request->branch : null;
            $log->bank_account_id = $request->payment_type == 2 ? $request->account : null;
            $log->cheque_no = $request->payment_type == 2 ? $request->cheque_no : null;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            if ($request->payment_type == 3) {
                $log->mobile_banking_id = $request->mobile_banking;
            }
            $log->transaction_id = $transaction->id;
            $log->user_id = Auth::id();
            $log->save();

            DB::commit();
            return redirect()->route('transaction.details', ['transaction' => $transaction->id]);
        } catch (Exception $exception) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function transactionEditPost(Request $request) {
        $rules = [
            'id' => 'required',
            'type' => 'required',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        $transaction = Transaction::find($request->id);

        if ($transaction->transaction_method == 1) {
            $balance = Cash::first()->amount;
        } elseif ($transaction->transaction_method == 3) {
            $balance = MobileBanking::find($transaction->mobile_banking_id)->amount;
        } else {
            $balance = BankAccount::find($transaction->bank_account_id)->balance;
        }

        if ($request->type == 1) {
            $updateBalance = ($balance - $transaction->amount) + $request->amount;
        } else {
            $updateBalance = ($balance + $transaction->amount) - $request->amount;
        }

        $validator->after(function ($validator) use ($updateBalance) {
            if ($updateBalance < 0) {
                $validator->errors()->add('amount', 'Insufficient balance.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $transaction->amount = $request->amount;
        $transaction->note = $request->note;
        $transaction->save();

        if ($transaction->transaction_method == 1) {
            Cash::first()->update([
                'amount' => $updateBalance
            ]);
        } elseif ($transaction->transaction_method == 3) {
            MobileBanking::find($transaction->mobile_banking_id)->update([
                'amount' => $updateBalance
            ]);
        } else {
            $balance = BankAccount::find($transaction->bank_account_id)->update([
                'balance' => $updateBalance
            ]);
        }

        TransactionLog::where('transaction_id', $transaction->id)
            ->update([
                'amount' => $request->amount,
                'note' => $request->note
            ]);

        return response()->json(['success' => true, 'message' => 'Transaction has been updated.']);
    }

    public function transactionDetails(Transaction $transaction) {
        $transaction->amount_in_word = DecimalToWords::convert($transaction->amount,'Taka',
            'Poisa');

        return view('accounts.transaction.details', compact('transaction'));
    }

    public function transactionDetailsJson(Request $request) {
        $transaction = Transaction::find($request->transactionId)->toArray();

        return response()->json($transaction);
    }

    public function balanceTransferAdd() {
        $banks = Bank::where('status', 1)
            ->orderBy('name')
            ->get();
        if (Auth::user()->role_id == 1) {
            $departments = Department::where('status', 1)->orderBy('name')->get();
        } else {
            $departments = Department::where('id', Auth::user()->department_id)->get();
        }

        return view('accounts.balance_transfer.add', compact('banks', 'departments'));
    }

    public function balanceTransferAddPost(Request $request) {
        $messages = [
            'source_bank.required_if' => 'The source bank field is required.',
            'source_branch.required_if' => 'The source branch field is required.',
            'source_account.required_if' => 'The source account field is required.',
            'target_bank.required_if' => 'The target bank field is required.',
            'target_branch.required_if' => 'The target branch field is required.',
            'target_account.required_if' => 'The target account field is required.',
        ];

        $validator = Validator::make($request->all(), [
            'department_id' => 'required',
            'type' => 'required|integer|min:1|max:3',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
            'source_bank' => 'required_if:type,==,1|required_if:type,==,3',
            'source_branch' => 'required_if:type,==,1|required_if:type,==,3',
            'source_account' => 'required_if:type,==,1|required_if:type,==,3',
            'source_cheque_no' => 'nullable|string|max:255',
            'source_cheque_image' => 'nullable|image',
            'target_bank' => 'required_if:type,==,2|required_if:type,==,3',
            'target_branch' => 'required_if:type,==,2required_if:type,==,3',
            'target_account' => 'required_if:type,==,2|required_if:type,==,3',
            'target_cheque_no' => 'nullable|string|max:255',
            'target_cheque_image' => 'nullable|image',
        ], $messages);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validator->after(function ($validator) use ($request) {
            if ($request->type == 1 || $request->type == 3) {
                $bankAccount = BankAccount::find($request->source_account);

                if ($request->amount > $bankAccount->balance)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            } else {
                $cash = Cash::first();

                if ($request->amount > $cash->amount)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $sourceImage = null;
        $targetImage = null;
        if ($request->type == 1 || $request->type == 3) {
            $sourceImage = 'img/no_image.png';

            if ($request->source_cheque_image) {
                // Upload Image
                $file = $request->file('source_cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/balance_transfer_cheque';
                $file->move($destinationPath, $filename);

                $sourceImage = 'uploads/balance_transfer_cheque/'.$filename;
            }
        }

        if ($request->type == 2 || $request->type == 3) {
            $targetImage = 'img/no_image.png';

            if ($request->target_cheque_image) {
                // Upload Image
                $file = $request->file('target_cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/balance_transfer_cheque';
                $file->move($destinationPath, $filename);

                $targetImage = 'uploads/balance_transfer_cheque/'.$filename;
            }
        }

        $transfer = new BalanceTransfer();
        $transfer->department_id = $request->department_id;
        $transfer->type = $request->type;
        $transfer->source_bank_id = in_array($request->type, [1, 3]) ? $request->source_bank : null;
        $transfer->source_branch_id = in_array($request->type, [1, 3]) ? $request->source_branch : null;
        $transfer->source_bank_account_id = in_array($request->type, [1, 3]) ? $request->source_account : null;
        $transfer->source_cheque_no = in_array($request->type, [1, 3]) ? $request->source_cheque_no : null;
        $transfer->source_cheque_image = $sourceImage;
        $transfer->target_bank_id = in_array($request->type, [2, 3]) ? $request->target_bank : null;
        $transfer->target_branch_id = in_array($request->type, [2, 3]) ? $request->target_branch : null;
        $transfer->target_bank_account_id = in_array($request->type, [2, 3]) ? $request->target_account : null;
        $transfer->target_cheque_no = in_array($request->type, [2, 3]) ? $request->target_cheque_no : null;
        $transfer->target_cheque_image = $targetImage;
        $transfer->amount = $request->amount;
        $transfer->date = $request->date;
        $transfer->note = $request->note;
        $transfer->user_id = Auth::id();
        $transfer->save();

        if ($request->type == 1) {
            // Bank To Cash
            BankAccount::find($request->source_account)->decrement('balance', $request->amount);
            Cash::first()->increment('amount', $request->amount);

            $log = new TransactionLog();
            $log->department_id = $request->department_id;
            $log->date = $request->date;
            $log->particular = 'Balance Transfer';
            $log->transaction_type = 2;
            $log->transaction_method = 2;
            $log->account_head_type_id = 4;
            $log->account_head_sub_type_id = 4;
            $log->bank_id = $request->source_bank;
            $log->branch_id = $request->source_branch;
            $log->bank_account_id = $request->source_account;
            $log->cheque_no = $request->source_cheque_no;
            $log->cheque_image = $sourceImage;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->balance_transfer_id = $transfer->id;
            $log->user_id = Auth::id();
            $log->save();

            $log = new TransactionLog();
            $log->department_id = $request->department_id;
            $log->date = $request->date;
            $log->particular = 'Balance Transfer';
            $log->transaction_type = 1;
            $log->transaction_method = 1;
            $log->account_head_type_id = 3;
            $log->account_head_sub_type_id = 3;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->balance_transfer_id = $transfer->id;
            $log->user_id = Auth::id();
            $log->save();
        } elseif ($request->type == 2) {
            // Cash To Bank
            Cash::first()->decrement('amount', $request->amount);
            BankAccount::find($request->target_account)->increment('balance', $request->amount);

            $log = new TransactionLog();
            $log->department_id = $request->department_id;
            $log->date = $request->date;
            $log->particular = 'Balance Transfer';
            $log->transaction_type = 2;
            $log->transaction_method = 1;
            $log->account_head_type_id = 4;
            $log->account_head_sub_type_id = 4;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->balance_transfer_id = $transfer->id;
            $log->user_id = Auth::id();
            $log->save();

            $log = new TransactionLog();
            $log->department_id = $request->department_id;
            $log->date = $request->date;
            $log->particular = 'Balance Transfer';
            $log->transaction_type = 1;
            $log->transaction_method = 2;
            $log->account_head_type_id = 3;
            $log->account_head_sub_type_id = 3;
            $log->bank_id = $request->target_bank;
            $log->branch_id = $request->target_branch;
            $log->bank_account_id = $request->target_account;
            $log->cheque_no = $request->target_cheque_no;
            $log->cheque_image = $targetImage;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->balance_transfer_id = $transfer->id;
            $log->user_id = Auth::id();
            $log->save();
        } else {
            // Bank To Bank
            BankAccount::find($request->source_account)->decrement('balance', $request->amount);
            BankAccount::find($request->target_account)->increment('balance', $request->amount);

            $log = new TransactionLog();
            $log->department_id = $request->department_id;
            $log->date = $request->date;
            $log->particular = 'Balance Transfer';
            $log->transaction_type = 2;
            $log->transaction_method = 2;
            $log->account_head_type_id = 4;
            $log->account_head_sub_type_id = 4;
            $log->bank_id = $request->source_bank;
            $log->branch_id = $request->source_branch;
            $log->bank_account_id = $request->source_account;
            $log->cheque_no = $request->source_cheque_no;
            $log->cheque_image = $sourceImage;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->balance_transfer_id = $transfer->id;
            $log->user_id = Auth::id();
            $log->save();

            $log = new TransactionLog();
            $log->department_id = $request->department_id;
            $log->date = $request->date;
            $log->particular = 'Balance Transfer';
            $log->transaction_type = 1;
            $log->transaction_method = 2;
            $log->account_head_type_id = 3;
            $log->account_head_sub_type_id = 3;
            $log->bank_id = $request->target_bank;
            $log->branch_id = $request->target_branch;
            $log->bank_account_id = $request->target_account;
            $log->cheque_no = $request->target_cheque_no;
            $log->cheque_image = $targetImage;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->balance_transfer_id = $transfer->id;
            $log->user_id = Auth::id();
            $log->save();
        }

        return redirect()->route('balance_transfer.add')->with('message', 'Balance transfer successful.');
    }

    public function transactionDatatable() {
        $query = Transaction::with(['accountHeadType', 'accountHeadSubType']);
        return DataTables::eloquent($query)
            ->addColumn('accountHeadType', function(Transaction $transaction) {
                return $transaction->accountHeadType->name??'';
            })
            ->addColumn('accountHeadSubType', function(Transaction $transaction) {
                return $transaction->accountHeadSubType->name??'';
            })
            ->addColumn('action', function(Transaction $transaction) {
                return '<a href="'.route('transaction.details', ['transaction' => $transaction->id]).'" class="btn btn-primary btn-sm">Details</a> <a role="button" data-id="'.$transaction->id.'" class="btn btn-info btn-sm btn-edit">Edit</a>';
            })
            ->editColumn('date', function(Transaction $transaction) {
                return Carbon::parse($transaction->date)->format('d-M-Y');
            })
            ->editColumn('transaction_type', function(Transaction $transaction) {
                if ($transaction->transaction_type == 1)
                    return '<span class="label label-success">Income</span>';
                else
                    return '<span class="label label-warning">Expense</span>';
            })
            ->editColumn('amount', function(Transaction $transaction) {
                return '৳'.number_format($transaction->amount, 2);
            })
            ->orderColumn('date', function ($query, $transaction) {
                $query->orderBy('date', $transaction)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action', 'transaction_type'])
            ->toJson();
    }
}
