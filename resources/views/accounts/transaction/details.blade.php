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
                    {{-- <a href="{{ route('sales.ient-primary btn-sm">SM Print <i class="fa fa-print"></i></a> --}}
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive" id="printAreaChallan">
                        <div class="row">
                            <div class="col-2">
                                <img height="70px" src="{{ asset('img/logo.jpg') }}" alt="">
                            </div>
                            <div class="col-8 text-center">
                                <h4 class="text-center m-3"><b style="border:2px solid black;padding:5px; border-radius:3%;">RECEIPT</b></h4>
                            </div>
                            <div class="col-2">
                                <b>Date : </b>  {{ \Carbon\Carbon::parse($transaction->date)->format('d-M-Y') }}<br>
                                <b>No  &nbsp;&nbsp;&nbsp;&nbsp;: </b>  {{ $transaction->id + 1000 }}
                            </div>
                        </div>

                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="20%">For Payment of</th>
                                    <td>
                                        {{ $transaction->accountHeadType->name.' - '.$transaction->accountHeadSubType->name }}
                                    </td>
                                    <th width="10%">Amount</th>
                                    <td width="15%">৳{{ number_format($transaction->amount, 2) }}</td>
                                </tr>

                                <tr>
                                    <th>Amount (In Word)</th>
                                    <td colspan="3">{{ $transaction->amount_in_word }}</td>
                                </tr>

                                <tr>
                                    <th>Paid By</th>
                                    <td colspan="3">
                                        @if($transaction->transaction_method == 1)
                                            Cash
                                        @elseif($transaction->transaction_method == 3)
                                            Mobile Banking
                                        @else
                                            Bank - {{ $transaction->bank->name.' - '.$transaction->branch->name.' - '.$transaction->account->account_no }}
                                        @endif
                                    </td>
                                </tr>

                                @if($transaction->transaction_method == 2)
                                    <tr>
                                        <th>Cheque No.</th>
                                        <td colspan="3">{{ $transaction->cheque_no }}</td>
                                    </tr>
                                @endif

                                <tr>
                                    <th>Note</th>
                                    <td colspan="3">{{ $transaction->note }}</td>
                                </tr>

                                @if($transaction->transaction_method == 2)
                                    <tr>
                                        <th>Cheque Image</th>
                                        <td colspan="3" class="text-center">
                                            <img src="{{ asset($transaction->cheque_image) }}" height="300px">
                                        </td>
                                    </tr>
                                @endif
                            </table>
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
            document.title = "payment details";
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
