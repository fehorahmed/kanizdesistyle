<template>
  <div
    v-for="product in products"
    :key="product.id"
    class="w-full overflow-hidden box-border rounded-[5px] bg-[#fff] !border border-gray-200 transition-all duration-300 ease-linear cursor-pointer group hover:border-primary_color hover:shadow-[0_15px_35px_#0000001a]"
  >
    <!-- Product Image -->
    <div class="product-image relative">
      <router-link :to="{ name: 'product_details', params: { slug: product.slug || 'default-slug' } }">
        <div v-if="!imageLoaded[product.id]" class="w-full h-48 bg-gray-200 animate-pulse rounded-t-lg"></div>
        <img
          :src="'/'+product.image"
          :alt="product.name"
          loading="lazy"
          class="w-full object-cover rounded-t-[5px] transition-all duration-500 ease-in-out"
          :class="{'blur-xl scale-105': !imageLoaded[product.id], 'blur-0 scale-100': imageLoaded[product.id]}"
          @load="handleImageLoad(product.id)"
        />
      </router-link>
      <!-- Overlay icons -->
      <div class="icons absolute w-full bottom-0 opacity-0 transition-all duration-400 ease-linear transform translate-y-0 group-hover:translate-y-[-15px] group-hover:opacity-100 group-hover:visible">
        <ul class="flex justify-center gap-2">
            <li v-if="product.youtube_video_link != null">
                <a role="button" @click="openModal(product.youtube_video_link)" class="w-8 h-8 flex items-center justify-center bg-primary_color text-white rounded-lg shadow-[0_7px_12px_#0000001f] text-base">
                    <i class="fas fa-play"></i>
                </a>
            </li>
            <li>
                <a role="button" @click="handleWishlist(product)" title="Add to Wishlist" class="w-8 h-8 flex items-center justify-center bg-primary_color text-white rounded-lg shadow-[0_7px_12px_#0000001f] text-base">
                    <i class="fas fa-heart" :class="{ 'text-red-500': product.inWishlist }"></i>
                </a>
            </li>
            <li>
                <a role="button" @click="handleCompare(product)" title="Compare" class="w-8 h-8 flex items-center justify-center bg-primary_color text-white rounded-lg shadow-[0_7px_12px_#0000001f] text-base">
                    <i class="material-icons !text-[18px]" :class="{ 'text-yellow-500': product.inCompare }">compare</i>
                </a>
            </li>
        </ul>
      </div>

      <!-- Tag -->
      <span
        class="tag absolute top-1 left-1 bg-primary_color text-white text-[10px] font-medium px-1 py-0 rounded-md"
      >
        New
      </span>
    </div>

    <!-- Body -->
    <div class="product-body space-y-2 px-[10px] py-[10px]">
      <!-- Ratings -->
      <div class="flex items-center gap-1 text-yellow-500">
        <ul class="flex text-yellow-500">
          <li v-for="i in 5" :key="i">
            <i :class="i <= product.rating ? 'fas fa-star' : 'far fa-star'"></i>
          </li>
        </ul>
        <span class="ml-1 text-sm text-gray-600">({{ product.total_review }})</span>
      </div>

      <!-- Title -->
      <router-link
        :to="{ name: 'product_details', params: { slug: product.slug || 'default-slug' } }"
        class="text-[#39404a] font-normal text-base 
         leading-[18px] mb-[0px] mt-0 
         min-h-[38px] 
         overflow-hidden"
        style="-webkit-box-orient: vertical; display: -webkit-box; -webkit-line-clamp: 2;"
      >
        {{ product.name }}
      </router-link>
 
      <!-- Price -->
      <div class="product-price flex items-center gap-2 !mt-[2px]">
        <span class="text-primary_color font-bold text-[17px]">
            {{ $currency(product.selling_price) }}
        </span>
        <span v-if="product.discount > 0" class="text-[#ff3838] font-medium text-sm line-through">
            {{ $currency(product.old_selling_price) }}
        </span>
      </div>
      <div v-if="product.discount_percent > 0" class="product-price mt-0">
        <h3 class="text-gray-700 text-[12px] font-medium">
            Discount : 
            <span class="text-red-500 font-bold ml-1">{{ product.discount_percent }}%</span>
        </h3>
      </div>
      <div v-else class="h-[7px]">

      </div>

      <!-- Button -->
      <button
        @click="addToCart(product)"
        class="w-full flex items-center justify-center gap-2.5 px-[10px] py-[3px] bg-[#e8e8e8] text-[#39404a] text-base font-normal border border-transparent rounded-[7px] transition-all duration-300 ease-linear group-hover:bg-primary_color group-hover:text-[#fff]"
      >
        <i class="fas fa-shopping-basket"></i> Add
      </button>
    </div>
  </div>

  <!-- Modal -->
    <TransitionRoot :show="isOpen" as="template">
      <Dialog as="div" class="fixed inset-0 z-50 overflow-y-auto" @close="closeModal">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
          <DialogOverlay class="fixed inset-0 bg-black bg-opacity-50" />

          <span class="inline-block h-screen align-middle" aria-hidden="true">&#8203;</span>

          <div class="inline-block w-full max-w-3xl p-4 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <!-- Close Button -->
            <button @click="closeModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-[30px]">&times;</button>

            <!-- YouTube iframe -->
            <div class="w-full aspect-video">
              <iframe
                v-if="currentVideo"
                :src="`https://www.youtube.com/embed/${extractVideoID(currentVideo)}?autoplay=1`"
                frameborder="0"
                allow="autoplay; encrypted-media"
                allowfullscreen
                class="w-full h-full rounded-lg"
              ></iframe>
            </div>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
</template>

<script>
import { Dialog, DialogOverlay, TransitionRoot } from "@headlessui/vue";
import { mapActions, mapGetters } from "vuex";
import { useToast } from "vue-toastification";

export default {
  name: "ProductCard",
  data() {
    return {
      drawer: null,
    };
  },
  components: {
    Dialog,
    DialogOverlay,
    TransitionRoot
  },
  props: {
    products: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      isOpen: false,
      currentVideo: null,
      imageLoaded: {},
    };
  },
  computed:{
     ...mapGetters({ isLoggedIn: 'auth/isLoggedIn',}),
  },
  methods: {
    ...mapActions("cart", ["addToCart"]),
    ...mapActions("wishlist", ["addToWishlist", "removeFromWishlist"]),
    ...mapActions("compare", ["addToCompare", "removeFromCompare"]),
    openModal(link) {
      this.currentVideo = link;
      this.isOpen = true;
    },
    closeModal() {
      this.isOpen = false;
      this.currentVideo = null;
    },
    extractVideoID(url) {
      const regex = /(?:https?:\/\/)?(?:www\.)?youtu(?:be\.com\/watch\?v=|\.be\/)([\w-]{11})/;
      const match = url.match(regex);
      return match ? match[1] : null;
    },
    handleImageLoad(id) {
      this.imageLoaded = { ...this.imageLoaded, [id]: true };
    },
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
    }
  }
};
</script>
