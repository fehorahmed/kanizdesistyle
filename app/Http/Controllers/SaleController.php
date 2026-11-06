<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Department;
use App\Models\Customer;
use App\Models\EcomOrderInfo;
use App\Models\MobileBanking;
use App\Models\OrderLog;
use App\Models\PurchaseInventory;
use App\Models\PurchaseInventoryLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\PurchasePayment;
use App\Models\Product;
use App\Models\SalesOrderProduct;
use App\Models\SalePayment;
use App\Models\SalesOrder;
use App\Models\SalesVat;
use App\Models\Service;
use App\Models\SisterConcern;
use App\Models\TransactionLog;
use App\Models\Unit;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;
use SakibRahaman\DecimalToWords\DecimalToWords;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{
    public function pos()
    {
        $sisterConcerns = SisterConcern::where('id', 1)->where('status', 1)->orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $products = Product::where('status', 1)->orderBy('name')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();

        return view('sale.pos.pos', compact('banks', 'accounts', 'sisterConcerns', 'products', 'mobileBankings'));
    }

    public function salesOrder()
    {
        $sisterConcerns = SisterConcern::where('id', 1)->where('status', 1)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $products = Product::where('sister_concern_id', 1)->where('status', 1)->orderBy('name')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();

        return view('sale.sales_order.create', compact(
            'banks',
            'accounts',
            'customers',
            'sisterConcerns',
            'products',
            'mobileBankings'
        ));
    }

    public function salesOrderPost(Request $request)
    {
        //dd($request->all());
        $total = $request->total;
        $due = $request->due_total;
        if ($request->sale_type == 2) { //For Online
            $request['payment_type'] = 1;
            $request['paid'] = 0;
            $request['paid_two'] = 0;
            $request['paid_three'] = 0;
        }
        $rules = [
            'sister_concern_id' => 'required',
            'customer' => 'required',
            'product.*' => 'required',
            'batch.*' => 'required',
            'quantity.*' => 'required|numeric|min:.01',
            'unit_price.*' => 'required|numeric|min:0',
            'date' => 'required|date',
            'received_by' => 'nullable|string|max:255',
            'discount.*' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0|max:' . $total,
            'paid_two' => 'required|numeric|min:0|max:' . $total,
            'paid_three' => 'required|numeric|min:0|max:' . $total,
            'payment_type' => 'required',
            'sale_type' => 'required',
        ];
        if ($request->paid_two > 0) {
            $rules['payment_type_two'] = 'required';
            if ($request->payment_type_two == '2') {
                $rules['account_two'] = 'required';
                //$rules['cheque_no'] = 'nullable|string|max:255';
                //$rules['cheque_image'] = 'nullable|image';
            }
            if ($request->payment_type_two == '3') {
                $rules['mobile_banking_two'] = 'required';
            }
        }
        if ($request->paid_three > 0) {
            $rules['payment_type_three'] = 'required';
            if ($request->payment_type_three == '2') {
                $rules['account_three'] = 'required';
                //$rules['cheque_no'] = 'nullable|string|max:255';
                //$rules['cheque_image'] = 'nullable|image';
            }
            if ($request->payment_type_three == '3') {
                $rules['mobile_banking_three'] = 'required';
            }
        }
        if ($request->payment_type == '2') {
            $rules['account'] = 'required';
            //$rules['cheque_no'] = 'nullable|string|max:255';
            //$rules['cheque_image'] = 'nullable|image';
        }
        if ($request->payment_type == '3') {
            $rules['mobile_banking'] = 'required';
        }

        $request->validate($rules);

        $available = true;
        $message = '';

        if ($request->paid_three + $request->paid_two + $request->paid > $total) {
            //throw new Err
            return redirect()->back()->withInput()->with('error', 'Paid amount is not greater than Order amount!');
        }

        if ($request->product) {
            foreach ($request->product as $key => $product) {
                // $batch_no = str_replace("Batch-", "", $request->batch[$key]);
                $batch_no = $request->batch[$key];
                $product = Product::where('id', $product)->first();
                $inventory = PurchaseInventory::where('product_id', $product->id)
                    ->where('batch_no', $batch_no)
                    ->where('sister_concern_id', $request->sister_concern_id)
                    ->first();
                // dd($request->quantity[$key]);
                if ($request->quantity[$key] > $inventory->quantity) {
                    $available = false;
                    $message = 'Insufficient ' . $inventory->product->name;
                    break;
                }
            }
        }
        // return($request->quantity[0]);
        if (!$available) {
            return redirect()->back()->withInput()->with('error', $message);
        }

        $lastSalesOrder = SalesOrder::where('sister_concern_id', $request->sister_concern_id)->where('sale_type', $request->sale_type)->latest('id')->first();

        // Generate the next code
        $nextCodeNumber = !empty($lastSalesOrder) ? intval(substr($lastSalesOrder->order_no, 5)) + 1 : 1;

        // Format the new code as 'p-000001'
        if ($request->sister_concern_id == 1) {
            $newCodeOrder = 'SO-' . str_pad($nextCodeNumber, 8, '0', STR_PAD_LEFT);
        } else {
            $newCodeOrder = 'SO-' . str_pad($nextCodeNumber, 8, '0', STR_PAD_LEFT);
        }

        DB::beginTransaction();
        try {

            $discountAmts = $request->input('discount_amt');
            if (is_array($discountAmts)) {
                $totalDiscount = array_sum($discountAmts);
            }
            $order = new SalesOrder();
            $order->order_no = $newCodeOrder;
            $order->sister_concern_id = $request->sister_concern_id;
            $order->customer_id = $request->customer;
            $order->sale_type = $request->sale_type;
            $order->received_by = $request->received_by;
            $order->date = date('Y-m-d', strtotime($request->date));
            $order->sub_total = 0;
            $order->discount = floor($totalDiscount);
            $order->total = 0;
            $order->paid = $request->paid + $request->paid_two + $request->paid_three;
            $order->due = 0;
            $order->opening_sale = 0;
            $order->note = $request->notes;
            $order->user_id = Auth::id();
            if ($request->sale_type == 2) {
                $order->status = 0;
            }
            $order->save();

            $counter = 0;
            $subTotal = 0;
            $totalVat = 0;
            $totalDiscount = 0;
            $buyingPrice = 0;
            $sellingPrice = 0;

            if ($request->product) {
                foreach ($request->product as $productId) {
                    // $batch_no = str_replace("Batch-", "", $request->batch[$counter]);
                    $batch_no = $request->batch[$counter];
                    $product = Product::where('id', $productId)->first();
                    $inventory = PurchaseInventory::where('product_id', $product->id)
                        ->where('sister_concern_id', $request->sister_concern_id)
                        ->where('batch_no', $batch_no)
                        ->with('product')
                        ->first();

                    $buyingPrice += $inventory->unit_price * $request->quantity[$counter];
                    $sellingPrice += $request->unit_price[$counter] * $request->quantity[$counter];

                    $salesOrderProduct = new SalesOrderProduct();
                    $salesOrderProduct->sister_concern_id = $request->sister_concern_id;
                    $salesOrderProduct->product_id = $product->id;
                    $salesOrderProduct->sales_order_id = $order->id;
                    $salesOrderProduct->batch_no = $batch_no;
                    $salesOrderProduct->name = $product->name;
                    $salesOrderProduct->serial = $product->code;
                    $salesOrderProduct->sale_type = $request->sale_type;
                    $salesOrderProduct->quantity = $request->quantity[$counter];
                    $salesOrderProduct->discount_percentage = $request->discount[$counter];
                    $salesOrderProduct->discount_amt = $request->discount_amt[$counter];
                    
                    $salesOrderProduct->vat_percent = $request->vat_percent[$counter];
                    $vat_amount = $request->unit_price[$counter] * ($request->vat_percent[$counter] / 100);
                    $salesOrderProduct->vat_amount = $vat_amount;

                    $salesOrderProduct->unit_price = $request->unit_price[$counter];
                    $salesOrderProduct->buy_price = $inventory->unit_price ?? 0;
                    $salesOrderProduct->date = date('Y-m-d', strtotime($request->date));
                    $salesOrderProduct->total = $request->quantity[$counter] * $request->unit_price[$counter];
                    $salesOrderProduct->user_id = Auth::id();
                    $salesOrderProduct->save();

                    $inventoryLog = new PurchaseInventoryLog();
                    $inventoryLog->sister_concern_id = $request->sister_concern_id;
                    $inventoryLog->purchase_inventory_id = $inventory->id;
                    $inventoryLog->product_id = $product->id;
                    $inventoryLog->sales_order_id = $order->id;
                    $inventoryLog->batch_no = $batch_no;
                    $inventoryLog->type = 2;
                    $inventoryLog->sale_type = $request->sale_type;
                    $inventoryLog->date = date('Y-m-d', strtotime($request->date));
                    $inventoryLog->quantity = $request->quantity[$counter];
                    $inventoryLog->discount_percentage = $request->discount[$counter];
                    $inventoryLog->discount_amt = $request->discount_amt[$counter];
                    $inventoryLog->unit_price = $inventory->unit_price;
                    $inventoryLog->selling_price = $request->unit_price[$counter];
                    $inventoryLog->user_id = Auth::id();
                    $inventoryLog->save();


                    $inventory->quantity = $product->inventory_qty($product->id, $request->sister_concern_id, $batch_no);
                    $inventory->save();

                    $subTotal += $request->quantity[$counter] * $request->unit_price[$counter];
                    
                    $totalVat += $vat_amount;
                    $totalDiscount += $request->discount_amt[$counter];
                    $counter++;
                }
            }

            $counter = 0;

            $order->sub_total = $subTotal;
            $order->vat_amount = $totalVat;
            $total = $subTotal - floor($totalDiscount) + floor($totalVat);
            $order->total = $total;
            $order->due = $subTotal - $request->paid - $request->paid_two - $request->paid_three - floor($totalDiscount) + floor($totalVat);
            $order->save();

            //Vat amount
            if ($totalVat > 0) {
                $saleVat = new SalesVat();
                $saleVat->sales_order_id  = $order->id;
                $saleVat->customer_id = $request->customer;
                $saleVat->amount = $totalVat;
                $saleVat->date = Carbon::parse($request->date)->format('Y-m-d');
                $saleVat->status = 0;
                $saleVat->save();
            }

            //Logs
            $orderLog = new OrderLog();
            $orderLog->order_type = 'SALE';
            $orderLog->order_id  = $order->id;
            $orderLog->created_by  = Auth::id();
            $orderLog->status  = 'SALE CREATE';
            $orderLog->save();

            // Sales Payment
            if ($request->paid > 0) {
                if ($request->payment_type == 1 || $request->payment_type == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sale_type = $request->sale_type;
                    $payment->transaction_method = $request->payment_type;
                    $payment->received_type = 1;
                    if ($request->payment_type == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking;
                    }
                    $payment->amount = $request->paid;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    if ($request->payment_type == 1)
                        Cash::first()->increment('amount', $request->paid);
                    else
                        MobileBanking::find($request->mobile_banking)->increment('amount', $request->paid);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->sale_type = $request->sale_type;
                    if ($request->payment_type == 3) {
                        $log->mobile_banking_id = $request->mobile_banking;
                    }
                    $log->transaction_method = $request->payment_type;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->paid;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->transaction_method = 2;
                    $payment->sale_type = $request->sale_type;
                    $payment->received_type = 1;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->cheque_image = $image;
                    $payment->amount = $request->paid;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    BankAccount::find($request->account)->increment('balance', $request->paid);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->sale_type = $request->sale_type;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account;
                    $log->cheque_no = $request->cheque_no;
                    $log->cheque_image = $image;
                    $log->amount = $request->paid;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                }
            }

            // Sales Payment
            if ($request->paid_two > 0) {
                if ($request->payment_type_two == 1 || $request->payment_type_two == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sale_type = $request->sale_type;
                    $payment->transaction_method = $request->payment_type_two;
                    $payment->received_type = 1;
                    if ($request->payment_type_two == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking_two;
                    }
                    $payment->amount = $request->paid_two;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    if ($request->payment_type_two == 1)
                        Cash::first()->increment('amount', $request->paid_two);
                    else
                        MobileBanking::find($request->mobile_banking_two)->increment('amount', $request->paid_two);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->sale_type = $request->sale_type;
                    if ($request->payment_type_two == 3) {
                        $log->mobile_banking_id = $request->mobile_banking_two;
                    }
                    $log->transaction_method = $request->payment_type_two;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->paid_two;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account_two);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->transaction_method = 2;
                    $payment->sale_type = $request->sale_type;
                    $payment->received_type = 1;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account_two;
                    $payment->cheque_no = $request->cheque_no ?? '';
                    $payment->cheque_image = $image;
                    $payment->amount = $request->paid_two;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    BankAccount::find($request->account_two)->increment('balance', $request->paid_two);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->sale_type = $request->sale_type;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account_two;
                    $log->cheque_no = $request->cheque_no ?? '';
                    $log->cheque_image = $image;
                    $log->amount = $request->paid_two;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                }
            }

            // Sales Payment
            if ($request->paid_three > 0) {
                if ($request->payment_type_three == 1 || $request->payment_type_three == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sale_type = $request->sale_type;
                    $payment->transaction_method = $request->payment_type_three;
                    $payment->received_type = 1;
                    if ($request->payment_type_three == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking_three;
                    }
                    $payment->amount = $request->paid_three;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    if ($request->payment_type_three == 1)
                        Cash::first()->increment('amount', $request->paid_three);
                    else
                        MobileBanking::find($request->mobile_banking_three)->increment('amount', $request->paid_three);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->sale_type = $request->sale_type;
                    if ($request->payment_type_three == 3) {
                        $log->mobile_banking_id = $request->mobile_banking_three;
                    }
                    $log->transaction_method = $request->payment_type_three;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->paid_three;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account_three);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->transaction_method = 2;
                    $payment->sale_type = $request->sale_type;
                    $payment->received_type = 1;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account_three;
                    $payment->cheque_no = $request->cheque_no ?? '';
                    $payment->cheque_image = $image;
                    $payment->amount = $request->paid_three;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    BankAccount::find($request->account_three)->increment('balance', $request->paid_three);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->sale_type = $request->sale_type;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account_three;
                    $log->cheque_no = $request->cheque_no ?? '';
                    $log->cheque_image = $image;
                    $log->amount = $request->paid_three;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                }
            }

            // Buying Price log
            $log = new TransactionLog();
            $log->sister_concern_id = $request->sister_concern_id;
            $log->date = date('Y-m-d', strtotime($request->date));
            $log->particular = 'Buying price for ' . $order->order_no;
            $log->sale_type = $request->sale_type;
            $log->transaction_type = 4;
            $log->transaction_method = 0;
            $log->account_head_type_id = 0;
            $log->account_head_sub_type_id = 0;
            $log->amount = $buyingPrice;
            $log->sales_order_id = $order->id;
            $log->customer_id = $order->customer_id;
            $log->note = $request->notes;
            $log->user_id = Auth::id();
            $log->save();

            // Selling Price log
            $log = new TransactionLog();
            $log->sister_concern_id = $request->sister_concern_id;
            $log->sale_type = $request->sale_type;
            $log->date = date('Y-m-d', strtotime($request->date));
            $log->particular = 'Selling price for ' . $order->order_no;
            $log->transaction_type = 5;
            $log->transaction_method = 0;
            $log->account_head_type_id = 0;
            $log->account_head_sub_type_id = 0;
            $log->amount = $sellingPrice + floor($totalVat);
            $log->discount_amt = floor($totalDiscount);
            $log->vat_amount = floor($totalVat);
            $log->sales_order_id = $order->id;
            $log->customer_id = $order->customer_id;
            $log->note = $request->notes;
            $log->user_id = Auth::id();
            $log->save();

            DB::commit();
            if ($request->type_of_sale == 'pos') {
                return response()->json([
                    'status' => true,
                    'message' => 'Sales order created successfully.',
                    'redirect_url' => route('sale_receipt.details', ['order' => $order->id]),
                ]);
            } else {
                return redirect()->route('sale_receipt.details', ['order' => $order->id]);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
            if ($request->type_of_sale == 'pos') {
                return response()->json([
                    'status' => false,
                    'message' => $exception->getMessage() . ' Line:' . $exception->getLine(),
                ]);
            } else {
                return redirect()->back()->with('error', $exception->getMessage())->withInput();
            }
        }
    }
    public function posEdit(SalesOrder $order)
    {
        $sisterConcerns = SisterConcern::where('id', 1)->where('status', 1)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $products = Product::where('status', 1)->orderBy('name')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();

        return view('sale.pos.pos_edit', compact('order','customers', 'banks', 'accounts', 'sisterConcerns', 'products', 'mobileBankings'));
    }
    public function salesOrderEdit(SalesOrder $order)
    {
        $sisterConcerns = SisterConcern::where('id', 1)->where('status', 1)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $products = Product::where('sister_concern_id', 1)->where('status', 1)->orderBy('name')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();

        return view('sale.sales_order.edit', compact(
            'banks','order',
            'accounts',
            'customers',
            'sisterConcerns',
            'products',
            'mobileBankings'
        ));
    }
    public function salesOrderEditPost(SalesOrder $order, Request $request){
        
        $total = $request->total;
        $due = $request->due_total;
        // if ($request->sale_type == 2) { //For Online
        //     $request['payment_type'] = 1;
        //     $request['paid'] = 0;
        //     $request['paid_two'] = 0;
        //     $request['paid_three'] = 0;
        // }
        $rules = [
            'sister_concern_id' => 'required',
            'customer' => 'required',
            'product.*' => 'required',
            'batch.*' => 'required',
            'quantity.*' => 'required|numeric|min:.01',
            'unit_price.*' => 'required|numeric|min:0',
            'date' => 'required|date',
            'received_by' => 'nullable|string|max:255',
            'discount.*' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0|max:' . $total,
            'paid_two' => 'required|numeric|min:0|max:' . $total,
            'paid_three' => 'required|numeric|min:0|max:' . $total,
            'payment_type' => 'required',
            'sale_type' => 'required',
        ];
        if ($request->paid_two > 0) {
            $rules['payment_type_two'] = 'required';
            if ($request->payment_type_two == '2') {
                $rules['account_two'] = 'required';
            }
            if ($request->payment_type_two == '3') {
                $rules['mobile_banking_two'] = 'required';
            }
        }
        if ($request->paid_three > 0) {
            $rules['payment_type_three'] = 'required';
            if ($request->payment_type_three == '2') {
                $rules['account_three'] = 'required';
            }
            if ($request->payment_type_three == '3') {
                $rules['mobile_banking_three'] = 'required';
            }
        }
        if ($request->payment_type == '2') {
            $rules['account'] = 'required';
        }
        if ($request->payment_type == '3') {
            $rules['mobile_banking'] = 'required';
        }

        $request->validate($rules);

        $available = true;
        $message = '';

        if ($request->paid_three + $request->paid_two + $request->paid > $total) {
            return redirect()->back()->withInput()->with('error', 'Paid amount is not greater than Order amount!');
        }

        if ($request->product) {
            foreach ($request->product as $key => $productId) {
                // $batch_no = str_replace("Batch-", "", $request->batch[$key]);
                $batch_no = $request->batch[$key];
                $product = Product::where('id', $productId)->first();
                $inventory = PurchaseInventory::where('product_id', $product->id)
                    ->where('batch_no', $batch_no)
                    ->where('sister_concern_id', $request->sister_concern_id)
                    ->first();
                $prev_qty = SalesOrderProduct::where('sister_concern_id',$order->sister_concern_id)->where('sales_order_id',$order->id)->where('product_id',$productId)->first()->quantity ?? 0;
                // dd($request->quantity[$key]);
                if ($request->quantity[$key] > $inventory->quantity + $prev_qty) {
                    $available = false;
                    $message = 'Insufficient ' . $inventory->product->name;
                    break;
                }
            }
        }
        // return($request->quantity[0]);
        if (!$available) {
            return redirect()->back()->withInput()->with('error', $message);
        }

        DB::beginTransaction();
        try {
            //dd($request->all());
            //Order previous inventory return
            foreach($order->products as $orderproduct){
                $inventory = PurchaseInventory::where('product_id', $orderproduct->product_id)
                        ->where('sister_concern_id', $orderproduct->sister_concern_id)
                        ->where('batch_no', $orderproduct->batch_no)
                        ->first();
                $inventory->quantity += $orderproduct->quantity;
                $inventory->save();

                if(!in_array($orderproduct->product_id,$request->product)){
                    SalesOrderProduct::where('sister_concern_id',$order->sister_concern_id)->where('sales_order_id',$order->id)->where('product_id',$orderproduct->product_id)->delete();
                    PurchaseInventoryLog::where('sister_concern_id',$order->sister_concern_id)->where('sales_order_id',$order->id)->where('product_id',$orderproduct->product_id)->delete();
                }
            }
            //Sale Payments Delete
            $pre_payments = SalePayment::where('sister_concern_id',$order->sister_concern_id)->where('sales_order_id',$order->id)->where('customer_id',$order->customer_id)->get();
            if(count($pre_payments) > 0){
                foreach($pre_payments as $pre_payment){
                    if($pre_payment->transaction_method == 1){
                        Cash::first()->decrement('amount', $pre_payment->amount);
                    }elseif($pre_payment->transaction_method == 2){
                        BankAccount::find($pre_payment->bank_account_id)->decrement('balance', $pre_payment->amount);
                    }elseif($pre_payment->transaction_method == 3){
                        MobileBanking::find($pre_payment->mobile_banking_id)->decrement('amount', $pre_payment->amount);
                    }
                    TransactionLog::where('sister_concern_id',$pre_payment->sister_concern_id)->where('sale_payment_id',$pre_payment->id)->delete();
                }
                SalePayment::where('sister_concern_id',$order->sister_concern_id)->where('sales_order_id',$order->id)->where('customer_id',$order->customer_id)->delete();
            }

            $discountAmts = $request->input('discount_amt');
            if (is_array($discountAmts)) {
                $totalDiscount = array_sum($discountAmts);
            }
            $order->sister_concern_id = $request->sister_concern_id;
            $order->customer_id = $request->customer;
            $order->sale_type = $request->sale_type;
            $order->received_by = $request->received_by;
            $order->date = date('Y-m-d', strtotime($request->date));
            $order->sub_total = 0;
            $order->discount = $totalDiscount;
            $order->total = 0;
            $order->paid = $request->paid + $request->paid_two + $request->paid_three;
            $order->due = 0;
            $order->opening_sale = 0;
            $order->note = $request->notes;
            $order->user_id = Auth::id();
            // if ($request->sale_type == 2) {
            //     $order->status = 0;
            // }
            $order->save();

            //Logs
            $orderLog = new OrderLog();
            $orderLog->order_type = 'SALE';
            $orderLog->order_id  = $order->id;
            $orderLog->created_by  = Auth::id();
            $orderLog->status  = 'SALE UPDATE';
            $orderLog->save();

            $counter = 0;
            $subTotal = 0;
            $totalVat = 0;
            $totalDiscount = 0;
            $buyingPrice = 0;
            $sellingPrice = 0;

            if ($request->product) {
                foreach ($request->product as $productId) {
                    //$batch_no = str_replace("Batch-", "", $request->batch[$counter]);
                    $batch_no = $request->batch[$counter];
                    $product = Product::where('id', $productId)->first();
                    $inventory = PurchaseInventory::where('product_id', $product->id)
                        ->where('sister_concern_id', $request->sister_concern_id)
                        ->where('batch_no', $batch_no)
                        ->with('product')
                        ->first();

                    $buyingPrice += $inventory->unit_price * $request->quantity[$counter];
                    $sellingPrice += $request->unit_price[$counter] * $request->quantity[$counter];

                    $salesOrderProduct = SalesOrderProduct::where('sister_concern_id',$order->sister_concern_id)->where('sales_order_id',$order->id)->where('product_id',$product->id)->first();
                    if(!$salesOrderProduct){
                        $salesOrderProduct = new SalesOrderProduct();
                    }
                    $salesOrderProduct->sister_concern_id = $request->sister_concern_id;
                    $salesOrderProduct->product_id = $product->id;
                    $salesOrderProduct->sales_order_id = $order->id;
                    $salesOrderProduct->batch_no = $batch_no;
                    $salesOrderProduct->name = $product->name;
                    $salesOrderProduct->serial = $product->code;
                    $salesOrderProduct->sale_type = $request->sale_type;
                    $salesOrderProduct->quantity = $request->quantity[$counter];

                    $salesOrderProduct->vat_percent = $request->vat_percent[$counter];
                    $vat_amount = $request->unit_price[$counter] * ($request->vat_percent[$counter] / 100);
                    $salesOrderProduct->vat_amount = $vat_amount;

                    $salesOrderProduct->discount_percentage = $request->discount[$counter];
                    $salesOrderProduct->discount_amt = $request->discount_amt[$counter];
                    $salesOrderProduct->unit_price = $request->unit_price[$counter];
                    $salesOrderProduct->buy_price = $inventory->unit_price ?? 0;
                    $salesOrderProduct->date = date('Y-m-d', strtotime($request->date));
                    $salesOrderProduct->total = $request->quantity[$counter] * $request->unit_price[$counter];
                    $salesOrderProduct->user_id = Auth::id();
                    $salesOrderProduct->save();

                    $inventoryLog = PurchaseInventoryLog::where('sister_concern_id',$order->sister_concern_id)->where('sales_order_id',$order->id)->where('product_id',$product->id)->first();
                    if(!$inventoryLog){
                        $inventoryLog = new PurchaseInventoryLog();
                    }
                    $inventoryLog->sister_concern_id = $request->sister_concern_id;
                    $inventoryLog->purchase_inventory_id = $inventory->id;
                    $inventoryLog->product_id = $product->id;
                    $inventoryLog->sales_order_id = $order->id;
                    $inventoryLog->batch_no = $batch_no;
                    $inventoryLog->type = 2;
                    $inventoryLog->sale_type = $request->sale_type;
                    $inventoryLog->date = date('Y-m-d', strtotime($request->date));
                    $inventoryLog->quantity = $request->quantity[$counter];
                    $inventoryLog->discount_percentage = $request->discount[$counter];
                    $inventoryLog->discount_amt = $request->discount_amt[$counter];
                    $inventoryLog->unit_price = $inventory->unit_price;
                    $inventoryLog->selling_price = $request->unit_price[$counter];
                    $inventoryLog->user_id = Auth::id();
                    $inventoryLog->save();

                    $inventory->quantity = $product->inventory_qty($product->id, $request->sister_concern_id, $batch_no);
                    $inventory->save();

                    $subTotal += $request->quantity[$counter] * $request->unit_price[$counter];
                    
                    $totalVat += $vat_amount;
                    $totalDiscount += $request->discount_amt[$counter];
                    $counter++;
                }
            }

            $counter = 0;

            $order->sub_total = $subTotal;
            $order->vat_amount = $totalVat;
            $total = $subTotal - floor($totalDiscount) + floor($totalVat);
            $order->total = $total;
            $order->due = $subTotal - $request->paid - $request->paid_two - $request->paid_three - floor($totalDiscount) + floor($totalVat);
            $order->save();

            //Vat amount
            if ($totalVat > 0) {
                $saleVat = SalesVat::where('sales_order_id',$order->id)->first();
                if(!$saleVat){
                    $saleVat = new SalesVat();
                }
                $saleVat->sales_order_id  = $order->id;
                $saleVat->customer_id = $request->customer;
                $saleVat->amount = $totalVat;
                $saleVat->date = Carbon::parse($request->date)->format('Y-m-d');
                $saleVat->status = 0;
                $saleVat->save();
            }

            // Sales Payment
            if ($request->paid > 0) {
                if ($request->payment_type == 1 || $request->payment_type == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sale_type = $request->sale_type;
                    $payment->transaction_method = $request->payment_type;
                    $payment->received_type = 1;
                    if ($request->payment_type == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking;
                    }
                    $payment->amount = $request->paid;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    if ($request->payment_type == 1)
                        Cash::first()->increment('amount', $request->paid);
                    else
                        MobileBanking::find($request->mobile_banking)->increment('amount', $request->paid);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->sale_type = $request->sale_type;
                    if ($request->payment_type == 3) {
                        $log->mobile_banking_id = $request->mobile_banking;
                    }
                    $log->transaction_method = $request->payment_type;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->paid;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->transaction_method = 2;
                    $payment->sale_type = $request->sale_type;
                    $payment->received_type = 1;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->cheque_image = $image;
                    $payment->amount = $request->paid;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    BankAccount::find($request->account)->increment('balance', $request->paid);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->sale_type = $request->sale_type;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account;
                    $log->cheque_no = $request->cheque_no;
                    $log->cheque_image = $image;
                    $log->amount = $request->paid;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                }
            }

            // Sales Payment
            if ($request->paid_two > 0) {
                if ($request->payment_type_two == 1 || $request->payment_type_two == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sale_type = $request->sale_type;
                    $payment->transaction_method = $request->payment_type_two;
                    $payment->received_type = 1;
                    if ($request->payment_type_two == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking_two;
                    }
                    $payment->amount = $request->paid_two;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    if ($request->payment_type_two == 1)
                        Cash::first()->increment('amount', $request->paid_two);
                    else
                        MobileBanking::find($request->mobile_banking_two)->increment('amount', $request->paid_two);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->sale_type = $request->sale_type;
                    if ($request->payment_type_two == 3) {
                        $log->mobile_banking_id = $request->mobile_banking_two;
                    }
                    $log->transaction_method = $request->payment_type_two;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->paid_two;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account_two);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->transaction_method = 2;
                    $payment->sale_type = $request->sale_type;
                    $payment->received_type = 1;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account_two;
                    $payment->cheque_no = $request->cheque_no ?? '';
                    $payment->cheque_image = $image;
                    $payment->amount = $request->paid_two;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    BankAccount::find($request->account_two)->increment('balance', $request->paid_two);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->sale_type = $request->sale_type;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account_two;
                    $log->cheque_no = $request->cheque_no ?? '';
                    $log->cheque_image = $image;
                    $log->amount = $request->paid_two;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                }
            }

            // Sales Payment
            if ($request->paid_three > 0) {
                if ($request->payment_type_three == 1 || $request->payment_type_three == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sale_type = $request->sale_type;
                    $payment->transaction_method = $request->payment_type_three;
                    $payment->received_type = 1;
                    if ($request->payment_type_three == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking_three;
                    }
                    $payment->amount = $request->paid_three;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    if ($request->payment_type_three == 1)
                        Cash::first()->increment('amount', $request->paid_three);
                    else
                        MobileBanking::find($request->mobile_banking_three)->increment('amount', $request->paid_three);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->sale_type = $request->sale_type;
                    if ($request->payment_type_three == 3) {
                        $log->mobile_banking_id = $request->mobile_banking_three;
                    }
                    $log->transaction_method = $request->payment_type_three;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->paid_three;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account_three);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->transaction_method = 2;
                    $payment->sale_type = $request->sale_type;
                    $payment->received_type = 1;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account_three;
                    $payment->cheque_no = $request->cheque_no ?? '';
                    $payment->cheque_image = $image;
                    $payment->amount = $request->paid_three;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->note = $request->notes;
                    $payment->save();

                    BankAccount::find($request->account_three)->increment('balance', $request->paid_three);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Received for ' . $order->order_no;
                    $log->sale_type = $request->sale_type;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account_three;
                    $log->cheque_no = $request->cheque_no ?? '';
                    $log->cheque_image = $image;
                    $log->amount = $request->paid_three;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->note = $request->notes;
                    $log->save();
                }
            }

            // Buying Price log
            //$log = new TransactionLog();
            $log = TransactionLog::where('sister_concern_id',$request->sister_concern_id)->where('sales_order_id',$order->id)->where('transaction_type',4)->first();
            $log->sister_concern_id = $request->sister_concern_id;
            $log->date = date('Y-m-d', strtotime($request->date));
            $log->particular = 'Buying price for ' . $order->order_no;
            $log->sale_type = $request->sale_type;
            $log->transaction_type = 4;
            $log->transaction_method = 0;
            $log->account_head_type_id = 0;
            $log->account_head_sub_type_id = 0;
            $log->amount = $buyingPrice;
            $log->sales_order_id = $order->id;
            $log->customer_id = $order->customer_id;
            $log->note = $request->notes;
            $log->user_id = Auth::id();
            $log->save();

            // Selling Price log
            // $log = new TransactionLog();
            $log = TransactionLog::where('sister_concern_id',$request->sister_concern_id)->where('sales_order_id',$order->id)->where('transaction_type',5)->first();
            $log->sister_concern_id = $request->sister_concern_id;
            $log->sale_type = $request->sale_type;
            $log->date = date('Y-m-d', strtotime($request->date));
            $log->particular = 'Selling price for ' . $order->order_no;
            $log->transaction_type = 5;
            $log->transaction_method = 0;
            $log->account_head_type_id = 0;
            $log->account_head_sub_type_id = 0;
            $log->amount = $sellingPrice + floor($totalVat);
            $log->discount_amt = floor($totalDiscount);
            $log->vat_amount = floor($totalVat);
            $log->sales_order_id = $order->id;
            $log->customer_id = $order->customer_id;
            $log->note = $request->notes;
            $log->user_id = Auth::id();
            $log->save();

            DB::commit();
            if ($request->type_of_sale == 'pos') {
                return response()->json([
                    'status' => true,
                    'message' => 'Sales order updated successfully.',
                    'redirect_url' => route('sale_receipt.details', ['order' => $order->id]),
                ]);
            } else {
                return redirect()->route('sale_receipt.details', ['order' => $order->id]);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception->getLine());
            if ($request->type_of_sale == 'pos') {
                return response()->json([
                    'status' => false,
                    'message' => $exception->getMessage() . ' Line:' . $exception->getLine(),
                ]);
            } else {
                return redirect()->back()->with('error', $exception->getMessage())->withInput();
            }
        }
    }

    public function salesReturnFM()
    {
        $departments = Department::where('id', 1)->where('status', 1)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $products = Product::where('sister_concern_id', 1)->where('status', 1)->orderBy('name')->get();


        return view('sale.sales_return.create', compact(
            'banks',
            'accounts',
            'customers',
            'departments',
            'products'
        ));
    }
    public function getCustomerSalesOrder(Request $request)
    {
        $orders = SalesOrder::where('sale_type',$request->sale_type)->where('customer_id', $request->customer)->where('is_return',0)->orderBy('id', 'desc')->first()->toArray();
        return response($orders);
    }
    public function getSalesOrderItem(Request $request)
    {
        $order = SalesOrder::find($request->orderId);
        $orderPayments = SalePayment::where('sales_order_id',$order->id)->get();
        $orderProducts = SalesOrderProduct::where('sales_order_id', $request->orderId)->get();
        $orderProductHtml = view('sale.sales_return.order_products', compact('orderProducts'))->render();
        $orderPaymentsHtml = view('sale.sales_return.return_order_payments',compact('orderPayments'))->render();

        return response()->json([
            'html' => $orderProductHtml,
            'orderPaymentsHtml' => $orderPaymentsHtml,
            'order' => $order,
        ]);
    }
    public function salesReturnFMPost(Request $request)
    {
        $rules = [
            'customer' => 'required',
            'order' => 'required',
            'return_quantity.*' => 'required|numeric|min:0',
            'return_paid' => 'required|numeric|min:0',
            'payment_type' => 'required',
        ];
        if ($request->payment_type == '2') {
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }
        $request->validate($rules);

        DB::beginTransaction();
        try {
            $order = SalesOrder::find($request->order);
            //dd($order);
            $counter = 0;
            $returnPrice = 0;

            if ($request->order_product_id) {
                foreach ($request->order_product_id as $order_product_id) {
                    $salesOrderProduct = SalesOrderProduct::find($order_product_id);
                    $product = Product::where('id', $salesOrderProduct->product_id)->first();

                    $inventory = PurchaseInventory::where('product_id', $product->id)
                        ->where('sister_concern_id', $salesOrderProduct->sister_concern_id)
                        ->where('batch_no', $salesOrderProduct->batch_no)
                        ->with('product')
                        ->first();

                    // $salesOrderProduct = new SalesOrderProduct();
                    // $salesOrderProduct->sister_concern_id = $request->sister_concern_id;
                    // $salesOrderProduct->product_id = $product->id;
                    // $salesOrderProduct->sales_order_id = $order->id;
                    // $salesOrderProduct->batch_no = $batch_no;
                    // $salesOrderProduct->name = $product->name;
                    // $salesOrderProduct->serial = $product->code;
                    // $salesOrderProduct->sale_type = $request->sale_type;
                    // $salesOrderProduct->quantity = $request->quantity[$counter];
                    // $salesOrderProduct->discount_percentage = $request->discount[$counter];
                    // $salesOrderProduct->discount_amt = $request->discount_amt[$counter];
                    // $salesOrderProduct->unit_price = $request->unit_price[$counter];
                    // $salesOrderProduct->buy_price = $inventory->unit_price ?? 0;
                    // $salesOrderProduct->date = date('Y-m-d',strtotime($request->date));
                    // $salesOrderProduct->total = $request->quantity[$counter] * $request->unit_price[$counter];
                    // $salesOrderProduct->user_id = Auth::id();
                    // $salesOrderProduct->save();

                    $inventoryLog = new PurchaseInventoryLog();
                    $inventoryLog->sister_concern_id = $salesOrderProduct->sister_concern_id;
                    $inventoryLog->product_id = $product->id;
                    $inventoryLog->sales_order_id = $order->id;
                    $inventoryLog->batch_no = $salesOrderProduct->batch_no;
                    $inventoryLog->type = 1;
                    $inventoryLog->sale_type = $request->sale_type;
                    $inventoryLog->date = date('Y-m-d', strtotime($request->date));
                    $inventoryLog->quantity = $request->return_quantity[$counter];
                    $inventoryLog->discount_percentage = $salesOrderProduct->discount;
                    $inventoryLog->discount_amt = $salesOrderProduct->discount_amt;
                    $inventoryLog->unit_price = $inventory->unit_price;
                    $inventoryLog->selling_price = $salesOrderProduct->unit_price;
                    $inventoryLog->user_id = Auth::id();
                    $inventoryLog->save();

                    $salesOrderProduct->return_quantity = $request->return_quantity[$counter];
                    $salesOrderProduct->return_date = date('Y-m-d', strtotime($request->date));
                    $salesOrderProduct->save();


                    $inventory->quantity = $product->inventory_pack_qty($product->id, $salesOrderProduct->sister_concern_id, $salesOrderProduct->batch_no);
                    $inventory->quantity = $product->inventory_qty($product->id, $salesOrderProduct->sister_concern_id, $salesOrderProduct->batch_no);
                    $inventory->save();

                    $returnPrice += $request->return_quantity[$counter] * $salesOrderProduct->unit_price;
                    $counter++;
                }
                $order->return_amount = $returnPrice;
                $order->return_status = 1;
                $order->save();

                // Return Price log
                // $log = new TransactionLog();
                // $log->sister_concern_id = $order->sister_concern_id;
                // $log->sale_type = $order->sale_type;
                // $log->date = date('Y-m-d',strtotime($request->date));
                // $log->particular = 'Return price for '.$order->order_no;
                // $log->transaction_type = 6;//Return Price
                // $log->transaction_method = 0;
                // $log->account_head_type_id = 0;
                // $log->account_head_sub_type_id = 0;
                // $log->amount = $returnPrice;
                // $log->discount_amt = 0;
                // $log->sales_order_id = $order->id;
                // $log->customer_id = $order->customer_id;
                // $log->user_id = Auth::id();
                // $log->save();
            }

            DB::commit();

            return redirect()->route('sale_receipt.details', ['order' => $order->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception);
            return redirect()->back()->with('error', $exception->getMessage())->withInput();
        }
    }
    // public function posPost(Request $request) {
    //     $total = $request->total;
    //     $due = $request->due_total;
    //     //dd($request->all());
    //     $rules = [
    //         'sister_concern_id' => 'required',
    //         'customer' => 'required',
    //         'product.*' => 'required',
    //         'batch.*' => 'required',
    //         'quantity.*' => 'required|numeric|min:.01',
    //         'unit_price.*' => 'required|numeric|min:0',
    //         'date' => 'required|date',
    //         'received_by' => 'nullable|string|max:255',
    //         'discount.*' => 'required|numeric|min:0',
    //         'paid' => 'required|numeric|min:0|max:'.$total,
    //         'payment_type' => 'required',
    //         'sale_type' => 'required',
    //     ];

    //     if ($request->payment_type == '2') {
    //         $rules['account'] = 'required';
    //         $rules['cheque_no'] = 'nullable|string|max:255';
    //         $rules['cheque_image'] = 'nullable|image';
    //     }

    //     $request->validate($rules);

    //     $available = true;
    //     $message = '';

    //     //dd($request->all());

    //     if ($request->product) {
    //         foreach ($request->product as $key => $product) {
    //             $batch_no = str_replace("Batch-", "", $request->batch[$key]);
    //             $product = Product::where('id', $product)->first();
    //             $inventory = PurchaseInventory::where('product_id', $product->id)
    //                 ->where('batch_no',$batch_no )
    //                 ->where('sister_concern_id', $request->sister_concern_id)
    //                 ->first();
    //                 // dd($request->quantity[$key]);
    //             if ($request->quantity[$key] > $inventory->quantity) {
    //                 $available = false;
    //                 $message = 'Insufficient ' . $inventory->product->name;
    //                 break;
    //             }
    //         }
    //     }
    //     // return($request->quantity[0]);
    //     if (!$available) {
    //         return redirect()->back()->withInput()->with('error', $message);
    //     }

    //     $lastSalesOrder = SalesOrder::where('sister_concern_id',$request->sister_concern_id)->latest('id')->first();

    //     // Generate the next code
    //     $nextCodeNumber = !empty($lastSalesOrder) ? intval(substr($lastSalesOrder->order_no, 5)) + 1 : 1;

    //     // Format the new code as 'p-000001'
    //     if($request->sister_concern_id == 1){
    //         $newCodeOrder = 'FMSO-' . str_pad($nextCodeNumber, 8, '0', STR_PAD_LEFT);
    //     }else{
    //         $newCodeOrder = 'FBSO-' . str_pad($nextCodeNumber, 8, '0', STR_PAD_LEFT);
    //     }

    //     DB::beginTransaction();
    //     try{
    //         $discountAmts = $request->input('discount_amt');
    //         if (is_array($discountAmts)) {
    //             $totalDiscount = array_sum($discountAmts);
    //         }
    //         $order = new SalesOrder();
    //         $order->order_no = $newCodeOrder;
    //         $order->sister_concern_id = $request->sister_concern_id;
    //         $order->customer_id = $request->customer;
    //         $order->sale_type = $request->sale_type;
    //         $order->received_by = $request->received_by;
    //         $order->date = date('Y-m-d',strtotime($request->date));
    //         $order->sub_total = 0;
    //         $order->discount = $totalDiscount;
    //         $order->total = 0;
    //         $order->paid = $request->paid;
    //         $order->due = 0;
    //         $order->opening_sale = 0;
    //         $order->user_id = Auth::id();
    //         $order->save();

    //         $counter = 0;
    //         $subTotal = 0;
    //         $totalDiscount = 0;
    //         $buyingPrice = 0;
    //         $sellingPrice = 0;

    //         if  ($request->product) {
    //             foreach ($request->product as $product) {
    //                 $batch_no = str_replace("Batch-", "", $request->batch[$counter]);
    //                 $product = Product::where('id', $product)->first();
    //                 $inventory = PurchaseInventory::where('product_id', $product->id)
    //                     ->where('sister_concern_id', $request->sister_concern_id)
    //                     ->where('batch_no', $batch_no)
    //                     ->with('product')
    //                     ->first();

    //                 $buyingPrice += $inventory->unit_price * $request->quantity[$counter];
    //                 $sellingPrice += $request->unit_price[$counter] * $request->quantity[$counter];

    //                 $salesOrderProduct = new SalesOrderProduct();
    //                 $salesOrderProduct->sister_concern_id = $request->sister_concern_id;
    //                 $salesOrderProduct->product_id = $product->id;
    //                 $salesOrderProduct->sales_order_id = $order->id;
    //                 $salesOrderProduct->batch_no = $batch_no;
    //                 $salesOrderProduct->name = $product->name;
    //                 $salesOrderProduct->serial = $product->code;
    //                 $salesOrderProduct->sale_type = $request->sale_type;
    //                 $salesOrderProduct->quantity = $request->quantity[$counter];
    //                 $salesOrderProduct->discount_percentage = $request->discount[$counter];
    //                 $salesOrderProduct->discount_amt = $request->discount_amt[$counter];
    //                 $salesOrderProduct->unit_price = $request->unit_price[$counter];
    //                 $salesOrderProduct->buy_price = $inventory->unit_price ?? 0;
    //                 $salesOrderProduct->date = date('Y-m-d',strtotime($request->date));
    //                 $salesOrderProduct->total = $request->quantity[$counter] * $request->unit_price[$counter];
    //                 $salesOrderProduct->user_id = Auth::id();
    //                 $salesOrderProduct->save();

    //                 $inventoryLog = new PurchaseInventoryLog();
    //                 $inventoryLog->sister_concern_id = $request->sister_concern_id;
    //                 $inventoryLog->product_id = $product->id;
    //                 $inventoryLog->sales_order_id = $order->id;
    //                 $inventoryLog->batch_no = $batch_no;
    //                 $inventoryLog->type = 2;
    //                 $inventoryLog->sale_type = $request->sale_type;
    //                 $inventoryLog->date = date('Y-m-d',strtotime($request->date));
    //                 $inventoryLog->quantity = $request->quantity[$counter];
    //                 $inventoryLog->discount_percentage = $request->discount[$counter];
    //                 $inventoryLog->discount_amt = $request->discount_amt[$counter];
    //                 $inventoryLog->unit_price = $inventory->unit_price;
    //                 $inventoryLog->selling_price = $request->unit_price[$counter];
    //                 $inventoryLog->user_id = Auth::id();
    //                 $inventoryLog->save();


    //                 $inventory->quantity = $product->inventory_pack_qty($product->id, $request->sister_concern_id,$batch_no);
    //                 $inventory->quantity = $product->inventory_qty($product->id, $request->sister_concern_id,$batch_no);
    //                 $inventory->save();

    //                 $subTotal += $request->quantity[$counter] * $request->unit_price[$counter];
    //                 $totalDiscount += $request->discount_amt[$counter];
    //                 $counter++;
    //             }
    //         }

    //         $counter = 0;

    //         $order->sub_total = $subTotal;
    //         $total = $subTotal - $totalDiscount;
    //         $order->total = $total;
    //         $order->due = $subTotal-$request->paid-$totalDiscount;
    //         $order->next_payment = $due > 0 ? date('Y-m-d',strtotime($request->next_payment)) : null;
    //         $order->save();

    //         // Sales Payment
    //         if ($request->paid > 0) {
    //             if ($request->payment_type == 1 || $request->payment_type == 3) {
    //                 $payment = new SalePayment();
    //                 $payment->sister_concern_id = $request->sister_concern_id;
    //                 $payment->sales_order_id = $order->id;
    //                 $payment->customer_id = $order->customer_id;
    //                 $payment->sale_type = $request->sale_type;
    //                 $payment->transaction_method = $request->payment_type;
    //                 $payment->received_type = 1;
    //                 $payment->amount = $request->paid;
    //                 $payment->date = date('Y-m-d',strtotime($request->date));
    //                 $payment->user_id = Auth::id();
    //                 $payment->save();

    //                 if ($request->payment_type == 1)
    //                     Cash::first()->increment('amount', $request->paid);
    //                 else
    //                     MobileBanking::first()->increment('amount', $request->paid);

    //                 $log = new TransactionLog();
    //                 $log->sister_concern_id = $request->sister_concern_id;
    //                 $log->date = date('Y-m-d',strtotime($request->date));
    //                 $log->particular = 'Payment for '.$order->order_no;
    //                 $log->transaction_type = 1;
    //                 $log->sale_type = $request->sale_type;
    //                 $log->transaction_method = $request->payment_type;
    //                 $log->account_head_type_id = 2;
    //                 $log->account_head_sub_type_id = 2;
    //                 $log->amount = $request->paid;
    //                 $log->sale_payment_id = $payment->id;
    //                 $log->customer_id = $order->customer_id;
    //                 $log->user_id = Auth::id();
    //                 $log->save();
    //             } else {
    //                 $bankAccount = BankAccount::find($request->account);
    //                 $image = 'img/no_image.png';

    //                 if ($request->cheque_image) {
    //                     // Upload Image
    //                     $file = $request->file('cheque_image');
    //                     $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
    //                     $destinationPath = 'public/uploads/sales_payment_cheque';
    //                     $file->move($destinationPath, $filename);

    //                     $image = 'uploads/sales_payment_cheque/'.$filename;
    //                 }

    //                 $payment = new SalePayment();
    //                 $payment->sister_concern_id = $request->sister_concern_id;
    //                 $payment->sales_order_id = $order->id;
    //                 $payment->customer_id = $order->customer_id;
    //                 $payment->transaction_method = 2;
    //                 $payment->sale_type = $request->sale_type;
    //                 $payment->received_type = 1;
    //                 $payment->bank_id = $bankAccount->bank_id;
    //                 $payment->branch_id = $bankAccount->branch_id;
    //                 $payment->bank_account_id = $request->account;
    //                 $payment->cheque_no = $request->cheque_no;
    //                 $payment->cheque_image = $image;
    //                 $payment->amount = $request->paid;
    //                 $payment->date = date('Y-m-d',strtotime($request->date));
    //                 $payment->user_id = Auth::id();
    //                 $payment->save();

    //                 BankAccount::find($request->account)->increment('balance', $request->paid);

    //                 $log = new TransactionLog();
    //                 $log->sister_concern_id = $request->sister_concern_id;
    //                 $log->date = date('Y-m-d',strtotime($request->date));
    //                 $log->particular = 'Payment for '.$order->order_no;
    //                 $log->sale_type = $request->sale_type;
    //                 $log->transaction_type = 1;
    //                 $log->transaction_method = 2;
    //                 $log->account_head_type_id = 2;
    //                 $log->account_head_sub_type_id = 2;
    //                 $log->bank_id = $bankAccount->bank_id;
    //                 $log->branch_id = $bankAccount->branch_id;
    //                 $log->bank_account_id = $request->account;
    //                 $log->cheque_no = $request->cheque_no;
    //                 $log->cheque_image = $image;
    //                 $log->amount = $request->paid;
    //                 $log->sale_payment_id = $payment->id;
    //                 $log->customer_id = $order->customer_id;
    //                 $log->user_id = Auth::id();
    //                 $log->save();
    //             }
    //         }

    //         // Buying Price log
    //         $log = new TransactionLog();
    //         $log->sister_concern_id = $request->sister_concern_id;
    //         $log->date = date('Y-m-d',strtotime($request->date));
    //         $log->particular = 'Buying price for '.$order->order_no;
    //         $log->sale_type = $request->sale_type;
    //         $log->transaction_type = 4;
    //         $log->transaction_method = 0;
    //         $log->account_head_type_id = 0;
    //         $log->account_head_sub_type_id = 0;
    //         $log->amount = $buyingPrice;
    //         $log->sales_order_id = $order->id;
    //         $log->customer_id = $order->customer_id;
    //         $log->user_id = Auth::id();
    //         $log->save();

    //         // Selling Price log
    //         $log = new TransactionLog();
    //         $log->sister_concern_id = $request->sister_concern_id;
    //         $log->sale_type = $request->sale_type;
    //         $log->date = date('Y-m-d',strtotime($request->date));
    //         $log->particular = 'Salling price for '.$order->order_no;
    //         $log->transaction_type = 5;
    //         $log->transaction_method = 0;
    //         $log->account_head_type_id = 0;
    //         $log->account_head_sub_type_id = 0;
    //         $log->amount = $sellingPrice;
    //         $log->discount_amt = $totalDiscount;
    //         $log->sales_order_id = $order->id;
    //         $log->customer_id = $order->customer_id;
    //         $log->user_id = Auth::id();
    //         $log->save();

    //         DB::commit();
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Sales order created successfully.',
    //             'redirect_url' => route('sale_receipt.details', ['order' => $order->id]),
    //         ]);
    //     }catch(Exception $exception){
    //         DB::rollBack();
    //         // dd('Hasan');
    //        return response()->json([
    //            'status' => false,
    //            'message' => $exception->getMessage().' Line:'.$exception->getLine(),
    //        ]);
    //     }
    // }
    public function posProductSearch(Request $request)
    {
        $inventory = PurchaseInventory::where('serial_no', $request->productCode)
            ->where('batch_no', $request->batchNo)
            ->where('sister_concern_id', 1)
            ->where('quantity', '>', 0)
            ->with('product', 'product.unit')
            ->first();
        if ($inventory && $inventory->quantity > 0) {
            return response()->json([
                'status' => true,
                'inventory' => $inventory,
            ]);
        } elseif ($inventory && $inventory->quantity == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Stock out !',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Not Found !',
            ]);
        }
    }
    public function posPreorderProductSearch(Request $request)
    {
        $product = Product::where('code', $request->productCode)
            ->where('sister_concern_id', 1)
            ->with('unit')
            ->first();
        if ($product) {
            return response()->json([
                'status' => true,
                'product' => $product,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Not Found !',
            ]);
        }
    }
    public function searchBarcodeAutocomplete(Request $request)
    {
        // Retrieve the 'query' term from the request
        // $search = $request->get('query');

        // // Query the Inventory model for matching product codes
        // $result = PurchaseInventory::where('sister_concern_id', 1)
        //     // ->where('inventory_type',1)//Finished Goods
        //     ->where('quantity','>',0)
        //     ->where('serial_no', 'LIKE', '%' . $search . '%')
        //     ->pluck('serial_no'); // Only return product codes as an array

        $search = $request->get('query');

        $result = PurchaseInventory::where('sister_concern_id', 1)
            ->where('quantity', '>', 0)
            ->where(function ($query) use ($search) {
                $query->where('serial_no', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('product', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%');
                    });
            })
            ->with('product')
            ->get()
            ->map(function ($inventory) {
                return $inventory->product->name . ' | ' . $inventory->serial_no . ' | ' . $inventory->batch_no;
            })
            ->toArray();

        // Return the array of product codes in JSON format
        return response()->json($result);
    }
    public function searchPreorderBarcodeAutocomplete(Request $request)
    {
        $search = $request->get('query');

        $result = Product::query()
            ->where('sister_concern_id', 1)
            ->where(function ($query) use ($search) {
                $query->where('code', 'LIKE', '%' . $search . '%')
                    ->orWhere('name', 'LIKE', '%' . $search . '%');
            })
            ->get(['name', 'code'])
            ->map(function ($product) {
                return $product->name . ' | ' . $product->code;
            })
            ->toArray();

        return response()->json($result);
    }
    public function partyJson(Request $request)
    {
        if (!$request->searchTerm) {
            $clients = Customer::where('status', 1)
                ->limit(10)
                ->get();
        } else {
            $clients = Customer::where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->searchTerm . '%')
                    ->orWhere('mobile_no', 'like', '%' . $request->searchTerm . '%');
            })->where('status', 1)
                ->limit(50)
                ->get();
        }
        $data = array();
        foreach ($clients as $client) {
            $text = $client->name;
            if ($client->mobile_no != '') {
                $text .= ' - Mobile No. ' . $client->mobile_no;
            }
            $data[] = [
                'id' => $client->id,
                'text' => $text,
            ];
        }

        echo json_encode($data);
    }
    // public function homeoSalesOrder() {
    //     if (Auth::user()->role_id == 1) {
    //         $departments = Department::whereIn('id',[6])->where('status', 1)->orderBy('name')->get();
    //         $customers = Customer::orderBy('name')->get();
    //     } else {
    //         $departments = Department::whereIn('id',[6])->where('id', Auth::user()->sister_concern_id)->get();
    //         $customers = Customer::orderBy('name')->where('user_id', Auth::id())->get();
    //     }
    //     $banks = Bank::where('status', 1)->orderBy('name')->get();
    //     $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
    //     $units = Unit::where('status', 1)->orderBy('name')->get();


    //     return view('sale.sales_order.home_create', compact('banks', 'accounts',
    //     'customers','departments','units'));
    // }

    public function homeoSalesOrderPost(Request $request)
    {
        $total = $request->total;
        $due = $request->due_total;
        $rules = [
            'sister_concern_id' => 'required',
            'customer' => 'required',
            'date' => 'required|date',
            'received_by' => 'nullable|string|max:255',
            'discount' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0|max:' . $total,
            'payment_type' => 'required',
        ];

        if ($request->payment_type == '2') {
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $cash = Cash::first();

            if ($request->total > $cash->amount)
                $validator->errors()->add('amount', 'Insufficient cash balance.');
        });

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        // dd($request->all());
        DB::beginTransaction();
        try {
            //purchase
            $purOrder = new PurchaseOrder();
            $purOrder->order_no = rand(10000000, 99999999);
            $purOrder->sister_concern_id = $request->sister_concern_id;
            $purOrder->supplier_id = 0;
            $purOrder->date = Carbon::parse($request->date)->format('Y-m-d');
            $purOrder->discount_percentage = 0;
            $purOrder->discount = 0;
            $purOrder->sub_total = 0;
            $purOrder->total = 0;
            $purOrder->paid = 0;
            $purOrder->due = 0;
            $purOrder->user_id = Auth::id();
            $purOrder->save();
            $purOrder->order_no = $purOrder->id;
            $purOrder->save();

            $purCounter = 0;
            $sub_total = 0;

            foreach ($request->product as $reqProduct) {
                //create product
                $product = new Product();
                $product->name = $reqProduct;
                $product->code = substr($reqProduct, 0, 3) . rand(1000, 9999);
                $product->alert_quantity = 0;
                $product->unit_id = $request->unit[$purCounter];
                $product->brand_id = 0;
                $product->status = 1;
                $product->user_id = Auth::id();;
                $product->save();

                $purchaseOrderProduct = new PurchaseOrderProduct();
                $purchaseOrderProduct->purchase_order_id =  $purOrder->id;
                $purchaseOrderProduct->sister_concern_id =  $request->sister_concern_id;
                $purchaseOrderProduct->product_id =  $product->id;
                $purchaseOrderProduct->name =  $product->name;
                $purchaseOrderProduct->type =  2;
                $purchaseOrderProduct->serial_no =  $product->code;
                $purchaseOrderProduct->quantity =  $request->quantity[$purCounter];
                $purchaseOrderProduct->unit_price =  $request->unit_price[$purCounter];
                $purchaseOrderProduct->selling_price =  $request->selling_price[$purCounter];
                $purchaseOrderProduct->total =  $request->quantity[$purCounter] * $request->unit_price[$purCounter];
                $purchaseOrderProduct->user_id =  Auth::id();
                $purchaseOrderProduct->save();

                $sub_total += $request->quantity[$purCounter] * $request->unit_price[$purCounter];

                // Inventory Log
                $inventoryLog = new PurchaseInventoryLog();
                $inventoryLog->sister_concern_id = $request->sister_concern_id;
                $inventoryLog->product_id = $product->id;
                $inventoryLog->type = 1;
                $inventoryLog->date = Carbon::parse($request->date)->format('Y-m-d');
                $inventoryLog->quantity = $request->quantity[$purCounter];
                $inventoryLog->unit_price = $request->unit_price[$purCounter];
                $inventoryLog->supplier_id = 0;
                $inventoryLog->purchase_order_id = $purOrder->id;
                $inventoryLog->user_id = Auth::id();
                $inventoryLog->save();

                // Inventory
                $inventory =  PurchaseInventory::where('sister_concern_id', $request->sister_concern_id)
                    ->where('product_id', $product->id)
                    ->first();

                if (empty($inventory)) {
                    $inventory = new PurchaseInventory();
                }

                $inventory->sister_concern_id = $request->sister_concern_id;
                $inventory->product_id = $product->id;
                $inventory->quantity = $product->inventory_qty($product->id, $request->sister_concern_id, $inventory->batch_no);
                $inventory->serial_no = $product->code;
                $inventory->unit_price = $request->unit_price[$purCounter];
                $inventory->selling_price = $request->selling_price[$purCounter];
                $inventory->save();

                $purCounter++;
            }

            $purOrder->sub_total = $sub_total;
            $purOrder->total = $sub_total;
            $purOrder->paid = $sub_total;
            $purOrder->due = 0;
            $purOrder->save();

            // purchase Payment

            $payment = new PurchasePayment();
            $payment->sister_concern_id = $request->sister_concern_id;
            $payment->supplier_id = 0;
            $payment->purchase_order_id = $purOrder->id;
            $payment->transaction_method = 1;
            $payment->amount = $sub_total;
            $payment->date = Carbon::parse($request->date)->format('Y-m-d');
            $payment->note = NULL;
            $payment->user_id = Auth::id();
            $payment->save();

            Cash::first()->decrement('amount', $sub_total);

            $log = new TransactionLog();
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->sister_concern_id = $purOrder->sister_concern_id;
            $log->supplier_id = 0;
            $log->particular = 'Payment for ' . $purOrder->supplier->name . ' for ' . $purOrder->order_no;
            $log->transaction_type = 3;
            $log->transaction_method = 1;
            $log->account_head_type_id = 1;
            $log->account_head_sub_type_id = 1;
            $log->amount = $sub_total;
            $log->note = NULL;
            $log->purchase_payment_id = $payment->id;
            $log->user_id = Auth::id();
            $log->save();


            //Sale
            $order = new SalesOrder();
            $order->order_no = rand(10000000, 99999999);
            $order->sister_concern_id = $request->sister_concern_id;
            $order->customer_id = $request->customer;
            $order->received_by = $request->received_by;
            $order->date = date('Y-m-d', strtotime($request->date));
            $order->sub_total = 0;
            $order->vat_percentage = $request->vat;
            $order->vat = 0;
            $order->discount = $request->discount;
            $order->discount_percentage = $request->discount_percentage ?? 0;
            $order->total = 0;
            $order->paid = $request->paid;
            $order->due = 0;
            $order->service_sub_total = 0;
            $order->service_vat_percentage = 0;
            $order->service_vat = 0;
            $order->service_discount = 0;
            $order->opening_sale = 0;
            $order->user_id = Auth::id();
            $order->save();
            $order->order_no = $order->id;
            $order->save();

            $counter = 0;
            $subTotal = 0;
            $buyingPrice = 0;
            $sellingPrice = 0;
            //  dd($purOrder->products);
            if ($purOrder) {
                foreach ($purOrder->products as $saleProduct) {
                    $inventory = PurchaseInventory::where('product_id', $saleProduct->pivot->product_id)
                        ->where('sister_concern_id', $request->sister_concern_id)
                        ->with('product')
                        ->first();

                    $buyingPrice += $inventory->unit_price * $request->quantity[$counter];
                    $sellingPrice += $request->selling_price[$counter] * $request->quantity[$counter];

                    $order->products()->attach($inventory->product->id, [
                        'sister_concern_id' => $request->sister_concern_id,
                        'name' => $inventory->product->name,
                        'serial' => $inventory->product->code,
                        'warranty' => $inventory->warranty,
                        'quantity' => $request->quantity[$counter],
                        'unit_price' => $request->selling_price[$counter],
                        'buy_price' => $inventory->unit_price ?? 0,
                        'total' => $request->quantity[$counter] * $request->selling_price[$counter],
                        'user_id' => Auth::id(),
                    ]);

                    $inventoryLog = new PurchaseInventoryLog();
                    $inventoryLog->sister_concern_id = $request->sister_concern_id;
                    $inventoryLog->product_id = $inventory->product->id;
                    $inventoryLog->type = 2;
                    $inventoryLog->date = date('Y-m-d', strtotime($request->date));
                    $inventoryLog->quantity = $request->quantity[$counter];
                    $inventoryLog->unit_price = $request->selling_price[$counter];
                    $inventoryLog->sales_order_id = $order->id;
                    $inventoryLog->user_id = Auth::id();
                    $inventoryLog->save();

                    $inventory->quantity = $product->inventory_qty($product->id, $request->sister_concern_id, $inventory->batch_no);
                    $inventory->save();

                    $subTotal += $request->quantity[$counter] * $request->selling_price[$counter];
                    $counter++;
                }
            }


            $order->sub_total = $subTotal;
            $total = $subTotal - $request->discount;
            $order->total = $total;
            $due = $total - $request->paid;
            $order->due = $due;
            $order->next_payment = $due > 0 ? date('Y-m-d', strtotime($request->next_payment)) : null;
            $order->save();

            // Sales Payment
            if ($request->paid > 0) {
                if ($request->payment_type == 1 || $request->payment_type == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->transaction_method = $request->payment_type;
                    $payment->received_type = 1;
                    $payment->amount = $request->paid;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->save();

                    if ($request->payment_type == 1)
                        Cash::first()->increment('amount', $request->paid);
                    else
                        MobileBanking::first()->increment('amount', $request->paid);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Payment for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = $request->payment_type;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->paid;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $request->sister_concern_id;
                    $payment->sales_order_id = $order->id;
                    $payment->customer_id = $order->customer_id;
                    $payment->transaction_method = 2;
                    $payment->received_type = 1;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->cheque_image = $image;
                    $payment->amount = $request->paid;
                    $payment->date = date('Y-m-d', strtotime($request->date));
                    $payment->user_id = Auth::id();
                    $payment->save();

                    BankAccount::find($request->account)->increment('balance', $request->paid);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $request->sister_concern_id;
                    $log->date = date('Y-m-d', strtotime($request->date));
                    $log->particular = 'Payment for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account;
                    $log->cheque_no = $request->cheque_no;
                    $log->cheque_image = $image;
                    $log->amount = $request->paid;
                    $log->sale_payment_id = $payment->id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->save();
                }
            }

            // Buying Price log
            $log = new TransactionLog();
            $log->sister_concern_id = $request->sister_concern_id;
            $log->date = date('Y-m-d', strtotime($request->date));
            $log->particular = 'Buying price for ' . $order->order_no;
            $log->transaction_type = 4;
            $log->transaction_method = 0;
            $log->account_head_type_id = 0;
            $log->account_head_sub_type_id = 0;
            $log->amount = $buyingPrice;
            $log->sales_order_id = $order->id;
            $log->customer_id = $order->customer_id;
            $log->user_id = Auth::id();
            $log->save();

            // Selling Price log
            $log = new TransactionLog();
            $log->sister_concern_id = $request->sister_concern_id;
            $log->date = date('Y-m-d', strtotime($request->date));
            $log->particular = 'Selling price for ' . $order->order_no;
            $log->transaction_type = 5;
            $log->transaction_method = 0;
            $log->account_head_type_id = 0;
            $log->account_head_sub_type_id = 0;
            $log->amount = $sellingPrice;
            $log->sales_order_id = $order->id;
            $log->customer_id = $order->customer_id;
            $log->user_id = Auth::id();
            $log->save();
            DB::commit();

            return redirect()->route('sale_receipt.details', ['order' => $order->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception);
            return redirect()->back()->with('error', $exception->getMessage())->withInput();
        }
    }

    public function saleReceipt()
    {
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();
        return view('sale.receipt.all', compact('accounts', 'mobileBankings'));
    }

    public function makePayment(Request $request)
    {
        $rules = [
            'order' => 'required',
            'payment_type' => 'required',
            'amount' => 'required|numeric|min:0',
            'amount_two' => 'required|numeric|min:0',
            'amount_three' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->payment_type == '2') {
            $rules['account'] = 'required';
        }
        if ($request->payment_type == '3') {
            $rules['mobile_banking'] = 'required';
        }
        if ($request->amount_two > 0) {
            $rules['payment_type_two'] = 'required';
            if ($request->payment_type_two == '2') {
                $rules['account_two'] = 'required';
            }
            if ($request->payment_type_two == '3') {
                $rules['mobile_banking_two'] = 'required';
            }
        }
        if ($request->amount_three > 0) {
            $rules['payment_type_three'] = 'required';
            if ($request->payment_type_three == '2') {
                $rules['account_three'] = 'required';
            }
            if ($request->payment_type_three == '3') {
                $rules['mobile_banking_three'] = 'required';
            }
        }
        if ($request->order != '') {
            $order = SalesOrder::find($request->order);
            // $rules['amount'] = 'required|numeric|min:0|max:'.$order->due-$request->amount_two-$request->amount_three;
            // $rules['amount_two'] = 'required|numeric|min:0|max:'.$order->due-$request->amount-$request->amount_three;
            // $rules['amount_three'] = 'required|numeric|min:0|max:'.$order->due-$request->amount-$request->amount_two;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $order = SalesOrder::find($request->order);

        $total_amount = $request->amount + $request->amount_two + $request->amount_three;
        if ($total_amount > $order->due) {
            return response()->json(['success' => false, 'message' => 'Paid amount not greater than due amount!']);
        }
        DB::beginTransaction();
        try {
            if ($request->amount > 0) {
                if ($request->payment_type == 1 || $request->payment_type == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->transaction_method = $request->payment_type;
                    $payment->amount = $request->amount;
                    if ($request->payment_type == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking;
                    }
                    $payment->date = date('Y-m-d',strtotime($request->date));
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    if ($request->payment_type == 1)
                        Cash::first()->increment('amount', $request->amount);
                    elseif ($request->payment_type == 3)
                        MobileBanking::find($request->mobile_banking)->increment('amount', $request->amount);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->date = date('Y-m-d',strtotime($request->date));;
                    $log->particular = 'Payment from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = $request->payment_type;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->amount;
                    $log->note = $request->note;
                    $log->sale_payment_id = $payment->id;
                    if ($request->payment_type == 3) {
                        $log->mobile_banking_id = $request->mobile_banking;
                    }
                    $log->sale_type = $order->sale_type;
                    $log->user_id = Auth::id();
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->transaction_method = 2;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->cheque_image = $image;
                    $payment->amount = $request->amount;
                    $payment->date = date('Y-m-d',strtotime($request->date));;
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    BankAccount::find($request->account)->increment('balance', $request->amount);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->date = date('Y-m-d',strtotime($request->date));;
                    $log->particular = 'Payment from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account;
                    $log->cheque_no = $request->cheque_no;
                    $log->cheque_image = $image;
                    $log->amount = $request->amount;
                    $log->note = $request->note;
                    $log->sale_payment_id = $payment->id;
                    $log->sale_type = $order->sale_type;
                    $log->user_id = Auth::id();
                    $log->save();
                }

                $order->increment('paid', $request->amount);
                $order->decrement('due', $request->amount);
                $order->save();
            }
            if ($request->amount_two > 0) {
                if ($request->payment_type_two == 1 || $request->payment_type_two == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->transaction_method = $request->payment_type_two;
                    $payment->amount = $request->amount_two;
                    if ($request->payment_type_two == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking_two;
                    }
                    $payment->date = date('Y-m-d',strtotime($request->date));;
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    if ($request->payment_type_two == 1)
                        Cash::first()->increment('amount', $request->amount_two);
                    elseif ($request->payment_type_two == 3)
                        MobileBanking::find($request->mobile_banking_two)->increment('amount', $request->amount_two);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->date = date('Y-m-d',strtotime($request->date));;
                    $log->particular = 'Payment from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = $request->payment_type_two;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->amount_two;
                    $log->note = $request->note;
                    $log->sale_payment_id = $payment->id;
                    if ($request->payment_type_two == 3) {
                        $log->mobile_banking_id = $request->mobile_banking_two;
                    }
                    $log->sale_type = $order->sale_type;
                    $log->user_id = Auth::id();
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account_two);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->transaction_method = 2;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account_two;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->cheque_image = $image;
                    $payment->amount = $request->amount_two;
                    $payment->date = date('Y-m-d',strtotime($request->date));;
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    BankAccount::find($request->account_two)->increment('balance', $request->amount_two);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->date = date('Y-m-d',strtotime($request->date));;
                    $log->particular = 'Payment from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account_two;
                    $log->cheque_no = $request->cheque_no;
                    $log->cheque_image = $image;
                    $log->amount = $request->amount_two;
                    $log->note = $request->note;
                    $log->sale_payment_id = $payment->id;
                    $log->sale_type = $order->sale_type;
                    $log->user_id = Auth::id();
                    $log->save();
                }

                $order->increment('paid', $request->amount_two);
                $order->decrement('due', $request->amount_two);
                $order->save();
            }
            if ($request->amount_three > 0) {
                if ($request->payment_type_three == 1 || $request->payment_type_three == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->transaction_method = $request->payment_type_three;
                    $payment->amount = $request->amount_three;
                    if ($request->payment_type_three == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking_three;
                    }
                    $payment->date = date('Y-m-d',strtotime($request->date));;
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    if ($request->payment_type_three == 1)
                        Cash::first()->increment('amount', $request->amount_three);
                    elseif ($request->payment_type_three == 3)
                        MobileBanking::find($request->mobile_banking_three)->increment('amount', $request->amount_three);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->date = date('Y-m-d',strtotime($request->date));;
                    $log->particular = 'Payment from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = $request->payment_type_three;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->amount_three;
                    $log->note = $request->note;
                    $log->sale_payment_id = $payment->id;
                    if ($request->payment_type_three == 3) {
                        $log->mobile_banking_id = $request->mobile_banking_three;
                    }
                    $log->sale_type = $order->sale_type;
                    $log->user_id = Auth::id();
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account_three);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->transaction_method = 2;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account_three;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->cheque_image = $image;
                    $payment->amount = $request->amount_three;
                    $payment->date = date('Y-m-d',strtotime($request->date));;
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    BankAccount::find($request->account_three)->increment('balance', $request->amount_three);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->date = date('Y-m-d',strtotime($request->date));;
                    $log->particular = 'Payment from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account_three;
                    $log->cheque_no = $request->cheque_no;
                    $log->cheque_image = $image;
                    $log->amount = $request->amount_three;
                    $log->note = $request->note;
                    $log->sale_payment_id = $payment->id;
                    $log->sale_type = $order->sale_type;
                    $log->user_id = Auth::id();
                    $log->save();
                }

                $order->increment('paid', $request->amount_three);
                $order->decrement('due', $request->amount_three);
                $order->save();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('sale_receipt.details', ['order' => $order->id])]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
    public function salesPaymentEdit(Request $request)
    {
        $rules = [
            'sale_order_id' => 'required',
            'sale_payment_id' => 'required',
            'payment_type' => 'required',
            'amount' => 'required|numeric|min:0',
        ];

        if ($request->payment_type == '2') {
            $rules['account'] = 'required';
        }
        if ($request->payment_type == '3') {
            $rules['mobile_banking'] = 'required';
        }

        if ($request->sale_order_id != '') {
            $order = SalesOrder::find($request->sale_order_id);
            $payment = SalePayment::where('id', $request->sale_payment_id)->where('sales_order_id', $order->id)->first();
            $rules['amount'] = 'required|numeric|min:0|max:' . $order->due + $payment->amount;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        //dd($request->all());
        DB::beginTransaction();
        try {
            $order = SalesOrder::find($request->sale_order_id);

            $payment = SalePayment::where('id', $request->sale_payment_id)->where('sales_order_id', $order->id)->first();
            if ($payment->transaction_method == 1) {
                Cash::first()->decrement('amount', $payment->amount);
            } elseif ($payment->transaction_method == 2) {
                BankAccount::find($payment->bank_account_id)->decrement('balance', $payment->amount);
            } elseif ($payment->transaction_method == 3) {
                MobileBanking::find($payment->mobile_banking_id)->decrement('amount', $payment->amount);
            }

            $order->decrement('paid', $payment->amount);
            $order->increment('due', $payment->amount);
            $order->save();

            if ($request->payment_type == 1 || $request->payment_type == 3) {
                $payment->transaction_method = $request->payment_type;
                if ($request->payment_type == 3) {
                    $payment->mobile_banking_id = $request->mobile_banking;
                } else {
                    $payment->mobile_banking_id = null;
                }
                $payment->bank_id = null;
                $payment->branch_id = null;
                $payment->bank_account_id = null;
                $payment->amount = $request->amount;
                $payment->note = $request->note;
                $payment->save();

                if ($request->payment_type == 1)
                    Cash::first()->increment('amount', $request->amount);
                else
                    MobileBanking::find($request->mobile_banking)->increment('amount', $request->amount);

                $log = TransactionLog::where('sale_payment_id', $payment->id)->first();
                $log->particular = 'Payment from ' . $order->customer->name . ' for ' . $order->order_no;
                $log->transaction_type = 1;
                $log->transaction_method = $request->payment_type;
                $log->account_head_type_id = 2;
                $log->account_head_sub_type_id = 2;
                if ($request->payment_type == 3) {
                    $log->mobile_banking_id = $request->mobile_banking;
                } else {
                    $log->mobile_banking_id = null;
                }
                $log->bank_id = null;
                $log->branch_id = null;
                $log->bank_account_id = null;

                $log->amount = $request->amount;
                $log->note = $request->note;
                $log->sale_payment_id = $payment->id;
                $log->save();
            } else {
                $bankAccount = BankAccount::find($request->account);

                $payment->transaction_method = 2;
                $payment->mobile_banking_id = null;
                $payment->bank_id = $bankAccount->bank_id;
                $payment->branch_id = $bankAccount->branch_id;
                $payment->bank_account_id = $request->account;
                $payment->amount = $request->amount;
                $payment->note = $request->note;
                $payment->save();

                BankAccount::find($request->account)->increment('balance', $request->amount);

                $log = TransactionLog::where('sale_payment_id', $payment->id)->first();
                $log->particular = 'Payment from ' . $order->customer->name . ' for ' . $order->order_no;
                $log->transaction_type = 1;
                $log->transaction_method = 2;
                $log->account_head_type_id = 2;
                $log->account_head_sub_type_id = 2;
                $log->mobile_banking_id = null;
                $log->bank_id = $bankAccount->bank_id;
                $log->branch_id = $bankAccount->branch_id;
                $log->bank_account_id = $request->account;
                $log->amount = $request->amount;
                $log->note = $request->note;
                $log->sale_payment_id = $payment->id;
                $log->save();
            }

            $order->increment('paid', $request->amount);
            $order->decrement('due', $request->amount);
            $order->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payment info has been updated.', 'redirect_url' => route('sale_receipt.payment_details', ['payment' => $payment->id])]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
    public function statusChange(Request $request)
    {
        try {
            $salesOrder = SalesOrder::find($request->status_order);
            $salesOrder->status = $request->status;
            $salesOrder->save();

            return response()->json(['success' => true, 'message' => 'successfully']);
        } catch (Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
    public function saleReceiptDetails(SalesOrder $order)
    {
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();
        return view('sale.receipt.details', compact('order', 'accounts', 'mobileBankings'));
    }

    public function saleReceiptPrint(SalesOrder $order)
    {
        $order->amount_in_word = DecimalToWords::convert(
            $order->total,
            'Taka',
            'Poisa'
        );

        return view('sale.receipt.print', compact('order'));
    }

    public function saleReceiptShare(SalesOrder $order)
    {
        abort(404, 'Page not found');
        $order->amount_in_word = DecimalToWords::convert(
            $order->total,
            'Taka',
            'Poisa'
        );
        return view('sale.receipt.share', compact('order'));
    }

    public function salePaymentDetails(SalePayment $payment)
    {
        $payment->amount_in_word = DecimalToWords::convert(
            $payment->amount,
            'Taka',
            'Poisa'
        );
        return view('sale.receipt.payment_details', compact('payment'));
    }

    public function salePaymentShare(SalePayment $payment)
    {
        abort('404', 'Page not found.');
        $payment->amount_in_word = DecimalToWords::convert(
            $payment->amount,
            'Taka',
            'Poisa'
        );
        return view('sale.receipt.payment_share', compact('payment'));
    }

    public function salePaymentPrint(SalePayment $payment)
    {
        $payment->amount_in_word = DecimalToWords::convert(
            $payment->amount,
            'Taka',
            'Poisa'
        );
        return view('sale.receipt.payment_print', compact('payment'));
    }
 
    public function customerPayment()
    {
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();

        return view('sale.customer_payment.all', compact('banks', 'accounts', 'mobileBankings'));
    }

    public function customerPaymentGetOrders(Request $request)
    {
        if (Auth::user()->role_id == 1) {
            $orders = SalesOrder::where('customer_id', $request->customerId)
                ->where('sister_concern_id',Auth::user()->sister_concern_id)
                ->where('sale_type', $request->saleType)
                ->where('due', '>', 0)
                ->orderBy('order_no')
                ->get()->toArray();
        } else {
            $orders = SalesOrder::where('customer_id', $request->customerId)
                ->where('sale_type', $request->saleType)
                ->where('sister_concern_id', Auth::user()->sister_concern_id)
                ->where('due', '>', 0)
                ->orderBy('order_no')
                ->get()->toArray();
        }



        return response()->json($orders);
    }

    public function customerPaymentGetRefundOrders(Request $request)
    {
        $orders = SalesOrder::where('customer_id', $request->customerId)
            ->where('refund', '>', 0)
            ->orderBy('order_no')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function customerMakePayment(Request $request)
    {
        // return response()->json(['success' => false, 'message' => $request->amount]);
        $rules = [
            'order' => 'required',
            'payment_type' => 'required',
            'amount' => 'required|numeric|min:0',
            'amount_two' => 'required|numeric|min:0',
            'amount_three' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->payment_type == '2') {
            $rules['account'] = 'required';
        }
        if ($request->payment_type == '3') {
            $rules['mobile_banking'] = 'required';
        }
        if ($request->amount_two > 0) {
            $rules['payment_type_two'] = 'required';
            if ($request->payment_type_two == '2') {
                $rules['account_two'] = 'required';
            }
            if ($request->payment_type_two == '3') {
                $rules['mobile_banking_two'] = 'required';
            }
        }
        if ($request->amount_three > 0) {
            $rules['payment_type_three'] = 'required';
            if ($request->payment_type_three == '2') {
                $rules['account_three'] = 'required';
            }
            if ($request->payment_type_three == '3') {
                $rules['mobile_banking_three'] = 'required';
            }
        }
        if ($request->order != '') {
            $order = SalesOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:' . $order->due;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $order = SalesOrder::find($request->order);
        $total_amount = $request->amount + $request->amount_two + $request->amount_three;
        if ($total_amount > $order->due) {
            return response()->json(['success' => false, 'message' => 'Paid amount not greater than due amount!']);
        }
        DB::beginTransaction();
        try {
            if ($request->amount > 0) {
                if ($request->payment_type == 1 || $request->payment_type == 3) {
                    $payment = new SalePayment();
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->transaction_method = $request->payment_type;
                    if ($request->payment_type == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking;
                    }
                    $payment->amount = $request->amount;
                    $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    if ($request->payment_type == 1)
                        Cash::first()->increment('amount', $request->amount);
                    else
                        MobileBanking::find($request->mobile_banking)->increment('amount', $request->amount);

                    $log = new TransactionLog();
                    $log->date = Carbon::parse($request->date)->format('Y-m-d');
                    $log->particular = 'Received from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = $request->payment_type;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->amount;
                    $log->note = $request->note;
                    if ($request->payment_type == 3) {
                        $log->mobile_banking_id = $request->mobile_banking;
                    }
                    $log->sale_payment_id = $payment->id;
                    $log->sale_type = $order->sale_type;
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->transaction_method = 2;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->cheque_image = $image;
                    $payment->amount = $request->amount;
                    $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    BankAccount::find($request->account)->increment('balance', $request->amount);

                    $log = new TransactionLog();
                    $log->date = Carbon::parse($request->date)->format('Y-m-d');
                    $log->particular = 'Received from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account;
                    $log->cheque_no = $request->cheque_no;
                    $log->cheque_image = $image;
                    $log->amount = $request->amount;
                    $log->note = $request->note;
                    $log->sale_payment_id = $payment->id;
                    $log->sale_type = $order->sale_type;
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->user_id = Auth::id();
                    $log->save();
                }

                $order->increment('paid', $request->amount);
                $order->decrement('due', $request->amount);
                $order->save();
            }
            if ($request->amount_two > 0) {
                if ($request->payment_type_two == 1 || $request->payment_type_two == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->transaction_method = $request->payment_type_two;
                    $payment->amount = $request->amount_two;
                    if ($request->payment_type_two == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking_two;
                    }
                    $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    if ($request->payment_type_two == 1)
                        Cash::first()->increment('amount', $request->amount_two);
                    elseif ($request->payment_type_two == 3)
                        MobileBanking::find($request->mobile_banking_two)->increment('amount', $request->amount_two);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->date = Carbon::parse($request->date)->format('Y-m-d');
                    $log->particular = 'Received from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = $request->payment_type_two;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->amount_two;
                    $log->note = $request->note;
                    $log->sale_payment_id = $payment->id;
                    if ($request->payment_type_two == 3) {
                        $log->mobile_banking_id = $request->mobile_banking_two;
                    }
                    $log->sale_type = $order->sale_type;
                    $log->user_id = Auth::id();
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account_two);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->transaction_method = 2;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account_two;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->cheque_image = $image;
                    $payment->amount = $request->amount_two;
                    $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    BankAccount::find($request->account_two)->increment('balance', $request->amount_two);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->date = Carbon::parse($request->date)->format('Y-m-d');
                    $log->particular = 'Received from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account_two;
                    $log->cheque_no = $request->cheque_no;
                    $log->cheque_image = $image;
                    $log->amount = $request->amount_two;
                    $log->note = $request->note;
                    $log->sale_payment_id = $payment->id;
                    $log->sale_type = $order->sale_type;
                    $log->user_id = Auth::id();
                    $log->save();
                }

                $order->increment('paid', $request->amount_two);
                $order->decrement('due', $request->amount_two);
                $order->save();
            }
            if ($request->amount_three > 0) {
                if ($request->payment_type_three == 1 || $request->payment_type_three == 3) {
                    $payment = new SalePayment();
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->transaction_method = $request->payment_type_three;
                    $payment->amount = $request->amount_three;
                    if ($request->payment_type_three == 3) {
                        $payment->mobile_banking_id = $request->mobile_banking_three;
                    }
                    $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    if ($request->payment_type_three == 1)
                        Cash::first()->increment('amount', $request->amount_three);
                    elseif ($request->payment_type_three == 3)
                        MobileBanking::find($request->mobile_banking_three)->increment('amount', $request->amount_three);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->date = Carbon::parse($request->date)->format('Y-m-d');
                    $log->particular = 'Received from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = $request->payment_type_three;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->amount = $request->amount_three;
                    $log->note = $request->note;
                    $log->sale_payment_id = $payment->id;
                    if ($request->payment_type_three == 3) {
                        $log->mobile_banking_id = $request->mobile_banking_three;
                    }
                    $log->sale_type = $order->sale_type;
                    $log->user_id = Auth::id();
                    $log->save();
                } else {
                    $bankAccount = BankAccount::find($request->account_three);
                    $image = 'img/no_image.png';

                    if ($request->cheque_image) {
                        // Upload Image
                        $file = $request->file('cheque_image');
                        $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = 'public/uploads/sales_payment_cheque';
                        $file->move($destinationPath, $filename);

                        $image = 'uploads/sales_payment_cheque/' . $filename;
                    }

                    $payment = new SalePayment();
                    $payment->sister_concern_id = $order->sister_concern_id;
                    $payment->customer_id = $order->customer_id;
                    $payment->sales_order_id = $order->id;
                    $payment->sale_type = $order->sale_type;
                    $payment->transaction_method = 2;
                    $payment->bank_id = $bankAccount->bank_id;
                    $payment->branch_id = $bankAccount->branch_id;
                    $payment->bank_account_id = $request->account_three;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->cheque_image = $image;
                    $payment->amount = $request->amount_three;
                    $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                    $payment->note = $request->note;
                    $payment->user_id = Auth::id();
                    $payment->save();

                    BankAccount::find($request->account_three)->increment('balance', $request->amount_three);

                    $log = new TransactionLog();
                    $log->sister_concern_id = $order->sister_concern_id;
                    $log->customer_id = $order->customer_id;
                    $log->date = Carbon::parse($request->date)->format('Y-m-d');
                    $log->particular = 'Received from ' . $order->customer->name . ' for ' . $order->order_no;
                    $log->transaction_type = 1;
                    $log->transaction_method = 2;
                    $log->account_head_type_id = 2;
                    $log->account_head_sub_type_id = 2;
                    $log->bank_id = $bankAccount->bank_id;
                    $log->branch_id = $bankAccount->branch_id;
                    $log->bank_account_id = $request->account_three;
                    $log->cheque_no = $request->cheque_no;
                    $log->cheque_image = $image;
                    $log->amount = $request->amount_three;
                    $log->note = $request->note;
                    $log->sale_payment_id = $payment->id;
                    $log->sale_type = $order->sale_type;
                    $log->user_id = Auth::id();
                    $log->save();
                }

                $order->increment('paid', $request->amount_three);
                $order->decrement('due', $request->amount_three);
                $order->save();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('sale_receipt.payment_details', ['payment' => $payment->id])]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function customerMakeRefund(Request $request)
    {
        $rules = [
            'order' => 'required',
            'payment_type' => 'required',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->payment_type == '2') {
            // $rules['bank'] = 'required';
            // $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        if ($request->order != '') {
            $order = SalesOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:' . $order->refund;
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            if ($request->payment_type == 1) {
                $cash = Cash::first();

                if ($request->amount > $cash->amount)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            } else {
                if ($request->account != '') {
                    $account = BankAccount::find($request->account);

                    if ($request->amount > $account->balance)
                        $validator->errors()->add('amount', 'Insufficient balance.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $order = SalesOrder::find($request->order);

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
            $payment->type = 2;
            $payment->transaction_method = $request->payment_type;
            $payment->received_type = 1;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->user_id = Auth::id();
            $payment->save();

            if ($request->payment_type == 1)
                Cash::first()->decrement('amount', $request->amount);
            else
                MobileBanking::first()->decrement('amount', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Refund to ' . $order->customer->name . ' for ' . $order->order_no;
            $log->transaction_type = 6;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 21;
            $log->account_head_sub_type_id = 43;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->user_id = Auth::id();
            $log->save();
        } else {
            $bankAccount = BankAccount::find($request->account);
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/sales_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/sales_payment_cheque/' . $filename;
            }

            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
            $payment->type = 2;
            $payment->transaction_method = 2;
            $payment->received_type = 1;
            $payment->bank_id = $bankAccount->bank_id;
            $payment->branch_id = $bankAccount->branch_id;
            $payment->bank_account_id = $request->account;
            $payment->cheque_no = $request->cheque_no;
            $payment->cheque_image = $image;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->user_id = Auth::id();
            $payment->save();

            BankAccount::find($request->account)->increment('balance', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Refund to ' . $order->customer->name . ' for ' . $order->order_no;
            $log->transaction_type = 6;
            $log->transaction_method = 2;
            $log->account_head_type_id = 21;
            $log->account_head_sub_type_id = 43;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->user_id = Auth::id();
            $log->save();
        }

        $order->decrement('refund', $request->amount);

        return response()->json(['success' => true, 'message' => 'Refund has been completed.', 'redirect_url' => route('sale_receipt.payment_details', ['payment' => $payment->id])]);
    }

    public function saleInformation()
    {
        return view('sale.product_sale_information.index');
    }

    public function saleInformationPost(Request $request)
    {
        $product = DB::table('purchase_order_purchase_product')
            ->where('serial_no', $request->serial)
            ->first();

        $sale = DB::table('sales_order_products')
            ->where('serial', $request->serial)->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Invalid serial.']);
        } elseif ($product->quantity > 1) {
            return response()->json(['success' => false, 'message' => 'This serial has many products.']);
        } elseif (!$sale) {
            return response()->json(['success' => false, 'message' => 'This serial not sell yet.']);
        } else {
            $order = SalesOrder::find($sale->sales_order_id);
            $purchaseOrder = PurchaseOrder::find($product->purchase_order_id);

            return response()->json(['success' => true, 'message' => 'This serial is sold.', 'redirect_url' => route('sale_information.print', ['purchaseOrder' => $purchaseOrder->id, 'saleOrder' => $order->id, 'serial' => $request->serial])]);
        }
    }

    public function saleInformationPrint(PurchaseOrder $purchaseOrder, SalesOrder $saleOrder)
    {
        $saleOrder->amount_in_word = DecimalToWords::convert(
            $saleOrder->total,
            'Taka',
            'Poisa'
        );

        return view('sale.product_sale_information.print', compact(
            'purchaseOrder',
            'saleOrder'
        ));
    }

    public function saleReceiptEdit(SalesOrder $order)
    {
        if (Auth::user()->role_id == 1) {
            $departments = Department::where('status', 1)->orderBy('name')->get();
        } else {
            $departments = Department::where('id', Auth::user()->sister_concern_id)->get();
        }
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('status', 1)->orderBy('name')->get();

        return view('sale.receipt.edit_new', compact(
            'order',
            'banks',
            'customers',
            'departments',
            'products'
        ));
    }

    public function saleReceiptEditPost(SalesOrder $order, Request $request)
    {
        $total = $request->total;
        $due = $request->due_total;
        $refund = $request->refund;

        $rules = [
            'sister_concern_id' => 'required',
            'customer' => 'required',
            'date' => 'required|date',
            'received_by' => 'nullable|string|max:255',
            'vat' => 'required|numeric|min:0',
            'service_vat' => 'nullable|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'service_discount' => 'nullable|numeric|min:0',
        ];

        if ($request->serial) {
            $rules['serial.*'] = 'required';
            $rules['product_name.*'] = 'required';
            $rules['quantity.*'] = 'required|numeric|min:0';
            $rules['exchange_qty.*'] = 'required|numeric|min:0';
            $rules['unit_price.*'] = 'required|numeric|min:0';
        }

        if ($request->service_name) {
            $rules['service_name.*'] = 'required';
            $rules['service_quantity.*'] = 'required|numeric|min:0';
            $rules['service_unit_price.*'] = 'required|numeric|min:0';
        }

        if ($due > 0)
            $rules['next_payment'] = 'required|date';

        if ($request->payment_type == '2') {
            // $rules['bank'] = 'required';
            // $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        $request->validate($rules);

        $available = true;
        $message = '';
        $counter = 0;

        $previousSerials = [];

        foreach ($order->products as $product)
            $previousSerials[] = $product->pivot->serial;

        if ($request->serial) {
            foreach ($request->serial as $serial) {
                $product = Product::where('code', $serial)->first();
                if (empty($product)) {
                    dd($serial);
                }
                if (in_array($serial, $previousSerials)) {
                    $inventory = PurchaseInventory::where('product_id', $product->id)
                        ->where('sister_concern_id', $request->sister_concern_id)
                        ->first();

                    $orderProduct = SalesOrderProduct::where('sales_order_id', $order->id)
                        ->where('sister_concern_id', $request->sister_concern_id)
                        ->where('serial', $serial)
                        ->first();



                    if ($request->quantity[$counter] > $inventory->quantity + $orderProduct->quantity) {
                        $available = false;
                        $message = 'Insufficient ' . $inventory->product->name;
                        break;
                    }
                } else {
                    $inventory = PurchaseInventory::where('product_id', $product->id)
                        ->where('sister_concern_id', $request->sister_concern_id)
                        ->first();

                    if ($request->quantity[$counter] > $inventory->quantity) {
                        $available = false;
                        $message = 'Insufficient ' . $inventory->product->name;
                        break;
                    }
                }

                if ($request->unit_price[$counter] < $product->min_price) {
                    $available = false;
                    $message = 'Minimum price of ' . $product->name . ' is ' . $product->min_price;
                    break;
                }

                $counter++;
            }
        }

        if (!$available) {
            return redirect()->back()->withInput()->with('message', $message);
        }
        DB::beginTransaction();
        try {
            $counter = 0;
            $subTotal = 0;

            if ($request->serial) {
                foreach ($request->serial as $serial) {
                    $product = Product::where('code', $serial)->first();
                    if (in_array($serial, $previousSerials)) {
                        // Old Item

                        $purchaseProduct = SalesOrderProduct::where('serial', $serial)
                            ->where('sister_concern_id', $request->sister_concern_id)
                            ->where('sales_order_id', $order->id)
                            ->first();

                        $new_qty = $request->quantity[$counter] - $purchaseProduct->quantity;

                        if ($purchaseProduct) {
                            $purchaseProduct->update([
                                'quantity' => $request->quantity[$counter],
                                'exchange_qty' => $request->exchange_qty[$counter],
                                'unit_price' => $request->unit_price[$counter],
                                'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                            ]);
                        }

                        $subTotal += $request->quantity[$counter] * $request->unit_price[$counter];

                        // Inventory
                        $log = PurchaseInventoryLog::where('product_id', $product->id)
                            ->where('sister_concern_id', $request->sister_concern_id)
                            ->where('sales_order_id', $order->id)
                            ->where('type', 2)->update([
                                'quantity' => $request->quantity[$counter] + $request->exchange_qty[$counter],
                                'unit_price' => $request->unit_price[$counter],
                                'total' => ($request->quantity[$counter] + $request->exchange_qty[$counter]) * $request->unit_price[$counter]
                            ]);

                        $inventory = PurchaseInventory::where('product_id', $product->id)
                            ->where('sister_concern_id', $request->sister_concern_id)
                            ->first();

                        // Inventory Update
                        $inventory->quantity = $product->inventory_qty($product->id, $request->sister_concern_id);
                        $inventory->save();

                        if (($key = array_search($serial, $previousSerials)) !== false) {
                            unset($previousSerials[$key]);
                        }
                    } else {
                        // New Item
                        $inventory = PurchaseInventory::where('product_id', $product->id)
                            ->where('sister_concern_id', $request->sister_concern_id)
                            ->first();

                        $order->products()->attach($inventory->product->id, [
                            'name' => $inventory->product->name,
                            'sister_concern_id' => $request->sister_concern_id,
                            'serial' => $request->serial[$counter],
                            'warranty' => $inventory->warranty,
                            'quantity' => $request->quantity[$counter],
                            'exchange_qty' => $request->exchange_qty[$counter],
                            'unit_price' => $request->unit_price[$counter],
                            'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                            'user_id' => Auth::id(),
                        ]);

                        $inventoryLog = PurchaseInventoryLog::where('product_id', $product->id)
                            ->where('sister_concern_id', $request->sister_concern_id)
                            ->where('sales_order_id', $order->id)
                            ->where('type', 2)->first();

                        if (empty($inventoryLog)) {
                            $inventoryLog = new PurchaseInventoryLog();
                        }

                        $inventoryLog->product_id = $inventory->product->id;
                        $inventoryLog->note = 'Sale Product';
                        $inventoryLog->type = 2;
                        $inventoryLog->date = date('Y-m-d', strtotime($request->date));
                        $inventoryLog->sister_concern_id = $request->sister_concern_id;
                        $inventoryLog->quantity = $request->quantity[$counter] + $request->exchange_qty[$counter];
                        $inventoryLog->unit_price = $request->unit_price[$counter];
                        $inventoryLog->sales_order_id = $order->id;
                        $inventoryLog->user_id = Auth::id();
                        $inventoryLog->save();


                        // Inventory Update
                        $inventory->quantity = $product->inventory_qty($product->id, $request->sister_concern_id);
                        $inventory->save();

                        $subTotal += $request->quantity[$counter] * $request->unit_price[$counter];
                    }

                    $counter++;
                }
            }

            // Delete items
            foreach ($previousSerials as $serial) {
                $product = Product::where('code', $serial)->first();
                $purchaseProduct = SalesOrderProduct::where('sales_order_id', $order->id)
                    ->where('sister_concern_id', $request->sister_concern_id)
                    ->where('serial', $serial)
                    ->first();

                // Inventory
                PurchaseInventoryLog::where('product_id', $product->id)
                    ->where('sister_concern_id', $request->sister_concern_id)
                    ->where('sales_order_id', $order->id)
                    ->where('type', 2)->delete();

                $inventory = PurchaseInventory::where('product_id', $product->id)
                    ->where('sister_concern_id', $request->sister_concern_id)
                    ->first();

                // Inventory Update
                $inventory->quantity = $product->inventory_qty($product->id, $request->sister_concern_id);
                $inventory->save();

                // Remove
                $purchaseProduct->delete();
            }

            // Services
            $order->services()->delete();

            $counter = 0;
            $serviceSubTotal = 0;
            if ($request->service_name) {
                foreach ($request->service_name as $service) {
                    $service = new Service();
                    $service->sales_order_id = $order->id;
                    $service->name = $request->service_name[$counter];
                    $service->quantity = $request->service_quantity[$counter];
                    $service->unit_price = $request->service_unit_price[$counter];
                    $service->total = $request->service_quantity[$counter] * $request->service_unit_price[$counter];
                    $service->save();

                    $serviceSubTotal += $request->service_quantity[$counter] * $request->service_unit_price[$counter];
                    $counter++;
                }
            }

            // Update Order
            $order->customer_id = $request->customer;
            $order->received_by = $request->received_by;
            $order->date = date('Y-m-d', strtotime($request->date));
            $order->sub_total = $subTotal;
            $order->vat_percentage = $request->vat;
            $vat = ($subTotal * $request->vat) / 100;
            $order->vat = $vat;
            $order->discount = $request->discount;
            $order->service_sub_total = $serviceSubTotal;
            $order->service_vat_percentage = $request->service_vat;
            $serviceVat = ($serviceSubTotal * $request->service_vat) / 100;
            $order->service_vat = $serviceVat;
            $order->service_discount = $request->service_discount;
            // $order->created_by = Auth::user()->id;
            $order->total = $total;
            $order->due = $due;
            $order->refund = $refund;
            $order->next_payment = $due > 0 ? $request->next_payment : null;
            $order->save();
            DB::commit();

            return redirect()->route('sale_receipt.details', ['order' => $order->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage())->withInput();
        }
    }

    public function saleProductDetails(Request $request)
    {
        $purchaseProduct = Product::where('code', $request->serial)->where('status', 1)->first();
        $product = PurchaseInventory::where('product_id', $purchaseProduct->id ?? '')
            ->where('sister_concern_id', $request->departmentId)
            ->where('quantity', '>', 0)
            ->with('product')
            ->first();

        if ($product) {
            $product = $product->toArray();
            return response()->json(['success' => true, 'data' => $product, 'count' => $product['quantity']]);
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }

    public function customerPayments(Request $request, Customer $customer)
    {
        if ($request->ajax()) {
            $query = SalePayment::with(['sisterConcern', 'customer', 'user']);
            if ($customer) {
                $query->where('customer_id', $customer->id);
            }
            if (Auth::user()->role_id != 1) {
                $query->where('sister_concern_id', Auth::user()->sister_concern_id);
                $query->where('user_id', Auth::user()->id);
            }
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('sister_concern', function (SalePayment $payment) {
                    return $payment->sisterConcern->name ?? '';
                })
                ->addColumn('customer', function (SalePayment $payment) {
                    return $payment->customer->name ?? '';
                })
                ->addColumn('user', function (SalePayment $payment) {
                    return $payment->user->name ?? '';
                })
                ->addColumn('action', function (SalePayment $payment) {
                    // return '<a href="'.route('purchase_receipt_fm.details', ['order' => $order->id]).'" class="btn btn-primary btn-sm">View</a> <a href="'.route('purchase_receipt.qr_code', ['order' => $order->id]).'" class="btn btn-success btn-sm">QR Code</a> <a href="'.route('purchase_receipt.edit', ['order' => $order->id]).'" class="btn btn-info btn-sm">Edit</a>';
                    $btn = '<a href="' . route('sale_receipt.payment_details', ['payment' => $payment->id]) . '" class="btn btn-primary btn-sm"> Invoice </a> ';

                    return $btn;
                })
                ->editColumn('date', function (SalePayment $payment) {
                    return date('d-M-Y', strtotime($payment->date));
                })
                ->editColumn('amount', function (SalePayment $payment) {
                    return '৳' . number_format($payment->amount, 2);
                })
                ->orderColumn('date', function ($query, $payment) {
                    $query->orderBy('date', $payment)->orderBy('created_at', 'desc');
                })
                ->rawColumns(['action', 'amount'])
                ->toJson();
        }
        return view('sale.customer_payment.customer_payments', compact('customer'));
    }

    public function saleReceiptDatatable(Request $request)
    {
        $query = SalesOrder::with(['customer', 'sisterConcern', 'user'])->where('is_return',0)->where('is_pre_order',0)->orderBy('id', 'desc');

        $query->where('sister_concern_id', 1);
        if ($request->input('start_date') && $request->input('end_date')) {
            $query->whereBetween('date', [date('Y-m-d', strtotime($request->input('start_date'))), date('Y-m-d', strtotime($request->input('end_date')))]);
        }
        if ($request->input('paid_by')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('transaction_method', $request->input('paid_by'));
            });
        }
        if ($request->input('type') == 'offline') {
            $query->where('sale_type', 1);
        } else {
            $query->where('sale_type', 2);
        }
        // $query->whereHas('payment', function ($q) use ($request) {
        //     if ($request->input('paid_by')) {
        //         $q->where('status', $request->input('paid_by')); // Example condition
        //     }
        // });

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('customer_name', function (SalesOrder $order) {
                return $order->customer->name;
            })
            ->addColumn('user', function (SalesOrder $order) {
                return $order->user->name ?? '';
            })
            ->addColumn('customer_mobile_no', function (SalesOrder $order) {
                return $order->customer->mobile_no;
            })
            ->addColumn('paid_by', function (SalesOrder $order) {
                if ($order->payment && $order->payment->transaction_method == 1) {
                    return 'Cash';
                } elseif ($order->payment && $order->payment->transaction_method == 2) {
                    return 'Bank';
                } elseif ($order->payment && $order->payment->transaction_method == 3) {
                    return $order->payment->mobileBanking->account_name;
                }
            })
            ->addColumn('status', function (SalesOrder $order) {
                if ($order->status == 0) {
                    return '<span class"badge bg-warning">Pending</span>';
                } elseif ($order->status == 1) {
                    return '<span class"badge bg-info">Processing</span>';
                } elseif ($order->status == 2) {
                    return '<span class"badge bg-info">In-transit</span>';
                } elseif ($order->status == 3) {
                    return '<span class"badge bg-info">Completed</span>';
                }
            })
            ->addColumn('action', function (SalesOrder $order) {
                $btn = '';
                $btn .= '<a href="' . route('sale_receipt.details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a> ';
                if ($order->sale_type == 1 && $order->is_vat_paid == 0 && auth()->user()->can('sales_receipt_offline_edit')) {
                    $btn .= '<a class="btn btn-info btn-sm" href="' . route('sales_order.edit_pos', ['order' => $order->id]) . '"><i class="fas fa-edit"></i></a> ';
                }
                if($order->sale_type == 2 && $order->is_vat_paid == 0 && auth()->user()->can('sales_receipt_online_edit')){
                    $btn .= '<a class="btn btn-info btn-sm" href="' . route('sales_order.edit', ['order' => $order->id]) . '"><i class="fas fa-edit"></i></a> ';
                }
                if ($order->sale_type == 2 && auth()->user()->can('sales_receipt_online')) {
                    $btn .= '<a role="button" class="btn btn-info btn-sm btn-status" data-id="' . $order->id . '" data-status="' . $order->status . '">Order Status</a> ';
                }
                $latestOrder = SalesOrder::where('customer_id', $order->customer_id)->latest('id')->first();
                //$order->id === $latestOrder->id &&
                if ($order->is_vat_paid == 0 && (auth()->user()->can('sales_receipt_offline_delete') || auth()->user()->can('sales_receipt_online_delete'))) {
                    $btn .= ' <a role="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $order->id . '"><i class="fa fa-trash"></i></a> ';
                }
                if ($order->due > 0 && auth()->user()->can('customer_payment')) {
                    $btn .= ' <a role="button" class="btn btn-success btn-sm btn-payment" data-id="' . $order->id . '">Payment</a> ';
                }
                $btn .= '<a class="btn btn-info btn-sm" href="' . route('sale_logs', ['orderId' => $order->id]) . '">Logs</a> ';
                return $btn;
            })
            ->editColumn('date', function (SalesOrder $order) {
                return Carbon::parse($order->date)->format('d-M-Y');
            })
            ->filterColumn('date', function ($query, $keyword) {
                $query->where('date', 'like', '%' . date('Y-m-d', strtotime($keyword)) . '%');
            })
            ->editColumn('next_payment', function (SalesOrder $order) {
                if ($order->next_payment)
                    return Carbon::parse($order->next_payment)->format('d-M-Y');
                else
                    return '';
            })
            ->editColumn('total', function (SalesOrder $order) {
                return '৳' . number_format($order->total, 2);
            })
            ->editColumn('paid', function (SalesOrder $order) {
                return '৳' . number_format($order->paid, 2);
            })
            ->editColumn('due', function (SalesOrder $order) {
                return '৳' . number_format($order->due, 2);
            })
            // ->orderColumn('date', function ($query, $order) {
            //     $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            // })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function customerPaymentDatatable()
    {
        $query = Customer::query();

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function (Customer $customer) {
                $btns = '<a class="btn btn-info btn-sm btn-pay" role="button" data-id="' . $customer->id . '" data-name="' . $customer->name . '">Payment</a> ';
                $btns .= '<a href="' . route('customer_payments', ['customer' => $customer->id]) . '" class="btn btn-primary btn-sm"> Details </a> ';
                // if ($customer->refund > 0)
                //     $btns .= ' <a class="btn btn-danger btn-sm btn-refund" role="button" data-id="'.$customer->id.'" data-name="'.$customer->name.'">Refund</a>';

                return $btns;
            })
            ->addColumn('paid', function (Customer $customer) {
                return number_format($customer->paid, 2);
            })
            ->addColumn('due', function (Customer $customer) {
                return number_format($customer->due, 2);
            })
            ->addColumn('total', function (Customer $customer) {
                return number_format($customer->total, 2);
            })
            ->addColumn('current_opening_due', function (Customer $customer) {
                return number_format($customer->opening_due - $customer->opening_paid, 2);
            })
            // ->addColumn('refund', function(Customer $customer) {
            //     return number_format($customer->refund, 2);
            // })
            ->rawColumns(['action'])
            ->toJson();
    }
    public function posPrint(SalesOrder $saleOrder)
    {

        $saleOrder->load('payment');
        $saleOrder->amount_in_word = DecimalToWords::convert(
            $saleOrder->total,
            'Taka',
            'Poisa'
        );
        return view('sale.receipt.pos_print', compact('saleOrder'));
    }


    public function orderDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            $salesOrder = SalesOrder::findOrFail($request->orderId);
            $salePayments = SalePayment::where('sales_order_id', $salesOrder->id)->get();

            //Logs
            $orderLog = new OrderLog();
            $orderLog->order_type = 'SALE';
            $orderLog->order_id  = $salesOrder->id;
            $orderLog->created_by  = Auth::id();
            $orderLog->status  = 'SALE DELETE';
            $orderLog->save();

            foreach ($salePayments as $salePayment) {
                if ($salePayment->transaction_method == 1) {
                    $cash = Cash::first();
                    if ($cash->amount < $salePayment->amount) {
                        return response()->json(['error' => 'Insufficient Cash balance to proceed.!']);
                    }
                    $cash->decrement('amount', $salePayment->amount);
                } elseif ($salePayment->transaction_method == 2) {
                    $bankAccount = BankAccount::find($salePayment->bank_account_id);
                    if ($bankAccount->balance < $salePayment->amount) {
                        return response()->json(['error' => 'Insufficient bank balance to proceed.!']);
                    }
                    $bankAccount->decrement('balance', $salePayment->amount);
                } elseif ($salePayment->transaction_method == 3) {
                    $mobileBanking = MobileBanking::find($salePayment->mobile_banking_id);
                    if ($mobileBanking->amount < $salePayment->amount) {
                        return response()->json(['error' => 'Insufficient mobile banking balance to proceed.!']);
                    }
                    $mobileBanking->decrement('amount', $salePayment->amount);
                }
                TransactionLog::where('sale_payment_id', $salePayment->id)->delete();
            }

            foreach (SalesOrderProduct::where('sales_order_id', $salesOrder->id)->get() as $salesOrderProduct) {
                $purchaseInventory = PurchaseInventory::where('product_id', $salesOrderProduct->product_id)
                    ->where('batch_no', $salesOrderProduct->batch_no)
                    ->first();
                if($salesOrder->status != 0){
                    if (!$purchaseInventory) {
                        return response()->json(['error' => 'Purchase inventory not found.']);
                    }
                    $purchaseInventory->increment('quantity', $salesOrderProduct->quantity);
                }    
            }

            PurchaseInventoryLog::where('sales_order_id', $salesOrder->id)->delete();
            SalePayment::where('sales_order_id', $salesOrder->id)->delete();
            SalesOrderProduct::where('sales_order_id', $salesOrder->id)->delete();
            TransactionLog::where('sales_order_id', $salesOrder->id)->delete();
            if($salesOrder->sale_type == 3){
                EcomOrderInfo::where('order_id',$salesOrder->id)->delete();
            }
            $salesOrder->delete();

            DB::commit();
            return response()->json(['message' => 'Invoice Delete Successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function productInventoryInfo(Request $request){
        $inventory = PurchaseInventory::where('sister_concern_id',1)->where('product_id',$request->id)->orderBy('id','desc')->first() ?? null;
        return response($inventory);
    }
    public function productAndInventoryInfo(Request $request){
        $inventory = PurchaseInventory::where('sister_concern_id',1)->where('product_id',$request->id)->orderBy('id','desc')->first() ?? [];
        $product = Product::find($request->id);
        return response()->json(['inventory'=>$inventory,'product'=>$product]);
    }
    public function saleLogs($orderId){
        $saleOrder = SalesOrder::withTrashed()->find($orderId);
        $logs = OrderLog::where('order_type','SALE')->where('order_id',$saleOrder->id)->get();
        return view('sale.sales_order.logs',compact('logs'));
    }
}
