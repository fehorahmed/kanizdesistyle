<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInventory;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\SalePayment;
use App\Models\SalesOrder;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // if (Auth::user()->role_id == 1) {
            $todaySale = SalesOrder::whereDate('date', date('Y-m-d'))->sum('total');
            $todayDue = SalesOrder::whereDate('date', date('Y-m-d'))->sum('due');
            $todayDueCollection = SalePayment::whereDate('date', date('Y-m-d'))
            ->where('type', 1)
            ->where('received_type', 2)->sum('amount');

            $totalStock = PurchaseInventory::sum('quantity');

            $totalStockAmounts = PurchaseInventory::where('quantity', '>', 0)->get();
            $allTotalStockAmount = 0;
            foreach ($totalStockAmounts as $totalStockAmount) {

                $allTotalStockAmount += $totalStockAmount->quantity *  $totalStockAmount->unit_price;
            }



            $todayCashSale = SalePayment::whereDate('date', date('Y-m-d'))
            ->where('type', 1)
            ->where('received_type', 1)->sum('amount');
            $todayExpense = TransactionLog::whereDate('date', date('Y-m-d'))
            ->whereIn('transaction_type', [3, 2, 6])->sum('amount');

            $todaySaleReceipt = SalesOrder::whereDate('date', date('Y-m-d'))
            ->with('customer')
            ->orderBy('created_at', 'desc')->paginate(10);
            $todaySaleReceipt->setPageName('sale_receipt');
            $todayPurchaseReceipt = PurchaseOrder::whereDate('date', date('Y-m-d'))
            ->with('supplier')
            ->orderBy('created_at', 'desc')->paginate(10);
            $todayPurchaseReceipt->setPageName('purchase_receipt');

            // Order Count By Month
            $startDate = [];
            $endDate = [];
            $saleAmountLabel = [];
            $saleAmount = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now();
                $saleAmountLabel[] = $date->startOfMonth()->subMonths($i)->format('M, Y');
                $startDate[] = $date->format('Y-m-d');
                $endDate[] = $date->endOfMonth()->format('Y-m-d');
            }

            for ($i = 0; $i < 12; $i++) {
                $saleAmount[] = SalesOrder::where('date', '>=', $startDate[$i])
                    ->where('date', '<=', $endDate[$i])
                    ->sum('total');
            }

            // Product Upload chart
            $orderCount = [];

            for ($i = 0; $i < 12; $i++) {
                $orderCount[] = SalesOrder::where('date', '>=', $startDate[$i])
                    ->where('date', '<=', $endDate[$i])
                    ->count();
            }

            // Best Seller Products
            $bestSellingItemsSql = "SELECT products.id, count
                FROM products
                LEFT JOIN (SELECT product_id, SUM(quantity) count FROM sales_order_products GROUP BY product_id) t ON products.id = t.product_id
                WHERE products.status = 1
                ORDER BY count DESC
                LIMIT 10";

            $bestSellingItemsResult = DB::select($bestSellingItemsSql);
            $bestSellingItemsIds = [];

            foreach ($bestSellingItemsResult as $item)
            $bestSellingItemsIds[] = $item->id;

            $bestSellingItemsIdsString = implode(",", $bestSellingItemsIds);
            $bestSellingProductsQuery = Product::query();
            $bestSellingProductsQuery->whereIn('id', $bestSellingItemsIds);

            if (count($bestSellingItemsIds) > 0)
            $bestSellingProductsQuery->orderByRaw('FIELD(id,' . $bestSellingItemsIdsString . ')');
            $bestSellingProducts = $bestSellingProductsQuery->get();

            foreach ($bestSellingProducts as $product) {
                $product->count = DB::table('sales_order_products')
                ->where('product_id', $product->id)
                    ->sum('quantity');
            }

            // Recently Added Product
            $recentlyProducts = Product::take(10)->latest()->get();
        // }else {
            // $todaySale = SalesOrder::where('department_id', Auth::user()->department_id)->whereDate('date', date('Y-m-d'))->sum('total');
            // $todayDue = SalesOrder::where('department_id', Auth::user()->department_id)->whereDate('date', date('Y-m-d'))->sum('due');
            // $todayDueCollection = SalePayment::where('department_id', Auth::user()->department_id)->whereDate('date', date('Y-m-d'))
            // ->where('type', 1)
            // ->where('received_type', 2)->sum('amount');

            // $totalStock = PurchaseInventory::where('department_id', Auth::user()->department_id)->sum('pack_quantity');

            // $totalStockAmounts = PurchaseInventory::where('department_id', Auth::user()->department_id)->where('pack_quantity', '>', 0)->get();
            // $allTotalStockAmount = 0;
            // foreach ($totalStockAmounts as $totalStockAmount) {

            //     $allTotalStockAmount += $totalStockAmount->pack_quantity *  $totalStockAmount->unit_price;
            // }



            // $todayCashSale = SalePayment::where('department_id', Auth::user()->department_id)->whereDate('date', date('Y-m-d'))
            // ->where('type', 1)
            // ->where('received_type', 1)->sum('amount');
            // $todayExpense = TransactionLog::where('department_id', Auth::user()->department_id)->whereDate('date', date('Y-m-d'))
            // ->whereIn('transaction_type', [3, 2, 6])->sum('amount');

            // $todaySaleReceipt = SalesOrder::where('department_id', Auth::user()->department_id)->whereDate('date', date('Y-m-d'))
            // ->with('customer')
            // ->orderBy('created_at', 'desc')->paginate(10);
            // $todaySaleReceipt->setPageName('sale_receipt');
            // $todayPurchaseReceipt = PurchaseOrder::where('department_id', Auth::user()->department_id)->whereDate('date', date('Y-m-d'))
            // ->with('supplier')
            // ->orderBy('created_at', 'desc')->paginate(10);
            // $todayPurchaseReceipt->setPageName('purchase_receipt');

            // // Order Count By Month
            // $startDate = [];
            // $endDate = [];
            // $saleAmountLabel = [];
            // $saleAmount = [];

            // for ($i = 11; $i >= 0; $i--) {
            //     $date = Carbon::now();
            //     $saleAmountLabel[] = $date->startOfMonth()->subMonths($i)->format('M, Y');
            //     $startDate[] = $date->format('Y-m-d');
            //     $endDate[] = $date->endOfMonth()->format('Y-m-d');
            // }

            // for ($i = 0; $i < 12; $i++) {
            //     $saleAmount[] = SalesOrder::where('date', '>=', $startDate[$i])
            //         ->where('date', '<=', $endDate[$i])
            //         ->sum('total');
            // }

            // // Product Upload chart
            // $orderCount = [];

            // for ($i = 0; $i < 12; $i++) {
            //     $orderCount[] = SalesOrder::where('date', '>=', $startDate[$i])
            //         ->where('date', '<=', $endDate[$i])
            //         ->count();
            // }

            // // Best Seller Products
            // $bestSellingItemsSql = "SELECT products.id, count
            //     FROM products
            //     LEFT JOIN (SELECT product_id, SUM(pack_quantity) count FROM sales_order_products GROUP BY product_id) t ON products.id = t.product_id
            //     WHERE products.status = 1
            //     ORDER BY count DESC
            //     LIMIT 10";

            // $bestSellingItemsResult = DB::select($bestSellingItemsSql);
            // $bestSellingItemsIds = [];

            // foreach ($bestSellingItemsResult as $item)
            // $bestSellingItemsIds[] = $item->id;

            // $bestSellingItemsIdsString = implode(",", $bestSellingItemsIds);
            // $bestSellingProductsQuery = Product::query();
            // $bestSellingProductsQuery->whereIn('id', $bestSellingItemsIds);

            // if (count($bestSellingItemsIds) > 0)
            // $bestSellingProductsQuery->orderByRaw('FIELD(id,' . $bestSellingItemsIdsString . ')');
            // $bestSellingProducts = $bestSellingProductsQuery->get();

            // foreach ($bestSellingProducts as $product) {
            //     $product->count = DB::table('sales_order_products')
            //     ->where('department_id', Auth::user()->department_id)
            //     ->where('product_id', $product->id)
            //         ->sum('pack_quantity');
            // }

            // // Recently Added Product
            // $recentlyProducts = Product::take(10)->latest()->get();
        // }


        $data = [
            'todaySale' => $todaySale,
            'todayDue' => $todayDue,
            'todayDueCollection' => $todayDueCollection,
            'todayExpense' => $todayExpense,
            'todayCashSale' => $todayCashSale,
            'todaySaleReceipt' => $todaySaleReceipt,
            'todayPurchaseReceipt' => $todayPurchaseReceipt,
            'saleAmountLabel' => json_encode($saleAmountLabel),
            'saleAmount' => json_encode($saleAmount),
            'orderCount' => json_encode($orderCount),
            'bestSellingProducts' => $bestSellingProducts,
            'recentlyProducts' => $recentlyProducts,
            'totalStock'=>$totalStock,
            'allTotalStockAmount' => $allTotalStockAmount,
        ];

        return view('dashboard', $data);
    }
}
