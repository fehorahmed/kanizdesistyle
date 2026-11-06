<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - Thank You!</title>
    <meta http-equiv="refresh" content="3;url={{ route('ecommerce_receipt.download', ['order_no' => $order->order_no, 'mobile' => $order->billing_mobile_no]) }}">
    {{-- Include Tailwind CSS via CDN for styling --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Optional: Add a smooth background color to the body */
        body {
            background-color: #f3f4f6; /* Light gray background */
            min-height: 100vh;
        }
        /* Optional: Basic dark mode toggle if not using Tailwind's native dark mode logic */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1f2937;
            }
        }
    </style>
</head>
<body>

    <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8 sm:p-10 text-center">

            {{-- Confirmation Icon --}}
            <div class="flex justify-center mb-6">
                {{-- Checkmark Icon (SVG) --}}
                <svg class="w-20 h-20 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-3">
                Order Placed Successfully!
            </h1>

            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Thank you for your purchase. Your order has been confirmed and is being processed.
            </p>

            {{-- Order Summary Block (Uses Blade for data display) --}}
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md inline-block mb-8">
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    Order ID:
                    {{-- Blade syntax for accessing the Order ID --}}
                    <span class="text-indigo-600 dark:text-indigo-400">#{{ $order->order_no }}</span>
                </p>
                @if (isset($order->total))
                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">
                        Total Amount: {{ number_format($order->total+$order->shipping_charge, 2) }}
                    </p>
                @endif
            </div>

            {{-- Next Steps and Call-to-Actions (CTAs) --}}
            <div class="space-y-4">
                {{-- Link to view order details --}}
                {{-- <a href="{{ route('orders.show', $order->id) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition">
                    View Order Details
                </a> --}}

                {{-- Link back to shopping --}}
                {{-- <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 ml-0 sm:ml-4 transition">
                    Continue Shopping
                </a> --}}
            </div>

        </div>
    </div>

</body>
</html>