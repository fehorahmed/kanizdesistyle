<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
        body,html{
                font-family: "Roboto", sans-serif;
                font-weight: 700;
                font-style: normal;
        }
        .row {
            margin-right: 0;
            margin-left: 0;
        }
        @page {
            /* size: 3in 10in; */
            /* margin: 0; */
        }
        .text-right{
            text-align: right !important;
        }
        .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > thead > tr > th {
            border: 1px solid #000;
            /* font-size: 10px; */
            padding: 2px;
            /* text-align: center; */
        }
        .card{
            border: 1px solid transparent;
        }
        .card-body{
            padding: 0px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <!-- /.card-header -->
                <div class="card-body" id="printAreaChallan">
                    <div class="row">
                        <div class="col-2">
                            <img height="70px" src="{{ asset('img/logo.jpg') }}" alt="">
                        </div>
                        <div class="col-8 text-center">
                            <h4 class="text-center m-3"><b
                                    style="border:2px solid black;padding:5px; border-radius:3%;">SALE INVOICE</b></h4>
                        </div>
                        <div class="col-2">

                        </div>
                    </div>
                    <div class="row pt-3 pb-3">
                        <div class="col-6 text-left">
                            <div style="border: 1px solid black;padding:10px;">
                                <h6><b>Invoice No : </b> {{ $order->order_no ?? '' }} </h6>
                                <h6><b>Invoice Date : </b>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</h6>
                                <h6><b>Created Date & Time : </b>{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y, h:i:s A') }}</h6>
                                <h6><b>Updated Date & Time : </b>{{ \Carbon\Carbon::parse($order->updated_at)->format('d-m-Y, h:i:s A') }}</h6>
                                {{-- <h6><b>Received By : </b>{{ $order->received_by }}</h6> --}}

                            </div>
                        </div>
                        <div class="col-6 text-left">
                            <div style="border: 1px solid black;padding:10px;">
                                <h6><b>Customer Name :</b> {{ $order->customer->name??'' }}</h6>
                                <h6><b>Customer Mobile :</b>{{ $order->customer->mobile_no ?? '' }}</h6>
                                <h6><b>Customer Address :</b> {{ $order->customer->address ?? '' }}</h6>
                                <h6><b>Invoice By User :</b> {{ $order->user->name ?? '' }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-3 pb-3">
                        <div class="col-6 text-left">
                            <div style="border: 1px solid black;padding:10px;">
                                <h6><b>Billing Name : </b> {{ $order->orderInfo->pi_name ?? '' }} </h6>
                                <h6><b>Billing Mobile : </b>{{ $order->orderInfo->pi_phone_no ?? '' }}</h6>
                                <h6><b>Billing Email : </b>{{ $order->orderInfo->pi_email ?? '' }}</h6>
                            </div>
                        </div>
                        <div class="col-6 text-left">
                            <div style="border: 1px solid black;padding:10px;">
                                <h6><b>Shipping City : </b> {{ $order->orderInfo->shipping_city ?? '' }} </h6>
                                <h6><b>Shipping Country : </b>{{ $order->orderInfo->country ?? '' }}</h6>
                                <h6><b>Shipping Address : </b>{{ $order->orderInfo->shipping_address ?? '' }}</h6>
                            </div>
                        </div>
                    </div>
                    @if (count($order->products) > 0)
                    <div class="row" style="padding-left: 11px; padding-right: 11px;">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Product Code </th>
                                <th>Pack Size/U</th>
                                <th>Selling Price/U</th>
                                <th>Discount Price/U</th>
                                <th>Pack Qty</th>
                                @if ($order->return_status == 1)
                                    <th>Return Qty</th>
                                    <th>Return Price</th>
                                @endif
                                <th>Total Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $totalSalesPrice = 0;
                                $totalDiscountPrice = 0;
                                $totalQuantity = 0;
                                $totalReturnQuantity = 0;
                                $totalReturnAmount = 0;
                            @endphp
                        @foreach($order->products as $product)
                                <tr>
                                    <td>{{ $product->product->name ?? ''}}</td>
                                    <td>{{ $product->product->code ?? ''}}</td>
                                    <td>{{ number_format($product->pack_size, 2) }}</td>
                                    {{-- <td>৳{{ number_format($product->buy_price, 2) }}</td> --}}
                                    <td>৳{{ number_format($product->unit_price, 2) }}</td>
                                    <td>৳{{ number_format($product->discount_amt ?? 0, 2) }}</td>
                                    <td class="text-center">{{ $product->pack_quantity.''.$product->product->unit->name }}</td>
                                    @if ($order->return_status == 1)
                                        <td class="text-center">{{ $product->return_quantity.''.$product->product->unit->name }}</td>
                                        <td class="text-center">৳{{ number_format($product->return_quantity*$product->unit_price, 2) }}</td>
                                    @endif
                                    <td class="text-center">৳{{ number_format($product->total, 2) }}</td>
                                </tr>
                                @php
                                    $totalSalesPrice += $product->selling_price * $product->quantity;
                                    $totalDiscountPrice += $product->discount_amt ?? 0;
                                    $totalQuantity += $product->pack_quantity;
                                    $totalReturnQuantity += $product->return_quantity;
                                    $totalReturnAmount += $product->return_quantity*$product->unit_price;
                                @endphp
                            @endforeach
                                <tr>
                                    <th class="text-right" colspan="4">Sub Total</th>
                                    <th class="text-center">{{ number_format($totalDiscountPrice,2) }}</th>
                                    <th class="text-center">{{ number_format($totalQuantity) }}</th>
                                    @if ($order->return_status == 1)
                                        <th class="text-center">{{ number_format($totalReturnQuantity) }}</th>
                                        <th class="text-center">৳{{ number_format($totalReturnAmount,2) }}</th>
                                    @endif
                                    <th class="text-center">৳{{ number_format($order->sub_total,2) }}</th>
                                </tr>
                                
                                <tr>
                                    <th class="text-right" colspan="{{ $order->return_status == 1?'7':'5' }}">Discount</th>
                                    <th class="text-center" colspan="2">৳{{  number_format($order->discount, 2)}}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="{{ $order->return_status == 1?'7':'5' }}">Total Amount</th>
                                    <th class="text-center" colspan="2">৳{{  number_format($order->total, 2)}}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="{{ $order->return_status == 1?'7':'5' }}">Paid</th>
                                    <th class="text-center" colspan="2">৳{{  number_format($order->paid, 2)}}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="{{ $order->return_status == 1?'7':'5' }}">Due</th>
                                    <th class="text-center" colspan="2">৳{{  number_format($order->due, 2)}}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="{{ $order->return_status == 1?'7':'5' }}">Shipping</th>
                                    <th class="text-center" colspan="2">৳{{  number_format($order->shipping_charge, 2)}}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="{{ $order->return_status == 1?'7':'5' }}">Note</th>
                                    <th class="text-center" colspan="2">{{$order->note}}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="table-payments" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Transaction Method</th>
                                    <th>Bank</th>
                                    <th>Branch</th>
                                    <th>Account</th>
                                    {{-- <th>Received By</th> --}}
                                    <th>Amount</th>
                                    <th>Note</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($order->payments as $payment)
                                    <tr>
                                        <td>{{ date('Y-m-d',strtotime($payment->date)) }}</td>
                                        <td>
                                            @if($payment->type == 1)
                                                Pay
                                            @elseif($payment->type == 2)
                                                Refund
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->transaction_method == 1)
                                                Cash
                                            @elseif($payment->transaction_method == 3)
                                                Mobile Banking ({{ $payment->mobileBanking->account_name??'' }} - {{ $payment->mobileBanking->account_no??'' }})
                                            @else
                                                Bank
                                            @endif
                                        </td>
                                        <td>{{ $payment->bank ? $payment->bank->name : '' }}</td>
                                        <td>{{ $payment->branch ? $payment->branch->name : '' }}</td>
                                        <td>{{ $payment->account ? $payment->account->account_no : '' }}</td>
                                        {{-- <td>{{ $payment->user->name ?? '' }}</td> --}}
                                        <td>৳{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->note }}</td>
                                        {{-- <td>
                                            <a href="#" data-order_id="{{ $order->id }}" data-payment_id="{{ $payment->id }}" data-name="{{ $order->customer->name }}" data-payment_type="{{ $payment->transaction_method }}" data-pay_amount="{{ $payment->amount }}" data-bank_account="{{ $payment->account ? $payment->account->id : '' }}" data-mobile_banking="{{ $payment->mobileBanking->id??'' }}" class="btn btn-primary btn-sm pay-edit-btn"><i class="fas fa-edit"></i></a>
                                            <a href="{{ route('ebook_order.payment_details', ['payment' => $payment->id]) }}" class="btn btn-primary btn-sm">Details</a>
                                        </td> --}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<script>
    window.print();
    window.onafterprint = function () {
        window.history.back();
    };
</script>

</body>
</html>
