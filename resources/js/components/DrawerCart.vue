<template>
    <!-- Overlay -->
    <div
        v-if="isOpen"
        class="fixed inset-0 bg-black bg-opacity-50 z-[120]"
        @click="close"
    ></div>

    <!-- Drawer -->
    <div
        :class="[
            'fixed top-0 right-0 h-full bg-white shadow-lg z-[124] transform transition-transform duration-300 ease-in-out flex flex-col',
            'w-80 md:w-96',
            isOpen ? 'translate-x-0' : 'translate-x-full',
        ]"
    >
        <!-- Header -->
        <div
            class="cart-slide-top text-center justify-between items-center p-3 border-b bg-white"
        >
            <h3 class="text-lg font-semibold text-primary_color m-0">
                <i class="fas fa-shopping-basket text-xl"></i>
                Total Item ({{
                    totalCartItem.toLocaleString("en-US", {
                        minimumIntegerDigits: 2,
                        useGrouping: false,
                    })
                }})
            </h3>

            <button
                v-if="isOpen"
                @click="close"
                class="hover:bg-primary_color hover:text-white absolute top-4 -left-4 w-8 h-8 bg-[#fff] flex items-center justify-center rounded-full text-gray-600 text-[15px] font-normal shadow-sm cursor-pointer transition-all duration-300 ease-linear"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Cart Items -->
        <div class="p-2 flex-1 flex flex-col gap-4 overflow-y-auto">
            <div
                v-for="item in cartItems"
                :key="item.product_id"
                class="cart-item flex gap-4 p-2 border rounded hover:bg-gray-50 transition relative group"
            >
                <!-- Product Image -->
                <div class="cart-img relative w-20 h-20 flex-shrink-0">
                    <img
                        :src="item?.product?.image"
                        alt="Product"
                        class="w-full h-full object-cover rounded"
                    />
                    <button
                        @click="removeItem(item.product_id)"
                        class="flex items-center justify-center absolute top-0 left-0 w-full h-full bg-black/40 rounded-[8px] opacity-0 scale-0 origin-center transition-all duration-300 ease-linear group-hover:visible group-hover:opacity-100 group-hover:scale-100"
                    >
                        <span
                            class="w-[32px] h-[32px] text-[18px] leading-[32px] text-center rounded-[5px] inline-block text-[#ff3838] bg-white/90 transition-all duration-300 ease-linear cursor-pointer hover:text-white hover:bg-[#ff3838]"
                        >
                            <i class="fa fa-trash-alt"></i>
                        </span>
                    </button>
                </div>

                <!-- Product Details -->
                <div class="cart-text flex-1 flex flex-col justify-between">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700">
                            {{ item?.product?.name }}
                        </h4>
                        <span class="text-xs text-gray-500"
                            >Unit Price - ৳{{ item.price }}</span
                        >
                    </div>

                    <!-- Quantity & Total Price -->
                    <div
                        class="quantity-price flex items-center justify-between mt-2"
                    >
                        <!-- Quantity Controls -->
                        <div
                            class="quantity flex items-center justify-start gap-[5px]"
                        >
                            <button
                                class="text-[#555] w-[30px] h-[30px] text-[11px] flex items-center justify-center rounded-[5px] bg-[#f5f5f5] border-none outline-none cursor-pointer transition-all duration-300 ease-linear hover:text-white hover:bg-primary_color"
                                :disabled="item.quantity <= 1"
                                @click="
                                    decreaseQuantity(
                                        item.product_id,
                                        item.quantity
                                    )
                                "
                            >
                                <i class="fa fa-minus"></i>
                            </button>

                            <input
                                type="text"
                                readonly
                                :value="item.quantity"
                                class="text-primary_color font-bold text-[14px] w-[40px] h-[30px] flex items-center justify-center text-center rounded-[5px] bg-[#f5f5f5] border-none outline-none"
                            />

                            <button
                                class="text-[#555] w-[30px] h-[30px] text-[11px] flex items-center justify-center rounded-[5px] bg-[#f5f5f5] border-none outline-none cursor-pointer transition-all duration-300 ease-linear hover:text-white hover:bg-primary_color"
                                @click="
                                    increaseQuantity(
                                        item.product_id,
                                        item.quantity
                                    )
                                "
                            >
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>

                        <!-- Total Price -->
                        <div
                            class="text-primary_color font-medium text-[17px] block"
                        >
                            ৳{{ item.price * item.quantity }}
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="cartItems.length === 0"
                class="text-center text-gray-500 mt-8"
            >
                Your cart is empty
            </div>
        </div>

        <!-- Footer -->
        <div class="cart-slide-bottom p-4 border-t bg-white">
            <router-link
                :to="{ name: 'checkout' }"
                class="flex w-full bg-primary_color py-2 px-0 box-border rounded-md items-center justify-center transition-all duration-300 ease-linear hover:bg-[#39404a]"
            >
                <p
                    class="w-full h-[30px] text-[16px] leading-[30px] tracking-[0.3px] font-sans font-normal text-center capitalize text-white mb-0"
                >
                    Proceed To Checkout
                </p>
                <span
                    class="px-[25px] tracking-[0.3px] text-white border-l border-[#e8e8e8] font-sans font-normal text-[16px] block"
                >
                    ৳{{ cartTotal }}
                </span>
            </router-link>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    name: "CartDrawer",
    data() {
        return {
            isOpen: false,
        };
    },
    computed: {
        ...mapGetters("cart", {
            cartItems: "cartItems",
            totalCartItem: "totalCartItem",
            cartTotal: "cartTotal",
        }),
    },
    methods: {
        ...mapActions("cart", ["updateCart", "removeFromCart"]),

        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
        },
        increaseQuantity(id, qty) {
            this.updateCart({ productId: id, quantity: qty + 1 });
        },
        decreaseQuantity(id, qty) {
            if (qty > 1) {
                this.updateCart({ productId: id, quantity: qty - 1 });
            }
        },
        removeItem(id) {
            this.removeFromCart(id);
        },
    },
};
</script>
