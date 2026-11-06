<template>
  <router-view></router-view>
</template>

<script>
// Import Swiper styles
import 'swiper/swiper-bundle.css';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';
import 'swiper/css/effect-fade';
import 'swiper/css/autoplay';
import 'swiper/css/effect-coverflow';

export default {
    name: 'App',
};
</script>

<style>
  .custom-toast {
    padding: 6px 10px !important;
    min-height: unset !important;
  }

  .custom-toast-body {
    font-size: 14px !important;
    line-height: 1.2;
    padding: 4px 6px;
  }
  .svg-inline--fa.fa-w-16 {
      width: 1.2em;
  }
  .Vue-Toastification__icon {
    margin: auto 0px auto 0px;
  }
</style>
