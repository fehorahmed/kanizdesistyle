<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use App\Models\EbookSaleOrderPayment;
use App\Models\MobileBanking;
use App\Models\SalePayment;
use App\Models\SalesOrder;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use URL;
use App\Http\Controllers\Controller;
use App\Models\EbookSaleOrder;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BkashController extends Controller
{
    private $base_url;
    private $username;
    private $password;
    private $app_key;
    private $app_secret;

    public function __construct()
    {
        env('SANDBOX') ? $this->base_url = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta' : $this->base_url = 'https://tokenized.pay.bka.sh/v1.2.0-beta';
        $this->username = env('BKASH_USERNAME');
        $this->password = env('BKASH_PASSWORD');
        $this->app_key = env('BKASH_APP_KEY');
        $this->app_secret = env('BKASH_APP_SECRET');
    }
    public function authHeaders()
    {
        return array(
            'Content-Type:application/json',
            'Authorization:' . $this->grant(),
            'X-APP-Key:' . $this->app_key
        );
    }

    public function curlWithBody($url, $header, $method, $body_data)
    {
        $curl = curl_init($this->base_url . $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body_data);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function getIdTokenFromRefreshToken($refresh_token){
	
	    $header = array(
            'Content-Type:application/json',
            'username:' . $this->username,
            'password:' . $this->password
        );

        $body_data = array('app_key' => $this->app_key, 'app_secret' => $this->app_secret, 'refresh_token' => $refresh_token);

        $response = $this->curlWithBody('/tokenized/checkout/token/refresh', $header, 'POST', json_encode($body_data));

        $idToken = json_decode($response)->id_token;

        return $idToken;
    	
    }

    public function grant()
    {

        if (!Schema::hasTable('bkash_token')) {
            // DB::beginTransaction();
            Schema::create('bkash_token', function ($table) {
                $table->boolean('sandbox_mode')->notNullable();
                $table->bigInteger('id_expiry')->notNullable();
                $table->string('id_token', 2048)->notNullable();
                $table->bigInteger('refresh_expiry')->notNullable();
                $table->string('refresh_token', 2048)->notNullable();
            });
            $insertedRows = DB::table('bkash_token')->insert([
                'sandbox_mode' => 1, 
                'id_expiry' => 0, 
                'id_token' => 'id_token', 
                'refresh_expiry' => 0, 
                'refresh_token' => 'refresh_token',
            ]);
            
            if ($insertedRows > 0) {
                
                // echo 'Row inserted successfully.';
            } else {
                echo 'Error inserting row.';
            }
            
            
            
            $insertedRows = DB::table('bkash_token')->insert([
                'sandbox_mode' => 0, 
                'id_expiry' => 0, 
                'id_token' => 'id_token', 
                'refresh_expiry' => 0, 
                'refresh_token' => 'refresh_token', 
            ]);
            
            if ($insertedRows > 0) {
                // echo 'Row inserted successfully.';
                
            } else {
                echo 'Error inserting row.';
            }
            // DB::commit();
        }


        // DB::beginTransaction();

        $sandbox = env('SANDBOX');
        
        $tokenData = DB::table('bkash_token')->where('sandbox_mode', $sandbox)->first();

        if ($tokenData) {
            // Access the token data
            $idExpiry = $tokenData->id_expiry;
            $idToken = $tokenData->id_token;
            $refreshExpiry = $tokenData->refresh_expiry;
            $refreshToken = $tokenData->refresh_token;
            
            if($idExpiry>time()){
                // dd("Id token from db: ".$idToken);
                return $idToken;
            }
            if($refreshExpiry>time()){
                $idToken = $this->getIdTokenFromRefreshToken($refreshToken);
                $updatedRows = DB::table('bkash_token')
                    ->where('sandbox_mode',$sandbox)
                    ->update([
                        'id_expiry' => time() + 3600, // Set new expiry time
                        'id_token' => $idToken,
                    ]);
                
                if ($updatedRows > 0) {
                    // DB::commit();
                    //echo 'Rows updated successfully.';
                } else {
                    //echo 'Error updating rows.';
                }
                // dd("Id token from refresh api: ".$idToken);
                return $idToken;
            }
            // Do something with the token data
        } else {
            echo 'Token not found.';
        }
       
        
        $header = array(
            'Content-Type:application/json',
            'username:' . $this->username,
            'password:' . $this->password
        );

        $body_data = array('app_key' => $this->app_key, 'app_secret' => $this->app_secret);

        $response = $this->curlWithBody('/tokenized/checkout/token/grant', $header, 'POST', json_encode($body_data));

        $idToken = json_decode($response)->id_token;

        $updatedRows = DB::table('bkash_token')
            ->where('sandbox_mode',$sandbox)
            ->update([
                'id_expiry' => time() + 3600, // Set new expiry time
                'id_token' => $idToken,
                'refresh_expiry' => time() + 864000,
                'refresh_token' => json_decode($response)->refresh_token,
            ]);
        
        if ($updatedRows > 0) {
            // DB::commit();
            //echo 'Rows updated successfully.';
        } else {
            //echo 'Error updating rows.';
        }
        // dd("Id token from grant api: ".$idToken);
        return $idToken;
        	

    }

    public function payment(Request $request)
    {
        return view('bkash.pay');
    }

    public function createPayment(Request $request)
    {
        // dd($request->all());
        $orderId = $request->query('order_id');
        $type = $request->query('type');
        if($type == 'Ebook'){
            $order = EbookSaleOrder::where('id',$orderId)->first();
        }else if($type == 'Ecom'){
            $order = SalesOrder::where('id',$orderId)->first();
        }
        
        if (!$order->total || $order->total < 1) {
            return response()->json(['error' => 'You should pay greater than 1 TK !!'], 400);
        }

        $header = $this->authHeaders();

        $website_url = URL::to("/");

        $inv = $order->order_no;

        $body_data = array(
            'mode' => '0011',
            'payerReference' => $inv.'|'.$type,
            'callbackURL' => $website_url . '/bkash-callback',
            'amount' => $order->total,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $inv
        );

        $response = $this->curlWithBody('/tokenized/checkout/create', $header, 'POST', json_encode($body_data));

        // dd($response);

        return redirect((json_decode($response)->bkashURL));
    }

    public function executePayment($paymentID)
    {

        $header = $this->authHeaders();

        $body_data = array(
            'paymentID' => $paymentID
        );


        $response = $this->curlWithBody('/tokenized/checkout/execute', $header, 'POST', json_encode($body_data));

        return $response;
    }
    public function queryPayment($paymentID)
    {
        $header = $this->authHeaders();

        $body_data = array(
            'paymentID' => $paymentID,
        );

        $response = $this->curlWithBody('/tokenized/checkout/payment/status', $header, 'POST', json_encode($body_data));

        return $response;
    }

    public function callback(Request $request)
    {
        $allRequest = $request->all();
        if (isset($allRequest['status']) && $allRequest['status'] == 'success') {
            $response1 = $this->executePayment($allRequest['paymentID']);

            // dd($response);
            if(is_null($response1)){
                sleep(1);
                $response1 = $this->queryPayment($allRequest['paymentID']);
            } 

            $response = json_decode($response1, true);
            
            if (array_key_exists("statusCode", $response) && $response['statusCode'] == '0000' && array_key_exists("transactionStatus", $response) && $response['transactionStatus'] == 'Completed') {
                
                try{
                    // DB::beginTransaction();
                    //Initiate Success
                    // dd($response['payerReference']);
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

                        $orderId = $order->id;

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
                        
                        $orderId = $order->id;
                    }
                    
                    
                    // DB::commit();
                    // End Payment Success
                    // payment success case
                    return view('bkash.success')->with([
                        'transId' => $response['trxID'],
                        'orderId' =>$orderId,
                        'type' =>$itemType,
                    ]);
                }catch(Exception $exception){
                    // DB::rollBack();
                    return view('bkash.fail')->with([
                        'response' => $response['statusMessage'],
                    ]);
                }
                
            }

            return view('bkash.fail')->with([
                'response' => $response['statusMessage'],
            ]);

        } else {
            return view('bkash.fail')->with([
                'response' => 'Payment Failed !!',
            ]);
        }

    }
    public function getRefund(Request $request)
    {
        return view('bkash.refund');
    }

    public function refundPayment(Request $request)
    {
        $header = $this->authHeaders();

        $body_data = array(
            'paymentID' => $request->paymentID,
            'trxID' => $request->trxID
        );

        $response = $this->curlWithBody('/tokenized/checkout/payment/refund', $header, 'POST', json_encode($body_data));

        $res_array = json_decode($response, true);

        $message = "Refund Failed !!";

        if (!isset($res_array['refundTrxID'])) {

            $body_data = array(
                'paymentID' => $request->paymentID,
                'amount' => $request->amount,
                'trxID' => $request->trxID,
                'sku' => 'sku',
                'reason' => 'Quality issue'
            );

            $response = $this->curlWithBody('/tokenized/checkout/payment/refund', $header, 'POST', json_encode($body_data));

            $res_array = json_decode($response, true);

            if (isset($res_array['refundTrxID'])) {
                // your database insert operation    
                $message = "Refund successful !!.Your Refund TrxID : " . $res_array['refundTrxID'];
            }

        } else {
            $message = "Already Refunded !!.Your Refund TrxID : " . $res_array['refundTrxID'];
        }

        return view('bkash.refund')->with([
            'response' => $message,
        ]);
    }

    public function queryPaymentAPI(Request $request,$paymentID)
    {
        $header = $this->authHeaders();

        $body_data = array(
            'paymentID' => $paymentID,
        );

        $response = $this->curlWithBody('/tokenized/checkout/payment/status', $header, 'POST', json_encode($body_data));

        return $response;
    }


    public function getSearchTransaction(Request $request)
    {
        return view('bkash.search');
    }

    public function searchTransaction(Request $request)
    {

        $header = $this->authHeaders();
        $body_data = array(
            'trxID' => $request->trxID,
        );

        $response = $this->curlWithBody('/tokenized/checkout/general/searchTransaction', $header, 'POST', json_encode($body_data));


        return view('bkash.search')->with([
            'response' => $response,
        ]);
    }
}
