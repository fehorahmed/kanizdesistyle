<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 600;
        }

        .badge-status {
            font-size: 0.9rem;
            padding: 8px 12px;
            border-radius: 30px;
            font-weight: bold;
        }

        .order-section h4 {
            font-size: 1.15rem;
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info-list li {
            margin-bottom: 8px;
            font-size: 0.95rem;
            color: #555;
        }

        .table {
            margin-top: 20px;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table th {
            background-color: #007bff;
            color: white;
            text-align: center;
            font-weight: 600;
        }

        .table td {
            background-color: white;
            text-align: center;
            vertical-align: middle;
        }

        .btn {
            border-radius: 30px;
            font-size: 0.9rem;
            padding: 10px 20px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .text-end button {
            margin-left: 10px;
        }

        .card-footer {
            background-color: #f4f6f9;
            border-top: 2px solid #ddd;
            padding: 20px;
        }

        .btn-primary {
            background: linear-gradient(90deg, #007bff, #0056b3);
            border: none;
        }

        .btn-info {
            background: linear-gradient(90deg, #17a2b8, #138496);
            border: none;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Order Details</h3>
                <span
                    class="badge badge-status
                    @if ($order->status === 'pending') bg-warning text-dark 
                    @elseif($order->status === 'processing') bg-info text-white 
                    @elseif($order->status === 'completed') bg-success text-white 
                    @elseif($order->status === 'cancelled') bg-danger text-white @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="order-section">
                            <h4>Order Information</h4>
                            <ul class="info-list">
                                <li><strong>Order Number:</strong> {{ $order->order_number }}</li>
                                <li><strong>Ordered At:</strong> {{ $order->ordered_at->format('Y-m-d H:i:s') }}</li>
                                <li><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</li>
                                <li><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="order-section">
                            <h4>Billing Information</h4>
                            <ul class="info-list">
                                <li><strong>Name:</strong> {{ $order->pi_first_name }} {{ $order->pi_last_name }}</li>
                                <li><strong>Email:</strong> {{ $order->pi_email }}</li>
                                <li><strong>Phone:</strong> {{ $order->pi_phone_no }}</li>
                                <li><strong>Billing Address:</strong> {{ $order->billing_address }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="order-section">
                            <h4>Shipping Information</h4>
                            <ul class="info-list">
                                <li><strong>City:</strong> {{ $order->shipping_city }}</li>
                                <li><strong>Country:</strong> {{ $order->country ?? 'N/A' }}</li>
                                <li><strong>Address:</strong> {{ $order->shipping_address }}</li>
                                <li><strong>Order Notes:</strong> {{ $order->order_notes ?? 'N/A' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h5 class="mt-4">Order Items</h5>
                <table class="table">
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
            <div class="card-footer text-end">
                <button type="button" class="btn btn-info text-white"
                    onclick="window.location.href='/'">
                    <i class="fas fa-home"></i> Home
                </button>
                <button type="button" class="btn btn-primary text-white"
                    onclick="window.location.href='/frontend/products'">
                    <i class="fas fa-shopping-cart"></i> Continue Shopping
                </button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
