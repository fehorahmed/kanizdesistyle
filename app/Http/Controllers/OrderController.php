<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Cash;
use App\Models\Customer;
use App\Models\Department;
use App\Models\EcomOrderInfo;
use App\Models\MobileBanking;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderLog;
use App\Models\Product;
use App\Models\PurchaseInventory;
use App\Models\PurchaseInventoryLog;
use App\Models\SalePayment;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\TransactionLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller{
    public function index(){
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();
        return view('ecommerce.order.all', compact('accounts', 'mobileBankings'));
    }
    public function datatable(Request $request){
        $query = SalesOrder::with(['customer', 'sisterConcern', 'user']);
        
        $query->where('sister_concern_id', 1);
        if ($request->input('start_date') && $request->input('end_date')) {
            $query->whereBetween('date', [date('Y-m-d', strtotime($request->input('start_date'))), date('Y-m-d', strtotime($request->input('end_date')))]);
        }
        if ($request->input('paid_by')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('transaction_method', $request->input('paid_by'));
            });
        }
        $query->where('sale_type', 3)->where('is_promotion',0);
        $query->orderBy('id', 'desc');

        if(request('type') === 'trash'){
            $query->onlyTrashed();
        }else{
            if(request('type') === 'cancel'){
                $query->where('status','=',4);
            }else{
                $query->whereIn('status',[0,1,2,3]);
            }
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('customer_name', function (SalesOrder $order) {
                return $order->customer->name??'';
            })
            ->addColumn('user', function (SalesOrder $order) {
                return $order->user->name ?? '';
            })
            ->addColumn('customer_mobile_no', function (SalesOrder $order) {
                return $order->customer->mobile_no??'';
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
                } elseif ($order->status == 4) {
                    return '<span class"badge bg-info">Cancelled</span>';
                }
            })
            ->addColumn('action', function (SalesOrder $order) {
                $btn = '';
                $btn .= '<a href="' . route('ecom_order.details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a> ';
                if($order->deleted_at == null && $order->status != 4){
                    if($order->sale_type == 3 && $order->status == 0 && auth()->user()->can('ecom_order_confirm')){
                        $btn .= '<a class="btn btn-info btn-sm" href="' . route('ecom_order.confirm', ['order' => $order->id]) . '"><i class="fas fa-shipping-fast"></i></a> ';
                    }
                    if ($order->sale_type == 3 && $order->status > 0 && auth()->user()->can('ecom_order_status')) {
                        $btn .= '<a role="button" class="btn btn-info btn-sm btn-status" data-id="' . $order->id . '" data-status="' . $order->status . '">Order Status</a> ';
                    }
                    if ($order->sale_type == 3 && $order->status == 0 && auth()->user()->can('ecom_order_status')) {
                        $btn .= '<a role="button" class="btn btn-danger btn-sm btn-cancel-status" data-id="' . $order->id . '" data-status="' . $order->status . '">Cancel</a> ';
                    }
                    $latestOrder = SalesOrder::where('customer_id', $order->customer_id)->latest('id')->first();
                    //$order->id === $latestOrder->id &&
                    if (auth()->user()->can('ecom_order_delete')) {
                        $btn .= ' <a role="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $order->id . '"><i class="fa fa-trash"></i></a> ';
                    }
                    if ($order->due > 0 && $order->status > 0 && $order->sale_type == 3) {
                        $btn .= ' <a role="button" class="btn btn-success btn-sm btn-payment" data-id="' . $order->id . '">Payment</a> ';
                    }
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
    
    public function salesConfirm(SalesOrder $order){
        // dd($order->products);
        if($order->status > 0){
            return redirect()->route('ecom_order')->with('error','Order Alredy Confirm');
        } 
        $customers = Customer::orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $products = Product::where('sister_concern_id', 1)->where('status', 1)->orderBy('name')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();

        return view('ecommerce.order.edit', compact(
            'banks','order',
            'accounts',
            'customers',
            'products',
            'mobileBankings'
        ));
    }
    public function salesConfirmPost(SalesOrder $order, Request $request){
        
        // dd($request->all());
        $total = $request->total;
        $due = $request->due_total;
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
            'payment_type' => 'nullable',
            'sale_type' => 'required',
        ];
        if($request->paid > 0){
            $rules['payment_type'] = 'required';
            if ($request->payment_type == '2') {
                $rules['account'] = 'required';
            }
            if ($request->payment_type == '3') {
                $rules['mobile_banking'] = 'required';
            }
        }
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
        // dd($request->all());
        $available = true;
        $message = '';

        if ($request->paid_three + $request->paid_two + $request->paid > $total) {
            return redirect()->back()->withInput()->with('error', 'Paid amount is not greater than Order amount!');
        }

        if ($request->product) {
            foreach ($request->product as $key => $productId) {
                $batch_no = str_replace("Batch-", "", $request->batch[$key]);
                $product = Product::where('id', $productId)->first();
                $inventory = PurchaseInventory::where('product_id', $product->id)
                    ->where('batch_no', $batch_no)
                    ->where('sister_concern_id', $request->sister_concern_id)
                    ->first();
                
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

        DB::beginTransaction();
        try {
            //dd($request->all());
            //Order previous inventory return
            foreach($order->products as $orderproduct){
                if(!in_array($orderproduct->product_id,$request->product)){
                    SalesOrderProduct::where('sister_concern_id',$order->sister_concern_id)->where('sales_order_id',$order->id)->where('product_id',$orderproduct->product_id)->delete();
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
            $order->shipping_charge = $request->shipping_charge;
            $order->total = 0;
            $order->paid = $request->paid + $request->paid_two + $request->paid_three;
            $order->due = 0;
            $order->note = $request->notes;
            $order->user_id = Auth::id();
            $order->status = 1;//Processing
            $order->save();

            //Logs
            $orderLog = new OrderLog();
            $orderLog->order_type = 'SALE';
            $orderLog->order_id  = $order->id;
            $orderLog->created_by  = Auth::id();
            $orderLog->status  = 'ORDER PROCESSING';
            $orderLog->save();

            $counter = 0;
            $subTotal = 0;
            $totalDiscount = 0;
            $buyingPrice = 0;
            $sellingPrice = 0;

            if ($request->product) {
                foreach ($request->product as $product) {
                    $batch_no = str_replace("Batch-", "", $request->batch[$counter]);
                    $product = Product::where('id', $product)->first();
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
                    $salesOrderProduct->discount_percentage = $request->discount[$counter];
                    $salesOrderProduct->discount_amt = $request->discount_amt[$counter];
                    $salesOrderProduct->unit_price = $request->unit_price[$counter];
                    $salesOrderProduct->buy_price = $inventory->unit_price ?? 0;
                    $salesOrderProduct->date = date('Y-m-d', strtotime($request->date));
                    $salesOrderProduct->total = $request->quantity[$counter] * $request->unit_price[$counter];
                    $salesOrderProduct->user_id = Auth::id();
                    $salesOrderProduct->save();

                    // $inventoryLog = PurchaseInventoryLog::where('sister_concern_id',$order->sister_concern_id)->where('sales_order_id',$order->id)->where('product_id',$product->id)->first();
                    // if(!$inventoryLog){
                    //     $inventoryLog = new PurchaseInventoryLog();
                    // }
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
                    $totalDiscount += $request->discount_amt[$counter];
                    $counter++;
                }
            }

            $counter = 0;
            $order->coupon_discount = $request->coupon_discount;
            $order->discount = floor($totalDiscount) + $request->coupon_discount;
            $order->sub_total = $subTotal;
            $total = $subTotal - floor($totalDiscount) - $request->coupon_discount;
            $order->total = ceil($total);
            $order->due = ceil($total) - $request->paid - $request->paid_two - $request->paid_three;
            $order->save();

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
                    $log->particular = 'Payment for ' . $order->order_no;
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
                    $log->particular = 'Payment for ' . $order->order_no;
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
                    $log->particular = 'Payment for ' . $order->order_no;
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
                    $log->particular = 'Payment for ' . $order->order_no;
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
                    $log->particular = 'Payment for ' . $order->order_no;
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
                    $log->particular = 'Payment for ' . $order->order_no;
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
            // $log = TransactionLog::where('sister_concern_id',$request->sister_concern_id)->where('sales_order_id',$order->id)->where('transaction_type',4)->first();
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
            // $log = TransactionLog::where('sister_concern_id',$request->sister_concern_id)->where('sales_order_id',$order->id)->where('transaction_type',5)->first();
            $log->sister_concern_id = $request->sister_concern_id;
            $log->sale_type = $request->sale_type;
            $log->date = date('Y-m-d', strtotime($request->date));
            $log->particular = 'Selling price for ' . $order->order_no;
            $log->transaction_type = 5;
            $log->transaction_method = 0;
            $log->account_head_type_id = 0;
            $log->account_head_sub_type_id = 0;
            $log->amount = $sellingPrice;
            $log->discount_amt = $totalDiscount;
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
                return redirect()->route('ecom_order.details', ['order' => $order->id]);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            dd($request->all());
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
    public function details(SalesOrder $order){
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();
        return view('ecommerce.order.details', compact('order', 'accounts', 'mobileBankings'));
    }
    public function orderReceiptPrint($id){
        $order = SalesOrder::where('id',$id)->first();
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();
        return view('ecommerce.order.print', compact('order', 'accounts', 'mobileBankings'));
    }
    public function orderStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $order = SalesOrder::find($request->status_order);
            // dd($request->all());
            if($request->status == '4'){
                if($order->status > 0){
                    return response()->json(['success' => false, 'message' => "You can't cancel this Order! You can cancel only pending order!"]);
                }
            }
            if($order->status > $request->status){
                return response()->json(['success' => false, 'message' => "You can't get back for this Order!"]);
            }
            if($order->status == $request->status){
                return response()->json(['success' => false, 'message' => "You already is in this status! You have to move forward!"]);
            }
            if($request->status == '4'){
                $order->note = $request->note;
            }
            $order->status = $request->status;
            $order->save();

            //Logs
            $orderLog = new OrderLog();
            $orderLog->order_type = 'SALE';
            $orderLog->order_id  = $order->id;
            $orderLog->created_by  = Auth::id();
            if($request->status == '0'){
                $orderLog->status  = 'ORDER PENDING';
            }elseif($request->status == '1'){
                $orderLog->status  = 'ORDER PROCESSING';
            }elseif($request->status == '2'){
                $orderLog->status  = 'ORDER IN-TRANSIT';
            }elseif($request->status == '3'){
                $orderLog->status  = 'ORDER COMPLETED';
            }elseif($request->status == '4'){
                $orderLog->status  = 'ORDER CANCELLED';
            }
            $orderLog->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'successfully']);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function store1(Request $request)
    {
        dd(auth()->id());
        DB::beginTransaction();

        try {
            // Step 1: Store order data
            $order = Order::create([
                'user_id' => auth()->id(), // Assuming the user is authenticated
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'pi_first_name' => $request->billing['firstName'],
                'pi_last_name' => $request->billing['lastName'],
                'pi_email' => $request->billing['email'],
                'pi_phone_no' => $request->billing['phone'],
                'country' => $request->shipping['country'], // Assuming this maps to your countries table
                'shipping_city' => $request->shipping['city'],
                'order_notes' => $request->shipping['orderNotes'],
                'total_amount' => $request->totals['total'],
                'status' => 'pending',
                'shipping_address' => $request->shipping['address'],
                'billing_address' => $request->billing['address'] ?? null, // Assuming `billing['address']` exists
                'payment_method' => $request->paymentMethod,
                'payable_amount' => $request->totals['total'], // Assuming this matches `total_amount`
                'ordered_at' => now(),
            ]);

            // Step 2: Store order items
            $items = $request->input('items'); // Array of items
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_price' => $item['quantity'] * $item['price'],
                    'attributes' => isset($item['attributes']) ? json_encode($item['attributes']) : null,
                    'custom_note' => $item['custom_note'] ?? null,
                    'status' => $item['status'] ?? 'pending',
                ]);

                $cart = Cart::find($item['cart_id']);
                $cartItem = CartItem::find($item['id']);
                $cartItem->status = 'p2c';
                $cart->total_price = 0.00;
                $cartItem->total_price = 0.00;
                $cart->tax = null;
                $cart->discount = null;
                $cart->update();
                $cartItem->update();
            }


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place the order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $items = $request->input('items');
        // foreach ($items as $item){
        //     dd($item);
        // }
        DB::beginTransaction();

        try {
            $request['sister_concern_id'] = 1;//Functional Medicine
            if(auth()->user()->customer_id){
                $request['customer'] = auth()->user()->customer_id;//Customer ID
            }else{
                $customer = new Customer();
                $customer->name = auth()->user()->name;
                $customer->sister_concern_id = auth()->user()->sister_concern_id;
                $customer->mobile_no = $request->billing['phone'];
                $customer->save();

                $user = auth()->user();
                $user->customer_id = $customer->id;
                $user->save();

                $request['customer'] = $customer->id;//Customer ID
            }

            $lastSalesOrder = SalesOrder::where('sister_concern_id', $request->sister_concern_id)->where('sale_type', 3)->latest('id')->first();
            $nextCodeNumber = !empty($lastSalesOrder) ? intval(substr($lastSalesOrder->order_no, 7)) + 1 : 1;
            if ($request->sister_concern_id == 1) {
                $newCodeOrder = 'ECOMSO-' . str_pad($nextCodeNumber, 10, '0', STR_PAD_LEFT);
            } else {
                $newCodeOrder = 'ECOMSO-' . str_pad($nextCodeNumber, 10, '0', STR_PAD_LEFT);
            }
            
            $order = new SalesOrder();
            $order->order_no = $newCodeOrder;
            $order->sister_concern_id = $request->sister_concern_id;
            $order->customer_id = $request->customer;
            $order->sale_type = 3;
            $order->received_by = '';
            $order->date = date('Y-m-d');
            $order->sub_total = 0;
            $order->discount = 0;
            $order->shipping_charge = $request->shipping_charge;
            $order->total = 0;
            $order->paid = 0;
            $order->due = 0;
            $order->opening_sale = 0;
            $order->note = $request->notes ?? 'Ecommerce';
            $order->user_id = Auth::id();
            $order->status = 0;//Initial
            $order->save();

            //Logs
            $orderLog = new OrderLog();
            $orderLog->order_type = 'SALE';
            $orderLog->order_id  = $order->id;
            $orderLog->created_by  = Auth::id();
            $orderLog->status  = 'ORDER PROCESSING';
            $orderLog->save();

            //OrderInfo
            $ecomOrderInfo = EcomOrderInfo::create([
                'order_id' => $order->id, // Assuming the user is authenticated
                'user_id' => auth()->id(), // Assuming the user is authenticated
                'order_number' => $newCodeOrder,
                'pi_name' => $request->billing['name'],
                'pi_email' => $request->billing['email'],
                'pi_phone_no' => $request->billing['phone'],
                'country' => $request->shipping['country'], // Assuming this maps to your countries table
                'zip_code' => $request->shipping['zip'],
                'shipping_city' => $request->shipping['city'],
                'order_notes' => $request->shipping['orderNotes'],
                'shipping_charge' => $request->shipping_charge,
                'total_amount' => $request->totals['total'],
                'discount' => $request->totals['discount'],
                'status' => 'pending',
                'shipping_address' => $request->shipping['address'],
                'billing_address' => $request->billing['address'] ?? null, // Assuming `billing['address']` exists
                'payment_method' => $request->paymentMethod,
                'payable_amount' => $request->totals['total'], // Assuming this matches `total_amount`
                'ordered_at' => now(),
            ]);

            $subTotal = 0;
            $totalDiscount = 0;
            $buyingPrice = 0;
            $sellingPrice = 0;
            $items = $request->input('items');
            if ($request->items) {
                foreach ($items as $item) {
                    $batch_no = $item['batch_no'];
                    $product = Product::where('id', $item['product_id'])->first();
                    $inventory = PurchaseInventory::where('product_id', $product->id)
                        ->where('sister_concern_id', $request->sister_concern_id)
                        ->where('batch_no', $batch_no)
                        ->with('product')
                        ->first();

                    $buyingPrice += $inventory->unit_price * $item['quantity'];
                    $sellingPrice += ($item['price'] + $item['discount']) * $item['quantity'];


                    $salesOrderProduct = new SalesOrderProduct();
                    $salesOrderProduct->sister_concern_id = $request->sister_concern_id;
                    $salesOrderProduct->product_id = $product->id;
                    $salesOrderProduct->sales_order_id = $order->id;
                    $salesOrderProduct->batch_no = $batch_no;
                    $salesOrderProduct->name = $product->name;
                    $salesOrderProduct->serial = $product->code;
                    $salesOrderProduct->sale_type = 3;//Ecommerce
                    $salesOrderProduct->quantity = $item['quantity'];
                    $salesOrderProduct->discount_percentage = $item['discount_percent'];
                    $salesOrderProduct->discount_amt = $item['discount'];
                    $salesOrderProduct->unit_price = $item['price'] + $item['discount'];
                    $salesOrderProduct->buy_price = $inventory->unit_price ?? 0;
                    $salesOrderProduct->date = date('Y-m-d');
                    $salesOrderProduct->total = ($item['price'] + $item['discount']) * $item['quantity'];
                    $salesOrderProduct->user_id = Auth::id();
                    $salesOrderProduct->save();

                    $subTotal += ($item['price'] + $item['discount']) * $item['quantity'];
                    $totalDiscount += $item['discount'] * $item['quantity'];

                }
            }
            if(!$request->isBuyNow){
                //Empty Cart After Order
                $cart = Cart::where('user_id',auth()->id())->first();
                CartItem::where('cart_id',$cart->id)->delete();
                $cart->total_price = 0;
                $cart->discount = 0;
                $cart->tax = 0;
                $cart->save();
            }
            $order->sub_total = $subTotal;
            // $total = $subTotal - $totalDiscount + $request->shipping_charge;
            $total = $subTotal - $totalDiscount;
            $order->discount = $totalDiscount;
            $order->total = ceil($total);
            $order->due = ceil($total);
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id,
                'order_number' => $order->order_no,
                'redirect' => route('bkash-create', ['order_id' => $order->id,'type'=>'Ecom']) 
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place the order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function instantOrder(Request $request)
    {
        //dd($request->all());
        $items = $request->input('items');
        // foreach ($items as $item){
        //     dd($item);
        // }
        $rules = [
            'recaptchaToken' => 'required',
            'billing.name' => 'required|string|max:255',
            'billing.email' => 'nullable',
            'billing.phone' => 'required|numeric|regex:/^01[3-9]\d{8}$/',
            'shipping.country' => 'required|string',
            'shipping.address' => 'required|string',
            'shipping.city' => 'required|string',
            'shipping.state' => 'nullable',
            'shipping.zip' => 'required|string',
        ];
        
        $request->validate($rules, [
            'billing.phone.regex' => 'The mobile number must start with 01 and be 11 digits long.',
            'billing.email.email' => 'The email must be a valid email address.',
            'recaptchaToken.required' => 'Please complete the reCAPTCHA.',
        ]);
        DB::beginTransaction();

        try {
            $request['sister_concern_id'] = 1;//Functional Medicine
            $checkCustomer = Customer::where('mobile_no',$request->billing['phone'])->first();
            //dd($checkCustomer);
            if($checkCustomer){
                $request['customer'] = $checkCustomer->id;//Customer ID
                $userId = User::where('customer_id',$checkCustomer->id)->first()->id ?? null;
                //dd($checkCustomer);
                if(!$userId){
                    $user = new User();
                    $user->name = $checkCustomer->name;
                    $user->username = $checkCustomer->mobile_no;
                    $user->email = $checkCustomer->email ?? $checkCustomer->mobile_no.'@gmail.com';
                    $user->customer_id = $checkCustomer->id;
                    $user->password = bcrypt($checkCustomer->mobile_no);
                    $user->role_id = 3;
                    $user->save();

                    $userId = $user->id;
                }
            }else{
                $customer = new Customer();
                $customer->name = $request->billing['name'];
                $customer->sister_concern_id = 1;
                $customer->email = $request->billing['email'];
                $customer->mobile_no = $request->billing['phone'];
                $customer->address = $request->shipping['address'];
                $customer->save();

                $user = new User();
                $user->name = $customer->name;
                $user->username = $customer->mobile_no;
                $user->email = $customer->email;
                $user->customer_id = $customer->id;
                $user->password = bcrypt($customer->mobile_no);
                $user->role_id = 3;
                $user->save();

                $userId = $user->id;
                $request['customer'] = $customer->id;//Customer ID
            }
            // dd($userId);
            $lastSalesOrder = SalesOrder::where('sister_concern_id', $request->sister_concern_id)->where('sale_type', 3)->latest('id')->first();
            $nextCodeNumber = !empty($lastSalesOrder) ? intval(substr($lastSalesOrder->order_no, 7)) + 1 : 1;
            if ($request->sister_concern_id == 1) {
                $newCodeOrder = 'ECOMSO-' . str_pad($nextCodeNumber, 10, '0', STR_PAD_LEFT);
            } else {
                $newCodeOrder = 'ECOMSO-' . str_pad($nextCodeNumber, 10, '0', STR_PAD_LEFT);
            }
            
            $order = new SalesOrder();
            $order->order_no = $newCodeOrder;
            $order->sister_concern_id = $request->sister_concern_id;
            $order->customer_id = $request->customer;
            $order->sale_type = 3;
            $order->received_by = '';
            $order->date = date('Y-m-d');
            $order->sub_total = 0;
            $order->discount = 0;
            $order->shipping_charge = $request->shipping_charge;
            $order->total = 0;
            $order->paid = 0;
            $order->due = 0;
            $order->opening_sale = 0;
            $order->note = $request->notes ?? 'Ecommerce';
            $order->user_id = $userId;
            $order->status = 0;//Initial
            $order->save();

            //Logs
            $orderLog = new OrderLog();
            $orderLog->order_type = 'SALE';
            $orderLog->order_id  = $order->id;
            $orderLog->created_by  = $userId;
            $orderLog->status  = 'ORDER PROCESSING';
            $orderLog->save();

            //OrderInfo
            $ecomOrderInfo = EcomOrderInfo::create([
                'order_id' => $order->id, // Assuming the user is authenticated
                'user_id' => $userId, // Assuming the user is authenticated
                'order_number' => $newCodeOrder,
                'pi_name' => $request->billing['name'],
                'pi_email' => $request->billing['email'],
                'pi_phone_no' => $request->billing['phone'],
                'country' => $request->shipping['country'], // Assuming this maps to your countries table
                'shipping_city' => $request->shipping['city'],
                'zip_code' => $request->shipping['zip'],
                'order_notes' => $request->shipping['orderNotes'],
                'total_amount' => $request->totals['total'],
                'discount' => $request->totals['discount'],
                'status' => 'pending',
                'shipping_address' => $request->shipping['address'],
                'billing_address' => $request->billing['address'] ?? null, // Assuming `billing['address']` exists
                'payment_method' => $request->paymentMethod,
                'payable_amount' => $request->totals['total'], // Assuming this matches `total_amount`
                'ordered_at' => now(),
            ]);

            $subTotal = 0;
            $totalDiscount = 0;
            $buyingPrice = 0;
            $sellingPrice = 0;
            $items = $request->input('items');
            if ($request->items) {
                foreach ($items as $item) {
                    $batch_no = $item['batch_no'];
                    $product = Product::where('id', $item['product_id'])->first();
                    $inventory = PurchaseInventory::where('product_id', $product->id)
                        ->where('sister_concern_id', $request->sister_concern_id)
                        ->where('batch_no', $batch_no)
                        ->with('product')
                        ->first();

                    $buyingPrice += $inventory->unit_price * $item['quantity'];
                    $sellingPrice += ($item['price'] + $item['discount']) * $item['quantity'];


                    $salesOrderProduct = new SalesOrderProduct();
                    $salesOrderProduct->sister_concern_id = $request->sister_concern_id;
                    $salesOrderProduct->product_id = $product->id;
                    $salesOrderProduct->sales_order_id = $order->id;
                    $salesOrderProduct->batch_no = $batch_no;
                    $salesOrderProduct->name = $product->name;
                    $salesOrderProduct->serial = $product->code;
                    $salesOrderProduct->sale_type = 3;//Ecommerce
                    $salesOrderProduct->quantity = $item['quantity'];
                    $salesOrderProduct->discount_percentage = $item['discount_percent'];
                    $salesOrderProduct->discount_amt = $item['discount'];
                    $salesOrderProduct->unit_price = ($item['price'] + $item['discount']);
                    $salesOrderProduct->buy_price = $inventory->unit_price ?? 0;
                    $salesOrderProduct->date = date('Y-m-d');
                    $salesOrderProduct->total = ($item['price'] + $item['discount']) * $item['quantity'];
                    $salesOrderProduct->user_id = $userId;
                    $salesOrderProduct->save();

                    $subTotal += ($item['price'] + $item['discount']) * $item['quantity'];
                    $totalDiscount += $item['discount'] * $item['quantity'];

                }
            }
            if(!$request->isBuyNow){
                //Empty Cart After Order
                // $cart = Cart::where('user_id',auth()->id())->first();
                // CartItem::where('cart_id',$cart->id)->delete();
                // $cart->total_price = 0;
                // $cart->discount = 0;
                // $cart->tax = 0;
                // $cart->save();
            }
            $order->sub_total = $subTotal;
            // $total = $subTotal - $totalDiscount + $request->shipping_charge;
            $total = $subTotal - $totalDiscount;
            $order->discount = $totalDiscount;
            $order->total = ceil($total);
            $order->due = ceil($total);
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id,
                'order_number' => $order->order_no,
                'redirect' => route('bkash-create', ['order_id' => $order->id,'type'=>'Ecom']) 
            ]);
        } catch (\Exception $e) { 
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place the order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
