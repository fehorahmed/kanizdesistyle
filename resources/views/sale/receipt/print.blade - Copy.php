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
    </style>
</head>
<body>
<header id="pageHeader" class="text-center" style="margin-bottom: 10px">
    <h2>{{ config('app.name') }}</h2>
    <p style="margin: 0px">26 No. Zella Parishad Market(First Floor), Vanga Rastar Mor, Goalchamot, Faridpur.</p>
    <strong>Email: </strong>nikhuthamim@gmail.com <br>
    <strong>Mobile: </strong>01973-722225, 01687676312, 01511-722225
</header>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-6">
            <table class="table table-bordered">
                <tr>
                    <th>Order No.</th>
                    <td>{{ $order->order_no }}</td>
                </tr>
                <tr>
                    <th>Order Date</th>
                    <td>{{ $order->date->format('d-M-Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="col-xs-6">
            <table class="table table-bordered">
                <tr>
                    <th>Name</th>
                    <td>{{ $order->customer_name }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $order->phone }}</td>
                </tr>
            </table>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Warranty (Month)</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($order->products as $product)
                        <tr>
                            <td>{{ $product->pivot->name }}</td>
                            <td>{{ $product->pivot->warranty }}</td>
                            <td>{{ $product->pivot->quantity.' '.$product->pivot->unit }}</td>
                            <td>৳{{ number_format($product->pivot->unit_price, 2) }}</td>
                            <td>৳{{ number_format($product->pivot->total, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-offset-6 col-xs-6">
            <table class="table table-bordered">
                <tr>
                    <th>Sub Total</th>
                    <td>৳{{ number_format($order->sub_total, 2) }}</td>
                </tr>
                <tr>
                    <th>Vat</th>
                    <td>৳{{ number_format($order->vat, 2) }}</td>
                </tr>
                <tr>
                    <th>Discount</th>
                    <td>৳{{ number_format($order->discount, 2) }}</td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td>৳{{ number_format($order->total, 2) }}</td>
                </tr>
                <tr>
                    <th>Paid</th>
                    <td>৳{{ number_format($order->paid, 2) }}</td>
                </tr>
                <tr>
                    <th>Due</th>
                    <td>৳{{ number_format($order->due, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>


<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
