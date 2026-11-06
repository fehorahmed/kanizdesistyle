<template>
    <div class="container custom_container t_pro_container  animate__animated animate__fadeInUp mt-4">
        <div class="row">
            <div class="col-12 text-center">
                <div>
                    <h3 class="text-[20px] md:text-[38px] leading-[20px] md:leading-[50px] text-[#39404a] block mb-0">New Arrivals</h3>
                    <p class="text-[13px] md:text-[18px]">Get Your Desire Product from New Categories</p>
                </div> 
            </div> 
        </div> <!-- row -->

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2">
            <ProductCard v-if="!homeLoading" :products="newProducts" />
            <ProductCardSkelton v-else v-for="n in 6" :key="n"/>
        </div>
        <div class="text-center mt-4" v-if="newProducts.length < total">
            <button
                @click="loadMore"
                :disabled="loading"
                class="relative inline-flex items-center px-6 py-2 font-semibold text-white bg-primary_color rounded-lg shadow-lg hover:bg-primary_color/90 transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <!-- Spinner -->
                <svg
                v-if="loading"
                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>

                <span v-if="!loading">Load More</span>
                <span v-else>Loading...</span>
            </button>
        </div>
    </div>
</template>
<script>
import ProductCard from '../ProductCard.vue';
import ProductCardSkelton from '../ProductCardSkelton.vue';
import { mapGetters, mapActions } from "vuex";

export default {
    name: 'NewArrivalProduct',
    data() {
        return {
            isLoading: true,
        };
    },
    computed: {
        ...mapGetters("home", ["new_products", "newProductsLoading", "newProductsTotal"]),
        newProducts() {
            return this.new_products;
        },
        loading() {
            return this.newProductsLoading;
        },
        total() {
            return this.newProductsTotal;
        },
    },
    methods: {
        ...mapActions("home", ["fetchNewProducts"]),
        loadMore() {
            this.fetchNewProducts();
        },
    },
    mounted() {
        if (!this.newProducts.length) {
            this.fetchNewProducts({ reset: true });
        }
    },
    components:{
        ProductCard,
        ProductCardSkelton,
    }
};
</script>