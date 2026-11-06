<?php

namespace App\Http\Controllers;

use App\Models\AccountHeadSubType;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Department;
use App\Models\Customer;
use App\Models\HomeoSaleService;
use App\Models\HomeoSaleServicePayment;
use App\Models\MobileBanking;
use App\Models\PathologySaleService;
use App\Models\PurchaseInventory;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\SalePayment;
use App\Models\SalesOrder;
use App\Models\Supplier;
use App\Models\AccountHeadType;
use App\Models\AcupunctureSaleService;
use App\Models\AcupunctureSaleServicePayment;
use App\Models\Appointment;
use App\Models\Bank;
use App\Models\Doctor;
use App\Models\PathologyReferar;
use App\Models\PathologySaleServicePayment;
use App\Models\PathologyServiceDetails;
use App\Models\PathologyTestReport;
use App\Models\PurchaseInventoryLog;
use App\Models\ProductSalesOrder;
use App\Models\PurchasePayment;
use App\Models\SalesOrderProduct;
use App\Models\SisterConcern;
use App\Models\TherapySaleService;
use App\Models\TherapySaleServicePayment;
use App\Models\TransactionLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use SakibRahaman\DecimalToWords\DecimalToWords;
use Yajra\DataTables\Facades\DataTables;
use Rakibhstu\Banglanumber\NumberToBangla;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ReportController extends Controller
{
    public function purchase(Request $request) {
        $suppliers = Supplier::where('id','!=',0)->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $sisterConcerns = SisterConcern::where('status',1)->orderBy('name')->get();
        $appends = [];
        $query = PurchaseOrder::query();

        if ($request->start_date != '' && $request->end_date != '') {
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d');

            $query->whereBetween('date', [$start_date, $end_date]);
            $appends['date'] = $request->date;
        }

        if ($request->sister_concern && $request->sister_concern != '') {
            $query->where('sister_concern_id', $request->sister_concern);
            $appends['sister_concern'] = $request->sister_concern;
        }
        if ($request->type && $request->type != '') {
            $supplierIds = Supplier::where('type',$request->type)->where('status',1)->pluck('id');
            $query->whereIn('supplier_id', $supplierIds);
            // $appends['supplier'] = $request->supplier;
            $suppliers = Supplier::where('id','!=',0)->where('type',$request->type)->orderBy('name')->get();
        }
        if ($request->supplier && $request->supplier != '') {
            $query->where('supplier_id', $request->supplier);
            $appends['supplier'] = $request->supplier;
        }

        if ($request->purchaseId && $request->purchaseId != '') {
            $query->where('order_no', $request->purchaseId);
            $appends['purchaseId'] = $request->purchaseId;
        }

        if ($request->product && $request->product != '') {
            $query->whereHas('products', function($q) use ($request) {
                $q->where('product_id', '=', $request->product);
            });

            $appends['product'] = $request->product;
        }

        $query->orderBy('date', 'asc')->orderBy('created_at', 'asc');

        $data = [
            'total' => $query->sum('total'),
            'due' => $query->sum('due'),
            'paid' => $query->sum('paid'),
        ];
        $orders = $query->get();



        return view('report.purchase', compact('orders', 'suppliers',
            'products', 'appends','sisterConcerns'))->with($data);
    }
    public function productWiseSummary(Request $request){
        // dd($request->all());
        $selected_products  = [];
        $products = Product::where('status',1)->get();
        $query = Product::where('status',1);
        
        if($request->start_date != '' && $request->end_date != ''){
            if($request->product != ''){
                $query->where('id',$request->product);
            }
            $selected_products = $query->get();
        }
        return view('report.product_wise_summary',compact('selected_products','products'));
    }
    public function stockReport(Request $request){
        // dd($request->all());
        $selected_products  = [];
        $products = Product::where('status',1)->get();
        $query = PurchaseInventory::with('product');
        
        if($request->product != ''){
            $query->where('product_id',$request->product);
        }      
        $inventories = $query->get();
        return view('report.stock_report',compact('inventories','products'));
    }
    public function stockStatement(Request $request) {
        $products = Product::where('status', 1)->get();
        $logs = [];
        if($request->product != '' && $request->batch != ''){
            $query = PurchaseInventoryLog::with('product');
            
            // Apply product filter if selected
            if ($request->product != '') {
                $query->where('product_id', $request->product);
            }
            
            // Apply date filter
            if ($request->start_date != '' && $request->end_date != '') {
                $start_date = \Carbon\Carbon::parse($request->start_date)->startOfDay();
                $end_date = \Carbon\Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('date', [$start_date,$end_date]);
            }
            $logs = $query->get();
        }
        // dd($logs);
        return view('report.stock_statement', compact('logs', 'products'));
    }
    public function productWisePurchase(Request $request){
        // dd($request->all());
        $purchase_order_products  = [];
        $products = Product::where('status',1)->get();
        if($request->start_date != '' && $request->end_date != ''){
            $startDate = \Carbon\Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate = \Carbon\Carbon::parse($request->end_date)->format('Y-m-d');

            $purchase_order_products = DB::table('purchase_order_products')
                ->join('purchase_orders','purchase_order_products.purchase_order_id','=','purchase_orders.id')
                ->where('purchase_order_products.product_id',$request->product)
                ->whereBetween('purchase_orders.date',[$startDate,$endDate])
                ->get();

            // dd($purchase_order_products);
        }
        return view('report.product_wise_purchase',compact('purchase_order_products','products'));
    }
    public function saleCostPrice(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $appends = [];
        $query = SalesOrder::query();

        if ($request->date && $request->date != '') {
            $dates = explode(' - ', $request->date);
            if (count($dates) == 2) {
                $query->whereBetween('date', [$dates[0], $dates[1]]);
                $appends['date'] = $request->date;
            }
        }

        if ($request->paid_due == 1 && $request->paid_due != '') {
            $query->where('due', 0);
            $appends['paid_due'] = $request->paid_due;
        }
        if ($request->paid_due == 2 && $request->paid_due != '') {
            $query->where('due', '>', 0);
            $appends['paid_due'] = $request->paid_due;
        }
        if ( $request->price != '') {
            $appends['price'] = $request->price;
        }

        if ($request->department != '') {
            foreach ($departments as  $department) {
                if ($department->id == $request->department) {
                    $query->where('department_id', $request->department);
                }
            }
            $appends['department'] = $request->department;
        }

        if ($request->customer && $request->customer != '') {
            $query->where('customer_id', $request->customer);
            $appends['customer'] = $request->customer;
        }

        if ($request->user && $request->user != '') {
            $query->where('user_id', $request->user);
            $appends['user'] = $request->user;
        }

        if ($request->saleId && $request->saleId != '') {
            $query->where('order_no', $request->saleId);
            $appends['saleId'] = $request->saleId;
        }

        if ($request->product && $request->product != '') {
            $query->whereHas('products', function ($q) use ($request) {
                $q->where('product_id', '=', $request->product);
            });

            $appends['product'] = $request->product;
        }

        $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        $data = [
            'total' => $query->sum('total'),
            'due' => $query->sum('due'),
            'paid' => $query->sum('paid'),
        ];

        if($request->is_request ==1){
            $orders = $query->get();
        }else{
            $orders = $query->paginate(100);
        }


        return view('report.sale_cost', compact(
            'customers',
            'products',
            'appends',
            'orders',
            'users',
            'departments'
        ));
    }

    public function sellingProductReport(Request $request)
    {
        $products = Product::orderBy('name')->get();
        // $totalCost = SalesOrderProduct::whereBetween('date', [
        //     Carbon::parse($request->start)->format('Y-m-d'),
        //     Carbon::parse($request->end)->format('Y-m-d'),
        // ])
        // ->selectRaw('SUM(buy_price * pack_quantity) as total_cost')
        // ->value('total_cost');
        // dd($totalCost);
        if ($request->start && $request->end) {
            $orderIds = SalesOrder::whereBetween('date', [
                Carbon::parse($request->start)->format('Y-m-d'),
                Carbon::parse($request->end)->format('Y-m-d'),
            ])->where('sale_type',3)->whereIn('status',[0,4])->pluck('id');
        }

        // dd($orderIds);

        $query = SalesOrderProduct::with('product')->where('sales_order_products.sister_concern_id', 1)
            ->whereNotNull('sales_order_id');
            if ($request->start && $request->end) {
                $query->whereNotIn('sales_order_id',$orderIds);
            }

        // Filter by product if provided
        if ($request->product && $request->product != '') {
            $query->where('product_id', $request->product);
        }

        // Filter by date range if provided
        if ($request->start && $request->end) {
            $query->whereBetween('sales_order_products.date', [
                Carbon::parse($request->start)->format('Y-m-d'),
                Carbon::parse($request->end)->format('Y-m-d'),
            ]);
        }

        // Calculate totals, profit, and distinguish between online/offline sales
        $query->selectRaw('
            product_id,
            SUM(quantity) as total_quantity,
            SUM(CASE WHEN sale_type = 1 THEN quantity ELSE 0 END) as offline_quantity,
            SUM(CASE WHEN sale_type = 2 OR sale_type = 3 THEN quantity ELSE 0 END) as online_quantity,
            SUM(discount_amt) as total_discount,
            SUM(buy_price * quantity) as total_cost,
            SUM(unit_price * quantity) as total_sales,
            SUM((unit_price - buy_price) * quantity) as profit,
            MAX(date) as latest_sale_date,
            AVG(buy_price) as avg_cost_price,
            AVG(discount_percentage) as discount_percentage,
            AVG(unit_price) as avg_selling_price
        ')
        ->join('products', 'sales_order_products.product_id', '=', 'products.id')
        ->orderBy('products.name', 'asc')
        ->groupBy('product_id');

        $inventories = $query->get();
        //dd($inventories);
        return view('report.selling_product_report', compact(
            'products',
            'inventories'
        ));
    }




    public function sale(Request $request) {
        $customers = Customer::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $appends = [];
        $query = SalesOrder::query();

        if ($request->date && $request->date != '') {
            $dates = explode(' - ', $request->date);
            if (count($dates) == 2) {
                $query->whereBetween('date', [$dates[0], $dates[1]]);
                $appends['date'] = $request->date;
            }
        }

        if($request->paid_due==1 && $request->paid_due!=''){
            $query->where('due', 0);
            $appends['paid_due'] = $request->paid_due;
        }
        if ($request->paid_due == 2 && $request->paid_due != '') {
            $query->where('due','>', 0);
            $appends['paid_due'] = $request->paid_due;
        }

        if ($request->customer && $request->customer != '') {
            $query->where('customer_id', $request->customer);
            $appends['customer'] = $request->customer;
        }

        if ($request->user && $request->user != '') {
            $query->where('user_id', $request->user);
            $appends['user'] = $request->user;
        }

        if ($request->saleId && $request->saleId != '') {
            $query->where('order_no', $request->saleId);
            $appends['saleId'] = $request->saleId;
        }

        if ($request->product && $request->product != '') {
            $query->whereHas('products', function($q) use ($request) {
                $q->where('product_id', '=', $request->product);
            });

            $appends['product'] = $request->product;
        }
        if ($request->sale_type && $request->sale_type != '') {
            $query->where('sale_type', $request->sale_type);
            $appends['sale_type'] = $request->sale_type;
        }
        if ($request->paid_by && $request->paid_by != '') {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('transaction_method', $request->paid_by);
            });
            $appends['paid_by'] = $request->paid_by;
        }
        $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        $data = [
            'total' => $query->sum('total'),
            'due' => $query->sum('due'),
            'paid' => $query->sum('paid'),
        ];
        if($request->is_request ==1){
            $orders = $query->get();
        }else{
            $orders = $query->paginate(100);
        }


        return view('report.sale', compact('customers', 'products',
            'appends', 'orders','users'))->with($data);
    }

    public function paymentReport(Request $request){
        //dd($request->department);
        $payments = [];
        $users = User::orderBy('name', 'asc')->get();
        
        $query = SalePayment::query();
        if($request->sale_type != ''){
            $query->where('sale_type',$request->sale_type);
        }
        if($request->start_date != '' && $request->end_date != ''){
            $query->whereBetween('date',[date('Y-m-d',strtotime($request->start_date)),date('Y-m-d',strtotime($request->end_date))]);
        }
        if($request->paid_by != ''){
            $query->where('transaction_method',$request->paid_by);
        }
        if($request->user_by != ''){
            $query->where('user_id',$request->user_by);
        }
        $payments = $query->get();

        return view('report.payment_report',compact('payments','users'));
    }
    public function saleReport(Request $request){
        //dd($request->department);
        $orders = [];
 
        $query = SalesOrder::query();
        if($request->sale_type != ''){
            $query->where('sale_type',$request->sale_type);
        }
        if($request->start_date != '' && $request->end_date != ''){
            $query->whereBetween('date',[date('Y-m-d',strtotime($request->start_date)),date('Y-m-d',strtotime($request->end_date))]);
        }
        if($request->paid_by != ''){
            $query->where('transaction_method',$request->paid_by);
        }
        if($request->type != ''){
            if($request->type == 1){
                $query->where('due','=',0);
            }elseif($request->type == 2){
                $query->where('due','>',0);
            }
        }
        $orders = $query->get();


        return view('report.sale_report',compact('orders'));
    }

    public function balanceSheet() {
        $bankAccounts = BankAccount::where('status', 1)->with('bank', 'branch')->get();
        $cash = Cash::first();
        $mobileBanking = MobileBanking::first();
        $customerTotalPaid = SalePayment::sum('amount');
        $customerTotalDue = SalesOrder::sum('due');
        $supplierTotalDue = PurchaseOrder::sum('due');
        // dd($supplierTotalDue);
        $suppliers = Supplier::all();
        $totalInventory = PurchaseInventory::select(DB::raw('SUM(`unit_price` * `quantity`) AS total'))
            ->get();

        return view('report.balance_sheet', compact('bankAccounts',
            'cash', 'mobileBanking', 'customerTotalPaid', 'customerTotalDue', 'supplierTotalDue','suppliers', 'totalInventory'));
    }

    public function profitAndLoss(Request $request) {
        $incomes = null;
        $expenses = null;
        $incomes = TransactionLog::where('transaction_type', 5)->whereBetween('date', [Carbon::parse($request->start)->format('Y-m-d'),Carbon::parse($request->end)->format('Y-m-d')])->get();
        $expenses = TransactionLog::whereIn('transaction_type', [4])->whereBetween('date', [Carbon::parse($request->start)->format('Y-m-d'),Carbon::parse($request->end)->format('Y-m-d')])->get();

        return view('report.profit_and_loss', compact('incomes', 'expenses'));
    }

    public function ledger(Request $request) {
        $incomes = null;
        $expenses = null;
        $incomes = TransactionLog::whereIn('transaction_type', [1, 5])->whereBetween('date', [Carbon::parse($request->start)->format('Y-m-d'),Carbon::parse($request->end)->format('Y-m-d')])->get();
        $expenses = TransactionLog::whereIn('transaction_type', [3, 2, 6])->whereBetween('date', [Carbon::parse($request->start)->format('Y-m-d'),Carbon::parse($request->end)->format('Y-m-d')])->get();

        return view('report.ledger', compact('incomes', 'expenses'));
    }

    public function transaction(Request $request) {
        $result = null;
        $types = AccountHeadType::whereNotIn('id', [1, 2, 3, 4, 20, 21])->get();
        $subTypes = AccountHeadSubType::whereNotIn('id', [1, 2, 3, 4, 42, 43])->get();

        if ($request->start && $request->end) {
            $query = TransactionLog::query();

            if ($request->department && $request->department != ''){
                $query->where('department_id',$request->department);
            }

            $query->select(DB::raw('sum(amount) as amount, account_head_type_id, account_head_sub_type_id, department_id'));
            $query->whereBetween('date', [Carbon::parse($request->start)->format('Y-m-d'),Carbon::parse($request->end)->format('Y-m-d')]);
            $query->whereNotIn('account_head_type_id', [0, 1, 2, 3, 4, 20, 21]);
            $query->whereNotIn('account_head_sub_type_id', [0, 1, 2, 3, 4, 42, 43]);

            if ($request->type && $request->type != ''){
                $query->where('account_head_type_id', $request->type);
            }

            if ($request->sub_type && $request->sub_type != ''){
                $query->where('account_head_sub_type_id', $request->sub_type);
            }

            $query->groupBy('account_head_sub_type_id', 'account_head_type_id','department_id');
            $query->with('accountHead');

            $result = $query->get();
        }
        return view('report.transaction', compact('result', 'types','subTypes'));
    }
    public function supplierLedger(Request $request){
        $suppliers = Supplier::select('id','name','status')->where('status',1)->get();
        $request->validate([
            'supplier' => 'nullable|numeric',
            'start_date' => 'nullable|date|required_with:end_date',
            'end_date' => 'nullable|date|required_with:end_date',
        ]);

        $allData = [];
        $supplierName = '';
        if ($request->supplier) {
            $supplierName = Supplier::find($request->supplier)->name;
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            //dd(Carbon::parse($request->start_date));
            $previousPurchaseAmount = PurchaseOrder::where('supplier_id', $request->supplier)->whereDate('date','<',$start_date)->sum('total');
            $previousPayment = PurchasePayment::where('supplier_id', $request->supplier)->whereDate('date','<',$start_date)->sum('amount');
            $previousBalance = $previousPurchaseAmount - $previousPayment;
            
            $purchases = PurchaseOrder::where('supplier_id', $request->supplier)->whereDate('date','>=',$start_date)->whereDate('date','<=',$end_date)->get();
            $payments = PurchasePayment::with('purchaseOrder')->where('supplier_id', $request->supplier)->whereDate('date','>=',$start_date)->whereDate('date','<=',$end_date)->get();
            $supplier = Supplier::find($request->supplier);

            if ($supplier->opening_balance != 0 || $previousBalance){
                $allData[] = [
                    'type' => 'Opening Balance',
                    'date' => date('Y-m-d',strtotime($start_date.'-1 day')),
                    'invoice' => $supplier->opening_balance+$previousBalance,
                    'payment' => 0,
                    'return' => 0,
                    'particular' => 'Opening Balance',
                    'created_at' => Carbon::parse($start_date)->subDays(1),
                ];
            }
            foreach ($purchases as $purchase) {
                $allData[] = [
                    'type' => 'Purchase',
                    'date' => $purchase->date,
                    'invoice' => $purchase->total,
                    'payment' => 0,
                    'return' => 0,
                    'particular' => $purchase->order_no .'-'. $purchase->notes,
                    'created_at' => $purchase->created_at,
                ];
            }

            foreach ($payments as $payment) {
                if ($payment->purchaseOrder){
                    $allData[] = [
                        'type' => 'Purchase',
                        'date' => $payment->date,
                        'invoice' => 0,
                        'payment' => $payment->amount,
                        'return' => 0,
                        'particular' => $payment->purchaseOrder->order_no .' '.$payment->note,
                        'created_at' => $payment->created_at,
                    ];
                }else{
                    $allData[] = [
                        'type' => 'Purchase',
                        'date' => $payment->date,
                        'invoice' => 0,
                        'payment' => $payment->amount,
                        'return' => 0,
                        'particular' => $payment->note,
                        'created_at' => $payment->created_at,
                    ];
                }
            }

            usort($allData, function ($a, $b) {
                return $a['created_at']->timestamp <=> $b['created_at']->timestamp;
            });
        }
        return view('report.supplier_ledger',compact('allData','suppliers','supplierName'));
    }
    public function customerLedger(Request $request){
        $customers = Customer::select('id','name','status')->where('status',1)->get();
        $request->validate([
            'customer' => 'nullable|numeric',
            'start_date' => 'nullable|date|required_with:end_date',
            'end_date' => 'nullable|date|required_with:end_date',
        ]);

        $allData = [];
        $customerName = '';
        if ($request->customer) {
            $customerName = Customer::find($request->customer)->name;
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
           
            $previousSaleAmount = SalesOrder::where('customer_id', $request->customer)->whereDate('date','<',$start_date)->sum('total');
            $previousPayment = SalePayment::where('customer_id', $request->customer)->whereDate('date','<',$start_date)->sum('amount');
            $previousBalance = $previousSaleAmount - $previousPayment;
            
            $sales = SalesOrder::where('customer_id', $request->customer)->whereDate('date','>=',$start_date)->whereDate('date','<=',$end_date)->get();
            $payments = SalePayment::with('salesOrder')->where('customer_id', $request->customer)->whereDate('date','>=',$start_date)->whereDate('date','<=',$end_date)->get();
            $customer = Customer::find($request->customer);
            //  dd($payments);
            if ($customer->opening_balance != 0 || $previousBalance){
                $allData[] = [
                    'type' => 'Opening Balance',
                    'date' => date('Y-m-d',strtotime($start_date.'-1 day')),
                    'invoice' => $supplier->opening_balance+$previousBalance,
                    'payment' => 0,
                    'return' => 0,
                    'particular' => 'Opening Balance',
                    'created_at' => Carbon::parse($start_date)->subDays(1),
                ];
            }
            foreach ($sales as $sale) {
                $allData[] = [
                    'type' => 'Sale',
                    'date' => $sale->date,
                    'invoice' => $sale->total,
                    'payment' => 0,
                    'return' => 0,
                    'particular' => $sale->order_no .'-'. $sale->note,
                    'created_at' => $sale->created_at,
                ];
            }

            foreach ($payments as $payment) {
                if ($payment->salesOrder){
                    $allData[] = [
                        'type' => 'Sale',
                        'date' => $payment->date,
                        'invoice' => 0,
                        'payment' => $payment->amount,
                        'return' => 0,
                        'particular' => $payment->salesOrder->order_no .' '.$payment->note,
                        'created_at' => $payment->created_at,
                    ];
                }else{
                    $allData[] = [
                        'type' => 'Sale',
                        'date' => $payment->date,
                        'invoice' => 0,
                        'payment' => $payment->amount,
                        'return' => 0,
                        'particular' => $payment->note,
                        'created_at' => $payment->created_at,
                    ];
                }
            }

            usort($allData, function ($a, $b) {
                return $a['created_at']->timestamp <=> $b['created_at']->timestamp;
            });
        }
        return view('report.customer_ledger',compact('allData','customers','customerName'));
    }
    public function cashbook(Request $request){
        $request->validate([
            'start_date' => 'nullable|date|required_with:end_date',
            'end_date' => 'nullable|date|required_with:end_date',
        ]);

        $result = null;
        $metaData = null;
        $result = [];
        if ($request->start_date && $request->end_date) {
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            $cash = Cash::first();
            $metaData = [
                'start_date' => $request->start,
                'end_date' => $request->end,
            ];

            $result = collect();

            $initialBalance = $cash->opening_balance;

            $previousDay = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));

            $totalIncome = TransactionLog::whereIn('transaction_type',[1,3])
                ->where('transaction_method', 1)
                ->whereDate('date', '<=', $previousDay)
                ->sum('amount');

            $totalExpense = TransactionLog::where('transaction_type', 2)
                ->where('transaction_method', 1)
                ->whereDate('date', '<=', $previousDay)
                ->sum('amount');

            $openingBalance = $initialBalance + ($totalIncome) - $totalExpense;

            $result->push(['date' => $start_date, 'particular' => 'Opening Balance', 'debit' => '', 'credit' => '', 'balance' => $openingBalance]);

            $transactionLogs = TransactionLog::whereIn('transaction_type',[1,2,3])
                ->where('transaction_method', 1)
                ->whereBetween('date', [$start_date, $end_date])
                ->get();

            $balance = $openingBalance;
            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($transactionLogs as $log) {
                if ($log->transaction_type == 1) {
                    // Income
                    if($log->transaction_id != null){
                        $balance += $log->amount ;
                        $totalDebit += $log->amount  ;
                        $result->push(['date' => $log->date, 'particular' => $log->particular .'('. $log->transaction->note .')', 'debit' => $log->amount, 'credit' => '', 'balance' => $balance]);
                    }else{
                        $balance += $log->amount ;
                        $totalDebit += $log->amount  ;
                        $result->push(['date' => $log->date, 'particular' => $log->particular, 'debit' => $log->amount, 'credit' => '', 'balance' => $balance]);
                    }

                } else {
                    if($log->transaction_id != null){
                        $balance -= $log->amount;
                        $totalCredit += $log->amount;
                        $result->push(['date' => $log->date, 'particular' => $log->particular .'('. $log->transaction->note .')', 'debit' => '', 'credit' => $log->amount, 'balance' => $balance]);
                    }else{
                        $balance -= $log->amount;
                        $totalCredit += $log->amount;
                        $result->push(['date' => $log->date, 'particular' => $log->particular, 'debit' => '', 'credit' => $log->amount, 'balance' => $balance]);
                    }
                }
            }

            $metaData['total_debit'] = $totalDebit;
            $metaData['total_credit'] = $totalCredit;
        }

        return view('report.cashbook', compact('metaData','result'));
    }
    public function bankbook(Request $request){
        $request->validate([
            'bank' => 'nullable|numeric',
            'start_date' => 'nullable|date|required_with:end_date',
            'end_date' => 'nullable|date|required_with:end_date',
        ]);

        $result = null;
        $metaData = null;
        $result = [];
        $accounts = BankAccount::where('status',1)->get();
        if ($request->start_date && $request->end_date) {
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            $metaData = [
                'start_date' => $request->start,
                'end_date' => $request->end,
            ];

            $result = collect();

            if($request->account != ''){
                //$initialBalance = $cash->opening_balance;
                $initialBalanceBank = BankAccount::where('id',$request->account)->where('status',1)->sum('opening_balance');

                $previousDay = date('Y-m-d', strtotime('-1 day', strtotime($request->start_date)));

                $totalIncome = TransactionLog::whereIn('transaction_type',[1,3])
                    ->where('transaction_method', 2)
                    ->where('bank_account_id', $request->account)
                    ->whereDate('date', '<=', $previousDay)
                    ->sum('amount');

                $totalExpense = TransactionLog::where('transaction_type', 2)
                    ->where('transaction_method', 2)
                    ->where('bank_account_id', $request->account)
                    ->whereDate('date', '<=', $previousDay)
                    ->sum('amount');

                $openingBalance = $initialBalanceBank + ($totalIncome) - $totalExpense;

                $result->push(['date' => $request->start_date, 'particular' => 'Opening Balance', 'payment_type'=>'','bank_type'=>'','bank_details'=>'', 'debit' => '', 'credit' => '', 'balance' => $openingBalance]);

                $transactionLogs = TransactionLog::whereIn('transaction_type',[1,2,3])
                    ->where('transaction_method', 2)
                    ->where('bank_account_id', $request->account)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->get();

                $balance = $openingBalance;
                $totalDebit = 0;
                $totalCredit = 0;
                foreach ($transactionLogs as $log) {
                    if ($log->transaction_type == 1) {
                        // Income
                        if($log->transaction_id != null){
                            $balance += $log->amount ;
                            $totalDebit += $log->amount  ;
                            $result->push(['date' => $log->date, 'particular' => $log->particular .'('. $log->transaction->note .')', 'payment_type'=>$log->transaction_method,'bank_type'=>$log->bank_type,'bank_details'=>'Bank: '.($log->bank->bank_name ?? '').'<br/>Acc: '.($log->bank->ac_number ?? ''), 'debit' => $log->amount, 'credit' => '', 'balance' => $balance]);
                        }else{
                            $balance += $log->amount ;
                            $totalDebit += $log->amount  ;
                            $result->push(['date' => $log->date, 'particular' => $log->particular, 'payment_type'=>$log->transaction_method,'bank_type'=>$log->bank_type,'bank_details'=>'Bank: '.($log->bank->bank_name ?? '').'<br/>Acc: '.($log->bank->ac_number ?? ''), 'debit' => $log->amount, 'credit' => '', 'balance' => $balance]);
                        }
                    } else {
                        if($log->transaction_id != null) {
                            $balance -= $log->amount;
                            $totalCredit += $log->amount;
                            $result->push(['date' => $log->date, 'particular' => $log->particular .'('. $log->transaction->note .')', 'payment_type'=>$log->transaction_method,'bank_type'=>$log->bank_type,'bank_details'=>'Bank: '.($log->bank->bank_name ?? '').'<br/>Acc: '.($log->bank->ac_number ?? ''), 'debit' => '', 'credit' => $log->amount, 'balance' => $balance]);
                        }else{
                            $balance -= $log->amount;
                            $totalCredit += $log->amount;
                            $result->push(['date' => $log->date, 'particular' => $log->particular, 'payment_type'=>$log->transaction_method,'bank_type'=>$log->bank_type,'bank_details'=>'Bank: '.($log->bank->bank_name ?? '').'<br/>Acc: '.($log->bank->ac_number ?? ''), 'debit' => '', 'credit' => $log->amount, 'balance' => $balance]);
                        }
                    }
                }
            }else{
                //$initialBalance = $cash->opening_balance;
                $initialBalanceBank = BankAccount::where('status',1)->sum('opening_balance');

                $previousDay = date('Y-m-d', strtotime('-1 day', strtotime($request->start_date)));

                $totalIncome = TransactionLog::whereIn('transaction_type',[1,3])
                    ->where('transaction_method', 2)
                    ->whereDate('date', '<=', $previousDay)
                    ->sum('amount');

                $totalExpense = TransactionLog::where('transaction_type', 2)
                    ->where('transaction_method', 2)
                    ->whereDate('date', '<=', $previousDay)
                    ->sum('amount');

                $openingBalance = $initialBalanceBank + ($totalIncome) - $totalExpense;

                $result->push(['date' => $request->start_date, 'particular' => 'Opening Balance', 'payment_type'=>'','bank_type'=>'','bank_details'=>'', 'debit' => '', 'credit' => '', 'balance' => $openingBalance]);

                $transactionLogs = TransactionLog::whereIn('transaction_type',[1,2,3])
                    ->where('transaction_method', 2)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->get();

                $balance = $openingBalance;
                $totalDebit = 0;
                $totalCredit = 0;
                foreach ($transactionLogs as $log) {
                    if ($log->transaction_type == 1) {
                        // Income
                        if($log->transaction_id != null){
                            $balance += $log->amount ;
                            $totalDebit += $log->amount  ;
                            $result->push(['date' => $log->date, 'particular' => $log->particular .'('. $log->transaction->note .')', 'payment_type'=>$log->transaction_method,'bank_type'=>$log->bank_type,'bank_details'=>'Bank: '.($log->bank->bank_name ?? '').'<br/>Acc: '.($log->bank->ac_number ?? ''), 'debit' => $log->amount, 'credit' => '', 'balance' => $balance]);
                        }else{
                            $balance += $log->amount ;
                            $totalDebit += $log->amount  ;
                            $result->push(['date' => $log->date, 'particular' => $log->particular, 'payment_type'=>$log->transaction_method,'bank_type'=>$log->bank_type,'bank_details'=>'Bank: '.($log->bank->bank_name ?? '').'<br/>Acc: '.($log->bank->ac_number ?? ''), 'debit' => $log->amount, 'credit' => '', 'balance' => $balance]);
                        }
                    } else {
                        if($log->transaction_id != null) {
                            $balance -= $log->amount;
                            $totalCredit += $log->amount;
                            $result->push(['date' => $log->date, 'particular' => $log->particular .'('. $log->transaction->note .')', 'payment_type'=>$log->transaction_method,'bank_type'=>$log->bank_type,'bank_details'=>'Bank: '.($log->bank->bank_name ?? '').'<br/>Acc: '.($log->bank->ac_number ?? ''), 'debit' => '', 'credit' => $log->amount, 'balance' => $balance]);
                        }else{
                            $balance -= $log->amount;
                            $totalCredit += $log->amount;
                            $result->push(['date' => $log->date, 'particular' => $log->particular, 'payment_type'=>$log->transaction_method,'bank_type'=>$log->bank_type,'bank_details'=>'Bank: '.($log->bank->bank_name ?? '').'<br/>Acc: '.($log->bank->ac_number ?? ''), 'debit' => '', 'credit' => $log->amount, 'balance' => $balance]);
                        }
                    }
                }
            }

            $metaData['total_debit'] = $totalDebit;
            $metaData['total_credit'] = $totalCredit;
        }

        return view('report.bankbook', compact('metaData','result','accounts'));
    }
}
