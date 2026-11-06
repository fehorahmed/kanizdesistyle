@extends('layouts.app')
@section('title','Sales Order Details')
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
            @page {
                size: auto;
                margin: 1in !important;
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
                    <a href="#" role="button" onclick="getprintChallan('printAreaChallan')" class="btn btn-primary bg-gradient-primary btn-sm">XL Print <i class="fa fa-print"></i></a>
                    <a href="{{ route('sales.sm_details',['sale'=>$sale->id]) }}" target="_blank" class="btn btn-primary bg-gradient-primary btn-sm">SM Print <i class="fa fa-print"></i></a>
                    {{-- <a href="{{ route('sales.details_print') }}" target="_blank" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    <a href="{{ route('sales.details_print2') }}" target="_blank" class="btn btn-primary bg-gradient-primary btn-sm">Print2 <i class="fa fa-print"></i></a> --}}
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive" id="printAreaChallan">
                        @include('layouts.partial.__report_header')
                        <div class="row pt-3 pb-3" >
                            <div class="col-3">
                                <h5 class="text-center m-0">Employee Ref.: {{ $sale->employee->name ?? '' }}</h5>
                            </div>
                            <div class="col-3">
                                <h5 class="text-center m-0">Customer: {{ $sale->customer->name ?? '' }}</h5>
                            </div>
                            <div class="col-3">
                                <h5 class="text-center m-0">Order No. {{ $sale->order_no }}</h5>
                            </div>
                            <div class="col-3">
                                <h5 class="text-center m-0">Date: {{ \Carbon\Carbon::parse($sale->date)->format('d-m-Y') }}</h5>
                            </div>
                        </div>
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center">S/L</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Sub Category</th>
                                <th class="text-center">Color</th>
                                <th class="text-center">Size</th>
                                <th class="text-center">Product</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">Barcode</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-center">Total Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $totalSalesPrice = 0;
                            @endphp
                            @foreach($sale->saleOrderItems as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-left">{{ $item->product->brand->name ?? '' }}</td>
                                    <td class="text-left">{{ $item->product->productCategory->name ?? '' }}</td>
                                    <td class="text-left">{{ $item->product->productSubCategory->name ?? '' }}</td>
                                    <td class="text-left">{{ $item->product->productColor->name ?? '' }}</td>
                                    <td class="text-left">{{ $item->product->productSize->name ?? '' }}</td>
                                    <td>{{ $item->product->name ?? '' }}</td>
                                    <td class="text-center">{{ $item->product->unit->name ?? '' }}</td>
                                    <td class="text-center">{{ $item->product_code }}</td>
                                    <td class="text-center">{{ number_format($item->quantity) }}</td>
                                    <td class="text-right">{{ number_format($item->selling_unit_price,2) }}</td>
                                    <td class="text-right">{{ number_format($item->quantity * $item->selling_unit_price,2) }}</td>
                                </tr>
                                @php
                                    $totalSalesPrice += $item->quantity * $item->selling_unit_price;
                                @endphp
                            @endforeach
                                <tr>
                                    <th class="text-right" colspan="9">Sub Total</th>
                                    <th class="text-center">{{ number_format($sale->saleOrderItems->sum('quantity')) }}</th>
                                    <th class="text-right" colspan="2">{{ number_format($sale->sub_total,2) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="10">Discount({{$sale->discount_percent}}%)</th>
                                    <th class="text-right" colspan="2">{{ number_format($sale->discount,2) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="10">VAT({{ $sale->vat_percent }}%)</th>
                                    <th class="text-right" colspan="2">{{ number_format($sale->vat_amount,2) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="10">Total</th>
                                    <th class="text-right" colspan="2">{{ number_format($sale->total,2) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="10">Paid</th>
                                    <th class="text-right" colspan="2">{{ number_format($sale->paid,2) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="10">Due</th>
                                    <th class="text-right" colspan="2">{{ number_format($sale->due,2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row signature-area" >
                            <div class="col text-center">
                                    <span style="border: 1px solid transparent !important;display: block;padding: 18px;font-size: 20px;font-weight: bold">
                                        {{ $sale->user->name ?? '' }}<br>
                                         Created By

                                    </span>
                            </div>
                            <div class="col text-center">
                                    <span style="border: 1px solid transparent !important;display: block;padding: 18px;font-size: 20px;font-weight: bold">
                                        <br>
                                        Checked By
                                    </span>
                            </div>
                        </div>
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
            document.title = "{{ $sale->order_no }}_sales_details";
            $(".extra-remove").remove();
            $(".footer-print-area").show();
            $('body').html($('#' + print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
