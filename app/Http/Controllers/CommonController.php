<?php

namespace App\Http\Controllers;

use App\Models\AccountHeadSubType;
use App\Models\AccountHeadType;
use App\Models\AcupunctureSaleService;
use App\Models\BankAccount;
use App\Models\PackSize;
use App\Models\Branch;
use App\Models\Cash;
use App\Models\CashLog;
use App\Models\Customer;
use App\Models\PurchaseInventory;
use App\Models\PurchaseInventoryLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\PurchasePayment;
use App\Models\Product;
use App\Models\ProductSalesOrder;
use App\Models\SalePayment;
use App\Models\SalesOrder;
use App\Models\TransactionLog;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
class CommonController extends Controller
{
    public function cash(Request $request)
    {
        $cash = Cash::first();
        $cashLogs = CashLog::get();
        return view('cash.add', compact('cash','cashLogs'));
    }
    public function cashPost(Request $request)
    {
        $this->validate($request, [
            'opening_balance' => 'required'
        ]);
        $data = $request->all();
        $cash = Cash::first();
        if ($cash) {
            $old_amount = $request->opening_balance - $cash->opening_balance;
            $cash->opening_balance = $request->opening_balance;
            $cash->amount = $cash->amount + $old_amount;
            $cash->save();
        }else {
            $data['amount'] = $request->opening_balance;
            Cash::create($data);
        }
        return redirect()->back()->with('message','Cash added successfully done.');
    }

    public function CashInOutPost(Request $request) {

        $rules = [
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $cash = Cash::first();

        if($request->type == 'opening'){
            $cash->amount = $cash->amount+$request->amount;
            $cash->opening_balance = $cash->opening_balance+$request->amount;
            $cash->save();

            $cashLog = new CashLog();
            $cashLog->type = 1; // 1 for opening
            $cashLog->amount = $request->amount;
            $cashLog->date = Carbon::now();
            $cashLog->note = $request->note;
            $cashLog->user_id = auth()->user()->id;
            $cashLog->save();

        }elseif($request->type == 'investment'){
            $cash->amount = $cash->amount+$request->amount;
            $cash->investment_amt =  $cash->investment_amt+$request->amount;
            $cash->save();

            $cashLog = new CashLog();
            $cashLog->type = 2; // 1 for Investment
            $cashLog->amount = $request->amount;
            $cashLog->date = Carbon::now();
            $cashLog->note = $request->note;
            $cashLog->user_id = auth()->user()->id;
            $cashLog->save();
        }elseif($request->type == 'withdraw'){
            if ($request->amount > $cash->amount){
                return response()->json(['success' => false, 'message' =>'Insufficient Cash Balance!']);
            }
            $cash->amount = $cash->amount - $request->amount;
            $cash->withdraw_amt = $cash->withdraw_amt+$request->amount;
            $cash->save();

            $cashLog = new CashLog();
            $cashLog->type = 3; // 3 for withdraw
            $cashLog->amount = $request->amount;
            $cashLog->date = Carbon::now();
            $cashLog->note = $request->note;
            $cashLog->user_id = auth()->user()->id;
            $cashLog->save();
        }


        return response()->json([
            'success' => true,
            'message' => 'Save successfully.',
            'redirect_url' => url()->previous()
        ]);
    }

    public function getPatientInfo($id)
    {
        $patient = Customer::find($id);

        $currentDate = Carbon::now();

        $years = $currentDate->diffInYears($patient->date_of_birth); // Calculate years
        $months = $currentDate->diffInMonths($patient->date_of_birth) % 12;


        if ($patient) {
            return response()->json([
                'sex' => $patient->sex,
                'age' => $years. ' years '.$months. ' months',
                'mobile_no' => $patient->mobile_no,
            ]);
        } else {
            return response()->json(['error' => 'Patient not found'], 404);
        }
    }



    public function getBranch(Request $request) {
        $branches = Branch::where('bank_id', $request->bankId)
            ->where('status', 1)
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($branches);
    }

    public function getBankAccount(Request $request) {
        $accounts = BankAccount::where('branch_id', $request->branchId)
            ->where('status', 1)
            ->orderBy('account_no')
            ->get()->toArray();

        return response()->json($accounts);
    }

    public function orderDetails(Request $request) {
        $order = SalesOrder::where('id', $request->orderId)->with('customer')->first()->toArray();

        return response()->json($order);
    }
    public function acupuntureOrderDetails(Request $request) {
        $order = AcupunctureSaleService::where('id', $request->orderId)->with('customer')->first()->toArray();

        return response()->json($order);
    }

    public function getAccountHeadType(Request $request) {
        $types = AccountHeadType::where('transaction_type', $request->type)
            ->where('status', 1)
            ->whereNotIn('id', [1, 2])
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($types);
    }

    public function getAccountHeadSubType(Request $request) {
        $subTypes = AccountHeadSubType::where('account_head_type_id', $request->typeId)
            ->where('status', 1)
            ->whereNotIn('id', [1, 2, 3, 4, 42, 43])
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($subTypes);
    }

    public function getSerialSuggestion(Request $request) {
        if ($request->has('term')) {
            return Product::where('code', 'like', '%'.$request->input('term').'%')->where('status',1)->get();
        }
    }

    public function vatCorrection() {
        $orders = SalesOrder::where('vat_percentage', '!=', 0)->get();

        foreach ($orders as $order) {
            $total = $order->sub_total + $order->vat - $order->discount;
            $order->total = $total;

            if ($order->due == 0) {
                $order->paid = $total;
            } else {
                $order->due = $total;
            }

            $order->save();
        }
    }

    public function inventoryAdjust(Request $request){
        $inventoryLog = PurchaseInventoryLog::where('stock_type', 1)->delete();
        $purchaseOrders = PurchaseOrderProduct::all();
        foreach ($purchaseOrders as $key => $purchaseOrder) {
            $inventoryLog = new PurchaseInventoryLog();
            $inventoryLog->department_id = $purchaseOrder->department_id;
            $inventoryLog->product_id = $purchaseOrder->product_id;
            $inventoryLog->type = 1;
            $inventoryLog->date = Carbon::parse($purchaseOrder->purchase_order->date)->format('Y-m-d');
            $inventoryLog->quantity = $purchaseOrder->quantity;
            $inventoryLog->unit_price = $purchaseOrder->unit_price;
            $inventoryLog->supplier_id = $purchaseOrder->supplier_id;
            $inventoryLog->purchase_order_id = $purchaseOrder->purchase_order_id;
            // $inventoryLog->user_id = Auth::id();
            $inventoryLog->save();

            // Inventory
            $product = Product::find($purchaseOrder->product_id);
            $inventory =  PurchaseInventory::where('department_id', $purchaseOrder->department_id)
                ->where('product_id', $purchaseOrder->product_id)
                ->first();

            if (empty($inventory)) {
                $inventory = new PurchaseInventory();
            }

            $inventory->department_id = $purchaseOrder->department_id;
            $inventory->product_id = $purchaseOrder->product_id;
            $inventory->quantity = $product->inventory_qty($product->id, $purchaseOrder->department_id);
            $inventory->serial_no = $product->code;
            $inventory->warranty = $purchaseOrder->warranty;
            $inventory->unit_price = $purchaseOrder->unit_price;
            $inventory->including_price = $purchaseOrder->including_price;
            $inventory->selling_price = $purchaseOrder->selling_price;
            $inventory->save();
        }
        $salesOrders = SalesOrderProduct::all();
        foreach ($salesOrders as $key => $salesOrder) {
            $inventoryLog =  PurchaseInventoryLog::where('department_id', $salesOrder->department_id)
                ->where('product_id', $salesOrder->product_id)
                ->where('sales_order_id', $salesOrder->id)
                ->where('type', 2)
                ->first();

            if (empty($inventoryLog)) {
                $inventoryLog = new PurchaseInventoryLog();
            }

            $inventoryLog->department_id = $salesOrder->department_id;
            $inventoryLog->product_id = $salesOrder->product_id;
            $inventoryLog->type = 2;
            $inventoryLog->date = Carbon::parse($salesOrder->sales_order->date)->format('Y-m-d');
            $inventoryLog->quantity = $salesOrder->quantity;
            $inventoryLog->unit_price = $salesOrder->buy_price;
            $inventoryLog->customer_id = $salesOrder->sales_order->customer_id;
            $inventoryLog->sales_order_id = $salesOrder->sales_order_id;
            // $inventoryLog->user_id = Auth::id();
            $inventoryLog->save();

            // Inventory
            $product = Product::find($salesOrder->product_id);
            $inventory =  PurchaseInventory::where('department_id', $salesOrder->department_id)
                ->where('product_id', $salesOrder->product_id)
                ->first();

            if (empty($inventory)) {
                $inventory = new PurchaseInventory();
            }

            $inventory->department_id = $salesOrder->department_id;
            $inventory->product_id = $salesOrder->product_id;
            $inventory->quantity = $product->inventory_qty($product->id, $salesOrder->department_id);
            $inventory->serial_no = $product->code;
            // $inventory->unit_price = $salesOrder->buy_price;
            $inventory->save();
        }

        return "done";
    }
    public function getProductInventory(Request $request)
    {
        $inventory = PurchaseInventory::where('product_id', $request->id)
        ->where('sister_concern_id', $request->sister_concern_id)
        ->selectRaw('SUM(quantity) as total_quantity, AVG(selling_price) as avg_selling_price') // Aggregate functions
        ->first();

        $batches = PurchaseInventory::where('product_id', $request->id)
        ->where('sister_concern_id', $request->sister_concern_id)
        ->where('quantity', '>', 0)
        ->pluck('batch_no') // Get only the batch_no column
        ->toArray();

        return response()->json([
            'quantity' => $inventory->total_quantity ?? 0, // If no records found, default to 0
            'selling_price' => $inventory->avg_selling_price ?? 0, // Default to 0
            'batches' => $batches,
        ]);
    }
    public function getProductInventoryBatchWise(Request $request)
    {
        $inventory = PurchaseInventory::where('batch_no', $request->id)
            ->where('product_id', $request->product_id)
            ->where('sister_concern_id', $request->sister_concern_id)
            ->first();
        $product = Product::find($request->product_id);

        return response()->json([
            'quantity' => $inventory->quantity ?? 0,
            'buying_price' => $inventory->unit_price ?? 0,
            'selling_price' => $inventory->selling_price ?? 0,
            'discount_percent' => $product->discount_percent ?? 0,
            'vat_percent' => $product->vat_percent ?? 0,
        ]);
    }

    public function patientNumberSuggestion(Request $request){
        if ($request->term){
            $query = Customer::query();
            $query->where('name', 'like', '%' . $request->term . '%');
            $query->orWhere('mobile_no', 'like', '%' . $request->term . '%');
            $query->take(12);
            $customers = $query->get();
            return response()->json(['success'=>true,'customers'=>$customers]);
        }
    }
    public function ckeditorUpload(Request $request){
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            
            // File name unique kora
            $fileName = $fileName . '_' . time() . '.' . $extension;
        
            // Public folder-e move kora
            $request->file('upload')->move(public_path('media'), $fileName);

            $url = asset('media/' . $fileName);

            return response()->json([
                'fileName' => $fileName,
                'uploaded' => 1,
                'url' => $url
            ]);
        }
    }
}
