<template>
  <div class="p-1">
    <h3 class="text-2xl font-bold mb-6">My Orders</h3>

    <!-- Loading Skeleton Table -->
    <div v-if="ordersLoading" class="overflow-x-auto">
      <table class="min-w-full bg-white rounded-lg shadow divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Order No</th>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Date</th>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Total</th>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
            <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="n in 5" :key="n">
            <td class="px-4 py-2">
              <div class="h-5 bg-gray-200 rounded animate-pulse w-24"></div>
            </td>
            <td class="px-4 py-2">
              <div class="h-5 bg-gray-200 rounded animate-pulse w-20"></div>
            </td>
            <td class="px-4 py-2">
              <div class="h-5 bg-gray-200 rounded animate-pulse w-16"></div>
            </td>
            <td class="px-4 py-2">
              <div class="h-5 bg-gray-200 rounded animate-pulse w-20"></div>
            </td>
            <td class="px-4 py-2 text-center space-x-2">
              <div class="inline-block h-5 bg-gray-200 rounded animate-pulse w-16"></div>
              <div class="inline-block h-5 bg-gray-200 rounded animate-pulse w-12 ml-2"></div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Error -->
    <div v-else-if="ordersError" class="text-red-500">{{ ordersError }}</div>

    <!-- Orders Table -->
    <div v-else>
      <div v-if="ordersCount === 0" class="text-gray-500">
        You haven’t placed any orders yet.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Order No</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Date</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Total</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
              <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="order in ordersList" :key="order.id">
              <td class="px-4 py-3">{{ order.order_no }}</td>
              <td class="px-4 py-3">{{ order.date }}</td>
              <td class="px-4 py-3">৳{{ order.total }}</td>
              <td class="px-4 py-3 capitalize">
                <span v-if="order.status==0">Pending</span>
                <span v-else-if="order.status==1">Processing</span>
                <span v-else-if="order.status==2">In-transit</span>
                <span v-else-if="order.status==3">Completed</span>
              </td>
              <td class="px-4 py-3 text-center space-x-2">
                <router-link
                  :to="`/api/order/${order.order_no}/${order.billing_mobile_no}/receipt`"
                  target="_blank"
                  class="px-3 py-1 bg-primary_color text-white rounded hover:bg-primary_color/50 text-sm"
                >
                  Receipt
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import axios from "@/axios";

export default {
  name: "Orders",
  computed: {
    ...mapGetters("order", [
      "ordersList",
      "ordersLoading",
      "ordersError",
      "ordersCount"
    ])
  },
  mounted() {
    this.fetchOrders();
  },
  methods: {
    ...mapActions("order", ["fetchOrders"]),

    async downloadReceipt(orderId) {
      try {
        const response = await axios.get(`/api/order/${orderId}/pdf`, {
          responseType: "blob"
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", `order-${orderId}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
      } catch (err) {
        console.error(err);
        alert("Failed to download receipt.");
      }
    }
  }
};
</script>

<style scoped>
table {
  border-collapse: separate;
  border-spacing: 0;
}
</style>
