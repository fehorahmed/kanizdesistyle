<template>
  <div>
    <!-- header -->
    <Header />

    <div id="checkout_page" class="checkout-page animate__animated animate__fadeInUp py-6 bg-gray-50 min-h-screen">
      <div class="container mx-auto">
        <div class="flex flex-col lg:flex-row gap-6">

          <!-- Cart Items -->
          <div class="flex-1">
            <div class="bg-white rounded-2xl shadow-md p-6">
              <h5 class="text-xl font-semibold mb-2 flex items-center gap-2">
                <i class="fas fa-shopping-basket"></i> Products
              </h5>

              <div>
                <!-- Cart Items List -->
                <div v-if="!cartLoading && totalCartItem > 0" class="space-y-4">
                  <div
                    v-for="item in cartItems"
                    :key="item.id"
                    class="flex flex-col sm:flex-row items-center justify-between border rounded-xl p-4 shadow-sm hover:shadow-md transition"
                  >
                    <!-- Image -->
                    <div class="w-20 h-20 flex-shrink-0">
                      <img :src="item?.product?.image" class="w-full h-full object-cover rounded-lg border" :alt="item.name" />
                    </div>

                    <!-- Details -->
                    <div class="flex-1 px-4 mt-3 sm:mt-0">
                      <h6 class="text-gray-800 font-semibold">{{ item?.product?.name }}</h6>
                      <div class="text-sm text-gray-500 mt-1">Unit Price: ৳{{ item.old_price }}</div>
                      <div class="text-sm text-primary_color mt-1">Discount: {{ item.discount_percent }}% (৳{{ item.discount }})</div>
                    </div>

                    <!-- Quantity & Total -->
                    <div class="flex flex-col items-end mt-3 sm:mt-0 space-y-2">
                      <div class="flex items-center space-x-2">
                        <button
                          class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-gray-600 hover:bg-primary_color hover:text-white transition"
                          :disabled="item.quantity <= 1"
                          @click="decreaseQuantity(item.product_id, item.quantity)"
                        >
                          <i class="fa fa-minus text-xs"></i>
                        </button>
                        <span class="w-10 h-8 flex items-center justify-center rounded-md bg-gray-50 text-primary_color font-semibold">
                          {{ item.quantity }}
                        </span>
                        <button
                          class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-gray-600 hover:bg-primary_color hover:text-white transition"
                          @click="increaseQuantity(item.product_id, item.quantity)"
                        >
                          <i class="fa fa-plus text-xs"></i>
                        </button>
                        <button
                          @click="removeItem(item.product_id)"
                          class="flex items-center justify-center w-8 h-8 bg-black/40 rounded-[8px] origin-center transition-all duration-300 ease-linear"
                        >
                          <span
                          class="w-[32px] h-[32px] text-[18px] leading-[32px] text-center rounded-[5px] inline-block text-[#ff3838] bg-white/90 transition-all duration-300 ease-linear cursor-pointer hover:text-white hover:bg-[#ff3838]"
                          >
                          <i class="fa fa-trash-alt"></i>
                          </span>
                        </button>
                      </div>
                      <div class="text-lg font-semibold text-primary_color">
                        ৳{{ (item.price * item.quantity) }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Loading Skeleton -->
                <div v-else-if="cartLoading" class="space-y-4">
                  <div
                    v-for="n in 3"
                    :key="n"
                    class="flex items-center justify-between border rounded-xl p-4 shadow-sm animate-pulse"
                  >
                    <div class="w-20 h-20 bg-gray-200 rounded-lg"></div>
                    <div class="flex-1 px-4 space-y-2">
                      <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                      <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                      <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                    </div>
                    <div class="flex flex-col items-end space-y-2">
                      <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gray-200 rounded"></div>
                        <div class="w-10 h-8 bg-gray-200 rounded"></div>
                        <div class="w-8 h-8 bg-gray-200 rounded"></div>
                      </div>
                      <div class="h-4 w-16 bg-gray-200 rounded"></div>
                    </div>
                  </div>
                </div>

                <!-- Empty Cart -->
                <div v-else class="text-center text-gray-500 py-10">
                  <p>Your cart is empty.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Checkout Form -->
          <div class="w-full lg:w-1/3">
            <div class="bg-white rounded-2xl shadow-md p-6 space-y-5">
              <h5 class="text-xl font-semibold flex items-center gap-2 mb-4">
                <i class="fas fa-shipping-fast"></i> Delivery Info
              </h5>

              <form @submit.prevent="placeOrder" class="space-y-4">

                <!-- Name -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-green-200"
                    required
                  />
                </div>

                <!-- Mobile -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Mobile No</label>
                  <input
                    v-model="form.mobile"
                    type="text"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-green-200"
                    required
                  />
                </div>

                <!-- Address -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                  <textarea
                    v-model="form.address"
                    rows="3"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-green-200"
                    required
                  ></textarea>
                </div>

                <!-- Delivery -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Charge</label>
                  <select v-model="deliveryCharge" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-green-200">
                    <option value="50">Inside City - ৳50</option>
                    <option value="100">Outside City - ৳100</option>
                  </select>
                </div>

                <!-- Payment -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                  <select v-model="paymentMethod" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-green-200">
                    <option value="cod">Cash on Delivery</option>
                    <option value="bkash">bKash</option>
                    <option value="card">Credit/Debit Card</option>
                  </select>
                </div>

                <!-- Coupon -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Coupon Code </label>
                  <div class="flex flex-wrap gap-2">
                    <input
                      v-model="couponCode"
                      type="text"
                      placeholder="Enter coupon code"
                      class="flex-1 min-w-[150px] rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-green-200"
                    />
                    <button
                      type="button"
                      @click="handleApplyCoupon"
                      class="flex-shrink-0 px-4 py-2 bg-primary_color text-white rounded-lg hover:bg-green-700 transition"
                    >
                      Apply
                    </button>
                    <button
                      v-if="couponDiscount > 0"
                        type="button"
                        @click="handleRemoveCoupon"
                        class="flex-shrink-0 px-2 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
                      >
                      Remove
                    </button>
                  </div>
                  <p v-if="couponDiscount > 0" class="text-sm text-primary_color mt-1">
                    Coupon applied! You saved ৳{{ couponDiscount }}
                  </p>
                </div>

                <!-- Order Summary -->
                <div class="border-t pt-4 space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span>SubTotal</span>
                    <span class="font-medium">৳{{ cartSubtotal }}</span>
                  </div>
                  <div v-if="cartDiscount > 0" class="flex justify-between text-primary_color">
                    <span>Discount</span>
                    <span>- ৳{{ cartDiscount }}</span>
                  </div>
				          <div class="flex justify-between">
                    <span>Delivery</span>
                    <span class="font-medium">৳{{ deliveryCharge }}</span>
                  </div>
                  <div class="flex justify-between font-bold text-gray-800 text-lg pt-2 border-t">
                    <span>Total</span>
                    <span>৳{{ grandTotal }}</span>
                  </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full py-2 bg-primary_color text-white rounded-lg font-bold text-lg hover:bg-[#5ca327] transition">
                  Place Order
                </button>

              </form>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script>
import Header from "../components/Header.vue";
import Footer from "../components/Footer.vue";
import { mapGetters, mapActions } from "vuex";
import { useToast } from 'vue-toastification';
import axios from "axios";

export default {
  name: "CheckoutPage",
  data() {
    return {
      form: { name: "", mobile: "", address: "" },
      deliveryCharge: 50,
      paymentMethod: "cod",
      couponCode: "",
    };
  },
  computed: {
    ...mapGetters("cart", ["cartLoading", "cartItems", "coupon", "totalCartItem", "cartSubtotal", "cartTotal", "cartDiscount", "productDiscount", "couponDiscount"]),
    ...mapGetters("auth", ["isLoggedIn","loginUser"]),
    grandTotal() {
      return (parseFloat(this.cartSubtotal) + parseFloat(this.deliveryCharge) - parseFloat(this.cartDiscount)).toFixed(2);
    }
  },
  watch: {
    coupon: {
      handler(newCoupon) {
        this.couponCode = newCoupon?.code || "";
      },
      immediate: true,
      deep: true
    }
  },
  mounted() {
    if (this.isLoggedIn) {
      this.form.name = this.loginUser.name || "";
      this.form.mobile = this.loginUser.mobile_no || "";
      this.form.address = this.loginUser.address || "";
    }
    if (window.innerWidth > 991) {
      $("#sp_vertical_menu .vertical_menu").insertBefore(
        "#sp_header_top .col-lg-9.text-left"
      );
    }
  },
  methods: {
    ...mapActions("cart", ["updateCart", "removeFromCart", "applyCoupon", "removeCoupon", "clearCartAfterOrder"]),
    increaseQuantity(id, qty) { this.updateCart({ productId: id, quantity: qty + 1 }); },
    decreaseQuantity(id, qty) { if(qty > 1) this.updateCart({ productId: id, quantity: qty - 1 }); },
	  removeItem(id) {
      this.removeFromCart(id);
    },
    async handleApplyCoupon() {
      const toast = useToast();
      if (!this.couponCode) {
        return alert("Enter a coupon code");
      }
      try {
        const response = await axios.post("/api/check-coupon", {
          code: this.couponCode,
          subtotal: this.cartSubtotal - this.cartDiscount,
        });

        if (response.data.success) {
          this.applyCoupon(response.data.coupon); // save coupon in Vuex
          toast.success(response.data.message);
        } else {
          toast.error(response.data.message || "Invalid coupon");
        }
      } catch (err) {
        toast.error(err.response?.data?.message || "Failed to apply coupon");
      }
    },
    async handleRemoveCoupon() {
      try { await this.removeCoupon(); this.couponCode = ""; } 
      catch (err) { alert(err || "Failed to remove coupon"); }
    },
    async placeOrder() {
	  const toast = useToast();
      try {
        const payload = {
          coupon_code: this.coupon?.code,
          coupon_discount: this.couponDiscount,
          shipping_charge: this.deliveryCharge,
          payment_method: this.paymentMethod,
          notes: 'Ecommerce',
          items: this.cartItems,
          billing: {
            name: this.form.name,
            phone: this.form.mobile,
            address: this.form.address
          }
        };
        const response = await axios.post("/api/order", payload);

        if (response.data.success) {
			    toast.success(`Order placed! Order No: ${response.data.order_no}`);

          await this.clearCartAfterOrder();

          if (response.data.redirect) {
            window.location.href = response.data.redirect;
          } else {
            this.$router.push(`/order-success/${response.data.order_no}`);
          }
        } else {
          	toast.error(response.data.message || "Failed to place order.");
        }
      } catch (error) {
        toast.error(error.response?.data?.message || "Server error, try again.");
      }
    }
  },
  components: { Header, Footer }
};
</script>

<style scoped>
/* optional: smooth shadow hover */
.checkout-page .shadow-sm:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}
</style>
