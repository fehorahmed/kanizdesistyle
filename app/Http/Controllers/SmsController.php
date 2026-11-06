<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class SmsController extends Controller{
    public function index(){
        return view('sms_panel.all');
    }
    public function addPost(Request $request){
        //dd($request->all());
        $rules = [
            'message' => 'required|string|max:255'
        ];
        $request->validate($rules);
        if ($request->patient==0){
            $customer_numbers = Customer::pluck('mobile_no')->toArray();
        }else{
            if ($request->mobile_numbers && count($request->mobile_numbers)>0){
                $customer_numbers = $request->mobile_numbers;
            }else{
                return redirect()->back()->withInput()->with('error','Please select patient mobile Number!!!');
            }
        }
        return redirect()->back()->with('success','Sms send successfully!');
    }
}

