@extends('layouts.app')
@section('title', ' Sale Receipt Details')
@section('style')
    <style>
        .table-bordered td,
        .table-bordered th {
            border: 1px solid #000000 !important;
            padding: 2px;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #000000 !important;
        }

        .customer-area-border {
            border: 1.5px solid #000 !important;
            ;
            padding: 3px;
            min-height: 128px;
            margin-top: 10px;
            margin-bottom: 50px;
        }

        .customer-area-border p {
            font-size: 20px !important;
        }

        .table-bordered td,
        .table-bordered th {
            font-size: 18px !important;
        }

        @media print {

            .table-bordered td,
            .table-bordered th {
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

            .signature-area {
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
                    <a href="{{route('pos_print',['saleOrder'=>$order->id])}}" target="_blank" class="btn btn-primary bg-gradient-indigo btn-sm">POS Print <i class="fa fa-print"></i></a>
                    {{-- <a href="{{ route('sales.sm_details',['sale'=>$sale->id]) }}" target="_blank" class="btn btn-primary bg-gradient-primary btn-sm">SM Print <i class="fa fa-print"></i></a> --}}
                </div>
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
                                <h6><b>Customer Name :</b> {{ $order->customer->name }}</h6>
                                <h6><b>Customer Mobile :</b>{{ $order->customer->mobile_no ?? '' }}</h6>
                                <h6><b>Customer Address :</b> {{ $order->customer->address ?? '' }}</h6>
                                <h6><b>Invoice By User :</b> {{ $order->user->name ?? '' }}</h6>
                            </div>
                        </div>
                    </div>
                    @if (count($order->products) > 0)
                    <table id="table" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Product Code </th>
                            {{-- <th>Buy Price/U</th> --}}
                            <th>Selling Price/U</th>
                            <th>Discount Price/U</th>
                            <th class="d-none">Vat/U</th>
                            <th>Qty</th>
                            @if ($order->is_return == 1)
                                <th>Return Qty</th>
                                {{-- <th>Return Price</th> --}}
                            @endif
                            <th>Total Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $totalSalesPrice = 0;
                            $totalDiscountPrice = 0;
                            $totalVatAmount = 0;
                            $totalQuantity = 0;
                            $totalReturnQuantity = 0;
                            $totalReturnAmount = 0;
                        @endphp
                       @foreach($order->products as $product)
                            <tr>
                                <td>{{ $product->product->name ?? ''}}</td>
                                <td>{{ $product->product->code ?? ''}}</td>
                                {{-- <td>৳{{ number_format($product->buy_price, 2) }}</td> --}}
                                <td>৳{{ number_format($product->unit_price, 2) }}</td>
                                <td>৳{{ number_format($product->discount_amt ?? 0, 2) }}</td>
                                <td class="d-none">৳{{ number_format($product->vat_amount ?? 0, 2) }}</td>
                                <td class="text-center">{{ $product->quantity.''.$product->product->unit->name }}</td>
                                @if ($order->is_return == 1)
                                    <td class="text-center">{{ $product->return_quantity.''.$product->product->unit->name }}</td>
                                    {{-- <td class="text-center">৳{{ number_format($product->return_quantity*$product->unit_price, 2) }}</td> --}}
                                @endif
                                <td class="text-center">৳{{ number_format($product->total, 2) }}</td>
                            </tr>
                            @php
                                $totalSalesPrice += $product->selling_price * $product->quantity;
                                $totalDiscountPrice += $product->discount_amt ?? 0;
                                $totalVatAmount += $product->vat_amount ?? 0;
                                $totalQuantity += $product->quantity;
                                $totalReturnQuantity += $product->return_quantity;
                                $totalReturnAmount += $product->return_quantity*$product->unit_price;
                            @endphp
                        @endforeach
                            <tr>
                                <th class="text-right" colspan="2">Sub Total</th>
                                <th class="text-center">{{ number_format($totalDiscountPrice,2) }}</th>
                                <th class="text-center">{{ number_format($totalVatAmount,2) }}</th>
                                <th class="text-center">{{ number_format($totalQuantity) }}</th>
                                @if ($order->is_return == 1)
                                    <th class="text-center">{{ number_format($totalReturnQuantity) }}</th>
                                    {{-- <th class="text-center">৳{{ number_format($totalReturnAmount,2) }}</th> --}}
                                @endif
                                <th class="text-center">৳{{ number_format($order->sub_total,2) }}</th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="{{ $order->is_return == 1?'5':'4' }}">@if ($order->is_return == 1) Previous @endif Discount(-)</th>
                                <th class="text-center" colspan="2">৳{{  number_format($order->discount, 2)}}</th>
                            </tr>
                            <tr class="d-none">
                                <th class="text-right" colspan="{{ $order->is_return == 1?'5':'4' }}">Vat</th>
                                <th class="text-center" colspan="2">৳{{  number_format($order->vat_amount, 2)}}</th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="{{ $order->is_return == 1?'5':'4' }}">Total Amount</th>
                                <th class="text-center" colspan="2">৳{{  number_format($order->total, 2)}}</th>
                            </tr>
                            @if ($order->is_return == 1)
                                <tr>
                                    <th class="text-right" colspan="{{ $order->is_return == 1?'5':'4' }}">Return(-)</th>
                                    <th class="text-center" colspan="2">৳{{  number_format($order->return_amount, 2)}}</th>
                                </tr>
                                {{-- <tr>
                                    <th class="text-right" colspan="{{ $order->is_return == 1?'7':'5' }}">Discount(After Return)(-)</th>
                                    <th class="text-center" colspan="2">৳{{  number_format($order->discount - $order->return_discount, 2)}}</th>
                                </tr> --}}
                                <tr>
                                    <th class="text-right" colspan="{{ $order->is_return == 1?'5':'4' }}">Deduction(+)</th>
                                    <th class="text-center" colspan="2">৳{{  number_format($order->deduction, 2)}}</th>
                                </tr>
                            @endif
                            <tr>
                                <th class="text-right" colspan="{{ $order->is_return == 1?'5':'4' }}">Paid</th>
                                <th class="text-center" colspan="2">৳{{  number_format($order->paid, 2)}}</th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="{{ $order->is_return == 1?'5':'4' }}">Due</th>
                                <th class="text-center" colspan="2">৳{{  number_format($order->due, 2)}}</th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="{{ $order->is_return == 1?'5':'4' }}">Note</th>
                                <th class="text-center" colspan="2">{{$order->note}}</th>
                            </tr>
                        </tbody>
                    </table>
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
                                    <th>Received By</th>
                                    <th>Amount</th>
                                    <th>Note</th>
                                    <th>Action</th>
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
                                        <td>{{ $payment->user->name ?? '' }}</td>
                                        <td>৳{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->note }}</td>
                                        <td>
                                            <a href="#" data-order_id="{{ $order->id }}" data-payment_id="{{ $payment->id }}" data-name="{{ $order->customer->name }}" data-payment_type="{{ $payment->transaction_method }}" data-pay_amount="{{ $payment->amount }}" data-bank_account="{{ $payment->account ? $payment->account->id : '' }}" data-mobile_banking="{{ $payment->mobileBanking->id??'' }}" class="btn btn-primary btn-sm pay-edit-btn"><i class="fas fa-edit"></i></a>
                                            <a href="{{ route('sale_receipt.payment_details', ['payment' => $payment->id]) }}" class="btn btn-primary btn-sm">Details</a>
                                        </td>
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
    <div class="modal fade" id="pay-edit">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Payment Edit Information</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="pay-edit-form" enctype="multipart/form-data" name="pay-edit-form">
                        <input type="hidden" name="sale_order_id" id="sale_order_id">
                        <input type="hidden" name="sale_payment_id" id="sale_payment_id">
                        <input type="hidden" name="customer_id" id="cutomer_id">
                        <div class="form-group">
                            <label>Name</label>
                            <input class="form-control" id="cutomer-name" disabled>
                        </div>
                        <div class="form-group">
                            <label>Payment Type</label>
                            <select class="form-control" id="pay-type" name="payment_type">
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                                <option value="3">Mobile Banking</option>
                            </select>
                        </div>
                        <div id="mobile-bank-info">
                            <div class="form-group">
                                <label>Mobile Banking Account</label>
                                <select class="form-control" id="mobile_banking" name="mobile_banking">
                                    <option value="">Select Account</option>
                                    @foreach ($mobileBankings as $mobileBanking)
                                        <option value="{{ $mobileBanking->id }}">
                                            {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="bank-info">
                            <div class="form-group">
                                <label>Account</label>
                                <select class="form-control modal-account" id="account" name="account">
                                    <option value="">Select Account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->account_no }} -
                                            {{ $account->branch->name??'' }} -
                                            {{ $account->bank->name??'' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="form-group">
                                <label>Cheque No.</label>
                                <input class="form-control" type="text" name="cheque_no" placeholder="Enter Cheque No.">
                            </div>

                            <div class="form-group">
                                <label>Cheque Image</label>
                                <input class="form-control" name="cheque_image" type="file">
                            </div> --}}
                        </div>

                        <div class="form-group">
                            <label>Amount</label>
                            <input class="form-control" name="amount" id="amount" placeholder="Enter Amount">
                        </div>

                        {{-- <div class="form-group">
                            <label>Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control date-picker" name="date" value="{{ date('d-m-Y') }}" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div> --}}

                        <div class="form-group">
                            <label>Note</label>
                            <input class="form-control" name="note" placeholder="Enter Note">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="pay-edit-submit-btn">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function(){
            $('body').on('click', '.pay-edit-btn', function (e) {
                e.preventDefault();
                var customerId = $(this).data('id');
                var saleType = $('#modal-sale-type').val();
                var customerName = $(this).data('name');
                var payment_type = $(this).data('payment_type');
                var pay_amount = $(this).data('pay_amount');
                var bank_account = $(this).data('bank_account');
                var mobile_banking = $(this).data('mobile_banking');
                var order_id = $(this).data('order_id');
                var payment_id = $(this).data('payment_id');
                $('#pay-type').val(payment_type).trigger('change');
                $('#amount').val(pay_amount);
                $('#cutomer-name').val(customerName);
                $('#account').val(bank_account);
                $('#mobile_banking').val(mobile_banking);
                $('#sale_order_id').val(order_id);
                $('#sale_payment_id').val(payment_id);

                $('#pay-edit').modal('show');
            });
            $('#pay-type').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#bank-info').hide();
                    $('#mobile-bank-info').hide();
                    if($(this).val() == '3'){
                        $('#mobile-bank-info').show();
                    }
                } else {
                    $('#bank-info').show();
                    $('#mobile-bank-info').hide();
                }
            });
            $('#pay-type').trigger('change');

            $('#pay-edit-submit-btn').click(function () {
                var formData = new FormData($('#pay-edit-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('sales_order_payment.edit') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-pay').modal('hide');
                            Swal.fire(
                                'Update!',
                                response.message,
                                'success'
                            ).then((result) => {
                                //location.reload();
                                window.location.href = response.redirect_url;
                            });
                        } else {
                            Swal.fire({ 
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    }
                });
            });
        });
        var APP_URL = '{!! url()->full() !!}';

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
