<?php

namespace App\Http\Controllers;

use App\Imports\PurchasesImport;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Brand;
use App\Models\PackSize;
use App\Models\Cash;
use App\Models\Category;
use App\Models\Department;
use App\Models\MobileBanking;
use App\Models\OrderLog;
use App\Models\PurchaseInventory;
use App\Models\PurchaseInventoryLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\PurchasePayment;
use App\Models\Product;
use App\Models\ProductSalesOrder;
use App\Models\SisterConcern;
use App\Models\Supplier;
use App\Models\TransactionLog;
use App\Models\Unit;
use SakibRahaman\DecimalToWords\DecimalToWords;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderController extends Controller
{
    public function purchaseOrder() {
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $suppliers = Supplier::where('status', 1)->orderBy('name')->get();
        $products = Product::where('sister_concern_id',1)->where('status', 1)->orderBy('name')->get();
        $brands = Brand::where('status',1)->whereNotIn('id',[0])->get();
        $categories = Category::where('status',1)->get();
        $units = Unit::where('status',1)->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();

        return view('purchase.purchase_order.create', compact('suppliers','units','categories', 'products','accounts','brands','mobileBankings'));
    }

    public function purchaseOrderPost(Request $request) {
        //dd($request->all());
        $total = $request->total;
        $rules = [
            'supplier' => 'required',
            'brand' => 'nullable',
            'date' => 'required|date',
            'product.*' => 'required',
            'quantity.*' => 'required|numeric|min:.01',
            'unit_price.*' => 'required|numeric|min:0',
            'selling_price.*' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0|max:'.$total,
        ];

        if ($request->payment_type == '2') {
            $rules['account'] = 'required';
            $rules['cheque_status'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            if ($request->payment_type == 1) {
                $cash = Cash::first();

                if ($request->total > $cash->amount)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            } elseif ($request->payment_type == 3) {
                $mobileBanking = MobileBanking::find($request->mobile_banking);

                if ($request->total > $mobileBanking->amount)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            } else {
                if ($request->account != '') {
                    $account = BankAccount::find($request->account);

                    if ($request->total > $account->balance)
                        $validator->errors()->add('amount', 'Insufficient balance.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $lastPurchaseOrder = PurchaseOrder::where('sister_concern_id',1)->latest('id')->first();
        
        $token = $request->submission_token;
        if (!$token || session('submission_token') !== $token) {
            return redirect()->route('purchase_receipt.all')->with('error', 'Invalid or duplicate submission.')->withInput();
        }
        session()->forget('submission_token');
        
        // Generate the next code
        // $nextCodeNumber = !empty($lastPurchaseOrder) ? intval(substr($lastPurchaseOrder->order_no, 5)) + 1 : 1;
        if ($lastPurchaseOrder && preg_match('/-(\d+)$/', $lastPurchaseOrder->order_no, $matches)) {
            $nextCodeNumber = intval($matches[1]) + 1;
        } else {
            $nextCodeNumber = 1;
        }
        // Format the new code as 'p-000001'
        $newCodeOrder = 'PO-' . str_pad($nextCodeNumber, 8, '0', STR_PAD_LEFT);

        //dd($newCodeOrder);
        DB::beginTransaction();
        try{
        $order = new PurchaseOrder();
        $order->order_no = $newCodeOrder;
        $order->sister_concern_id = 1;
        $order->supplier_id = $request->supplier;
        $order->brand_id = $request->brand ?? null;
        $order->date = Carbon::parse($request->date)->format('Y-m-d');
        $order->discount_percentage = $request->discount_percentage;
        $order->discount = $request->discount;
        $order->sub_total = 0;
        $order->total = 0;
        $order->paid = 0;
        $order->due = 0;
        $order->notes = $request->notes;
        $order->received_by = $request->received_by;
        $order->user_id = Auth::id();
        $order->save();

        $counter = 0;
        $sub_total = 0;
        foreach ($request->product as $reqProduct) {
            $product = Product::find($reqProduct);
            $purchaseOrderProduct = PurchaseOrderProduct::where('purchase_order_id', $order->id)
            ->whereNotIn('product_id',$request->product)
            ->first();
            //dd($request->all());
            if (empty($purchaseOrderProduct)) {
                $purchaseOrderProduct = new PurchaseOrderProduct();
                $purchaseOrderProduct->sister_concern_id = 1;
                $purchaseOrderProduct->purchase_order_id = $order->id;
                $purchaseOrderProduct->product_id = $product->id;
                $purchaseOrderProduct->name = $product->name;
                $purchaseOrderProduct->type = 2;
                $purchaseOrderProduct->serial_no = $product->code;
                $purchaseOrderProduct->quantity = $request->quantity[$counter];
                $purchaseOrderProduct->unit_price = $request->unit_price[$counter];
                $purchaseOrderProduct->selling_price = $request->selling_price[$counter];
                $purchaseOrderProduct->total = $request->quantity[$counter] * $request->unit_price[$counter];
                $purchaseOrderProduct->user_id = Auth::id();
                $purchaseOrderProduct->save();

                // Latest Sale Price
                $product->old_selling_price = $request->selling_price[$counter];
                $product->selling_price = $request->selling_price[$counter];
                $product->save();

                // Inventory Log
                $inventoryLog = new PurchaseInventoryLog();
                $inventoryLog->sister_concern_id = 1;
                $inventoryLog->product_id = $product->id;
                $inventoryLog->type = 1;
                $inventoryLog->date = Carbon::parse($request->date)->format('Y-m-d');
                $inventoryLog->quantity = $request->quantity[$counter];
                $inventoryLog->unit_price = $request->unit_price[$counter];
                $inventoryLog->selling_price = $request->selling_price[$counter];
                $inventoryLog->supplier_id = $request->supplier;
                $inventoryLog->purchase_order_id = $order->id;
                $inventoryLog->user_id = Auth::id();
                $inventoryLog->save();

                // Inventory
                $inventory =  PurchaseInventory::where('sister_concern_id', 1)
                ->where('product_id', $product->id)
                ->where('unit_price',$request->unit_price[$counter])
                ->first();

                $newCode = null;
                if (!$inventory) {

                    $lastPurchaseInventory = PurchaseInventory::where('product_id',$product->id)->where('sister_concern_id', 1)->latest('id')->first();
                    // Generate the next code
                    //  $nextCodeNumber = !empty($lastPurchaseInventory) ? intval($lastPurchaseInventory->batch_no) + 1 : 1;
                     $nextCodeNumber = $lastPurchaseInventory ? intval($lastPurchaseInventory->batch_no) + 1 : 1;

                    // Form at the new code as 'p-000001'
                    $newCode = $nextCodeNumber;
                    $inventory = new PurchaseInventory();

                }
                $batch_no = $newCode ?? $inventory->batch_no;
                //dd($batch_no);
                $inventoryLog->batch_no = $batch_no;
                $inventoryLog->save();

                $inventory->batch_no = $batch_no;
                $inventory->sister_concern_id = 1;
                $inventory->product_id = $product->id;
                $inventory->serial_no = $product->code;
                $inventory->quantity = $product->inventory_qty($product->id, 1, $batch_no);
                $inventory->unit_price = $request->unit_price[$counter];
                $inventory->selling_price = $request->selling_price[$counter];
                $inventory->save();

                $inventoryLog->purchase_inventory_id = $inventory->id;
                $inventoryLog->save();

                $purchaseOrderProduct->batch_no = $inventory->batch_no;
                $purchaseOrderProduct->save();

                $sub_total +=  $request->quantity[$counter] * $request->unit_price[$counter];

            }
            $counter++;
        }

        $order->sub_total = $sub_total;
        $total = $sub_total - $request->discount;
        $order->total = $total;
        $order->due = $total;
        $order->save();

        //Logs
        $orderLog = new OrderLog();
        $orderLog->order_type = 'PURCHASE';
        $orderLog->order_id  = $order->id;
        $orderLog->created_by  = Auth::id();
        $orderLog->status  = 'PURCHASE CREATE';
        $orderLog->save();

        // Sales Payment
        if ($request->paid > 0) {
            if ($request->payment_type == 1 || $request->payment_type == 3) {
                $payment = new PurchasePayment();
                $payment->sister_concern_id = 1;
                $payment->supplier_id = $request->supplier;
                $payment->purchase_order_id = $order->id;
                $payment->transaction_method = $request->payment_type;
                if ($request->payment_type == 3) {
                    $payment->mobile_banking_id = $request->mobile_banking;
                }
                $payment->amount = $request->paid;
                $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                $payment->note = $request->notes;
                $payment->user_id = Auth::id();
                $payment->save();

                if ($request->payment_type == 1)
                    Cash::first()->decrement('amount', $request->paid);
                else
                    MobileBanking::find($request->mobile_banking)->decrement('amount', $request->paid);

                $log = new TransactionLog();
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->sister_concern_id = 1;
                $log->supplier_id = $order->supplier_id;
                $log->particular = 'Payment for '.$order->supplier->name.' for '.$order->order_no;
                $log->transaction_type = 2;
                $log->transaction_method = $request->payment_type;
                $log->account_head_type_id = 1;
                $log->account_head_sub_type_id = 1;
                $log->amount = $request->paid;
                $log->note = $request->notes;
                if ($request->payment_type == 3) {
                    $log->mobile_banking_id = $request->mobile_banking;
                }
                $log->purchase_payment_id = $payment->id;
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

                $payment = new PurchasePayment();
                $payment->sister_concern_id = 1;
                $payment->supplier_id = $order->supplier_id;
                $payment->purchase_order_id = $order->id;
                $payment->transaction_method = 2;
                $payment->bank_id = $bankAccount->bank_id;
                $payment->branch_id = $bankAccount->branch_id;
                $payment->bank_account_id = $request->account;
                $payment->cheque_no = $request->cheque_no;
                $payment->cheque_image = $image;
                $payment->amount = $request->paid;
                $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                $payment->notes = $request->notes;
                $payment->user_id = Auth::id();
                $payment->save();

                BankAccount::find($request->account)->decrement('balance', $request->amount);

                $log = new TransactionLog();
                $log->sister_concern_id = 1;
                $log->supplier_id = $order->supplier_id;
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->particular = 'Paid to '.$order->supplier->name.' for '.$order->order_no;
                $log->transaction_type = 2;
                $log->transaction_method = 2;
                $log->account_head_type_id = 1;
                $log->account_head_sub_type_id = 1;
                $log->bank_id = $bankAccount->bank_id;
                $log->branch_id = $bankAccount->branch_id;
                $log->bank_account_id = $request->account;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_image = $image;
                $log->amount = $request->amount;
                $log->note = $request->notes;
                $log->purchase_payment_id = $payment->id;
                $log->user_id = Auth::id();
                $log->save();
            }
            $order->increment('paid', $request->paid);
            $order->decrement('due', $request->paid);
        }

        DB::commit();

        return redirect()->route('purchase_receipt.details', ['order' => $order->id]);

        }catch(Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('error',$exception->getMessage())->withInput();
        }
    }

    public function purchaseReceiptDetails(PurchaseOrder $order) {

        return view('purchase.receipt.details', compact('order'));
    }

    public function purchaseReceiptShare(PurchaseOrder $order) {
        return view('purchase.receipt.share', compact('order'));
    }

    public function purchaseReceiptPrint(PurchaseOrder $order) {
        return view('purchase.receipt.print', compact('order'));
    }

    public function barCode(PurchaseOrder $order) {
        $barCodes = collect();

        foreach($order->products as $product) {
            if ($product->type == 2) {
                $barCodes->push(['name' => $product->name, 'serial' => $product->serial_no]);
            } else {
                $barCodes->push(['name' => $product->name, 'serial' => $product->serial_no]);
            }
        }

        // return view('purchase.receipt.bar_code', compact('order', 'barCodes'));
        return view('purchase.receipt.barcode_print', compact('order', 'barCodes'));
    }
    public function inventoryBarCode(PurchaseInventory $purchaseInventory){
        //dd($purchaseInventory);
        return view('purchase.receipt.inventory_barcode_print', compact('purchaseInventory'));
    }
    public function qrCode(PurchaseOrder $order) {
        $qrCodes = collect();

        foreach($order->products as $product) {
            if ($product->pivot->type == 2) {
                $qrCodes->push(['name' => $product->pivot->name, 'serial' => $product->pivot->serial_no]);
            } else {
                $qrCodes->push(['name' => $product->pivot->name, 'serial' => $product->pivot->serial_no]);
            }
        }

        return view('purchase.receipt.qr_code', compact('order', 'qrCodes'));
    }

    public function qrCodePrint(PurchaseOrder $order) {
        $qrCodes = collect();

        foreach($order->products as $product) {
            if ($product->pivot->type == 2) {
                $qrCodes->push(['name' => $product->pivot->name, 'serial' => $product->pivot->serial_no]);
            } else {
                $qrCodes->push(['name' => $product->pivot->name, 'serial' => $product->pivot->serial_no]);
            }
        }
        return view('purchase.receipt.qr_code_print', compact('order', 'qrCodes'));
    }

    public function barCodePrint(PurchaseOrder $order) {
        $barCodes = collect();

        foreach($order->products as $product) {
            if ($product->type == 2) {
                $barCodes->push(['name' => $product->name, 'serial' => $product->serial_no]);
            } else {
                $barCodes->push(['name' => $product->name, 'serial' => $product->serial_no]);
            }
        }
        return view('purchase.receipt.bar_code_print', compact('order', 'barCodes'));
    }

    public function supplierPayment() {
        $suppliers = Supplier::all();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();

        return view('purchase.supplier_payment.all', compact('suppliers', 'banks', 'accounts', 'mobileBankings'));
    }

    public function supplierPaymentGetOrders(Request $request) {
        $orders = PurchaseOrder::where('supplier_id', $request->supplierId)
            ->where('due', '>', 0)
            ->orderBy('order_no')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function supplierPaymentGetRefundOrders(Request $request) {
        $orders = PurchaseOrder::where('supplier_id', $request->supplierId)
            ->where('refund', '>', 0)
            ->orderBy('order_no')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function supplierPaymentOrderDetails(Request $request) {
        $order = PurchaseOrder::where('id', $request->orderId)
            ->first()->toArray();

        return response()->json($order);
    }

    public function makePayment(Request $request) {
        $rules = [
            'order' => 'required',
            'payment_type' => 'required',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->payment_type == '2') {
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }elseif($request->payment_type == '3'){
            $rules['mobile_banking'] = 'required';
        }

        if ($request->order != '') {
            $order = PurchaseOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            if ($request->payment_type == 1) {
                $cash = Cash::first();

                if ($request->amount > $cash->amount)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            } elseif ($request->payment_type == 3) {
                $mobileBanking = MobileBanking::find($request->mobile_banking);

                if ($request->amount > $mobileBanking->amount)
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
        DB::beginTransaction();
        try {
            $order = PurchaseOrder::find($request->order);

            if ($request->payment_type == 1 || $request->payment_type == 3) {
                $payment = new PurchasePayment();
                $payment->sister_concern_id = $order->sister_concern_id;
                $payment->supplier_id = $order->supplier_id;
                $payment->purchase_order_id = $order->id;
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
                    Cash::first()->decrement('amount', $request->amount);
                else
                    MobileBanking::find($request->mobile_banking)->decrement('amount', $request->amount);

                $log = new TransactionLog();
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->sister_concern_id = $order->sister_concern_id;
                $log->supplier_id = $order->supplier_id;
                $log->particular = 'Paid to '.$order->supplier->name.' for '.$order->order_no;
                $log->transaction_type = 2;
                $log->transaction_method = $request->payment_type;
                $log->account_head_type_id = 1;
                $log->account_head_sub_type_id = 1;
                $log->amount = $request->amount;
                $log->note = $request->note;
                if ($request->payment_type == 3) {
                    $log->mobile_banking_id = $request->mobile_banking;
                }
                $log->purchase_payment_id = $payment->id;
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

                $payment = new PurchasePayment();
                $payment->sister_concern_id = $order->sister_concern_id;
                $payment->supplier_id = $order->supplier_id;
                $payment->purchase_order_id = $order->id;
                $payment->transaction_method = 2;
                $payment->bank_id = $bankAccount->bank_id;
                $payment->branch_id = $bankAccount->branch_id;
                $payment->bank_account_id = $request->account;
                $payment->cheque_status = $request->cheque_status;
                $payment->cheque_no = $request->cheque_no;
                $payment->cheque_image = $image;
                $payment->amount = $request->amount;
                $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                $payment->note = $request->note;
                $payment->user_id = Auth::id();
                $payment->save();

                BankAccount::find($request->account)->decrement('balance', $request->amount);

                $log = new TransactionLog();
                $log->sister_concern_id = $order->sister_concern_id;
                $log->supplier_id = $order->supplier_id;
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->particular = 'Paid to '.$order->supplier->name.' for '.$order->order_no;
                $log->transaction_type = 2;
                $log->transaction_method = 2;
                $log->account_head_type_id = 1;
                $log->account_head_sub_type_id = 1;
                $log->bank_id = $bankAccount->bank_id;
                $log->branch_id = $bankAccount->branch_id;
                $log->bank_account_id = $request->account;
                $log->cheque_status = $request->cheque_status;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_image = $image;
                $log->amount = $request->amount;
                $log->note = $request->note;
                $log->purchase_payment_id = $payment->id;
                $log->user_id = Auth::id();
                $log->save();
            }

            $order->increment('paid', $request->amount);
            $order->decrement('due', $request->amount);

            DB::rollback();
            return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('purchase_receipt.payment_details', ['payment' => $payment->id])]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function updateSellingPrice(Request $request) {
        $rules = [
            'inventory_id' => 'required',
            'product_id' => 'required',
            'amount' => 'required|numeric|min:0.1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        DB::beginTransaction();
        try {
            $inventory = PurchaseInventory::find($request->inventory_id);
            $inventory->selling_price = $request->amount;
            $inventory->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Selling price has been updated.']);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function makeRefund(Request $request) {
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
            $order = PurchaseOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:'.$order->refund;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $order = PurchaseOrder::find($request->order);

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new PurchasePayment();
            $payment->sister_concern_id = $order->sister_concern_id;
            $payment->supplier_id = $order->supplier_id;
            $payment->purchase_order_id = $order->id;
            $payment->type = 2;
            $payment->transaction_method = $request->payment_type;
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
            $log->sister_concern_id = $order->sister_concern_id;
            $log->supplier_id = $order->supplier_id;
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->particular = 'Refund from '.$order->supplier->name.' for '.$order->order_no;
            $log->transaction_type = 5;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 20;
            $log->account_head_sub_type_id = 42;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->purchase_payment_id = $payment->id;
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

            $payment = new PurchasePayment();
            $payment->sister_concern_id = $order->sister_concern_id;
            $payment->supplier_id = $order->supplier_id;
            $payment->purchase_order_id = $order->id;
            $payment->type = 2;
            $payment->transaction_method = 2;
            $payment->bank_id =  $bankAccount->bank_id;
            $payment->branch_id =  $bankAccount->branch_id;
            $payment->bank_account_id = $request->account;
            $payment->cheque_status = $request->cheque_status;
            $payment->cheque_no = $request->cheque_no;
            $payment->cheque_image = $image;
            $payment->amount = $request->amount;
            $payment->date = Carbon::parse($request->date)->format('Y-m-d');
            $payment->note = $request->note;
            $payment->user_id = Auth::id();
            $payment->save();

            BankAccount::find($request->account)->increment('balance', $request->amount);

            $log = new TransactionLog();
            $log->sister_concern_id = $order->sister_concern_id;
            $log->supplier_id = $order->supplier_id;
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->particular = 'Refund from '.$order->supplier->name.' for '.$order->order_no;
            $log->transaction_type = 5;
            $log->transaction_method = 2;
            $log->account_head_type_id = 20;
            $log->account_head_sub_type_id = 42;
            $log->bank_id =  $bankAccount->bank_id;
            $log->branch_id =  $bankAccount->branch_id;
            $log->bank_account_id = $request->account;
            $log->cheque_status = $request->cheque_status;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->purchase_payment_id = $payment->id;
            $log->user_id = Auth::id();
            $log->save();
        }

        $order->decrement('refund', $request->amount);

        return response()->json(['success' => true, 'message' => 'Refund has been completed.', 'redirect_url' => route('purchase_receipt.payment_details', ['payment' => $payment->id])]);
    }

    public function purchasePaymentDetails(PurchasePayment $payment) {
        $payment->amount_in_word = DecimalToWords::convert($payment->amount,'Taka',
            'Poisa');
        return view('purchase.receipt.payment_details', compact('payment'));
    }

    public function purchasePaymentShare(PurchasePayment $payment) {
        $payment->amount_in_word = DecimalToWords::convert($payment->amount,'Taka',
            'Poisa');
        return view('purchase.receipt.payment_share', compact('payment'));
    }


    public function batchWiseInventoryStock() {
        $categories = Category::where('status',1)->get();
        $brands = Brand::where('status',1)->get();
        return view('purchase.inventory.all',compact('categories','brands'));
    }
    public function outOfStock() {
        return view('purchase.inventory.out_of_stock');
    }
    public function outOfStockDatatable(Request $request) {

        $sort = 'asc';
        $query = PurchaseInventory::with(['product'=> function ($sq) use ($sort) {
            $sq->orderBy('name', $sort);
        },'department']);

        $query->where('sister_concern_id', 1)->where('quantity', '=',0);
        // $query->where('sister_concern_id', 1);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->filter(function ($query) {
                if (request()->has('search') && $search = request('search')['value']) {
                    $query->whereHas('product', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
                }
            })
            ->addColumn('product', function(PurchaseInventory $inventory) {
                return $inventory->product->name??'';
            })
            ->editColumn('product_image', function (PurchaseInventory $inventory) {
                $preview_html = '';
                $preview_html .= '<div class="zoom-gallery">';
                $preview_html .= '<a class="image_preview" title="'. $inventory->product->name .'" data-source="'. asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp') .'" href="'. asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp') .'"><img height="50px" src="'.asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp').'"></a>';
                if($inventory->product->images){
                    foreach($inventory->product->images as $image){
                        $preview_html .= '<a class="image_preview" title="'. $inventory->product->name .'" data-source="'. asset(file_exists($image->path) ? $image->path : 'img/no-image.webp') .'" href="'. asset(file_exists($image->path) ? $image->path : 'img/no-image.webp') .'"><img height="0px" src="'.asset(file_exists($image->path) ? $image->path : 'img/no-image.webp').'"></a>';
                    }
                }
                $preview_html .= '</div>';
                return $preview_html;
            })
            ->addColumn('product_code', function(PurchaseInventory $inventory) {
                return $inventory->product->code??'';
            })
            ->addColumn('department', function(PurchaseInventory $inventory) {
                return $inventory->department->name??'';
            })
            ->addColumn('action', function(PurchaseInventory $inventory) {
                $btn = '<a href="'.route('purchase_inventory.details', ['product' => $inventory->product_id, 'department' => $inventory->department->id??0,'batch'=>$inventory->batch_no]).'" class="btn btn-primary btn-sm">Details</a>&nbsp;';
                $btn .= '<a target="_blank" href="'.route('inventory.bar_code', ['purchaseInventory' => $inventory->id]).'" class="btn btn-primary btn-sm"><i class="fas fa-barcode"></i></a>';
                return $btn;

            })
            ->editColumn('quantity', function(PurchaseInventory $inventory) {
                if ($inventory->quantity > $inventory->product->alert_quantity) {
                    return number_format($inventory->quantity, 0).$inventory->product->unit->name ?? '';
                }else {
                    return '<span class="text-danger">'.number_format($inventory->quantity, 0).'</span>';
                }

            })
            ->addColumn('unit_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->unit_price, 2);
            })
            ->addColumn('selling_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->selling_price, 2);
            })
            ->addColumn('total_unit_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->quantity * $inventory->unit_price, 2);
            })
            ->addColumn('total_selling_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->quantity * $inventory->selling_price, 2);
            })
            ->addColumn('profit_margin', function (PurchaseInventory $inventory) {
                return '৳'.number_format(($inventory->quantity * $inventory->selling_price)-($inventory->quantity * $inventory->unit_price), 2);
            })
            ->editColumn('batch_no', function (PurchaseInventory $inventory) {
                return 'Batch-'.$inventory->batch_no;
            })
            ->rawColumns(['action', 'quantity', 'total_unit_price','batch_no','product_image'])
            ->toJson();
    }
    public function alertQuantityStock() {
        return view('purchase.inventory.alert_quantity_stock');
    }
    public function alertQuantityStockDatatable(Request $request) {
        $query = PurchaseInventory::select([
            'purchase_inventories.product_id',
            'purchase_inventories.sister_concern_id',
            DB::raw('SUM(purchase_inventories.quantity) as total_quantity'),
            DB::raw('SUM(purchase_inventories.quantity * purchase_inventories.unit_price) as total_unit_price'),
            DB::raw('SUM(purchase_inventories.quantity * purchase_inventories.selling_price) as total_selling_price'),
            DB::raw('AVG(purchase_inventories.unit_price) as unit_price'),
            DB::raw('AVG(purchase_inventories.selling_price) as selling_price'),
            'products.name as product_name',
            'products.code as product_code',
            'products.image as product_image', // Add this line
            'departments.name as department_name'
        ])
            ->join('products', 'purchase_inventories.product_id', '=', 'products.id')
            ->join('sister_concerns', 'purchase_inventories.sister_concern_id', '=', 'sister_concerns.id')
            ->where('purchase_inventories.sister_concern_id', 1)
            ->where('purchase_inventories.quantity', '>', 0)
            ->whereColumn('purchase_inventories.quantity', '<=', 'products.alert_quantity')
            ->groupBy(
                'purchase_inventories.product_id',
                'purchase_inventories.sister_concern_id',
                'products.name',
                'products.code',
                'products.image', // Add this line
                'sister_concerns.name'
            );

        return DataTables::of($query)
            // ->order(function ($query) {
            //     $query->orderBy('products.name', 'asc');
            // })
            ->filter(function ($query) {
                if (request()->has('search') && $search = request('search')['value']) {
                    $query->whereHas('product', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
                }
            })
            ->filterColumn('total_quantity', function ($query, $keyword) {
                $query->havingRaw('SUM(purchase_inventories.quantity) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('total_unit_price', function ($query, $keyword) {
                $query->havingRaw('SUM(purchase_inventories.quantity * purchase_inventories.unit_price) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('total_selling_price', function ($query, $keyword) {
                $query->havingRaw('SUM(purchase_inventories.quantity * purchase_inventories.selling_price) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('unit_price', function ($query, $keyword) {
                $query->havingRaw('AVG(purchase_inventories.unit_price) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('selling_price', function ($query, $keyword) {
                $query->havingRaw('AVG(purchase_inventories.selling_price) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('product_image', function ($query, $keyword) {
                $query->where('products.image', 'LIKE', "%{$keyword}%");
            })
            ->addIndexColumn()
            ->addColumn('action', function ($inventory) {
                $btn = '';
                // $btn = '<a href="' . route('purchase_inventory.details', [
                //     'product' => $inventory->product_id,
                //     'department' => $inventory->sister_concern_id
                // ]) . '" class="btn btn-primary btn-sm">Details</a>';

                return $btn;
            })
            ->editColumn('total_quantity', function ($inventory) {
                return '<span class="text-danger">' . number_format($inventory->total_quantity, 0) . '</span>';
            })
            ->editColumn('product_image', function ($inventory) {
                $preview_html = '';
                $preview_html .= '<div class="zoom-gallery">';
                $preview_html .= '<a class="image_preview" title="'. $inventory->product->name .'" data-source="'. asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp') .'" href="'. asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp') .'"><img height="50px" src="'.asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp').'"></a>';
                if($inventory->product->images){
                    foreach($inventory->product->images as $image){
                        $preview_html .= '<a class="image_preview" title="'. $inventory->product->name .'" data-source="'. asset(file_exists($image->path) ? $image->path : 'img/no-image.webp') .'" href="'. asset(file_exists($image->path) ? $image->path : 'img/no-image.webp') .'"><img height="0px" src="'.asset(file_exists($image->path) ? $image->path : 'img/no-image.webp').'"></a>';
                    }
                }
                $preview_html .= '</div>';
                return $preview_html;
            })
            ->editColumn('selling_price', function ($inventory) {
                return '৳' . number_format($inventory->selling_price, 2);
            })
            ->editColumn('unit_price', function ($inventory) {
                return '৳' . number_format($inventory->unit_price, 2);
            })
            ->editColumn('total_unit_price', function ($inventory) {
                return '৳' . number_format($inventory->total_unit_price, 2);
            })
            ->editColumn('total_selling_price', function ($inventory) {
                return '৳' . number_format($inventory->total_selling_price, 2);
            })
            ->rawColumns(['action', 'total_quantity','product_image'])
            ->toJson();
    }
    public function nearToExpireStock() {
        return view('purchase.inventory.near_to_expire_stock');
    }
    public function nearToExpireStockDatatable(Request $request) {

        $sort = 'asc';
        $eightMonthsFromNow = Carbon::now()->addMonths(8)->format('Y-m-d');
        $twelveMonthsFromNow = Carbon::now()->addMonths(12)->format('Y-m-d');
        $query = PurchaseInventory::with(['product'=> function ($sq) use ($sort) {
            $sq->orderBy('name', $sort);
        },'department','packSize']);
        $query->where('quantity', '>',0);

        $query->where(function ($q) use ($eightMonthsFromNow, $twelveMonthsFromNow) {
            $q->whereHas('packSize', function ($subQuery) use ($twelveMonthsFromNow) {
                $subQuery->where('number', '>=', 120);
            })->where('expiry_date', '<', $twelveMonthsFromNow)
            ->orWhere(function ($subQuery) use ($eightMonthsFromNow) {
                $subQuery->whereDoesntHave('packSize', function ($innerQuery) {
                    $innerQuery->where('number', '>=', 120);
                })->where('expiry_date', '<', $eightMonthsFromNow);
            });
        });
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->filter(function ($query) {
                if (request()->has('search') && $search = request('search')['value']) {
                    $query->whereHas('product', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
                }
            })
            ->addColumn('product', function(PurchaseInventory $inventory) {
                return $inventory->product->name??'';
            })
            ->editColumn('product_image', function (PurchaseInventory $inventory) {
                $preview_html = '';
                $preview_html .= '<div class="zoom-gallery">';
                $preview_html .= '<a class="image_preview" title="'. $inventory->product->name .'" data-source="'. asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp') .'" href="'. asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp') .'"><img height="50px" src="'.asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp').'"></a>';
                if($inventory->product->images){
                    foreach($inventory->product->images as $image){
                        $preview_html .= '<a class="image_preview" title="'. $inventory->product->name .'" data-source="'. asset(file_exists($image->path) ? $image->path : 'img/no-image.webp') .'" href="'. asset(file_exists($image->path) ? $image->path : 'img/no-image.webp') .'"><img height="0px" src="'.asset(file_exists($image->path) ? $image->path : 'img/no-image.webp').'"></a>';
                    }
                }
                $preview_html .= '</div>';
                return $preview_html;
            })
            ->addColumn('product_code', function(PurchaseInventory $inventory) {
                return $inventory->product->code??'';
            })
            ->addColumn('pack_size', function(PurchaseInventory $inventory) {
                return $inventory->packSize->name??'';
            })
            ->addColumn('department', function(PurchaseInventory $inventory) {
                return $inventory->department->name??'';
            })
            ->addColumn('action', function(PurchaseInventory $inventory) {
                $btn = '<a href="'.route('purchase_inventory.details', ['product' => $inventory->product_id, 'department' => $inventory->department->id??0,'batch'=>$inventory->batch_no]).'" class="btn btn-primary btn-sm">Details</a>&nbsp;';
                $btn .= '<a target="_blank" href="'.route('inventory.bar_code', ['purchaseInventory' => $inventory->id]).'" class="btn btn-primary btn-sm"><i class="fas fa-barcode"></i></a>';
                return $btn;

            })
            ->editColumn('quantity', function(PurchaseInventory $inventory) {
                if ($inventory->quantity > $inventory->product->alert_quantity) {
                    return number_format($inventory->quantity, 0).$inventory->product->unit->name ?? '';
                }else {
                    return '<span class="text-danger">'.number_format($inventory->quantity, 0).'</span>';
                }

            })
            ->addColumn('unit_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->unit_price, 2);
            })
            ->addColumn('selling_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->selling_price, 2);
            })
            ->addColumn('total_unit_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->quantity * $inventory->unit_price, 2);
            })
            ->addColumn('total_selling_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->quantity * $inventory->selling_price, 2);
            })
            ->addColumn('profit_margin', function (PurchaseInventory $inventory) {
                return '৳'.number_format(($inventory->quantity * $inventory->selling_price)-($inventory->quantity * $inventory->unit_price), 2);
            })
            ->editColumn('batch_no', function (PurchaseInventory $inventory) {
                return 'Batch-'.$inventory->batch_no;
            })
            ->rawColumns(['action', 'quantity', 'total_unit_price','batch_no','product_image'])
            ->toJson();
    }
    public function inventoryStock() {
            // dd($products);
        return view('purchase.inventory.inventory_stock');
    }
    public function inventoryStockDatatable(Request $request)
     {
        $query = PurchaseInventory::select([
            'purchase_inventories.product_id',
            'purchase_inventories.sister_concern_id',
            DB::raw('SUM(purchase_inventories.quantity) as total_quantity'),
            DB::raw('SUM(purchase_inventories.quantity * purchase_inventories.unit_price) as total_unit_price'),
            DB::raw('SUM(purchase_inventories.quantity * purchase_inventories.selling_price) as total_selling_price'),
            DB::raw('AVG(purchase_inventories.unit_price) as unit_price'),
            DB::raw('AVG(purchase_inventories.selling_price) as selling_price'),
            'products.name as product_name',
            'products.code as product_code',
            'products.image as product_image', // Add this line
            'sister_concerns.name as department_name'
        ])
            ->join('products', 'purchase_inventories.product_id', '=', 'products.id')
            ->join('sister_concerns', 'purchase_inventories.sister_concern_id', '=', 'sister_concerns.id')
            ->where('purchase_inventories.sister_concern_id', 1)
            ->groupBy(
                'purchase_inventories.product_id',
                'purchase_inventories.sister_concern_id',
                'products.name',
                'products.code',
                'products.image', // Add this line
                'sister_concerns.name'
            );

        return DataTables::of($query)
            // ->order(function ($query) {
            //     $query->orderBy('products.name', 'asc');
            // })
            ->filter(function ($query) {
                if (request()->has('search') && $search = request('search')['value']) {
                    $query->whereHas('product', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
                }
            })
            ->filterColumn('total_quantity', function ($query, $keyword) {
                $query->havingRaw('SUM(purchase_inventories.quantity) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('total_unit_price', function ($query, $keyword) {
                $query->havingRaw('SUM(purchase_inventories.quantity * purchase_inventories.unit_price) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('total_selling_price', function ($query, $keyword) {
                $query->havingRaw('SUM(purchase_inventories.quantity * purchase_inventories.selling_price) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('unit_price', function ($query, $keyword) {
                $query->havingRaw('AVG(purchase_inventories.unit_price) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('selling_price', function ($query, $keyword) {
                $query->havingRaw('AVG(purchase_inventories.selling_price) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('product_image', function ($query, $keyword) {
                $query->where('products.image', 'LIKE', "%{$keyword}%");
            })
            ->addIndexColumn()
            ->addColumn('action', function ($inventory) {
                // $btn = '<a href="' . route('purchase_inventory.details', [
                //     'product' => $inventory->product_id,
                //     'department' => $inventory->sister_concern_id
                // ]) . '" class="btn btn-primary btn-sm">Details</a>';

                // return $btn;
            })
            ->editColumn('total_quantity', function ($inventory) {
                if ($inventory->total_quantity > 10) {
                    return number_format($inventory->total_quantity, 0);
                } else {
                    return '<span class="text-danger">' . number_format($inventory->total_quantity, 0) . '</span>';
                }
            })
            ->editColumn('product_image', function ($inventory) {
                return '<a class="image_preview" href="'. asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp') .'"><img height="50px" src="'.asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp').'"></a>';
            })
            ->editColumn('selling_price', function ($inventory) {
                return '৳' . number_format($inventory->selling_price, 2);
            })
            ->editColumn('unit_price', function ($inventory) {
                return '৳' . number_format($inventory->unit_price, 2);
            })
            ->editColumn('total_unit_price', function ($inventory) {
                return '৳' . number_format($inventory->total_unit_price, 2);
            })
            ->editColumn('total_selling_price', function ($inventory) {
                return '৳' . number_format($inventory->total_selling_price, 2);
            })
            ->rawColumns(['action', 'total_quantity','product_image'])
            ->toJson();
    }

    public function purchaseInventoryDetails(Product $product, SisterConcern $sisterConcern,$batch) {
        return view('purchase.inventory.details', compact('product','sisterConcern','batch'));
    }

    public function purchaseInventoryBarCode(Product $product) {
        $rows = PurchaseInventory::where('product_id', $product->id)
            ->where('quantity', '>', 0)->get();

        return view('purchase.inventory.bar_code', compact('rows', 'product'));
    }

    public function purchaseInventoryQrCode(Product $product) {
        $rows = PurchaseInventory::where('product_id', $product->id)
            ->where('quantity', '>', 0)->get();

        return view('purchase.inventory.qr_code', compact('rows', 'product'));
    }

    public function purchaseReceiptEdit(PurchaseOrder $order)
    {
        $sisterConcerns = SisterConcern::where('id', 1)->where('status', 1)->orderBy('name')->get();
        $accounts = BankAccount::where('status', 1)->orderBy('account_no')->get();
        $suppliers = Supplier::where('status', 1)->whereNotIn('id',[0])->orderBy('name')->get();
        $products = Product::where('sister_concern_id',1)->where('status', 1)->orderBy('name')->get();
        $brands = Brand::where('status',1)->whereNotIn('id',[0])->get();
        $categories = Category::where('status',1)->get();
        $units = Unit::where('status',1)->get();
        $mobileBankings = MobileBanking::where('status', 1)->get();

        return view('purchase.purchase_order.edit', compact('order','suppliers','units','categories', 'products','sisterConcerns','accounts','brands','mobileBankings'));
    }

    public function purchaseReceiptEditPost(PurchaseOrder $order, Request $request) {

        $total = $request->total;
        $rules = [
            //'sister_concern_id' => 'required',
            'supplier' => 'required',
            'brand' => 'nullable',
            'date' => 'required|date',
            'product.*' => 'required',
            'quantity.*' => 'required|numeric|min:.01',
            'unit_price.*' => 'required|numeric|min:0',
            'selling_price.*' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0|max:'.$total,
        ];

        if ($request->payment_type == '2') {
            $rules['account'] = 'required';
            $rules['cheque_status'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request,$order) {
            if ($request->paid > 0 && $request->payment_type == null) {
                    $validator->errors()->add('payment_type', 'Please select Payment Type.');
            }
            if ($request->payment_type == 1) {
                $cash = Cash::first();

                if ($request->total > $cash->amount)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            } elseif ($request->payment_type == 3) {
                $mobileBanking = MobileBanking::find($request->mobile_banking);

                if ($request->total > $mobileBanking->amount)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            } else {
                if ($request->account != '') {
                    $account = BankAccount::find($request->account);

                    if ($request->total > $account->balance)
                        $validator->errors()->add('amount', 'Insufficient balance.');
                }
            }
            $purchaseInventoryLogs = PurchaseInventoryLog::where('purchase_order_id', $order->id)->get();
            foreach ($purchaseInventoryLogs as $key =>$purchaseInventoryLog) {
                $purchaseInventory = PurchaseInventory::where('id',$purchaseInventoryLog->purchase_inventory_id)->where('batch_no',$purchaseInventoryLog->batch_no)->first();

                if($purchaseInventory->quantity < $purchaseInventoryLog->quantity){
                    $validator->errors()->add('quantity', 'Prvious invoice product quantity exceeds the available stock, as you have already sold this product.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        // return($request->all());
        DB::beginTransaction();
        try{

        $order->supplier_id = $request->supplier;
        $order->brand_id = $request->brand ?? null;
        $order->date = Carbon::parse($request->date)->format('Y-m-d');
        $order->sub_total = 0;
        $order->total = 0;
        $order->paid = 0;
        $order->due = 0;
        $order->notes = $request->notes;
        $order->received_by = $request->received_by;
        $order->user_id = Auth::id();
        $order->save();

        // Get existing products for order
        // $existing_Products = $order->products()->pluck('product_id')->toArray();

        // // Find the product to add and delete
        // $products_to_add = array_diff($$request->product, $existing_Products);
        // $products_to_delete = array_diff($existing_Products, $$request->product);

        $purchaseInventoryLogs = PurchaseInventoryLog::where('purchase_order_id', $order->id)->get();
        foreach ($purchaseInventoryLogs as $key=>$purchaseInventoryLog) {
            $purchaseInventory = PurchaseInventory::where('id',$purchaseInventoryLog->purchase_inventory_id)->where('batch_no',$purchaseInventoryLog->batch_no)->first();
            if($purchaseInventory){
                $purchaseInventory->decrement('quantity',$purchaseInventoryLog->quantity);
            }
        }

        // delete previous data
        PurchaseOrderProduct::where('purchase_order_id', $order->id)->delete();

        PurchaseInventoryLog::where('purchase_order_id', $order->id)->delete();

        $counter = 0;
        $sub_total = 0;

        foreach ($request->product as $reqProduct) {
            $product = Product::find($reqProduct);
            $purchaseOrderProduct = PurchaseOrderProduct::where('purchase_order_id', $order->id)
            ->whereNotIn('product_id',$request->product)
            ->first();
            // dd($product);
            //dd($purchaseOrderProduct);
            if (empty($purchaseOrderProduct)) {
                $purchaseOrderProduct = new PurchaseOrderProduct();
                $purchaseOrderProduct->sister_concern_id = 1;
                $purchaseOrderProduct->purchase_order_id = $order->id;
                $purchaseOrderProduct->product_id = $product->id;
                $purchaseOrderProduct->name = $product->name;
                $purchaseOrderProduct->type = 2;
                $purchaseOrderProduct->serial_no = $product->code;
                $purchaseOrderProduct->quantity = $request->quantity[$counter];
                $purchaseOrderProduct->unit_price = $request->unit_price[$counter];
                $purchaseOrderProduct->selling_price =$request->selling_price[$counter];
                $purchaseOrderProduct->total = $request->quantity[$counter] * $request->unit_price[$counter];
                $purchaseOrderProduct->user_id = Auth::id();
                $purchaseOrderProduct->save();

                // Inventory Log
                $inventoryLog = new PurchaseInventoryLog();
                $inventoryLog->sister_concern_id = 1;
                $inventoryLog->product_id = $product->id;
                $inventoryLog->type = 1;
                $inventoryLog->date = Carbon::parse($request->date)->format('Y-m-d');
                $inventoryLog->quantity = $request->quantity[$counter];
                $inventoryLog->unit_price = $request->unit_price[$counter];
                $inventoryLog->selling_price = $request->selling_price[$counter];
                $inventoryLog->supplier_id = $request->supplier;
                $inventoryLog->purchase_order_id = $order->id;
                $inventoryLog->user_id = Auth::id();
                $inventoryLog->save();

                // Inventory
                $inventory =  PurchaseInventory::where('sister_concern_id', 1)
                ->where('product_id', $product->id)
                ->where('unit_price',$request->unit_price[$counter])
                // ->where('expiry_date',Carbon::parse($request->expiry_date[$counter])->format('Y-m-d'))
                ->first();

                $newCode = null;
                if (!$inventory) {

                    $lastPurchaseInventory = PurchaseInventory::where('product_id',$product->id)->where('sister_concern_id',1)->latest('id')->first();
                    // Generate the next code
                    //  $nextCodeNumber = !empty($lastPurchaseInventory) ? intval($lastPurchaseInventory->batch_no) + 1 : 1;
                     $nextCodeNumber = $lastPurchaseInventory ? intval($lastPurchaseInventory->batch_no) + 1 : 1;

                    // Form at the new code as 'p-000001'
                    if($request->sister_concern_id == 1){
                        $newCode = $nextCodeNumber;
                    }else{
                        $newCode = $nextCodeNumber;
                    }
                    $inventory = new PurchaseInventory();

                }
                $batch_no = $newCode ?? $inventory->batch_no;
                //dd($batch_no);
                $inventoryLog->batch_no = $batch_no;
                $inventoryLog->save();

                $inventory->batch_no = $batch_no;
                $inventory->sister_concern_id = 1;
                $inventory->product_id = $product->id;
                $inventory->serial_no = $product->code;
                $inventory->quantity = $product->inventory_qty($product->id, 1, $batch_no);
                $inventory->unit_price = $request->unit_price[$counter];
                $inventory->selling_price = $request->selling_price[$counter];
                $inventory->save();

                $inventoryLog->purchase_inventory_id = $inventory->id;
                $inventoryLog->save();

                $purchaseOrderProduct->batch_no = $inventory->batch_no;
                $purchaseOrderProduct->save();

                $sub_total +=  $request->quantity[$counter] * $request->unit_price[$counter];

            }
            $counter++;
        }

        $order->sub_total = $sub_total;
        $total = $sub_total - $request->discount;
        $order->total = $total;
        $order->due = $total;
        $order->save();

        //Logs
        $orderLog = new OrderLog();
        $orderLog->order_type = 'PURCHASE';
        $orderLog->order_id  = $order->id;
        $orderLog->created_by  = Auth::id();
        $orderLog->status  = 'PURCHASE UPDATE';
        $orderLog->save();

        //Increment old payment and delete payments
        $paymentAll = PurchasePayment::where('purchase_order_id',$order->id)->get();
        // return($paymentAll);
        foreach ($paymentAll as $payment) {
            if ($payment->transaction_method == 1){
                Cash::first()->increment('amount', $payment->amount);
            }else if($payment->transaction_method == 3) {
                MobileBanking::find($payment->mobile_banking_id)->increment('amount', $payment->amount);
            }else{
                BankAccount::find($payment->bank_account_id)->increment('balance', $payment->amount);
            }
            //TransactionLog delete
            TransactionLog::where('purchase_payment_id',$payment->id)->delete();
        }
        //delete all old payment
        PurchasePayment::where('purchase_order_id',$order->id)->delete();

        // Sales Payment
        if ($request->paid > 0) {
            if ($request->payment_type == 1 || $request->payment_type == 3) {
                $payment = new PurchasePayment();
                $payment->sister_concern_id = 1;
                $payment->supplier_id = $request->supplier;
                $payment->purchase_order_id = $order->id;
                $payment->transaction_method = $request->payment_type;
                if( $request->payment_type == 3){
                    $payment->mobile_banking_id = $request->mobile_banking;
                }
                $payment->amount = $request->paid;
                $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                $payment->note = $request->notes;
                $payment->user_id = Auth::id();
                $payment->save();

                if ($request->payment_type == 1)
                    Cash::first()->decrement('amount', $request->paid);
                else
                    MobileBanking::find($request->mobile_banking)->decrement('amount', $request->paid);

                $log = new TransactionLog();
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->sister_concern_id = 1;
                $log->supplier_id = $order->supplier_id;
                $log->particular = 'Payment for '.$order->supplier->name.' for '.$order->order_no;
                $log->transaction_type = 3;
                $log->transaction_method = $request->payment_type;
                $log->account_head_type_id = 1;
                $log->account_head_sub_type_id = 1;
                $log->amount = $request->paid;
                $log->note = $request->notes;
                if( $request->payment_type == 3){
                    $log->mobile_banking_id = $request->mobile_banking;
                }
                $log->purchase_payment_id = $payment->id;
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

                $payment = new PurchasePayment();
                $payment->sister_concern_id = 1;
                $payment->supplier_id = $order->supplier_id;
                $payment->purchase_order_id = $order->id;
                $payment->transaction_method = 2;
                $payment->bank_id = $bankAccount->bank_id;
                $payment->branch_id = $bankAccount->branch_id;
                $payment->bank_account_id = $request->account;
                $payment->cheque_no = $request->cheque_no;
                $payment->cheque_image = $image;
                $payment->amount = $request->paid;
                $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                $payment->notes = $request->notes;
                $payment->user_id = Auth::id();
                $payment->save();

                BankAccount::find($request->account)->decrement('balance', $request->amount);

                $log = new TransactionLog();
                $log->sister_concern_id = 1;
                $log->supplier_id = $order->supplier_id;
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->particular = 'Paid to '.$order->supplier->name.' for '.$order->order_no;
                $log->transaction_type = 3;
                $log->transaction_method = 2;
                $log->account_head_type_id = 1;
                $log->account_head_sub_type_id = 1;
                $log->bank_id = $bankAccount->bank_id;
                $log->branch_id = $bankAccount->branch_id;
                $log->bank_account_id = $request->account;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_image = $image;
                $log->amount = $request->amount;
                $log->note = $request->notes;
                $log->purchase_payment_id = $payment->id;
                $log->user_id = Auth::id();
                $log->save();
            }
            $order->increment('paid', $request->paid);
            $order->decrement('due', $request->paid);
        }

        DB::commit();

        return redirect()->route('purchase_receipt.details', ['order' => $order->id]);

        }catch(Exception $exception){
            DB::rollBack();
            dd($exception);
            return redirect()->back()->with('error',$exception->getMessage())->withInput();
        }
    }

    public function checkDeleteStatus(Request $request) {
        $inventory = PurchaseInventory::where('serial_no', $request->serial)->first();

        if ($inventory) {
            if ($inventory->quantity > 0) {
                return response()->json(['success' => true, 'message' => 'This product not sold.']);
            } else {
                return response()->json(['success' => false, 'message' => 'This product already sold.']);
            }
        }

        return response()->json(['success' => true, 'message' => 'Serial not found.']);
    }

    public function purchaseProductJson(Request $request) {
        if (!$request->searchTerm) {
            $products = Product::where('status', 1)->orderBy('name')->limit(10)->get();
        } else {
            $products = Product::where('status', 1)->where('name', 'like', '%'.$request->searchTerm.'%')->orderBy('name')->limit(10)->get();
        }

        $data = array();

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->id,
                'text' => $product->name
            ];
        }

        echo json_encode($data);
    }

    public function supplierPayments(Request $request, Supplier $supplier){

        if ($request->ajax()) {
            $query = PurchasePayment::with(['supplier','user']);
            if ($supplier) {
                $query->where('supplier_id', $supplier->id);
            }
            if (Auth::user()->role_id != 1) {
                $query->where('sister_concern_id', Auth::user()->sister_concern_id);
                $query->where('user_id', Auth::user()->id);
            }
            return DataTables::eloquent($query)
                ->addColumn('supplier', function (PurchasePayment $payment) {
                    return $payment->supplier->name ?? '';
                })
                ->addColumn('user', function (PurchasePayment $payment) {
                    return $payment->user->name ?? '';
                })
                ->addColumn('action', function (PurchasePayment $payment) {
                    // return '<a href="'.route('purchase_receipt.details', ['order' => $order->id]).'" class="btn btn-primary btn-sm">View</a> <a href="'.route('purchase_receipt.qr_code', ['order' => $order->id]).'" class="btn btn-success btn-sm">QR Code</a> <a href="'.route('purchase_receipt.edit', ['order' => $order->id]).'" class="btn btn-info btn-sm">Edit</a>';
                    $btn = '<a href="' . route('purchase_receipt.payment_details', ['payment' => $payment->id]) . '" class="btn btn-primary btn-sm"> Invoice </a> ';

                    return $btn;
                })
                ->editColumn('date', function (PurchasePayment $payment) {
                    return Carbon::parse($payment->date)->format('d-M-Y');
                })
                ->editColumn('amount', function (PurchasePayment $payment) {
                    return '৳' . number_format($payment->amount, 2);
                })
                ->orderColumn('date', function ($query, $payment) {
                    $query->orderBy('date', $payment)->orderBy('created_at', 'desc');
                })
                ->rawColumns(['action','amount'])
                ->toJson();
        }
        return view('purchase.supplier_payment.supplier_payments', compact('supplier'));
    }

    public function purchaseReceipt() {
        // $inventories = PurchaseInventory::where('sister_concern_id',1)->get();
        // //dd($inventories);
        // foreach($inventories as $inventory){
        //     $inventoryLog = PurchaseInventoryLog::where('type',1)
        //             ->where('product_id',$inventory->product_id)
        //             ->where('batch_no',$inventory->batch_no)
        //             ->orderBy('id','desc')
        //             ->first();
        //     $inventory->expiry_date = $inventoryLog->expiry_date;
        //     $inventory->save();
        // }
        // dd('done');
        return view('purchase.receipt.all');
    }

    public function purchaseReceiptDatatable(Request $request)
    {
        $query = PurchaseOrder::with(['supplier','user','purchaseOrder']);
        $query->orderBy('id','desc');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(PurchaseOrder $order) {
                $btn = '<a href="' . route('purchase_receipt.details', ['order' => $order->id]) . '" class="btn btn-info bg-gradient-info btn-sm btn-edit mr-1"><i class="fa fa-eye"></i></a>';
                if (Auth::user()->can('purchase_receipt_edit') && !empty($order->purchaseOrder)) {
                    $btn .= '<a href="' .  route('purchase_receipt.edit', ['order' => $order->id]) . '" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a> ';
                }
                if (Auth::user()->can('purchase_receipt_delete') && !empty($order->purchaseOrder)) {
                    $btn .= '<a href="#" data-id="'. $order->id .'" class="btn btn-danger bg-gradient-danger btn-sm btn-delete"><i class="fa fa-trash-alt"></i></a> ';
                }
                $btn .= '<a target="_blank" title="With Price" href="' . route('purchase_receipt.bar_code', ['order' => $order->id,'with_price'=>1]) . '" class="btn btn-success btn-sm"><i class="fas fa-barcode"></i></a> ';
                // $btn .= '<a target="_blank" title="Without Price" href="' . route('purchase_receipt.bar_code', ['order' => $order->id,'with_price'=>0]) . '" class="btn btn-success btn-sm"><i class="fas fa-barcode"></i></a> ';
                $btn .= '<a target="_blank" title="Logs" href="' . route('purchase_logs', ['orderId' => $order->id]) . '" class="btn btn-success btn-sm">Logs</a> ';
                return $btn;
            })
            ->addColumn('supplier', function(PurchaseOrder $order) {
                return $order->supplier->name??'';
            })
            ->addColumn('user', function(PurchaseOrder $order) {
                return $order->user->name ??'' ;
            })
            ->addColumn('department', function(PurchaseOrder $order) {
                return $order->sisterConcern->name??'';
            })
            ->editColumn('date', function(PurchaseOrder $order) {
                return Carbon::parse($order->date)->format('d-M-Y');
            })
            ->editColumn('total', function(PurchaseOrder $order) {
                return '৳'.number_format($order->total, 2);
            })
            ->editColumn('paid', function(PurchaseOrder $order) {
                return '৳'.number_format($order->paid, 2);
            })
            ->editColumn('due', function(PurchaseOrder $order) {
                return '৳'.number_format($order->due, 2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }
    public function purchaseReceiptDatatable1() {
        $query = PurchaseOrder::with(['supplier','department','user']);
        if (Auth::user()->role_id != 1) {
            $query->where('sister_concern_id', Auth::user()->sister_concern_id);
            $query->where('user_id', Auth::user()->id);
        }
        return DataTables::eloquent($query)
            ->addColumn('supplier', function(PurchaseOrder $order) {
                return $order->supplier->name??'';
            })
            ->addColumn('user', function(PurchaseOrder $order) {
                return $order->user->name??'';
            })
            ->addColumn('department', function(PurchaseOrder $order) {
                return $order->department->name??'';
            })
            ->addColumn('action', function(PurchaseOrder $order) {
                // return '<a href="'.route('purchase_receipt.details', ['order' => $order->id]).'" class="btn btn-primary btn-sm">View</a> <a href="'.route('purchase_receipt.qr_code', ['order' => $order->id]).'" class="btn btn-success btn-sm">QR Code</a> <a href="'.route('purchase_receipt.edit', ['order' => $order->id]).'" class="btn btn-info btn-sm">Edit</a>';
                $btn = '<a href="' . route('purchase_receipt.details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm">View</a> ';
                $btn .= '<a href="' . route('purchase_receipt.bar_code', ['order' => $order->id]) . '" class="btn btn-success btn-sm">Bar Code</a> ';
                if (Auth::user()->can('purchase_receipt_edit')) {
                    $btn .= '<a href="' . route('purchase_receipt.edit', ['order' => $order->id]) . '" class="btn btn-info btn-sm">Edit</a> ';
                }

                return $btn;
            })
            ->editColumn('date', function(PurchaseOrder $order) {
                return $order->date->format('d-M-Y');
            })
            ->editColumn('total', function(PurchaseOrder $order) {
                return '৳'.number_format($order->total, 2);
            })
            ->editColumn('paid', function(PurchaseOrder $order) {
                return '৳'.number_format($order->paid, 2);
            })
            ->editColumn('due', function(PurchaseOrder $order) {
                return '৳'.number_format($order->due, 2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function purchaseInventoryDatatable(Request $request) {

        $sort = 'asc';
        $query = PurchaseInventory::with(['product'=> function ($sq) use ($sort) {
            $sq->orderBy('name', $sort);
        },'sisterConcern','product.brand','product.category','product.subcategory']);

        $query->where('sister_concern_id', 1);
        if($request->category != ''){
            $query->whereHas('product.category',function($q) use ($request){
                $q->where('id', $request->category);
            });
        }
        if($request->sub_category != ''){
            $query->whereHas('product.subcategory',function($q) use ($request){
                $q->where('id', $request->sub_category);
            });
        }
        if($request->brand != ''){
            $query->whereHas('product.brand',function($q) use ($request){
                $q->where('id', $request->brand);
            });
        }
        if($request->stock != ''){
            $query->where('quantity','>',0);
        }
        if(request('productId') != ''){
            $query->where('product_id',request('productId'));
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->filter(function ($query) {
                if (request()->has('search') && $search = request('search')['value']) {
                    $query->whereHas('product', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
                }
            })
            ->addColumn('product', function(PurchaseInventory $inventory) {
                return $inventory->product->name??'';
            })
            ->editColumn('product_image', function (PurchaseInventory $inventory) {
                $preview_html = '';
                $preview_html .= '<div class="zoom-gallery">';
                $preview_html .= '<a class="image_preview" title="'. $inventory->product->name .'" data-source="'. asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp') .'" href="'. asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp') .'"><img height="50px" src="'.asset(file_exists($inventory->product->image) ? $inventory->product->image : 'img/no-image.webp').'"></a>';
                if($inventory->product->images){
                    foreach($inventory->product->images as $image){
                        $preview_html .= '<a class="image_preview" title="'. $inventory->product->name .'" data-source="'. asset(file_exists($image->path) ? $image->path : 'img/no-image.webp') .'" href="'. asset(file_exists($image->path) ? $image->path : 'img/no-image.webp') .'"><img height="0px" src="'.asset(file_exists($image->path) ? $image->path : 'img/no-image.webp').'"></a>';
                    }
                }
                $preview_html .= '</div>';
                return $preview_html;
            })
            ->addColumn('product_brand', function(PurchaseInventory $inventory) {
                return $inventory->product->brand->name??'';
            })
            ->addColumn('product_code', function(PurchaseInventory $inventory) {
                return $inventory->product->code??'';
            })
            ->addColumn('product_category', function(PurchaseInventory $inventory) {
                return $inventory->product->category->name??'';
            })
            ->addColumn('action', function(PurchaseInventory $inventory) {
                $btn = '<a href="'.route('purchase_inventory.details', ['product' => $inventory->product_id, 'sisterConcern' => $inventory->sisterConcern->id??0,'batch'=>$inventory->batch_no]).'" class="btn btn-primary btn-sm">Details</a>&nbsp;';
                $btn .= '<a role="button" title="Damage Out" data-id="'. $inventory->id .'" data-product="'. $inventory->product->name .'" class="btn btn-primary btn-sm btn-damage">Damage Out</a>&nbsp;';
                $btn .= '<a target="_blank" title="With Price" href="'.route('inventory.bar_code', ['purchaseInventory' => $inventory->id,'with_price'=>1]).'" class="btn btn-primary btn-sm"><i class="fas fa-barcode"></i></a>&nbsp;';
                // $btn .= '<a target="_blank" title="Without Price" href="'.route('inventory.bar_code', ['purchaseInventory' => $inventory->id,'with_price'=>0]).'" class="btn btn-primary btn-sm"><i class="fas fa-barcode"></i></a>';
                $btn .= '<a role="button" title="Update Selling Price" data-inventory_id="'.$inventory->id.'" data-product_id="'.$inventory->product->id.'" data-product_name="'.$inventory->product->name.'" data-selling_price="'.$inventory->selling_price.'" class="btn btn-primary btn-sm btn-price">Update  Price</a>';
                return $btn;

            })
            ->editColumn('quantity', function(PurchaseInventory $inventory) {
                if ($inventory->quantity > $inventory->product->alert_quantity) {
                    return number_format($inventory->quantity, 0).$inventory->product->unit->name ?? '';
                }else {
                    return '<span class="text-danger">'.number_format($inventory->quantity, 0).'</span>';
                }

            })
            ->addColumn('unit_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->unit_price, 2);
            })
            ->addColumn('selling_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->selling_price, 2);
            })
            ->addColumn('total_unit_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->quantity * $inventory->unit_price, 2);
            })
            ->addColumn('total_selling_price', function (PurchaseInventory $inventory) {
                return '৳'.number_format($inventory->quantity * $inventory->selling_price, 2);
            })
            ->addColumn('profit_margin', function (PurchaseInventory $inventory) {
                return '৳'.number_format(($inventory->quantity * $inventory->selling_price)-($inventory->quantity * $inventory->unit_price), 2);
            })
            ->editColumn('batch_no', function (PurchaseInventory $inventory) {
                return 'Batch-'.$inventory->batch_no;
            })
            ->rawColumns(['action', 'quantity', 'total_unit_price','batch_no','product_image'])
            ->toJson();
    }


    public function purchaseInventoryDetailsDatatable() {
        $query = PurchaseInventoryLog::where('product_id', request('product_id'))
            ->where('sister_concern_id', request('sister_concern_id'))
            ->where('batch_no', request('batch_no'))
            ->with('product', 'supplier', 'order');

        return DataTables::eloquent($query)
            ->editColumn('date', function(PurchaseInventoryLog $log) {
                return Carbon::parse($log->date)->format('d-M-Y');
            })
            ->editColumn('type', function(PurchaseInventoryLog $log) {
                if ($log->type == 1)
                    return '<span class="label label-success">In</span>';
                elseif ($log->type == 2)
                    return '<span class="label label-danger">Out</span>';
                elseif ($log->type == 3)
                    return '<span class="label label-success">Add</span>';
                else
                    return '<span class="label label-danger">Return</span>';
            })
            ->editColumn('quantity_in', function(PurchaseInventoryLog $log) {
                if ($log->type == 1){
                    return number_format($log->quantity, 2);
                }
            })
            ->editColumn('quantity_out', function(PurchaseInventoryLog $log) {
                if ($log->type == 2){
                    return number_format($log->quantity, 2);
                }
            })
            ->editColumn('unit_price', function(PurchaseInventoryLog $log) {
                if ($log->unit_price)
                    return '৳'.number_format($log->unit_price, 2);
                else
                    return '';
            })
            ->editColumn('selling_price', function(PurchaseInventoryLog $log) {
                if ($log->selling_price)
                    return '৳'.number_format($log->selling_price, 2);
                else
                    return '';
            })
            ->editColumn('supplier', function(PurchaseInventoryLog $log) {
                if ($log->supplier)
                    return $log->supplier->name;
                else
                    return '';
            })
            ->editColumn('order', function(PurchaseInventoryLog $log) {
                if ($log->order){
                    return '<a href="'.route('sale_receipt.details', ['order' => $log->order->id]).'">View Invoice</a>';
                }
                elseif($log->purchaseOrder){
                    return '<a href="'.route('purchase_receipt.details', ['order' => $log->purchaseOrder->id]).'">View Invoice</a>';
                }
                else
                    return '';
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['type', 'order'])
            ->filter(function ($query) {
                if (request()->has('date') && request('date') != '') {
                    $dates = explode(' - ', request('date'));
                    if (count($dates) == 2) {
                        $query->where('date', '>=', $dates[0]);
                        $query->where('date', '<=', $dates[1]);
                    }
                }

                if (request()->has('type') && request('type') != '') {
                    $query->where('type', request('type'));
                }
            })
            ->toJson();
    }
      //Excel Import
      public function purchaseImport(Request $request)
      {
          $request->validate([
              'excel_file' => 'required|mimes:xls,xlsx'
          ]);
          try {
              $file = $request->file('excel_file');
              $fileName = substr($file->getClientOriginalName(), 0, 5);
              Excel::import(new PurchasesImport($fileName), $file);

              return redirect()->route('purchase_receipt.all')->with('message', 'Import Successful');

          } catch (\Exception $e) {
              \Log::error($e->getMessage());
              return redirect()->route('purchase_receipt.all')->with('error', 'Import failed: ' . $e->getMessage());
          }
      }
    
    public function purchaseDelete(Request $request){
        DB::beginTransaction();
        try{
            // dd($request->all());
            $order = PurchaseOrder::find($request->orderId);
            $purchaseInventoryLogs = PurchaseInventoryLog::where('purchase_order_id', $order->id)->get();
            foreach ($purchaseInventoryLogs as $key =>$purchaseInventoryLog) {
                $purchaseInventory = PurchaseInventory::where('id',$purchaseInventoryLog->purchase_inventory_id)->where('batch_no',$purchaseInventoryLog->batch_no)->first();

                if($purchaseInventory->quantity < $purchaseInventoryLog->quantity){
                    // dd('Hasan');
                    return response()->json(['success'=>false,'error'=>'Prvious invoice product quantity exceeds the available stock, as you have already sold this product.']);
                }
            }

            PurchaseInventoryLog::where('purchase_order_id', $order->id)->delete();
            
            $purchaseOrderProducts = PurchaseOrderProduct::where('purchase_order_id', $order->id)->get();
            
            foreach ($purchaseOrderProducts as $key =>$purchaseOrderProduct) {
                $product = Product::find($purchaseOrderProduct->product_id);
                $inventory = PurchaseInventory::where('product_id',$purchaseOrderProduct->product_id)->where('batch_no',$purchaseOrderProduct->batch_no)->first();

                $inventory->quantity = $product->inventory_qty($product->id, $purchaseOrderProduct->sister_concern_id,$purchaseOrderProduct->batch_no);
                $inventory->save();
            }
            // dd('Hasan');
            PurchaseOrderProduct::where('purchase_order_id', $order->id)->delete();

            //Increment old payment and delete payments
            $paymentAll = PurchasePayment::where('purchase_order_id',$order->id)->get();
            // return($paymentAll);
            foreach ($paymentAll as $payment) {
                if ($payment->transaction_method == 1){
                    Cash::first()->increment('amount', $payment->amount);
                }else if($payment->transaction_method == 3) {
                    MobileBanking::find($payment->mobile_banking_id)->increment('amount', $payment->amount);
                }else{
                    BankAccount::find($payment->bank_account_id)->increment('balance', $payment->amount);
                }
                //TransactionLog delete
                TransactionLog::where('purchase_payment_id',$payment->id)->delete();
            }
            //delete all old payment
            PurchasePayment::where('purchase_order_id',$order->id)->delete();

            //Logs
            $orderLog = new OrderLog();
            $orderLog->order_type = 'PURCHASE';
            $orderLog->order_id  = $order->id;
            $orderLog->created_by  = Auth::id();
            $orderLog->status  = 'PURCHASE DELETE';
            $orderLog->save();

            $order->delete();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'Successfully']);
        }catch(Exception $exception){
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$exception->getMessage()]);
        }
    }
    public function purchaseLogs($orderId){
        $order = PurchaseOrder::withTrashed()->find($orderId);
        $logs = OrderLog::where('order_type','PURCHASE')->where('order_id',$order->id)->get();
        return view('purchase.receipt.logs',compact('logs'));
    }
    public function damageAdd(Request $request){

        $rules = [
            'inventory_id' => 'required',
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.1',
            'note' => 'nullable|string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $checkInventory = PurchaseInventory::where('id',$request->inventory_id)->first();
        if (!$checkInventory){
            return response()->json(['success' => false, 'message' => 'Inventory Not Exists!']);
        }
        if($checkInventory && $checkInventory->quantity < $request->quantity){
            return response()->json(['success' => false, 'message' => 'Insufficient Inventory!']);
        }
        DB::beginTransaction();
        try {
            $inventory = PurchaseInventory::where('id',$request->inventory_id)->first();
            $product = Product::find($inventory->product_id);

            $inventoryLog = new PurchaseInventoryLog();
            $inventoryLog->sister_concern_id = $inventory->sister_concern_id;
            $inventoryLog->purchase_inventory_id = $inventory->id;
            $inventoryLog->product_id = $product->id;
            //$inventoryLog->sales_order_id = $order->id;
            $inventoryLog->batch_no = $inventory->batch_no;
            $inventoryLog->type = 2;
            $inventoryLog->date = date('Y-m-d');
            $inventoryLog->quantity = $request->quantity;
            $inventoryLog->unit_price = $inventory->unit_price;
            $inventoryLog->selling_price = $inventory->unit_price;
            $inventoryLog->is_damage = 1;
            $inventoryLog->user_id = Auth::id();
            $inventoryLog->note = $request->note;
            $inventoryLog->save();


            $inventory->quantity = $product->inventory_qty($product->id, $inventory->sister_concern_id, $inventory->batch_no);
            $inventory->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Damage Product added successfully!']);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
}
