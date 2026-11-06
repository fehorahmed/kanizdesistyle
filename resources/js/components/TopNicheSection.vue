<template>
   <div class="container custom_container t_pro_container animate__animated animate__fadeInUp">
      <div class="row">
        <!-- Title -->
        <div class="text-center mb-6 col-12">
          <h1 class="text-2xl md:text-4xl text-gray-800">Browse By Top Niche</h1>
        </div>
      </div>
        <!-- Filters -->
        <div class="flex justify-center mb-2 bg-gray-50 py-3">
          <ul class="flex space-x-4">
            <li>
              <button
                :class="['flex items-center space-x-2 px-2 py-2 rounded', activeSection === 'sales' ? 'bg-primary_color text-white' : 'bg-white text-gray-700 shadow']"
                @click="activeSection = 'sales'"
              >
                <i class="fa fa-dollar-sign"></i>
                <span>Top Sales</span>
              </button>
            </li>
            <li>
              <button
                :class="['flex items-center space-x-2 px-4 py-2 rounded', activeSection === 'rating' ? 'bg-primary_color text-white' : 'bg-white text-gray-700 shadow']"
                @click="activeSection = 'rating'"
              >
                <i class="fa fa-star"></i>
                <span>Top Rating</span>
              </button>
            </li>
            <li>
              <button
                :class="['flex items-center space-x-2 px-4 py-2 rounded', activeSection === 'discount' ? 'bg-primary_color text-white' : 'bg-white text-gray-700 shadow']"
                @click="activeSection = 'discount'"
              >
                <i class="fa fa-percent"></i>
                <span>Top Discount</span>
              </button>
            </li>
          </ul>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2">
            <ProductCard v-if="!topNicheLoading" :products="activeProducts" />
            <ProductCardSkelton v-else v-for="n in 6" :key="n"/>
        </div>
      
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import ProductCard from "./ProductCard.vue";
import ProductCardSkelton from "./ProductCardSkelton.vue";

export default {
  name: "TopNicheSection",
  data() {
    return {
      activeSection: "sales",
    };
  },
  computed: {
    ...mapGetters("home", [
      "topSalesProducts",
      "topRatingProducts",
      "topDiscountProducts",
      "topNicheLoading",
    ]),
    activeProducts() {
      if (this.activeSection === "sales") return this.topSalesProducts;
      if (this.activeSection === "rating") return this.topRatingProducts;
      if (this.activeSection === "discount") return this.topDiscountProducts;
      return [];
    },
  },
  methods: {
    ...mapActions("home", ["fetchTopNiche"]),
  },
  components: {
    ProductCard,
    ProductCardSkelton,
  },
  mounted() {
    this.fetchTopNiche();
  },
};
</script>

