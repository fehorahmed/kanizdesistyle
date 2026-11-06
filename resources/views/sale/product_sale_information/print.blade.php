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

        .highlight-td {
            background-color: lightgrey !important;
        }
    </style>
</head>
<body>
<header id="pageHeader" style="margin-bottom: 10px">
    <div class="row">
        <div class="col-xs-2 col-xs-offset-1">
            <img src="{{ asset('img/logo.jpg') }}" width="100px">
        </div>

        <div class="col-xs-8">
            <p style="font-family: 'Times New Roman'; font-size: 30px; font-style: italic; margin: 0px">{{ config('app.name') }}</p>
            <p style="margin: 0px">
                26 No. Zella Porishad Market <br>
                Vanga Rastar Mor, Goalchamot, Faridpur <br>
                Mobile : 01973-722225
            </p>

        </div>
    </div>
</header>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 text-center" style="border: 1px solid black; padding: 5px; border-radius: 7px">
            <strong>Product Sale Information</strong>
        </div>
    </div>

    <div class="row" style="border: 1px solid black; margin-top: 3px">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-4">
                    <strong>Customer Name: </strong>{{ $saleOrder->customer->name }} <br>
                    <strong>Address: </strong>{{ $saleOrder->customer->address }} <br>
                    <strong>Mobile No. : </strong>{{ $saleOrder->customer->mobile_no }} <br>
                    <strong>Product Received By : </strong>{{ $saleOrder->received_by }}
                </div>

                <div class="col-xs-4">
                    <strong>Purchase Invoice No : </strong>{{ $purchaseOrder->order_no }} <br>
                    <strong>Date : </strong>{{ $purchaseOrder->date->format('d/m/Y') }} <br>
                    <strong>Supplier Name : </strong>{{ $purchaseOrder->supplier->name }} <br>
                    <strong>Supplier Mobile : </strong>{{ $purchaseOrder->supplier->mobile }}
                </div>

                <div class="col-xs-4 text-right">
                    <strong>Customer ID : </strong>{{ $saleOrder->customer->id }} <br>
                    <strong>Invoice No : </strong>{{ $saleOrder->order_no }} <br>
                    <strong>Date : </strong>{{ $saleOrder->date->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered product-table" style="margin-top: 5px; margin-bottom: 1px !important;">
    <thead>
    <tr>
        <th style="background-color: lightgrey !important;">#</th>
        <th style="background-color: lightgrey !important;">Product Name</th>
        <th style="background-color: lightgrey !important;">Serial Number</th>
        <th style="background-color: lightgrey !important;">Warr (Mon.)</th>
        <th style="background-color: lightgrey !important;">Qty</th>
        <th style="background-color: lightgrey !important;">Unit Price</th>
        <th style="background-color: lightgrey !important;">Amount</th>
    </tr>
    </thead>

    <tbody>
    @foreach($saleOrder->products as $product)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $product->pivot->name }}</td>
            <td style="{{ request()->get('serial') == $product->pivot->serial ? 'background-color: lightgrey !important;' : '' }}" ">{{ $product->pivot->serial }}</td>
            <td>{{ $product->pivot->warranty }}</td>
            <td>{{ $product->pivot->quantity.' '.$product->pivot->unit }}</td>
            <td>{{ number_format($product->pivot->unit_price, 2) }}</td>
            <td>{{ number_format($product->pivot->total, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="table-summary" style="width: 30%; float: right">
    <table class="table table-bordered" style="margin-top: 2px !important;">
        <tr>
            <th>Sub Total</th>
            <td>{{ number_format($saleOrder->sub_total, 2) }}</td>
        </tr>
        <tr>
            <th>Vat ({{ $saleOrder->vat_percentage }}%)</th>
            <td>{{ number_format($saleOrder->vat, 2) }}</td>
        </tr>
        <tr>
            <th>Discount</th>
            <td>{{ number_format($saleOrder->discount, 2) }}</td>
        </tr>
        <tr>
            <th>Total</th>
            <td>{{ number_format($saleOrder->total, 2) }}</td>
        </tr>
    </table>
</div>

<div class="text-center" style="clear: both">
    <strong>In Word: {{ $saleOrder->amount_in_word }} Only</strong>
</div>

<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
