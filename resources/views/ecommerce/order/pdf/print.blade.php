<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->order_no }}</title>
    <style>
        /* General Styles */
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header img { height: 70px; }
        .section-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .no-border { border: none; }
    </style>
</head>
<body>
<div class="container">

    <!-- Header -->
    <div class="header">
        <table class="no-border">
            <tr class="no-border">
                <td class="no-border" style="width:70px;">
                    <img src="{{ public_path('img/logo.jpg') }}" alt="Logo">
                </td>
                <td class="no-border text-center">
                    <h2 style="margin:0;">SALE INVOICE</h2>
                    <p style="margin:0;">Invoice No: {{ $order->order_no }}</p>
                    <p style="margin:0;">Date: {{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</p>
                </td>
                <td class="no-border"></td>
            </tr>
        </table>
    </div>

    <!-- Customer Info -->
    <div class="customer-info">
        <p class="section-title">Customer Information</p>
        <table>
            <tr>
                <td><strong>Name:</strong> {{ $order->customer->name ?? '-' }}</td>
                <td><strong>Mobile:</strong> {{ $order->customer->mobile_no ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Address:</strong> {{ $order->customer->address ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Billing & Shipping Info -->
    <div class="billing-shipping">
        <p class="section-title">Billing & Shipping</p>
        <table>
            <tr>
                <td>
                    <strong>Name:</strong> {{ $order->billing_name ?? '-' }}<br>
                    <strong>Mobile:</strong> {{ $order->billing_mobile_no ?? '-' }}<br>
                </td>
                <td>
                    <strong>Email:</strong> {{ $order->billing_email ?? '-' }}
                    <strong>Address:</strong> {{ $order->billing_address ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Products -->
    <div class="products">
        <p class="section-title">Order Products</p>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Code</th>
                    <th>Unit Price</th>
                    <th>Discount</th>
                    <th>Qty</th>
                    @if ($order->return_status == 1)
                        <th>Return Qty</th>
                        <th>Return Amount</th>
                    @endif
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalDiscount = 0;
                    $totalQty = 0;
                    $totalReturnQty = 0;
                    $totalReturnAmount = 0;
                @endphp
                @foreach($order->products as $product)
                    <tr>
                        <td>{{ $product->product->name ?? '-' }}</td>
                        <td>{{ $product->product->code ?? '-' }}</td>
                        <td>{{ number_format($product->unit_price, 2) }}</td>
                        <td>{{ number_format($product->discount_amt ?? 0, 2) }}</td>
                        <td class="text-center">{{ $product->quantity }}</td>
                        @if ($order->return_status == 1)
                            <td class="text-center">{{ $product->return_quantity ?? 0 }}</td>
                            <td class="text-center">{{ number_format(($product->return_quantity ?? 0)*$product->unit_price, 2) }}</td>
                        @endif
                        <td class="text-right">{{ number_format($product->total, 2) }}</td>
                    </tr>
                    @php
                        $totalDiscount += $product->discount_amt ?? 0;
                        $totalQty += $product->pack_quantity;
                        $totalReturnQty += $product->return_quantity ?? 0;
                        $totalReturnAmount += ($product->return_quantity ?? 0) * $product->unit_price;
                    @endphp
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="totals">
        <table>
            <tr>
                <td class="text-right" colspan="{{ $order->return_status == 1 ? 7 : 5 }}">Subtotal</td>
                <td class="text-right">{{ number_format($order->sub_total, 2) }}</td>
            </tr>
            <tr>
                <td class="text-right" colspan="{{ $order->return_status == 1 ? 7 : 5 }}">Discount</td>
                <td class="text-right">{{ number_format($order->discount, 2) }}</td>
            </tr>
            <tr>
                <td class="text-right" colspan="{{ $order->return_status == 1 ? 7 : 5 }}">Total Amount</td>
                <td class="text-right">{{ number_format($order->total, 2) }}</td>
            </tr>
            <tr>
                <td class="text-right" colspan="{{ $order->return_status == 1 ? 7 : 5 }}">Paid</td>
                <td class="text-right">{{ number_format($order->paid, 2) }}</td>
            </tr>
            <tr>
                <td class="text-right" colspan="{{ $order->return_status == 1 ? 7 : 5 }}">Due</td>
                <td class="text-right">{{ number_format($order->due, 2) }}</td>
            </tr>
            <tr>
                <td class="text-right" colspan="{{ $order->return_status == 1 ? 7 : 5 }}">Shipping</td>
                <td class="text-right">{{ number_format($order->shipping_charge, 2) }}</td>
            </tr>
            <tr>
                <td class="text-right" colspan="{{ $order->return_status == 1 ? 7 : 5 }}">Note</td>
                <td class="text-right">{{ $order->note ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Payments -->
    @if(count($order->payments) > 0)
    <div class="payments">
        <p class="section-title">Payment Details</p>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Transaction Method</th>
                    <th>Bank</th>
                    <th>Branch</th>
                    <th>Account</th>
                    <th>Amount</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->payments as $payment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($payment->date)->format('d-M-Y') }}</td>
                        <td>{{ $payment->type == 1 ? 'Pay' : ($payment->type == 2 ? 'Refund' : '-') }}</td>
                        <td>
                            @if($payment->transaction_method == 1)
                                Cash
                            @elseif($payment->transaction_method == 3)
                                Mobile Banking ({{ $payment->mobileBanking->account_name ?? '' }} - {{ $payment->mobileBanking->account_no ?? '' }})
                            @else
                                Bank
                            @endif
                        </td>
                        <td>{{ $payment->bank->name ?? '-' }}</td>
                        <td>{{ $payment->branch->name ?? '-' }}</td>
                        <td>{{ $payment->account->account_no ?? '-' }}</td>
                        <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->note ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <p class="text-center" style="margin-top:20px;">Thank you for your purchase!</p>

</div>
</body>
</html>
