<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!--Favicon-->
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon" />

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">

    <style>
        @page {
            @top-center {
                content: element(pageHeader);
            }
        }
        #pageHeader{
            position: running(pageHeader);
        }

        table.table-bordered{
            border:1px solid black !important;
            margin-top:20px;
        }
        table.table-bordered th{
            border:1px solid black !important;
        }
        table.table-bordered td{
            border:1px solid black !important;
        }

        .product-table th, .table-summary th {
            padding: 2px !important;
            text-align: center !important;
        }

        .product-table td, .table-summary td {
            padding: 2px !important;
            text-align: center !important;
        }

        @media screen {
            body{
                background: #dddddd;
            }
            .container{
                background: #fff;
            }
            .print_btn{
                margin: 10px 20px;
            }
            div.divFooter {
                display: none;
            }
        }
        @media print {
            div.divFooter {
                position: fixed;
                bottom: 0;
            }
        }

    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <div class="print_btn">
        <button class="btn btn-sm btn-success" onclick="printNow()"> Print Invoice </button>
    </div>
    <div class="row">
        <div class="col-xs-12 text-center" style="border: 1px solid black; padding: 3px; border-radius: 7px">
            <strong>Bon de Commande</strong>
        </div>
    </div>

    <div class="row" style="border: 1px solid black; margin-top: 3px; font-size: 12px">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-6">
                    <strong>Customer Name: </strong>{{ $order->customer->name??'' }} <br>
                    <strong>Address: </strong>{{ $order->customer->address??'' }} <br>
                                <span style="margin-left: 55px;">{{ $order->customer->post_code??'' }}</span> <br>
                                <span style="margin-left: 55px;">{{ $order->customer->area??'' }}</span> <br>
                    <strong>Mobile No. : </strong>{{ $order->customer->mobile_no??'' }} <br>
                    <strong>Order By : </strong>{{ $order->received_by }}
                </div>

                <div class="col-xs-6 text-right">
                    <strong>Customer ID : </strong>{{ $order->customer->id }} <br>
                    <strong>Order No : </strong>{{ $order->order_no }} <br>
                    <strong>Date : </strong>{{ $order->date->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @if(count($order->products) > 0)
            <table class="table table-bordered product-table" style="margin-top: 5px; margin-bottom: 1px !important; font-size: 12px">
                <thead>
                    <tr>
                        <th style="background-color: lightgrey !important;">#</th>
                        <th style="background-color: lightgrey !important;">Product Name</th>
                        <th style="background-color: lightgrey !important;">Product Code</th>
                        {{-- <th style="background-color: lightgrey !important;">Warr (Mon.)</th> --}}
                        <th style="background-color: lightgrey !important;">Qty</th>
                        <th style="background-color: lightgrey !important;">Unit Price</th>
                        <th style="background-color: lightgrey !important;">Amount</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($order->products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-left">{{ $product->pivot->name }}</td>
                        <td>{{ $product->pivot->serial }}</td>
                        {{-- <td>{{ $product->pivot->warranty }}</td> --}}
                        <td>{{ $product->pivot->quantity.' '.$product->pivot->unit }}</td>
                        <td>{{ number_format($product->pivot->unit_price, 2) }}</td>
                        <td>{{ number_format($product->pivot->total, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        @if(count($order->services) > 0)
            <table class="table table-bordered product-table" style="margin-top: 5px; margin-bottom: 1px !important; font-size: 12px">
                <thead>
                <tr>
                    <th style="background-color: lightgrey !important;">#</th>
                    <th style="background-color: lightgrey !important;">Name</th>
                    <th style="background-color: lightgrey !important;">Qty</th>
                    <th style="background-color: lightgrey !important;">Unit Price</th>
                    <th style="background-color: lightgrey !important;">Amount</th>
                </tr>
                </thead>

                <tbody>
                @foreach($order->services as $services)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $services->name }}</td>
                        <td>{{ $services->quantity }}</td>
                        <td>৳{{ number_format($services->unit_price, 2) }}</td>
                        <td>৳{{ number_format($services->total, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        <div class="table-summary" style="width: 60%; float: right; font-size: 12px">
            <table class="table table-bordered" style="margin-top: 2px !important;">
                <tr>
                    <th>Product Sub Total</th>
                    <td>{{ number_format($order->sub_total, 2) }}</td>
                </tr>
                <tr>
                    <th>Product TVA ({{ $order->vat_percentage }}%)</th>
                    <td>{{ number_format($order->vat, 2) }}</td>
                </tr>
                <tr>
                    <th>Product Discount</th>
                    <td>{{ number_format($order->discount, 2) }}</td>
                </tr>
                <tr>
                    <th colspan="1">Total</th>
                    <td>{{ number_format($order->total, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="text-center" style="clear: both">
            @if($order->service_vat > 0  || $order->vat > 0)
                TVA Money was given to the customer. <br>
            @endif
            <strong>In Word: {{ $order->amount_in_word }} Only</strong>
        </div>
    </div>

</div>


<script>
    function printNow() {
        $('.print_btn').hide();
        window.print();
        location.reload();
    }
    // window.print();
    // window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
