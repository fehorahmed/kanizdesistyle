<template>
  <nav
    class="fixed bottom-0 left-0 right-0 bg-white shadow-md block [@media(min-width:992px)]:hidden z-50 rounded-t-lg shadow-[0_-5px_15px_rgba(0,0,0,0.1)]"
  >
    <ul class="flex justify-around items-center text-center pt-2">
      <li>
        <router-link :to="{ name: 'home' }" :class="{ active: $route.name === 'home' }" class="flex flex-col items-center py-2 text-gray-600 hover:text-primary_color">
          <i class="fa fa-home text-xl"></i>
          <h4 class="text-xs mt-1 mb-0">Home</h4>
        </router-link>
      </li>
      <li>
        <a role="button" @click="categoryDrawer.open()" class="flex flex-col items-center py-2 text-gray-600 hover:text-primary_color">
          <i class="fas fa-list text-xl"></i>
          <h4 class="text-xs mt-1 mb-0">Category</h4>
        </a>
      </li>
      <li class="relative">
        <a role="button" @click="drawer.open()" class="flex flex-col items-center py-2 text-gray-600 hover:text-primary_color">
          <i class="fas fa-shopping-basket text-xl"></i>
          <h4 class="text-xs mt-1 mb-0">Carts</h4>
          <small
            class="absolute -top-1 -right-2 bg-primary_color text-white text-xs rounded-full px-1"
          >{{ totalCartItem.toLocaleString('en-US', { minimumIntegerDigits: 2, useGrouping: false }) }}</small>
        </a>
      </li>
      <li class="relative">
        <router-link :to="{ name: 'wishlist' }" :class="{ active: $route.name === 'wishlist' }" class="flex flex-col items-center py-2 text-gray-600 hover:text-primary_color">
          <i class="fas fa-heart text-xl"></i>
          <h4 class="text-xs mt-1 mb-0">Wishlist</h4>
          <small
            class="absolute -top-1 -right-2 bg-primary_color text-white text-xs rounded-full px-1"
          >{{ wishlistCount.toLocaleString('en-US', { minimumIntegerDigits: 2, useGrouping: false }) }}</small>
        </router-link>
      </li>
    </ul>
  </nav>

  <DrawerCart ref="drawer" />
  <DrawerCategory ref="categoryDrawer" />
</template>

<script>
import { mapGetters } from "vuex";
import DrawerCart from "./DrawerCart.vue";
import DrawerCategory from "./DrawerCategory.vue";

export default {
  name: "BottomNav",
  components: {
    DrawerCart,
    DrawerCategory,
  },
  data() {
    return {
      drawer: null,
      categoryDrawer: null,
    };
  },
  computed:{
    ...mapGetters('cart', ['totalCartItem', 'cartLoading']),
    ...mapGetters('wishlist', ['wishlistCount', 'wishlistLoading']),
  },
  mounted() {
    // Assign refs after component mounts
    this.drawer = this.$refs.drawer;
    this.categoryDrawer = this.$refs.categoryDrawer;
  },
};
</script>

<style scoped>
a {
  transition: color 0.2s ease-in-out;
}
</style>
