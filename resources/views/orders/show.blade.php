@extends('layouts.app')
@section('title', 'Department')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3>Order Details</h3>
                    <span class="badge 
                        @if($order->status === 'pending') badge-warning 
                        @elseif($order->status === 'processing') badge-info 
                        @elseif($order->status === 'completed') badge-success 
                        @elseif($order->status === 'cancelled') badge-danger 
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="order-details row">
                        <div class="col-md-4">
                            <section class="order-section">
                                <h4 class="section-title">Order Information</h4>
                                <ul class="info-list">
                                    <li><strong>Order Number:</strong> {{ $order->order_number }}</li>
                                    <li><strong>Ordered At:</strong> {{ $order->ordered_at->format('Y-m-d H:i:s') }}</li>
                                    <li><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</li>
                                    <li><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</li>
                                </ul>
                            </section>
                        </div>
                        <div class="col-md-4">
                            <section class="order-section">
                                <h4 class="section-title">Billing Information</h4>
                                <ul class="info-list">
                                    <li><strong>Name:</strong> {{ $order->pi_first_name }} {{ $order->pi_last_name }}</li>
                                    <li><strong>Email:</strong> {{ $order->pi_email }}</li>
                                    <li><strong>Phone:</strong> {{ $order->pi_phone_no }}</li>
                                    <li><strong>Billing Address:</strong> {{ $order->billing_address }}</li>
                                </ul>
                            </section>
                        </div>
                        <div class="col-md-4">
                            <section class="order-section">
                                <h4 class="section-title">Shipping Information</h4>
                                <ul class="info-list">
                                    <li><strong>City:</strong> {{ $order->shipping_city }}</li>
                                    <li><strong>Country:</strong> {{ $order->country ?? 'N/A' }}</li>
                                    <li><strong>Address:</strong> {{ $order->shipping_address }}</li>
                                    <li><strong>Order Notes:</strong> {{ $order->order_notes ?? 'N/A' }}</li>
                                </ul>
                            </section>
                        </div>                      
                    </div>

                    <h5>Items</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->order_items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>${{ number_format($item->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection