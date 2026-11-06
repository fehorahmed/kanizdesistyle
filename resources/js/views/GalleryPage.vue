<template>
  <div>
    <!-- header area -->
    <Header />

    <div id="portfolio_3_page" class="portfolio-3-page portfolio animate__animated animate__fadeInUp">
      <div class="sp_header bg-white p-3">
        <div class="container custom_container">
          <div class="row">
            <div class="col-12">
              <ul>
                <li class="d-inline-block font-weight-bolder">
                  <router-link :to="{ name: 'home' }">home</router-link>
                </li>
                <li
                  class="d-inline-block hr_ font-weight-bolder"
                >
                  <router-link :to="{ name: 'gallery' }" :class="{ active: $route.name === 'gallery' }">
                    gallery
                  </router-link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <!-- Portfolio Gallery Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Skeleton loading -->
           
          <template v-if="galleryLoading">
            <div v-for="n in 6" :key="n" class="overflow-hidden rounded-lg shadow-md border-[3px] border-[#e4e4e4] animate-pulse bg-gray-200 h-60"></div>
          </template>

          <!-- Actual gallery -->
          <template v-else>
            <div
              v-for="(gallery, index) in galleries"
              :key="index"
              class="overflow-hidden rounded-lg shadow-md border-[3px] border-[#e4e4e4]"
            >
              <div v-if="!imageLoaded[index]" class="overflow-hidden rounded-lg shadow-md border-[3px] border-[#e4e4e4] animate-pulse bg-gray-200 h-60"></div>
              <a
                :href="gallery.image"
                data-fancybox="gallery"
                :data-caption="gallery.title"
              >
                <img
                  :src="gallery.image"
                  :alt="gallery.title"
                  class="w-full h-auto object-cover transition-transform duration-300 hover:scale-105"
                  :class="{'opacity-0': !imageLoaded[index], 'opacity-100': imageLoaded[index]}"
                  @load="onImageLoad(index)"
                />
              </a>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- footer -->
    <Footer />
  </div>
</template>

<script>
import Header from "../components/Header.vue";
import Footer from "../components/Footer.vue";
import { Fancybox } from "@fancyapps/ui/dist/fancybox/";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import { mapActions, mapGetters } from "vuex";

export default {
  name: "GalleryPage",
  data() {
        return {
            imageLoaded: {},
        };
    },
  components: { Header, Footer },
  computed:{
    ...mapGetters("home", ["galleries","galleryLoading"]),
  },
  created(){
    this.fetchGalleries();
  },
  methods:{
    ...mapActions("home", ["fetchGalleries"]),
    onImageLoad(index) {
      this.imageLoaded = { ...this.imageLoaded, [index]: true };
    },
  },
  mounted() {
    Fancybox.bind("[data-fancybox='gallery']", {
      Thumbs: { autoStart: true },
    });

    if (window.innerWidth > 991) {
      $("#sp_vertical_menu .vertical_menu").insertBefore(
        "#sp_header_top .col-lg-9.text-left"
      );
    }
  },
};
</script>
