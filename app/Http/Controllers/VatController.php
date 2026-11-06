<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\MobileBanking;
use App\Models\SalesOrder;
use App\Models\SalesVat;
use App\Models\Cash;
use Carbon\Carbon;
use SakibRahaman\DecimalToWords\DecimalToWords;
use Rakibhstu\Banglanumber\NumberToBangla;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class VatController extends Controller
{
    public function vatPayment(Request $request){
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();
        $start = date('Y-m-d', strtotime($request->start_date));
        $end = date('Y-m-d', strtotime($request->end_date));
        $query = SalesVat::query();

        $saleVats = [];
        $inWordOfAmount = NULL;
        $inWordBangla = NULL;

        if ($request->end_date && $request->start_date) {
            $query->where('status', 0);
            $query->whereBetween('date', [$start, $end]);
            $saleVats = $query->orderBy('date')->get();

            $inWordOfAmount = DecimalToWords::convert($saleVats->sum('amount'), 'Taka','Poisa');
            $inWordBangla = new NumberToBangla();
        }
        return view('vat_ait.vat_payment',compact('saleVats','inWordOfAmount','accounts','mobileBankings','inWordBangla'));
    }
    public function vatRegister(Request $request){
        $start = date('Y-m-d', strtotime($request->start_date));
        $end = date('Y-m-d', strtotime($request->end_date));
        $query = SalesVat::query();

        $saleVats = [];
        $inWordOfAmount = NULL;
        $inWordBangla = NULL;

        if ($request->end_date && $request->start_date) {
            $query->where('status', 0);
            $query->whereBetween('date', [$start, $end]);
            $saleVats = $query->orderBy('date')->get();

            $inWordOfAmount = DecimalToWords::convert($saleVats->sum('amount'), 'Taka','Poisa');
            $inWordBangla = new NumberToBangla();
        }

        return view('vat_ait.vat_register',compact('saleVats','inWordOfAmount','inWordBangla'));
    }

    public function vatCertificateSixPointSix(Request $request)
    {
        $vats = [];
        $selectParty = null;
        $parties = Customer::where('status', 1)->orderBy('name')->get();
        if ($request->party) {
            $selectParty = Customer::where('id', $request->party)->first();

            $query = SalesVat::where('customer_id', $request->party)->where('status', 1);
            if ($request->start && $request->start != '' && $request->end && $request->end != '') {
                $query->whereBetween('date', [Carbon::parse($request->start)->format('Y-m-d'), Carbon::parse($request->end)->format('Y-m-d')]);
            }
            $vats = $query->orderBy('date')->get();
        }

        return view('vat_ait.vat_certificate_six_point_six', compact('vats', 'selectParty','parties'));
    }

    public function vatPayout(Request $request){
        // dd($request->all());
        $rules = [
            'document' => 'required',
            'date' => 'required',
            'payment_type' => 'required',
        ];
        if ($request->payment_type == '2') {
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }elseif($request->payment_type == '3'){
            $rules['mobile_banking'] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        if (!$request->vat_id){
            return response()->json(['success' => false, 'message' => 'VAT payout is empty data, please check in vat register']);
        }  
        DB::beginTransaction();
        try {
            $pdfFileLink = 'img/no_pdf.pdf';

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $extension = $file->extension();
                if ($extension == 'pdf') {
                    $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
                    $destinationPath = 'uploads/vat_document';
                    $file->move($destinationPath, $filename);
                    $pdfFileLink = 'uploads/vat_document/' . $filename;
                } else {
                    return response()->json(['success' => false, 'message' => 'Uploaded file must be a PDF.']);
                }
            }

            $totalVat = SalesVat::whereIn('id', $request->vat_id)->sum('amount');
            // dd($totalVat);
            $image = null;
            if ($totalVat > 0) {
                if ($request->payment_type == 1 || $request->payment_type == 3) {
                    $transaction = new Transaction();
                    $transaction->sister_concern_id = $request->sister_concern_id;
                    $transaction->transaction_type = 2;
                    $transaction->account_head_type_id = 3; // Vat Payout
                    $transaction->account_head_sub_type_id = 3; // Vat Payout
                    $transaction->transaction_method = $request->payment_type;
                    $transaction->bank_id = $request->payment_type == 2 ? $request->bank : null;
                    $transaction->branch_id = $request->payment_type == 2 ? $request->branch : null;
                    $transaction->bank_account_id = $request->payment_type == 2 ? $request->account : null;
                    $transaction->cheque_no = $request->payment_type == 2 ? $request->cheque_no : null;
                    $transaction->cheque_image = $image;
                    $transaction->amount = $totalVat;
                    $transaction->date = Carbon::parse($request->date)->format('Y-m-d');
                    $transaction->note = $request->notes . ' VAT payout from '.$request->start_date.' to '.$request->end_date;
                    if ($request->payment_type == 3) {
                        $transaction->mobile_banking_id = $request->mobile_banking;
                    }
                    $transaction->user_id = Auth::id();
                    $transaction->save();

                    if ($request->payment_type == 1)
                        Cash::first()->decrement('amount', $totalVat);
                    else
                        MobileBanking::find($request->mobile_banking)->decrement('amount', $totalVat);

                    $log = new TransactionLog();
                    $log->date = Carbon::parse($request->date)->format('Y-m-d');
                    $log->sister_concern_id = 1;
                    $log->particular = 'VAT payout from '.$request->start_date.' to '.$request->end_date;
                    $log->transaction_type = 2;
                    $log->transaction_method = $request->payment_type;
                    $log->account_head_type_id = 3; // Vat Payout
                    $log->account_head_sub_type_id = 3; // Vat Payout
                    $log->amount = $totalVat;
                    $log->note = $request->notes;
                    if ($request->payment_type == 3) {
                        $log->mobile_banking_id = $request->mobile_banking;
                    }
                    $log->transaction_id = $transaction->id;
                    $log->user_id = Auth::id();
                    $log->save();

                } else {
                    $bankAccount = BankAccount::find($request->account);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/purchase_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/purchase_payment_cheque/'.$filename;
                    }

                    $transaction = new Transaction();
                    $transaction->sister_concern_id = $request->sister_concern_id;
                    $transaction->transaction_type = 2;
                    $transaction->account_head_type_id = 3; // Vat Payout
                    $transaction->account_head_sub_type_id = 3; // Vat Payout
                    $transaction->transaction_method = $request->payment_type;
                    $transaction->bank_id = $request->payment_type == 2 ? $request->bank : null;
                    $transaction->branch_id = $request->payment_type == 2 ? $request->branch : null;
                    $transaction->bank_account_id = $request->payment_type == 2 ? $request->account : null;
                    $transaction->cheque_no = $request->payment_type == 2 ? $request->cheque_no : null;
                    $transaction->cheque_image = $image;
                    $transaction->amount = $totalVat;
                    $transaction->date = Carbon::parse($request->date)->format('Y-m-d');
                    $transaction->note = $request->notes . ' VAT payout from '.$request->start_date.' to '.$request->end_date;
                    if ($request->payment_type == 3) {
                        $transaction->mobile_banking_id = $request->mobile_banking;
                    }
                    $transaction->user_id = Auth::id();
                    $transaction->save();

                    BankAccount::find($request->account)->decrement('balance', $totalVat);

                    $log = new TransactionLog();
                    $log->sister_concern_id = 1;
                    $log->date = Carbon::parse($request->date)->format('Y-m-d');
                    $log->particular = 'VAT payout from '.$request->start_date.' to '.$request->end_date;
                    $log->transaction_type = 2;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 3; // Vat Payout
                    $log->account_head_sub_type_id = 3; // Vat Payout
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account;
                    $log->cheque_no = $request->cheque_no;
                    $log->cheque_image = $image;
                    $log->amount = $totalVat;
                    $log->note = $request->notes;
                    $log->transaction_id = $transaction->id;
                    $log->user_id = Auth::id();
                    $log->save();
                }
            }
            foreach ($request->vat_id as $vatId) {
                $salesVat = SalesVat::find($vatId);
                $salesVat->status = 1;
                $salesVat->vat_document = $pdfFileLink;
                $salesVat->payment_date = Carbon::parse($request->date)->format('Y-m-d');
                $salesVat->save();

                $salesOrder = SalesOrder::find($salesVat->sales_order_id);
                $salesOrder->is_vat_paid = 1;
                $salesOrder->save();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'VAT checkout successful']);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
}
