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

        #pageHeader {
            position: running(pageHeader);
        }

        table.table-bordered {
            border: 1px solid black !important;
            margin-top: 20px;
        }

        table.table-bordered th {
            border: 1px solid black !important;
        }

        table.table-bordered td {
            border: 1px solid black !important;
        }

        .product-table th,
        .table-summary th {
            padding: 2px !important;
            text-align: center !important;
        }

        .product-table td,
        .table-summary td {
            padding: 2px !important;
            text-align: center !important;
        }

        @media screen {
            body {
                background: #dddddd;
            }

            .container {
                background: #fff;
            }

            .print_btn {
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
            <div class="col-xs-4">
                <img src="{{ asset('img/logo.jpg') }}" height="50px" style="float: left">
                <h2 style="margin: 0px; float: left">RECEIPT</h2>
            </div>

            <div class="col-xs-4 text-center">
                <b>Date: </b> {{ $payment->date->format('d-M-Y') }}
            </div>

            <div class="col-xs-4 text-right">
                <b>No: </b> {{ $payment->id + 1000 }}
            </div>
        </div>

        <div class="row" style="margin-top: 20px">
            <div class="col-xs-12">
                <table class="table table-bordered">
                    <tr>
                        <th width="20%">
                            @if($payment->type == 1)
                                To
                            @elseif($payment->type == 2)
                                From
                            @endif
                        </th>
                        <td>{{ $payment->purchaseOrder->supplier->name }}</td>
                        <th width="10%">Amount</th>
                        <td width="15%">৳{{ number_format($payment->amount, 2) }}</td>
                    </tr>

                    <tr>
                        <th>Amount (In Word)</th>
                        <td colspan="3">{{ $payment->amount_in_word }}</td>
                    </tr>

                    <tr>
                        <th>For Payment of</th>
                        <td colspan="3">Order No. {{ $payment->purchaseOrder->order_no }}</td>
                    </tr>

                    <tr>
                        <th>Paid By</th>
                        <td colspan="3">
                            @if($payment->transaction_method == 1)
                                Cash
                            @elseif($payment->transaction_method == 3)
                                Mobile Banking
                            @else
                                Bank - {{ $payment->bank->name.' - '.$payment->branch->name.' - '.$payment->account->account_no }}
                            @endif
                        </td>
                    </tr>

                    @if($payment->transaction_method == 2)
                        <tr>
                            <th>Cheque No.</th>
                            <td colspan="3">{{ $payment->cheque_no }}</td>
                        </tr>
                    @endif

                    <tr>
                        <th>Note</th>
                        <td colspan="3">{{ $payment->note }}</td>
                    </tr>
                </table>
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
