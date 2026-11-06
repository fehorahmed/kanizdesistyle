<template>
  <div
    v-for="product in products"
    :key="product.id"
    class="w-full flex overflow-hidden box-border rounded-lg bg-white border border-gray-200 transition-all duration-300 ease-linear cursor-pointer group hover:border-[#70c332] hover:shadow-[0_15px_35px_#0000001a]"
  >
    <!-- Left: Product Image -->
    <div class="relative w-1/3">
      <router-link :to="{ name: 'product_details', params: { slug: product.slug || 'default-slug' } }">
        <img
          :src="'/' + product.image"
          :alt="product.name"
          class="w-full h-full object-cover rounded-l-lg"
          loading="lazy"
          style="filter: blur(10px); transition: filter 0.3s;"
          @load="($event.target.style.filter='blur(0)')"
        />
      </router-link>

      <!-- Overlay icons -->
      <div class="icons absolute w-full bottom-2 opacity-0 transition-all duration-300 ease-linear transform group-hover:translate-y-[-10px] group-hover:opacity-100">
        <ul class="flex justify-center gap-2">
          <li v-if="product.youtube_video_link">
            <a role="button" @click="openModal(product.youtube_video_link)"
               class="w-8 h-8 flex items-center justify-center bg-primary_color text-white rounded-lg shadow text-base">
              <i class="fas fa-play"></i>
            </a>
          </li>
          <li>
            <a role="button" @click="handleWishlist(product)" title="Add to Wishlist"
               class="w-8 h-8 flex items-center justify-center bg-primary_color text-white rounded-lg shadow text-base">
              <i class="fas fa-heart" :class="{ 'text-red-500': product.inWishlist }"></i>
            </a>
          </li>
          <li>
            <a role="button" @click="handleCompare(product)" title="Compare"
               class="w-8 h-8 flex items-center justify-center bg-primary_color text-white rounded-lg shadow text-base">
              <i class="material-icons !text-[18px]" :class="{ 'text-yellow-500': product.inCompare }">compare</i>
            </a>
          </li>
        </ul>
      </div>

      <!-- Tag -->
      <span class="absolute top-1 left-1 bg-primary_color text-white text-[10px] font-medium px-1 py-0 rounded-md">
        New
      </span>
    </div>

    <!-- Right: Product Body -->
    <div class="flex-1 p-2 flex flex-col justify-between">
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
          class="text-[#39404a] font-medium text-base leading-[20px] mt-1 mb-2"
          style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;"
        >
        {{ product.name }}
      </router-link>

      <!-- Price -->
      <div class="flex items-center gap-2">
        <span class="text-primary_color font-bold text-[17px]">
          {{ $currency(product.selling_price) }}
        </span>
        <span v-if="product.discount > 0" class="text-[#ff3838] font-medium text-sm line-through">
          {{ $currency(product.old_selling_price) }}
        </span>
      </div>

      <div v-if="product.discount_percent > 0" class="mt-1">
        <h3 class="text-gray-700 text-[12px] font-medium">
          Discount:
          <span class="text-red-500 font-bold ml-1">{{ product.discount_percent }}%</span>
        </h3>
      </div>

      <!-- Add to Cart Button -->
      <button
        @click="addToCart(product)"
        class="mt-2 w-full flex items-center justify-center gap-2 px-3 py-2 bg-[#e8e8e8] text-[#39404a] text-sm font-medium border rounded-md transition-all duration-300 ease-linear group-hover:bg-primary_color group-hover:text-white"
      >
        <i class="fas fa-shopping-basket"></i> Add
      </button>
    </div>

    <!-- Modal -->
    <TransitionRoot :show="isOpen" as="template">
      <Dialog as="div" class="fixed inset-0 z-50 overflow-y-auto" @close="closeModal">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
          <DialogOverlay class="fixed inset-0 bg-black bg-opacity-50" />
          <span class="inline-block h-screen align-middle">&#8203;</span>
          <div class="inline-block w-full max-w-3xl p-4 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <button @click="closeModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-[30px]">&times;</button>
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
  </div>
</template>

<script>
import { Dialog, DialogOverlay, TransitionRoot } from "@headlessui/vue";
import { mapActions, mapGetters } from "vuex";
import { useToast } from "vue-toastification";

export default {
  name: "ProductCardTwo",
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
      currentVideo: null
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
