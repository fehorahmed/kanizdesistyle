@extends('layouts.app')
@section('title','Purchase Order Details')
@section('style')
    <style>
        .table-bordered td, .table-bordered th {
            border: 1px solid #000000 !important;
            padding: 2px;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #000000 !important;;
        }
        .customer-area-border {
            border: 1.5px solid #000 !important;;
            padding: 3px;
            min-height: 128px;
            margin-top: 10px;
            margin-bottom: 50px;
        }
        .customer-area-border p {
            font-size: 20px !important;
        }
        .table-bordered td, .table-bordered th {
            font-size: 18px !important;
        }
        @media print {
            .table-bordered td, .table-bordered th {
                font-size: 18px !important;
            }
            img {
                display: block !important;
                max-width: 100% !important;
                height: auto !important;
            }
            @page {
                size: auto;
                margin: .5in !important;
            }
            .signature-area{
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
            }
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <a href="#" role="button" onclick="getprintChallan('printAreaChallan')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    @if(!empty($order->purchaseOrder))
                    <a href="{{ route('purchase_receipt.edit',['order'=>$order->id]) }}" class="btn btn-primary bg-gradient-primary btn-sm">Edit <i class="fa fa-edit"></i></a>
                    @endif
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive" id="printAreaChallan">
                        <div class="row">
                            <div class="col-2">
                                <img height="70px" src="{{ asset('img/logo.jpg') }}" alt="">
                            </div>
                            <div class="col-8 text-center">
                                <h4 class="text-center m-3"><b style="border:2px solid black;padding:5px; border-radius:3%;">PURCHASE INVOICE</b></h4>
                            </div>
                            <div class="col-2">

                            </div>
                        </div>
                        <div class="row pt-3 pb-3" >
                            <div class="col-6 text-left">
                                <div style="border: 1px solid black;padding:10px;">
                                    <h6><b>Invoice No : </b> {{ $order->order_no ??'' }} </h6>
                                    <h6><b>Invoice Date : </b>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</h6>
                                    <h6><b>Purchase User : </b>{{ $order->user->name??'' }}</h6>
                                    <h6><b>Created Date & Time : </b>{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y, h:i:s A') }}</h6>
                                </div>
                            </div>
                            <div class="col-6 text-left">
                                <div style="border: 1px solid black;padding:10px;">
                                    <h6><b>Supplier Name :</b> {{ $order->supplier->name }}</h6>
                                    <h6><b>Supplier Mobile :</b>{{ $order->supplier->mobile }}</h6>
                                    <h6><b>Supplier Address :</b> {{ $order->supplier->mobile }}</h6>
                                    <h6><b>Updated Date & Time : </b>{{ \Carbon\Carbon::parse($order->updated_at)->format('d-m-Y, h:i:s A') }}</h6>
                                </div>
                            </div>
                        </div>
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Product Code </th>
                                <th>Cost Price/U</th>
                                <th>Selling Price/U</th>
                                <th>Qty</th>
                                <th>Total Purchase Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $totalSalesPrice = 0;
                                $totalQuantity = 0;
                            @endphp
                           @foreach($order->products as $product)
                                <tr>
                                    <td>{{ $product->product->name ?? ''}}</td>
                                    <td>{{ $product->product->code ?? ''}}</td>
                                    <td>৳{{ number_format($product->unit_price, 2) }}</td>
                                    <td>৳{{ number_format($product->selling_price, 2) }}</td>
                                    <td class="text-center">{{ $product->quantity.''.$product->product->unit->name }}</td>
                                    <td class="text-center">৳{{ number_format($product->total, 2) }}</td>
                                </tr>
                                @php
                                    $totalSalesPrice += $product->selling_price * $product->quantity;
                                    $totalQuantity += $product->quantity;
                                @endphp
                            @endforeach
                                <tr>
                                    <th class="text-right" colspan="4">Sub Total</th>
                                    <th class="text-center">{{ number_format($totalQuantity) }}</th>
                                    <th class="text-center">৳{{ number_format($order->sub_total,2) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="5">Discount</th>
                                    <th class="text-center" colspan="3">৳{{  number_format($order->discount, 2)}}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="5">Total Amount</th>
                                    <th class="text-center" colspan="3">৳{{  number_format($order->total, 2)}}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="5">Paid</th>
                                    <th class="text-center" colspan="3">৳{{  number_format($order->paid, 2)}}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="5">Due</th>
                                    <th class="text-center" colspan="3">৳{{  number_format($order->due, 2)}}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var APP_URL = '{!! url()->full()  !!}';
        function getprintChallan(print) {
            document.title = "purchase_details";
            $(".extra-remove").remove();
            $(".footer-print-area").show();
            $('body').html($('#' + print).html());
            window.print();
            window.location.replace(APP_URL)
        }
        function myFunction() {
            /* Get the text field */
            var copyText = document.getElementById("myInput");
            /* Select the text field */
            copyText.select();
            copyText.setSelectionRange(0, 99999); /* For mobile devices */
            /* Copy the text inside the text field */
            navigator.clipboard.writeText(copyText.value);

            /* Alert the copied text */
            alert("Copied link: " + copyText.value);
        }
    </script>
@endsection
