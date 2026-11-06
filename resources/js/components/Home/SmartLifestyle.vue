<template>
  <div id="featured_category" class="container custom_container t_cat_container animate__animated animate__fadeInUp mt-4 mb-2">
    <div class="row">
      <div class="col-12 text-center">
        <div>
          <h3 class="text-[20px] md:text-[38px] leading-[20px] md:leading-[50px] text-[#39404a] block mb-0">
            Smart Lifestyle
          </h3>
          <p class="text-[13px] md:text-[18px]">
            Get Your Desire Product from Smart Lifestyle
          </p>
        </div>
      </div>
    </div>

    <!-- Slider -->
    <div v-if="!categoryLoading">
      <Swiper
        :modules="modules"
        :slides-per-view="4"
        :space-between="10"
        :breakpoints="{
          640: { slidesPerView: 4, spaceBetween: 10 },
          768: { slidesPerView: 6, spaceBetween: 15 },
          1024: { slidesPerView: 8, spaceBetween: 20 },
          1280: { slidesPerView: 10, spaceBetween: 20 }
        }"
        :navigation="true"
        :autoplay="{ delay: 3000, disableOnInteraction: false }"
        :observer="true"
        :observe-parents="true"
        class="my-6"
      >
        <SwiperSlide v-for="category in s_categories" :key="category.id">
          <div class="product_thumb top_thumb bg-white rounded-[5px] pt-1 shadow-sm hover:shadow-md transition">
            <div class="cat_effect">
              <div class="pro_image flex justify-center items-center">
                <router-link :to="{ name: 'products', params: { slug: category.slug } }" class="block">
                  <img :src="'/' + category.image" class="mx-auto h-12 w-12 object-contain" alt="category" />
                </router-link>
              </div>
              <div class="main_text pt-0">
                <p class="text-center pro-heading my-0 text-xs md:text-sm lg:text-base text-uppercase">
                  <router-link
                    :to="{ name: 'products', params: { slug: category.slug } }"
                    class="md:text-[12px] text-[10px] text-gray-600 hover:text-gray-900 transition"
                  >
                    {{ category.name }}
                  </router-link>
                </p>
              </div>
            </div>
          </div>
        </SwiperSlide>
      </Swiper>
    </div>

    <!-- Skeleton Loader -->
    <div v-else class="grid grid-cols-6 gap-4">
      <div v-for="n in 6" :key="n" class="bg-white rounded shadow p-3 animate-pulse">
        <div class="bg-gray-300 w-full h-32 rounded"></div>
        <div class="bg-gray-300 h-4 w-3/4 mx-auto mt-3 rounded"></div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination, Autoplay } from "swiper/modules"; // ✅ import modules

// Swiper styles
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

export default {
  name: "SmartLifestyle",
  components: { Swiper, SwiperSlide },
  setup() {
    // Pass modules directly to Swiper via :modules
    const modules = [Navigation, Pagination, Autoplay];
    return { modules };
  },
  computed: {
    ...mapGetters("home", ["s_categories", "categoryLoading"]),
  },
};
</script>
<style>
#featured_category .swiper-button-prev:after, .swiper-button-next:after{
    font-size: 20px;
}
#featured_category .swiper-button-next,
#featured_category .swiper-button-prev {
  color: #70c332;
  width: 40px;
  height: 40px;
  background: rgba(0,0,0,0.1);
  border-radius: 50%;
  transition: all 0.3s ease;
}

#featured_category .swiper-button-next:hover,
#featured_category .swiper-button-prev:hover {
  background: #70c332;
  color: #fff;
}

#featured_category .swiper-button-prev {
  left: 0px;
}

#featured_category .swiper-button-next {
  right: 0px;
}
</style>