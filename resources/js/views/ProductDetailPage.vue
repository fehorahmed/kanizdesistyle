<template>
  <div>
    <!-- header area -->
    <Header />

    <div id="single_product" class="single_product_page animate__animated animate__fadeInUp">
      <div class="sp_header bg-white p-3">
        <div class="container custom_container">  
          <div class="row">
            <div class="col-12">
              <ul class="flex space-x-2 overflow-x-auto">
                <li class="d-inline-block font-weight-bolder">
                  <router-link :to="{ name: 'home' }">home</router-link>
                </li>
                <li class="d-inline-block truncate hr_ font-weight-bolder"><a href="#">{{ product.name }}</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="container custom_container sp_pro_container">
        <div class="row sh_page">
          <div class="col-xl-5 col-md-6 col-12">
            <ProductDetailSlider :images="product.images" :main_image="product.image" />
          </div>
          <div class="col-xl-7 col-md-6 col-12 mt-3">
            <div class="sp_product_detail">
              <h1 class="font-weight-bold text-uppercase">
                <a href="#" class="text-[#39404a] font-bold text-[24px] leading-[30px] mb-2 transition-all duration-300 ease-linear">
                  {{ product.name }}
                </a>
              </h1>

              <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 my-3">
                <div class="product-other-info w-full md:w-1/2">
                  <div class="sp_add_info my-3">
                    <ul>
                      <li class="sku my-2">
                        <span class="font-weight-bolder">Product Code: </span>
                        <span>{{ product.code }}</span>
                      </li>
                      <li v-if="product?.brand" class="availability my-2">
                        <span class="font-weight-bolder">BRAND: </span>
                        <span>{{ product?.brand?.name }}</span>
                      </li>
                    </ul>
                  </div>

                  <div class="flex items-center gap-1 text-yellow-500 mt-2 mb-3">
                    <ul class="flex text-yellow-500">
                      <li v-if="productLoading" v-for="i in 5" :key="i">
                        <span class="block w-4 h-4 bg-gray-300 rounded-full animate-pulse"></span>
                      </li>
                      <li v-else v-for="i in 5" key="i">
                        <i :class="i <= product.rating ? 'fas fa-star' : 'far fa-star'"></i>
                      </li>
                    </ul>
                    <span class="ml-1 text-sm text-gray-600">({{ product.total_review }})</span>
                  </div>

                  <div class="product-price flex items-center gap-2 mb-2">
                    <span v-if="product.discount > 0" class="text-[#ff3838] font-medium text-sm line-through">
                      {{ $currency(product.old_selling_price) }}
                    </span>
                    <span class="text-primary_color font-bold text-[17px]">
                      {{ $currency(product.selling_price) }}
                    </span>
                  </div>

                  <div v-if="product.discount > 0" class="product-price">
                    <h3 class="text-gray-700 text-base font-medium">
                      Discount : 
                      <span class="text-red-500 font-bold ml-1">{{ product.discount_percent }}%</span>
                    </h3>
                  </div>
                </div>
                <div class="product-video my-3 w-full md:w-1/2">
                  <div v-if="youtubeEmbedUrl" class="relative pt-[56.25%]">
                    <iframe
                      :src="youtubeEmbedUrl"
                      frameborder="0"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                      allowfullscreen
                      class="absolute top-0 left-0 w-full h-full rounded-md"
                    ></iframe>
                  </div>
                </div>
              </div>

              <div class="sp_text !text-[15px]">
                {{ product.short_description }}
              </div>

              <!-- <div class="sp_counter">
                <div class="sp_c_count1">
                  <div class="number">
                    <div class="sp_counter">
                      <div class="input-group">
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default btn-number p-0" disabled data-type="minus" data-field="quant[1]"><span class="minus">-</span></button>
                        </span>
                        <input type="number" name="quant[1]" class="form-control input-number" value="1" min="1" max="10">
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default btn-number p-0" data-type="plus" data-field="quant[1]"><span class="plus">+</span></button>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div> -->
              <div class="flex gap-3 mt-4">
                <button @click="addToCart(product)" class="flex-1 text-uppercase bg-primary_color text-white font-medium py-2 px-4 rounded-lg hover:bg-[#539522] transition">
                  <i class="fas fa-shopping-basket"></i> Add to Cart
                </button>
                <button class="flex-1 bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg hover:bg-gray-300 transition">
                  Buy It Now
                </button>
              </div>

              <div class="sp_wish_com my-3">
                <span class="sp_comp1"><a role="button" @click="handleWishlist(product)" class="text-uppercase font-weight-bolder"><i class="far fa-heart"></i>add TO WISHLIST</a></span>
                <span class="sp_comp2"><a role="button" @click="handleCompare(product)" class="text-uppercase font-weight-bolder"><i class="fas fa-tasks"></i>ADD TO COMPARE</a></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <ProductTabs :product="product" />
      </div>

      <div class="container custom_container t_pro_container fadeInUp">
        <div class="row">
          <div class="col-12">
            <RelatedProducts :products="relatedProducts" />
          </div>
        </div>
      </div>

    </div>

    <!-- footer -->
    <Footer />

  </div>
</template>

<script>
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';
import { mapGetters, mapActions } from 'vuex';
import ProductDetailSlider from '../components/product/ProductDetailSlider.vue';
import RelatedProducts from '../components/product/RelatedProducts.vue';
import { useHead } from '@vueuse/head';
import ProductTabs from '../components/product/ProductTabs.vue';
import { useToast } from "vue-toastification";

export default {
  name: "ProductDetailPage",
  computed: {
    ...mapGetters({ isLoggedIn: 'auth/isLoggedIn'}),
    ...mapGetters('product_detail', ['product', 'relatedProducts', 'productLoading', 'seo']),

    youtubeVideoId() {
      if (!this.product.youtube_video_link) return null;

      // Regex to extract ID from different YouTube URLs
      const regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/;
      const match = this.product.youtube_video_link.match(regex);
      return match ? match[1] : null;
    },

    // Generate iframe embed URL
    youtubeEmbedUrl() {
      return this.youtubeVideoId
        ? `https://www.youtube.com/embed/${this.youtubeVideoId}`
        : null;
    },
  },
  created() {
    const slug = this.$route.params.slug;
    this.fetchProductData(slug);
  },
  mounted() {
    if (window.innerWidth > 991) {
      $("#sp_vertical_menu .vertical_menu").insertBefore("#sp_header_top .col-lg-9.text-left");
    }
  },
  watch: {
    '$route.params.slug'(newSlug) {
      this.fetchProductData(newSlug);
    },
    seo: {
      handler(seo) {
        useHead({
          title: seo.title,
          meta: [
            { name: 'description', content: seo.description },
            { property: 'og:title', content: seo.title },
            { property: 'og:description', content: seo.description },
            { property: 'og:type', content: 'product' },
            { property: 'og:url', content: seo.url },
            { property: 'og:image', content: seo.image },
            { name: 'twitter:card', content: 'summary_large_image' },
            { name: 'twitter:title', content: seo.title },
            { name: 'twitter:description', content: seo.description },
            { name: 'twitter:image', content: seo.image },
          ],
          link: [
            { rel: 'canonical', href: seo.url }
          ],
        });
      },
      immediate: true,
      deep: true
    }
  },
  methods: {
    ...mapActions('product_detail', ['fetchProductData']),
    ...mapActions("cart", ["addToCart"]),
    ...mapActions("wishlist", ["addToWishlist", "removeFromWishlist"]),
    ...mapActions("compare", ["addToCompare", "removeFromCompare"]),
    handleWishlist(product) {
      const toast = useToast();
      // toggle wishlist (example)
      if(!this.isLoggedIn){
        toast.error("Please log in to add items to your wishlist.");
        return;
      }
      if (product.inWishlist) {
        this.removeFromWishlist(product.id);
        product.inWishlist = false;
      } else {
        this.addToWishlist(product.id);
        product.inWishlist = true;
      }
    },

    handleCompare(product) {
      // toggle compare
      if (product.inCompare) {
        this.removeFromCompare(product.id);
        product.inCompare = false;
      } else {
        this.addToCompare(product.id);
        product.inCompare = true;
      }
    },
  },
  components: {
    Header,
    Footer,
    ProductDetailSlider,
    RelatedProducts,
    ProductTabs,
  },
};
</script>
