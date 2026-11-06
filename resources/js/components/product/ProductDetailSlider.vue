<template>
  <div class="product-carousel">
    <!-- Main Swiper with Zoom -->
    <Swiper
      :modules="[Navigation, Thumbs, Zoom]"
      :spaceBetween="10"
      :thumbs="{ swiper: thumbsSwiper }" 
      :zoom="true"
      class="main-swiper"
    >
      <SwiperSlide>
        <div class="swiper-zoom-container">
          <img :src="'/' + main_image" class="main-image" loading="lazy" style="filter: blur(10px); transition: filter 0.3s;" @load="($event.target.style.filter='blur(0)')"/>
        </div>
      </SwiperSlide>
      <SwiperSlide v-for="(image, index) in images" :key="index">
        <div class="swiper-zoom-container">
          <img :src="'/' + image.path" class="main-image" loading="lazy" style="filter: blur(10px); transition: filter 0.3s;" @load="($event.target.style.filter='blur(0)')"/>
        </div>
      </SwiperSlide>
    </Swiper>

    <!-- Thumbnail Swiper -->
    <Swiper
      @swiper="setThumbsSwiper"
      :modules="[Autoplay]"
      :spaceBetween="10"
      :slidesPerView="4"
      watch-slides-progress
      watch-slides-visibility
      freeMode="true"
      loop
      :loopedSlides="images.length+1"
      
      class="thumb-swiper"
    >
    <!-- :autoplay="{ delay: 2000, disableOnInteraction: false }" -->
     <SwiperSlide>
        <img :src="'/' + main_image" class="thumb-image" />
      </SwiperSlide>
      <SwiperSlide v-for="(image, index) in images" :key="'thumb-' + index">
        <img :src="'/' + image.path" class="thumb-image" />
      </SwiperSlide>
    </Swiper>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Thumbs, Zoom, Autoplay } from "swiper/modules";

// Swiper CSS
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/thumbs";
import "swiper/css/zoom";

const props = defineProps({
  images: {
    type: Array,
    default: () => [],
  },
  main_image: {
    type: String,
    default: '',
  },
});

const thumbsSwiper = ref(null);
const setThumbsSwiper = (swiper) => (thumbsSwiper.value = swiper);
</script>

<style scoped>
.product-carousel {
  width: 100%; 
  max-width: 500px; /* constrain main swiper width */
  margin: auto;
}

.main-swiper {
  width: 100%;
  height: 500px; /* set fixed height to prevent infinite growth */
}

.main-swiper .swiper-slide {
  display: flex;
  justify-content: center;
  align-items: center;
}

.swiper-zoom-container {
  width: 100%;
  height: 100%;
}

.main-image {
  /* width: 100%; */
  /* height: 100%; */
  object-fit: cover;
  cursor: zoom-in;
}

.thumb-swiper {
  margin-top: 10px;
  width: 100%;
}

.thumb-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  cursor: pointer;
  border: 1px solid #ddd;
  border-radius: 4px;
}

@media (max-width: 768px) {
  .main-swiper {
    height: 300px;
  }
  .thumb-swiper {
    overflow-x: scroll;
  }
}
</style>
