<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\MobileBanking;
use App\Models\OrderLog;
use App\Models\Product;
use App\Models\PurchaseInventory;
use App\Models\PurchaseInventoryLog;
use App\Models\SalePayment;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SaleReturnController extends Controller
{
    public function salesReturnFMReceiptOnline(){
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();
        return view('sale.sales_return.online',compact('accounts','mobileBankings'));
    }
    public function salesReturnFMReceiptOnlineDatatable(Request $request)
    {
        $query = SalesOrder::with(['customer', 'department', 'user'])->where('sale_type', 2)->where('is_return',1)->orderBy('id', 'desc');

        $query->where('department_id', 1);
        if ($request->input('start_date') && $request->input('end_date')) {
            $query->whereBetween('date', [date('Y-m-d', strtotime($request->input('start_date'))), date('Y-m-d', strtotime($request->input('end_date')))]);
        }

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
                if ($order->sale_type == 1 && auth()->user()->can('sales_receipt_offline_edit')) {
                    $btn .= '<a class="btn btn-info btn-sm" href="' . route('sales_order_fm.edit_pos', ['order' => $order->id]) . '"><i class="fas fa-edit"></i></a> ';
                }
                if (auth()->user()->can('sales_receipt_online_delete')) {
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
            ->editColumn('return', function (SalesOrder $order) {
                return '৳' . number_format($order->return_amount, 2);
            })
            ->editColumn('deduction', function (SalesOrder $order) {
                return '৳' . number_format($order->deduction, 2);
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
    public function salesReturnFMReceiptOffline(){
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();
        return view('sale.sales_return.offline',compact('accounts','mobileBankings'));
    }
    public function salesReturnFMReceiptOfflineDatatable(Request $request)
    {
        $query = SalesOrder::with(['customer', 'department', 'user'])->where('sale_type', 1)->where('is_return',1)->orderBy('id', 'desc');

        $query->where('department_id', 1);
        if ($request->input('start_date') && $request->input('end_date')) {
            $query->whereBetween('date', [date('Y-m-d', strtotime($request->input('start_date'))), date('Y-m-d', strtotime($request->input('end_date')))]);
        }

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
                if ($order->sale_type == 1 && auth()->user()->can('sales_receipt_offline_edit')) {
                    $btn .= '<a class="btn btn-info btn-sm" href="' . route('sales_order_fm.edit_pos', ['order' => $order->id]) . '"><i class="fas fa-edit"></i></a> ';
                }
                if (auth()->user()->can('sales_receipt_offline_delete')) {
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
            ->editColumn('return', function (SalesOrder $order) {
                return '৳' . number_format($order->return_amount, 2);
            })
            ->editColumn('deduction', function (SalesOrder $order) {
                return '৳' . number_format($order->deduction, 2);
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
    public function salesReturnFMOnline(){
        $customers = Customer::orderBy('name')->get();
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        return view('sale.sales_return.create',compact('customers','accounts'));
    }
    public function salesReturnFMOnlinePost(Request $request){
        // dd($request->all());
        $rules = [
            'sale_type' => 'required',
            'order' => 'required',
            'customer' => 'required',
            'product.*' => 'required',
            'batch.*' => 'required',
            'pack_quantity.*' => 'required|numeric|min:.01',
            'return_quantity.*' => 'required|numeric|min:.01',
            'unit_price.*' => 'required|numeric|min:0',
            'date' => 'required|date',
            'received_by' => 'nullable|string|max:255',
            'discount.*' => 'required|numeric|min:0',
            'pack_size.*' => 'required|numeric|min:0',
        ];

        $request->validate($rules);

        $saleOrder = SalesOrder::where('id',$request->order)->first();
        if ($request->product) {
            foreach ($request->product as $key => $productId) {
                $batch_no = str_replace("Batch-", "", $request->batch[$key]);
                $product = Product::where('id', $productId)->first();
                $inventory = PurchaseInventory::where('product_id', $product->id)
                    ->where('batch_no', $batch_no)
                    ->where('department_id', $saleOrder->department_id)
                    ->first();
                if ($request->return_quantity[$key] > $request->pack_quantity[$key]) {
                    return redirect()->back()->with('error', 'Return quantity must be less than or equal to sale quantity for ' . $inventory->product->name)->withInput();
                }
            }
        }

        DB::beginTransaction();
        try{
            // dd($saleOrder);
            
            //Logs
            $orderLog = new OrderLog();
            $orderLog->order_type = 'SALE';
            $orderLog->order_id  = $saleOrder->id;
            $orderLog->created_by  = Auth::id();
            $orderLog->status  = 'SALE RETURN';
            $orderLog->save();

            $counter = 0;
            $subTotal = 0;
            $totalReturnDiscount = 0;
            $buyingPrice = 0;
            $sellingPrice = 0;

            if ($request->product) {
                foreach ($request->product as $productId) {
                    if($request->return_quantity[$counter] > 0){
                        $batch_no = str_replace("Batch-", "", $request->batch[$counter]);
                        $product = Product::where('id', $productId)->first();
                        $inventory = PurchaseInventory::where('product_id', $product->id)
                            ->where('department_id', $saleOrder->department_id)
                            ->where('batch_no', $batch_no)
                            ->with('product')
                            ->first();
                            // dd($inventory);
                        $discount_percent = $request->discount_percentage[$counter];
                        $discountAmt = $discount_percent > 0 ? (($request->pack_quantity[$counter] - $request->return_quantity[$counter]) * (($discount_percent * $request->unit_price[$counter]) / 100)) : 0;

                        $salesOrderProduct = SalesOrderProduct::where('department_id',$saleOrder->department_id)->where('sales_order_id',$saleOrder->id)->where('product_id',$product->id)->first();
                        $salesOrderProduct->return_quantity = $request->return_quantity[$counter];
                        //$salesOrderProduct->discount_amt = $discountAmt;
                        $salesOrderProduct->save();

                        $inventoryLog = new PurchaseInventoryLog();
                        $inventoryLog->department_id = $saleOrder->department_id;
                        $inventoryLog->purchase_inventory_id = $inventory->id;
                        $inventoryLog->product_id = $product->id;
                        $inventoryLog->sales_order_id = $saleOrder->id;
                        $inventoryLog->batch_no = $batch_no;
                        $inventoryLog->type = 1; //Product Return In
                        $inventoryLog->sale_type = $request->sale_type;
                        $inventoryLog->date = date('Y-m-d', strtotime($request->date));
                        $inventoryLog->expiry_date = Carbon::parse($request->expiry_date[$counter])->format('Y-m-d');
                        $inventoryLog->pack_size_id = $inventory->pack_size_id;
                        $inventoryLog->pack_size = $request->pack_size[$counter];
                        $inventoryLog->pack_quantity = $request->return_quantity[$counter];
                        $inventoryLog->quantity = $request->pack_size[$counter] * $request->return_quantity[$counter];
                        $inventoryLog->unit_price = $inventory->unit_price;
                        $inventoryLog->selling_price = $request->unit_price[$counter];
                        $inventoryLog->is_return = 1;
                        $inventoryLog->user_id = Auth::id();
                        $inventoryLog->save();

                        $inventory->pack_quantity = $product->inventory_pack_qty($product->id, $saleOrder->department_id, $batch_no);
                        $inventory->quantity = $product->inventory_qty($product->id, $saleOrder->department_id, $batch_no);
                        $inventory->save();

                        $totalReturnDiscount += $request->return_discount_amt[$counter];
                    }

                    $buyingPrice += $inventory->unit_price * ($request->pack_quantity[$counter]-$request->return_quantity[$counter]);
                    $sellingPrice += $request->unit_price[$counter] * ($request->pack_quantity[$counter]-$request->return_quantity[$counter]);
                    
                    $counter++;
                }
            }

            //Payment Section Update
            $counter1 = 0;
            $totalPayment = 0;
            foreach ($request->payment_id as $paymentId) {
                $salePayment = SalePayment::where('sales_order_id',$saleOrder->id)->where('id',$paymentId)->first();
                $salePayment->amount = $request->payment_amount[$counter1];
                $salePayment->save();
                //dd($salePayment);
                $totalPayment += $request->payment_amount[$counter1];
                $counter1++;
            }
            $saleOrder->paid = $totalPayment;
            $saleOrder->save();
            //End Payment Section Update

            $saleOrder->is_return = 1;
            $saleOrder->return_date = Carbon::parse($request->date)->format('Y-m-d');
            $saleOrder->return_note = $request->note;
            $saleOrder->return_discount = floor($totalReturnDiscount);
            $saleOrder->deduction = $request->total_return_deduction;
            $saleOrder->return_amount = $request->total_return;
            // $due = $saleOrder->total - $request->total_return - ($saleOrder->discount - floor($totalReturnDiscount)) - $saleOrder->paid + $request->total_return_deduction;
            $due = $saleOrder->total - $request->total_return  - $saleOrder->paid + $request->total_return_deduction;
            $saleOrder->due = $due;
            $saleOrder->save();

            // Buying Price log
            //$log = new TransactionLog();
            $log = TransactionLog::where('department_id',$saleOrder->department_id)->where('sales_order_id',$saleOrder->id)->where('transaction_type',4)->first();
            $log->department_id = $saleOrder->department_id;
            $log->particular = 'Buying price for ' . $saleOrder->order_no;
            $log->sale_type = $request->sale_type;
            $log->transaction_type = 4;
            $log->transaction_method = 0;
            $log->account_head_type_id = 0;
            $log->account_head_sub_type_id = 0;
            $log->amount = $buyingPrice;
            $log->sales_order_id = $saleOrder->id;
            $log->customer_id = $saleOrder->customer_id;
            $log->note = $request->notes;
            $log->user_id = Auth::id();
            $log->save();

            // Selling Price log
            // $log = new TransactionLog();
            $log = TransactionLog::where('department_id',$saleOrder->department_id)->where('sales_order_id',$saleOrder->id)->where('transaction_type',5)->first();
            $log->department_id = $saleOrder->department_id;
            $log->sale_type = $request->sale_type;
            $log->particular = 'Selling price for ' . $saleOrder->order_no;
            $log->transaction_type = 5;
            $log->transaction_method = 0;
            $log->account_head_type_id = 0;
            $log->account_head_sub_type_id = 0;
            $log->amount = $sellingPrice;
            $log->discount_amt = floor($log->discount_amt - $totalReturnDiscount);
            $log->sales_order_id = $saleOrder->id;
            $log->customer_id = $saleOrder->customer_id;
            $log->note = $request->notes;
            $log->user_id = Auth::id();
            $log->save();

            DB::commit();

            if($saleOrder->sale_type == 2){
                return redirect()->route('sales_return_fm_receipt_online.all')->with('message','Sales Return created successfully');
            }else{
                return redirect()->route('sales_return_fm_receipt_offline.all')->with('message','Sales Return created successfully');
            }
            
        }catch(Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage())->withInput();
        }
    }
}
