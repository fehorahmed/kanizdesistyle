<template>
  <div class="w-full relative">
    <!-- shimmer while loading -->
    <div v-if="homeLoading" class="flex gap-2 w-full">
      <div
        class="relative w-full overflow-hidden rounded-md bg-gray-200 min-h-[300px]"
      >
        <div
          class="absolute inset-0 bg-gradient-to-r from-transparent via-white/60 to-transparent animate-shimmer"
        ></div>
      </div>
    </div>

    <!-- Swiper after data loaded -->
    <Swiper
      v-else
      :modules="modules"
      :pagination="{ clickable: true }"
      :autoplay="{ delay: 5000, disableOnInteraction: false }"
      :loop="true"
      :speed="1000"
      class="rounded-md overflow-hidden"
    >
      <SwiperSlide v-for="(banner, index) in banners" :key="index">
        <!-- image skeleton until loaded -->
        <div
          v-if="!imageLoaded[index]"
          class="w-full bg-gray-200 relative overflow-hidden rounded-md min-h-[300px]"
        >
          <div
            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/60 to-transparent animate-shimmer"
          ></div>
        </div>

        <img
          :src="banner.image"
          class="w-full transition-opacity duration-500"
          :alt="'banner-' + index"
          loading="lazy"
          :class="{ 'opacity-0': !imageLoaded[index], 'opacity-100': imageLoaded[index] }"
          @load="onImageLoad(index)"
        />
      </SwiperSlide>
    </Swiper>
  </div>
</template>

<script>
import { ref } from "vue";
import { mapGetters } from "vuex";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination, Autoplay } from "swiper/modules";

// Swiper styles
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

export default {
  name: "BannerSection",
  components: { Swiper, SwiperSlide },
  setup() {
    const modules = [Navigation, Pagination, Autoplay];
    const imageLoaded = ref({});

    const onImageLoad = (index) => {
      imageLoaded.value = { ...imageLoaded.value, [index]: true };
    };

    return { modules, imageLoaded, onImageLoad };
  },
  computed: {
    ...mapGetters("home", ["banners", "homeLoading"]),
  },
};
</script>

<style>
@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}
.animate-shimmer {
  animation: shimmer 2s infinite;
}
.swiper-pagination-bullet-active{
  background: var(--primary-color) !important;
}
</style>
