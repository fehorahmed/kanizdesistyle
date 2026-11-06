<template>
  <div class="container custom_container t_cat_container animate__animated animate__fadeInUp">
    <!-- Title -->
    <div class="row">
      <div class="col-12 text-center">
        <h5 class="mb-4 d-inline-block pr-3 font-bold text-[20px] md:text-[38px] leading-[20px] md:leading-[50px]">Shop by Brands</h5>
      </div>
    </div>

    <!-- Carousel -->
    <div v-if="!categoryLoading">
      <div id="top_carousel" class="owl-carousel owl-theme">
        <!-- Actual brands -->
        <div v-for="brand in brands" :key="brand.id" class="item">
          <div class="col-12">
            <div class="product_thumb top_thumb bg-white rounded-[5px] !pt-1">
              <div class="cat_effect">
                <div class="pro_image">
                  <router-link :to="{ name: 'products', query: { brand: brand.id } }">
                    <img :src="'/' + brand.image" class="mx-auto h-12 w-12 object-contain" :alt="brand.name" />
                  </router-link>
                </div>
                <div class="main_text pt-0">
                  <p class="text-center pro-heading my-0 text-uppercase">
                    <router-link class="text-muted md:text-[12px] text-[10px]" :to="{ name: 'products', query: { brand: brand.id } }">{{ brand.name }}</router-link>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
     <div v-else class="grid grid-cols-6 gap-4">
        <div v-for="n in 6" :key="n" class="bg-white rounded shadow p-3 animate-pulse">
            <div class="bg-gray-300 w-full h-32 rounded"></div>
            <div class="bg-gray-300 h-4 w-3/4 mx-auto mt-3 rounded"></div>
        </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'ShopByBrand',
  computed: {
    ...mapGetters('home', ['brands', 'categoryLoading'])
  },
  watch: {
    categoryLoading(newVal) {
      if (!newVal && this.brands.length) {
        // Wait for DOM update
        this.$nextTick(() => {
          // Destroy existing carousel first if any
          if ($('#top_carousel').hasClass('owl-loaded')) {
            $('#top_carousel').trigger('destroy.owl.carousel').removeClass('owl-loaded');
          }

          // Initialize Owl Carousel
          $('#top_carousel').owlCarousel({
            loop: false,
            nav: true,
            navText: [
              "<i class='fa fa-chevron-left'></i>",
              "<i class='fa fa-chevron-right'></i>"
            ],
            autoplay: false,
            autoplayHoverPause: true,
            responsive: {
              0: { items: 3 },
              480: { items: 3 },
              600: { items: 6 },
              992: { items: 7 },
              1200: { items: 9 },
              1410: { items: 9 },
              1850: { items: 10 }
            }
          });
        });
      }
    }
  }
};
</script>

<style scoped>
/* Optional: Skeleton loader */
.animate-pulse {
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.4;
  }
}
</style>
