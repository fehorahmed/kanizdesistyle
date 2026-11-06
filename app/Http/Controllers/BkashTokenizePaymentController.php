<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use App\Models\EbookSaleOrder;
use App\Models\EbookSaleOrderPayment;
use App\Models\MobileBanking;
use App\Models\SalePayment;
use App\Models\SalesOrder;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Karim007\LaravelBkashTokenize\Facade\BkashPaymentTokenize;
use Karim007\LaravelBkashTokenize\Facade\BkashRefundTokenize;

class BkashTokenizePaymentController extends Controller
{
    public function index()
    {
        return view('bkashT::bkash-payment');
    }
    public function createPayment(Request $request)
    {
        $orderId = $request->query('order_id');
        $type = $request->query('type');
        if($type == 'Ebook'){
            $order = EbookSaleOrder::where('id',$orderId)->first();
        }else if($type == 'Ecom'){
            $order = SalesOrder::where('id',$orderId)->first();
        }
        
        
        // $inv = uniqid();
        $inv = $order->order_no;
        $request['intent'] = 'sale';
        $request['mode'] = '0011'; //0011 for checkout
        $request['payerReference'] = $inv.'|'.$type;
        $request['currency'] = 'BDT';
        $request['amount'] = $order->total;
        $request['merchantInvoiceNumber'] = $inv;
        // $request['callbackURL'] = config("bkash.callbackURL");
        $request['callbackURL'] = "http://127.0.0.1:8000/bkash/callback";

        $request_data_json = json_encode($request->all());

        $response =  BkashPaymentTokenize::cPayment($request_data_json);
        //$response =  BkashPaymentTokenize::cPayment($request_data_json,1); //last parameter is your account number for multi account its like, 1,2,3,4,cont..

        //store paymentID and your account number for matching in callback request
        //dd($response); //if you are using sandbox and not submit info to bkash use it for 1 response

        if (isset($response['bkashURL'])) return redirect()->away($response['bkashURL']);
        else return redirect()->back()->with('error-alert2', $response['statusMessage'] ?? 'Something went wrong.');
    }

    public function callBack(Request $request)
    {
        //callback request params
        // paymentID=your_payment_id&status=success&apiVersion=1.2.0-beta
        //using paymentID find the account number for sending params
        
        if ($request->status == 'success'){
            
            $response = BkashPaymentTokenize::executePayment($request->paymentID);
            //$response = BkashPaymentTokenize::executePayment($request->paymentID, 1); //last parameter is your account number for multi account its like, 1,2,3,4,cont..
            if (!$response){ //if executePayment payment not found call queryPayment
                $response = BkashPaymentTokenize::queryPayment($request->paymentID);
                //$response = BkashPaymentTokenize::queryPayment($request->paymentID,1); //last parameter is your account number for multi account its like, 1,2,3,4,cont..
            }

            if (isset($response['statusCode']) && $response['statusCode'] == "0000" && $response['transactionStatus'] == "Completed") {
                /*
                 * for refund need to store
                 * paymentID and trxID
                 * */
                // dd($response);
                [$orderID, $itemType] = explode('|', $response['payerReference']);

                if($itemType == 'Ebook'){
                    $order = EbookSaleOrder::where('order_no',$response['merchantInvoiceNumber'])->first();
                    if($order->transactionStatus != 'Completed'){
                        $order->paymentID = $response['paymentID'];
                        $order->trxID = $response['trxID'];
                        $order->transactionStatus = 'Completed';
                        $order->online_payment_response = json_encode($response);
                        $order->status = 1;
                        $order->save();

                        $response['payment_type'] = 3;
                        
                        if($response['amount'] > 0){
                            if ($response['payment_type'] == 1 || $response['payment_type'] == 3) {
                                $payment = new EbookSaleOrderPayment();
                                $payment->department_id = $order->department_id;
                                $payment->customer_id = $order->customer_id;
                                $payment->ebook_sale_order_id = $order->id;
                                $payment->transaction_method = $response['payment_type'];
                                if ($response['payment_type'] == 3){
                                    $payment->mobile_banking_id = 1;//MobileBankig - bkash id = 1;
                                }
                                $payment->amount = $response['amount'];
                                $payment->date = date('Y-m-d');
                                $payment->note = "Online Payment";
                                $payment->user_id = Auth::id();
                                $payment->save();
                
                                if ($response['payment_type'] == 1)
                                    Cash::first()->increment('amount', $response['amount']);
                                else
                                    MobileBanking::find(1)->increment('amount', $response['amount']); //MobileBankig - bkash id = 1;
                
                                $log = new TransactionLog();
                                $log->department_id = $order->department_id;
                                $log->customer_id = $order->customer_id;
                                $log->date = date('Y-m-d');
                                $log->particular = 'Payment from '.$order->customer->name.' for '.$order->order_no;
                                $log->transaction_type = 1;
                                $log->transaction_method = $response['payment_type'];
                                $log->account_head_type_id = 2;
                                $log->account_head_sub_type_id = 2;
                                $log->amount = $response['amount'];
                                if ($response['payment_type'] == 3){
                                    $log->mobile_banking_id = 1; //MobileBankig - bkash id = 1;
                                }
                                $log->note = "Online Payment";
                                $log->ebook_sale_order_id = $order->id;
                                $log->ebook_sale_payment_id = $payment->id;
                                $log->user_id = Auth::id();
                                $log->save();
                            }
                
                            $order->increment('paid', $response['amount']);
                            $order->decrement('due', $response['amount']);
                            $order->save();
                        }
                    }
                }else if($itemType == 'Ecom'){
                    $order = SalesOrder::where('order_no',$response['merchantInvoiceNumber'])->first();
                    if($order->transactionStatus != 'Completed'){
                        $order->paymentID = $response['paymentID'];
                        $order->trxID = $response['trxID'];
                        $order->transactionStatus = 'Completed';
                        $order->online_payment_response = json_encode($response);
                        $order->save();

                        $response['payment_type'] = 3;
                        
                        if($response['amount'] > 0){
                            
                            if ($response['payment_type'] == 1 || $response['payment_type'] == 3) {
                                $payment = new SalePayment();
                                $payment->department_id = $order->department_id;
                                $payment->customer_id = $order->customer_id;
                                $payment->sales_order_id = $order->id;
                                $payment->sale_type = $order->sale_type;
                                $payment->transaction_method = $response['payment_type'];
                                if ($response['payment_type'] == 3){
                                    $payment->mobile_banking_id = 1;//MobileBankig - bkash id = 1;
                                }
                                $payment->amount = $response['amount'];
                                $payment->date = date('Y-m-d');
                                $payment->note = "Online Payment";
                                $payment->user_id = Auth::id();
                                $payment->save();
                
                                if ($response['payment_type'] == 1)
                                    Cash::first()->increment('amount', $response['amount']);
                                else
                                    MobileBanking::find(1)->increment('amount', $response['amount']); //MobileBankig - bkash id = 1;
                
                                $log = new TransactionLog();
                                $log->department_id = $order->department_id;
                                $log->customer_id = $order->customer_id;
                                $log->date = date('Y-m-d');
                                $log->particular = 'Payment from '.$order->customer->name.' for '.$order->order_no;
                                $log->transaction_type = 1;
                                $log->transaction_method = $response['payment_type'];
                                $log->account_head_type_id = 2;
                                $log->account_head_sub_type_id = 2;
                                $log->amount = $response['amount'];
                                if ($response['payment_type'] == 3){
                                    $log->mobile_banking_id = 1; //MobileBankig - bkash id = 1;
                                }
                                $log->note = "Online Payment";
                                $log->sales_order_id = $order->id;
                                $log->sale_payment_id = $payment->id;
                                $log->sale_type = $order->sale_type;
                                $log->user_id = Auth::id();
                                $log->save();
                            }
                
                            $order->increment('paid', $response['amount']);
                            $order->decrement('due', $response['amount']);
                            $order->save();
                        }
                    }
                }
                
                $orderId = $order->id;

                return BkashPaymentTokenize::success('Thank you for your payment', $response['trxID'],$orderId,$itemType);
            }
           
            return BkashPaymentTokenize::failure($response['statusMessage']);
        }else if ($request->status == 'cancel'){
            // dd($request->all());
            return BkashPaymentTokenize::cancel('Your payment is canceled');
        }else{
            
            return BkashPaymentTokenize::failure('Your transaction is failed');
        }
    }

    public function searchTnx($trxID)
    {
        //response
        return BkashPaymentTokenize::searchTransaction($trxID);
        //return BkashPaymentTokenize::searchTransaction($trxID,1); //last parameter is your account number for multi account its like, 1,2,3,4,cont..
    }

    public function refund(Request $request)
    {
        $paymentID='Your payment id';
        $trxID='your transaction no';
        $amount=5;
        $reason='this is test reason';
        $sku='abc';
        //response
        return BkashRefundTokenize::refund($paymentID,$trxID,$amount,$reason,$sku);
        //return BkashRefundTokenize::refund($paymentID,$trxID,$amount,$reason,$sku, 1); //last parameter is your account number for multi account its like, 1,2,3,4,cont..
    }
    public function refundStatus(Request $request)
    {
        $paymentID='Your payment id';
        $trxID='your transaction no';
        return BkashRefundTokenize::refundStatus($paymentID,$trxID);
        //return BkashRefundTokenize::refundStatus($paymentID,$trxID, 1); //last parameter is your account number for multi account its like, 1,2,3,4,cont..
    }
}
