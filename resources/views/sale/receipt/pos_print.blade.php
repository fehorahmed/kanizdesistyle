<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>{{ $saleOrder->order_no }}</title>
    <style>
        table.table-bordered {
            border: transparent !important;
        }
        table.table-bordered th {
            border: transparent !important;
        }

        table.table-bordered td {
            border: transparent !important;
        }

        .product-table th, .table-summary th {
            padding: 2px !important;
            text-align: center !important;
        }

        .product-table td, .table-summary td {
            padding: 2px !important;
            text-align: center !important;
        }
        .row {
            margin-right: 0;
            margin-left: 0;
        }
        @page {
            size: 3.5in 10in;
            margin: 0;
        }

        .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > thead > tr > th {
            border: 1px solid #ddd;
            font-size: 12px;
            padding: 1px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div style="margin-left:90px;margin-bottom:5px;">
                <img style="height: 100px;margin: 0" src="{{ asset('img/logo.jpg') }}" alt="">
            </div>
            <p style="margin: 0;font-size: 15px;text-align: center">Ivoice Time: {{ date('d-m-Y h:i:s A') }}</p>
            <p style="margin: 0;font-size: 15px;text-align: center">
                {{ config('app.address') }}<br>
                {{ config('app.contact') }}<br>
                {{ config('app.email') }}<br>
            </p>
            <p style="margin: 0 2px;border-bottom: 1px dashed #000"></p>
            <p style="margin: 5px 0;text-align: center;font-size: 18px">INVOICE</p>
            <p style="margin: 0 2px;border-bottom: 1px dashed #000"></p>
            <p style="margin: 5px 0;text-align: left;font-size: 15px">Inv No. {{ $saleOrder->order_no }}</p>
            <p style="margin: 5px 0;text-align: left;font-size: 15px">Date: {{ $saleOrder->date }} <span style="margin-left: 82px">Time: {{ $saleOrder->updated_at->format('H:i') }}</span></p>
            <p style="margin: 5px 0;text-align: left;font-size: 15px"><b>Customer Details</b></p>
            <p style="margin: 5px 0;text-align: left;font-size: 15px">Name : {{ $saleOrder->customer->name ?? '' }}</p>
            <p style="margin: 5px 0;text-align: left;font-size: 15px">Address :  {{ $saleOrder->customer->address ?? '' }}</p>
            <p style="margin: 5px 0;text-align: left;font-size: 15px">Contact No : {{ $saleOrder->customer->mobile_no ?? '' }}</p>
        </div>

    </div>
    <div class="row">
        <div class="col-12" style="overflow: hidden !important;">
            @if(count($saleOrder->products) > 0)
                <table class="table table-bordered"  style="width: 100% !important;border-collapse: collapse;">
                    <tr>
                        <td width="2%">Sl</td>
                        <td>Item <br> Details</td>
                        <td width="7%">Qty.</td>
                        <td width="9%">Rate</td>
                        <td width="10%">Total</td>
                    </tr>
                    @php
                    $totalSalesPrice = 0;
                    $totalDiscountPrice = 0;
                    $totalQuantity = 0;
                    @endphp
                    @foreach($saleOrder->products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td  style="text-align: left">
                                {{ $product->product->name ?? '' }}
                            </td>
                            <td>{{ $product->quantity ?? ''}}</td>
                            <td style="text-align: right">{{ number_format($product->unit_price) }}</td>
                            <td style="text-align: right">{{ number_format($product->total,2) }}</td>
                        </tr>
                        @php
                            $totalSalesPrice += $product->selling_price * $product->quantity;
                            $totalDiscountPrice += $product->discount_amt ?? 0;
                            $totalQuantity += $product->quantity;
                        @endphp
                    @endforeach
                    <tr>
                        <td colspan="4" style="text-align: left !important;">Bil Qty</td>
                        <td style="text-align: right !important;">
                            {{ $saleOrder->products->sum('pack_quantity') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: left !important;">Sub Total</td>
                        <td colspan="" style="text-align: right !important;">
                            {{ number_format($saleOrder->sub_total,2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: left !important;">Shipping</td>
                        <td colspan="" style="text-align: right !important;">
                            {{ number_format($saleOrder->shipping_charge,2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: left !important;">Discount</td>
                        <td colspan="" style="text-align: right !important;">
                            {{ number_format($saleOrder->discount,2) }}
                        </td>
                    </tr>
                    {{-- <tr class="d-none">
                        <td colspan="4" style="text-align: left !important;">Vat</td>
                        <td colspan="" style="text-align: right !important;">
                            {{ number_format($saleOrder->vat_amount,2) }}
                        </td>
                    </tr> --}}
                    <tr>
                        <td colspan="4" style="text-align: left !important;">Net Payable</td>
                        <td colspan="" style="text-align: right !important;">
                            {{ number_format($saleOrder->total,2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: left !important;">Paid</td>
                        <td colspan="" style="text-align: right !important;">
                            {{ number_format($saleOrder->paid,2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: left !important;">Due</td>
                        <td colspan="" style="text-align: right !important;">
                            {{ number_format($saleOrder->due,2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: left !important;">Payment Type</td>
                        <td colspan="" style="text-align: right !important;">
                            @if(!empty($saleOrder->payment->transaction_method) && $saleOrder->payment->transaction_method==1)
                               Cash
                            @elseif(!empty($saleOrder->payment->transaction_method) && $saleOrder->payment->transaction_method==2)
                               Bank
                            @elseif(!empty($saleOrder->payment->transaction_method) && $saleOrder->payment->transaction_method==3)
                                {{ $saleOrder->payment->mobileBanking->account_name??'' }} - {{ $saleOrder->payment->mobileBanking->account_no??'' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: left !important;">{{ $saleOrder->amount_in_word }} Only.</td>
                    </tr>
                </table>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center">
            <p style="font-weight: bold;font-size: 15px;text-align: center">Thanks for shopping</p>
        </div>
        <div class="col-12 text-center">
            <div style="margin-left:105px;margin-bottom:5px;">
                <img style="height: 100px;margin: 0" src="{{ asset('img/qr_code.png') }}" alt="">
            </div>
        </div>
    </div>
</div>

<script>
    window.print();
    window.onafterprint = function () {
        window.close()
    };
</script>
</body>
</html>
