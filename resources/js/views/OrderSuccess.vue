<template>
    <div class="min-h-screen bg-gray-50 p-4 flex flex-col items-center">
        <!-- When Order Found -->
        <div
            v-if="orderFound"
            class="bg-white rounded-2xl shadow-lg p-8 max-w-3xl w-full animate__animated animate__fadeIn"
        >
            <!-- Success Header -->
            <div class="flex flex-col items-center mb-6">
                <div class="text-primary_color text-6xl mb-2">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-1">
                    Thank you for your order!
                </h1>
                <p class="text-gray-600">
                    Your order has been successfully placed.
                </p>
            </div>

            <!-- Order Info -->
            <div
                class="bg-gray-100 rounded-lg p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4"
            >
                <div>
                    <p class="text-gray-700 font-medium">Order Number</p>
                    <p class="font-bold text-gray-900 text-lg">
                        {{ order.order_no }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Order Date</p>
                    <p class="font-bold text-gray-900 text-lg">
                        {{ order.date }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Payment Method</p>
                    <p class="font-bold text-gray-900 text-lg capitalize">
                        {{ order.payment_method }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Delivery Charge</p>
                    <p class="font-bold text-gray-900 text-lg">
                        ৳{{ order.shipping_charge }}
                    </p>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Shipping Address</h2>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-gray-700">{{ order.billing_name }}</p>
                    <p class="text-gray-700">{{ order.billing_address }}</p>
                    <p class="text-gray-700">
                        📞 {{ order.billing_mobile_no }}
                    </p>
                </div>
            </div>

            <!-- Products Table -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Order Summary</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border rounded-lg">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 text-left">
                                <th class="py-2 px-3">Product</th>
                                <th class="py-2 px-3">Qty</th>
                                <th class="py-2 px-3">Unit Price</th>
                                <th class="py-2 px-3">Discount</th>
                                <th class="py-2 px-3">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in order.products"
                                :key="item.product_id"
                                class="border-b"
                            >
                                <td class="py-2 px-3">{{ item.name }}</td>
                                <td class="py-2 px-3">{{ item.quantity }}</td>
                                <td class="py-2 px-3">
                                    ৳{{ item.unit_price }}
                                </td>
                                <td class="py-2 px-3">
                                    ৳{{ item.discount_amt }}
                                </td>
                                <td class="py-2 px-3 font-bold">
                                    ৳{{ item.unit_price * item.quantity }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total Summary -->
            <div
                class="flex flex-col sm:flex-row justify-end gap-6 text-gray-800 mb-6"
            >
                <div class="flex justify-between w-full sm:w-64">
                    <span>Subtotal:</span>
                    <span>৳{{ order.sub_total }}</span>
                </div>
                <div class="flex justify-between w-full sm:w-64">
                    <span>Discount:</span>
                    <span>-৳{{ order.discount }}</span>
                </div>
                <div class="flex justify-between w-full sm:w-64">
                    <span>Total:</span>
                    <span class="font-bold text-lg">৳{{ order.total }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <router-link
                    to="/"
                    class="px-6 py-2 rounded-lg bg-primary_color text-white font-semibold hover:bg-primary_color transition"
                >
                    Continue Shopping
                </router-link>
                <router-link
                    :to="`/api/order/${order.order_no}/${order.billing_mobile_no}/receipt`"
                    target="_blank"
                    class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition"
                >
                    Download Receipt
                </router-link>
            </div>
        </div>

        <!-- When Order Not Found -->
        <div
            v-else
            class="bg-white rounded-2xl shadow-lg p-8 max-w-lg w-full flex flex-col items-center text-center animate__animated animate__fadeIn"
        >
            <div class="text-red-500 text-6xl mb-4">
                <i class="fas fa-times-circle"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                Order Not Found
            </h1>
            <p class="text-gray-600 mb-6">
                Sorry, we couldn’t find an order with this ID.<br />
                Please check your email for confirmation or contact support.
            </p>
            <router-link
                to="/"
                class="px-6 py-2 rounded-lg bg-primary_color text-white font-semibold hover:bg-[#5ca327] transition"
            >
                Back to Home
            </router-link>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "OrderSuccess",
    data() {
        return {
            orderId: this.$route.params.id,
            order: null,
            orderFound: true,
        };
    },
    created() {
        axios
            .get(`/api/order/${this.orderId}`)
            .then((res) => {
                if (res.data && res.data.id) {
                    this.order = res.data;
                    this.orderFound = true;
                } else {
                    this.orderFound = false;
                }
            })
            .catch(() => {
                this.orderFound = false;
            });
    },
};
</script>

<style scoped>
.text-green-500 {
    animation: bounce 1s ease;
}
@keyframes bounce {
    0%,
    100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.2);
    }
}
</style>
