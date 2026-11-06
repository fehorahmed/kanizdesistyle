<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderLog;
use App\Models\Product;
use App\Models\PurchaseInventory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = SalesOrder::where('sale_type',3)->where('customer_id',auth()->user()->customer_id)->orderBy('id','desc')->get();
        return response()->json($orders);
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'billing.name'   => 'required|string|max:255',
            'billing.phone'  => 'required|string|max:20',
            'billing.address'=> 'required|string|max:500',

            'payment_method' => 'required|string|in:cod,cash,bkash,nagad,card',

            'coupon_code'    => 'nullable|string',
            'shipping_charge'=> 'nullable|numeric|min:0',

            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
            'items.*.discount'   => 'nullable|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            //Check Coupon validitity during Order place
            
            // dd($request->all());
            $request['sister_concern_id'] = 1;

            // Handle Customer
            if (auth()->user()->customer_id) {
                $request['customer'] = auth()->user()->customer_id;

                $user = auth()->user();
                if(!$user->mobile_no){
                    $user->mobile_no = $request->billing['phone'];
                    $user->address = $request->billing['address'];
                }
                $user->save();
            } else {
                $customer = new Customer();
                $customer->name = auth()->user()->name;
                $customer->sister_concern_id = auth()->user()->sister_concern_id;
                $customer->mobile_no = $request->billing['phone'];
                $customer->address = $request->billing['address'];
                $customer->save();

                $user = auth()->user();
                $user->customer_id = $customer->id;
                $user->save();

                $request['customer'] = $customer->id;
            }

            // Generate Order No
            $lastSalesOrder = SalesOrder::where('sister_concern_id', $request->sister_concern_id)
                ->where('sale_type', 3)
                ->latest('id')->first();

            $nextCodeNumber = !empty($lastSalesOrder) ? intval(substr($lastSalesOrder->order_no, 7)) + 1 : 1;
            $newCodeOrder = 'ESO-' . str_pad($nextCodeNumber, 10, '0', STR_PAD_LEFT);

            // Create SalesOrder
            $order = new SalesOrder();
            $order->order_no = $newCodeOrder;
            $order->billing_name = $request->billing['name'];
            $order->billing_mobile_no = $request->billing['phone'];
            $order->billing_address = $request->billing['address'];
            $order->payment_method = $request->payment_method;
            $order->sister_concern_id = $request->sister_concern_id;
            $order->customer_id = $request->customer;
            $order->sale_type = 3; // Ecommerce
            $order->received_by = '';
            $order->date = date('Y-m-d');
            $order->sub_total = 0;
            $order->discount = 0;
            $order->shipping_charge = $request->shipping_charge ?? 0;
            $order->total = 0;
            $order->paid = 0;
            $order->due = 0;
            $order->opening_sale = 0;
            $order->note = $request->notes ?? 'Ecommerce';
            $order->user_id = Auth::id();
            $order->status = 0; // Initial
            $order->save();

            // Order Log
            $orderLog = new OrderLog();
            $orderLog->order_type = 'SALE';
            $orderLog->order_id  = $order->id;
            $orderLog->created_by  = Auth::id();
            $orderLog->status  = 'ORDER CREATE';
            $orderLog->save();

            // Process Cart Items
            $subTotal = 0;
            $totalDiscount = 0;

            foreach ($request->items as $item) {
                $batch_no = null;
                $product = Product::findOrFail($item['product_id']);

                // $inventory = PurchaseInventory::where('product_id', $product->id)
                //     ->where('sister_concern_id', $request->sister_concern_id)
                //     ->where('batch_no', $batch_no)
                //     ->first();

                $salesOrderProduct = new SalesOrderProduct();
                $salesOrderProduct->sister_concern_id = $request->sister_concern_id;
                $salesOrderProduct->product_id = $product->id;
                $salesOrderProduct->sales_order_id = $order->id;
                $salesOrderProduct->batch_no = $batch_no;
                $salesOrderProduct->name = $product->name;
                $salesOrderProduct->serial = $product->code;
                $salesOrderProduct->sale_type = 3;
                $salesOrderProduct->quantity = $item['quantity'];
                $salesOrderProduct->discount_percentage = $item['discount_percent'] ?? 0;
                $salesOrderProduct->discount_amt = $item['discount'] ?? 0;
                $salesOrderProduct->unit_price = $item['price'] + ($item['discount'] ?? 0);
                $salesOrderProduct->buy_price = $inventory->unit_price ?? 0;
                $salesOrderProduct->date = date('Y-m-d');
                $salesOrderProduct->total = ($item['price'] + ($item['discount'] ?? 0)) * $item['quantity'];
                $salesOrderProduct->user_id = Auth::id();
                $salesOrderProduct->save();

                $subTotal += ($item['price'] + ($item['discount'] ?? 0)) * $item['quantity'];
                $totalDiscount += ($item['discount'] ?? 0) * $item['quantity'];
            }

            $subTotalAfterProductDiscount = $subTotal - $totalDiscount;
            $couponDiscount = 0;
            $coupon = null;

            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where('is_active', 1)
                    ->first();

                if (!$coupon) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid or inactive coupon code.'
                    ], 400);
                }

                $today = date('Y-m-d');
                if (($coupon->start_date && $today < $coupon->start_date) ||
                    ($coupon->end_date && $today > $coupon->end_date)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Coupon is not valid at this time.'
                    ], 400);
                }

                $alreadyUsed = CouponUsage::where('coupon_id', $coupon->id)
                    ->where('customer_id', $request['customer'])
                    ->exists();

                if ($alreadyUsed) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already used this coupon.'
                    ], 400);
                }

                if ($subTotalAfterProductDiscount < $coupon->min_purchase) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cart subtotal does not meet minimum purchase requirement for this coupon."
                    ], 400);
                }
                if ($coupon->type == 'fixed') {
                    $couponDiscount = $coupon->discount;
                } elseif ($coupon->type == 'percent') {
                    $couponDiscount = ($subTotalAfterProductDiscount * $coupon->discount) / 100;
                }
            }

            // Update Order totals
            $order->sub_total = $subTotal;
            $total = $subTotal - $totalDiscount;
            if ($couponDiscount > 0) {
                $totalDiscount += $couponDiscount;
                $total -= $couponDiscount;
            }
            $order->discount = $totalDiscount;
            $order->coupon_discount = $couponDiscount;
            $order->total = ceil($total);
            $order->due = ceil($total);
            $order->save();

            if ($couponDiscount > 0 && $coupon) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'customer_id' => $request['customer'],
                    'order_id' => $order->id,
                    'discount' => $couponDiscount
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_no' => $order->order_no,
                // 'redirect' => route('bkash-create', ['order_id' => $order->id, 'type'=>'Ecom'])
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
    public function buyNow(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'billing.name'   => 'required|string|max:255',
            // 'billing.phone'  => 'required|string|max:20',
            'billing.phone' => [
                'required',
                'string',
                'regex:/^(?:\+?88)?01[3-9]\d{8}$/',
                'max:14'
            ],
            'billing.address'=> 'required|string|max:500',

            'payment_method' => 'required|string|in:cod,cash,bkash,nagad,card',
            'g_recaptcha' => 'required',
            'coupon_code'    => 'nullable|string',
            'shipping_charge'=> 'nullable|numeric|min:0',

            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
            'items.*.discount'   => 'nullable|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            //Check Coupon validitity during Order place
            
            // dd($request->all());
            $request['sister_concern_id'] = 1;

            if(Auth::check()){
                 if (auth()->user()->customer_id) {
                    $request['customer'] = auth()->user()->customer_id;

                    $user = auth()->user();
                    if(!$user->mobile_no){
                        $user->mobile_no = $request->billing['phone'];
                        $user->address = $request->billing['address'];
                    }
                    $user->save();
                } else {
                    $customer = new Customer();
                    $customer->name = auth()->user()->name;
                    $customer->sister_concern_id = auth()->user()->sister_concern_id;
                    $customer->mobile_no = $request->billing['phone'];
                    $customer->address = $request->billing['address'];
                    $customer->save();

                    $user = auth()->user();
                    $user->customer_id = $customer->id;
                    $user->save();

                    $request['customer'] = $customer->id;
                }
            }else{
                // Handle Customer
                $mobile = $request->billing['phone'];
                $name   = $request->billing['name'] ?? 'Guest';
                $customer = null;
                $user = null;

                $user = User::where('mobile_no', $mobile)->first();
                $customer = Customer::where('mobile_no', $mobile)->first();

                if ($user && $customer) {
                    if (!$user->customer_id) {
                        $user->customer_id = $customer->id;
                        $user->save();
                    }
                }elseif ($user && !$customer) {
                    $customer = Customer::create([
                        'name' => $name,
                        'mobile_no' => $mobile,
                        'email' => $request->billing['email'] ?? null,
                        'address' => $request->billing['address'] ?? null,
                        'sister_concern_id' => 1,
                    ]);
                    $user->customer_id = $customer->id;
                    $user->save();
                }elseif (!$user && $customer) {
                    $user = User::create([
                        'name' => $name,
                        'mobile_no' => $mobile,
                        'username' => $mobile,
                        'role_id' => 4,
                        'email' => $request->billing['email'] ?? null,
                        'password' => Hash::make($mobile), // random password for guest
                        'customer_id' => $customer->id,
                        'sister_concern_id' => 1,
                    ]);
                }else {
                    $customer = Customer::create([
                        'name' => $name,
                        'mobile_no' => $mobile,
                        'email' => $request->billing['email'] ?? null,
                        'address' => $request->billing['address'] ?? null,
                        'sister_concern_id' => 1,
                    ]);

                    $user = User::create([
                        'name' => $name,
                        'mobile_no' => $mobile,
                        'username' => $mobile,
                        'role_id' => 4,
                        'email' => $request->billing['email'] ?? null,
                        'password' => Hash::make($mobile), // random password for guest
                        'customer_id' => $customer->id,
                        'sister_concern_id' => 1,
                    ]);
                }
                $request['customer'] = $customer->id;
            }
            

            // Generate Order No
            $lastSalesOrder = SalesOrder::where('sister_concern_id', $request->sister_concern_id)
                ->where('sale_type', 3)
                ->latest('id')->first();

            $nextCodeNumber = !empty($lastSalesOrder) ? intval(substr($lastSalesOrder->order_no, 7)) + 1 : 1;
            $newCodeOrder = 'ESO-' . str_pad($nextCodeNumber, 10, '0', STR_PAD_LEFT);

            // Create SalesOrder
            $order = new SalesOrder();
            $order->order_no = $newCodeOrder;
            $order->billing_name = $request->billing['name'];
            $order->billing_mobile_no = $request->billing['phone'];
            $order->billing_address = $request->billing['address'];
            $order->payment_method = $request->payment_method;
            $order->sister_concern_id = $request->sister_concern_id;
            $order->customer_id = $request->customer;
            $order->sale_type = 3; // Ecommerce
            $order->received_by = '';
            $order->date = date('Y-m-d');
            $order->sub_total = 0;
            $order->discount = 0;
            $order->shipping_charge = $request->shipping_charge ?? 0;
            $order->total = 0;
            $order->paid = 0;
            $order->due = 0;
            $order->opening_sale = 0;
            $order->note = $request->billing['note'] ?? 'Ecommerce';
            $order->user_id = $user->id;
            $order->status = 0; // Initial
            $order->save();

            // Order Log
            $orderLog = new OrderLog();
            $orderLog->order_type = 'SALE';
            $orderLog->order_id  = $order->id;
            $orderLog->created_by  = $user->id;
            $orderLog->status  = 'ORDER CREATE';
            $orderLog->save(); 

            // Process Cart Items
            $subTotal = 0;
            $totalDiscount = 0;

            foreach ($request->items as $item) {
                $batch_no = null;
                $product = Product::findOrFail($item['product_id']);

                // $inventory = PurchaseInventory::where('product_id', $product->id)
                //     ->where('sister_concern_id', $request->sister_concern_id)
                //     ->where('batch_no', $batch_no)
                //     ->first();

                $salesOrderProduct = new SalesOrderProduct();
                $salesOrderProduct->sister_concern_id = $request->sister_concern_id;
                $salesOrderProduct->product_id = $product->id;
                $salesOrderProduct->sales_order_id = $order->id;
                $salesOrderProduct->batch_no = $batch_no;
                $salesOrderProduct->name = $product->name;
                $salesOrderProduct->serial = $product->code;
                $salesOrderProduct->sale_type = 3;
                $salesOrderProduct->quantity = $item['quantity'];
                $salesOrderProduct->discount_percentage = $item['discount_percent'] ?? 0;
                $salesOrderProduct->discount_amt = $item['discount'] ?? 0;
                $salesOrderProduct->unit_price = $item['price'] + ($item['discount'] ?? 0);
                $salesOrderProduct->buy_price = $inventory->unit_price ?? 0;
                $salesOrderProduct->date = date('Y-m-d');
                $salesOrderProduct->total = ($item['price'] + ($item['discount'] ?? 0)) * $item['quantity'];
                $salesOrderProduct->user_id = $user->id;
                $salesOrderProduct->save();

                $subTotal += ($item['price'] + ($item['discount'] ?? 0)) * $item['quantity'];
                $totalDiscount += ($item['discount'] ?? 0) * $item['quantity'];
            }

            $subTotalAfterProductDiscount = $subTotal - $totalDiscount;
            $couponDiscount = 0;
            $coupon = null;

            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where('is_active', 1)
                    ->first();

                if (!$coupon) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid or inactive coupon code.'
                    ], 400);
                }

                $today = date('Y-m-d');
                if (($coupon->start_date && $today < $coupon->start_date) ||
                    ($coupon->end_date && $today > $coupon->end_date)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Coupon is not valid at this time.'
                    ], 400);
                }

                $alreadyUsed = CouponUsage::where('coupon_id', $coupon->id)
                    ->where('customer_id', $request['customer'])
                    ->exists();

                if ($alreadyUsed) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already used this coupon.'
                    ], 400);
                }

                if ($subTotalAfterProductDiscount < $coupon->min_purchase) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cart subtotal does not meet minimum purchase requirement for this coupon."
                    ], 400);
                }
                if ($coupon->type == 'fixed') {
                    $couponDiscount = $coupon->discount;
                } elseif ($coupon->type == 'percent') {
                    $couponDiscount = ($subTotalAfterProductDiscount * $coupon->discount) / 100;
                }
            }

            // Update Order totals
            $order->sub_total = $subTotal;
            $total = $subTotal - $totalDiscount;
            if ($couponDiscount > 0) {
                $totalDiscount += $couponDiscount;
                $total -= $couponDiscount;
            }
            $order->discount = $totalDiscount;
            $order->coupon_discount = $couponDiscount;
            $order->total = ceil($total);
            $order->due = ceil($total);
            $order->save();

            if ($couponDiscount > 0 && $coupon) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'customer_id' => $request['customer'],
                    'order_id' => $order->id,
                    'discount' => $couponDiscount
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_no' => $order->order_no,
                'mobile_no' => $order->billing_mobile_no,
                // 'redirect' => route('bkash-create', ['order_id' => $order->id, 'type'=>'Ecom'])
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
    public function buyNowShow($order_no,$mobile)
    {
        $order = SalesOrder::with('products')->where('order_no', $order_no)->where('sale_type',3)->where('billing_mobile_no',$mobile)->first();
        return response()->json($order);
    }
    public function show($order_no)
    {
        $order = SalesOrder::with('products')->where('order_no', $order_no)->where('sale_type',3)->where('customer_id',auth()->user()->customer_id)->first();
        return response()->json($order);
    }
    public function downloadReceipt($order_no, $mobile)
    {
        // Fetch the order (guest or logged-in user)
        $order = SalesOrder::with('products')
            ->where('order_no', $order_no)
            ->where('billing_mobile_no', $mobile)
            ->where('sale_type', 3)
            ->firstOrFail();

        // Load a view to generate PDF
        $pdf = PDF::loadView('ecommerce.order.pdf.print', compact('order'));

        // Download PDF
        return $pdf->download("Order-{$order->order_no}.pdf");
    }
}
