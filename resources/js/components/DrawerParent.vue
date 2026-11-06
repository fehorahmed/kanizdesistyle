<template>
  <!-- Fixed Cart Box -->
  <div class="hidden [@media(min-width:992px)]:block fixed bottom-56 right-0 z-50">
    <div
      class="bg-white !w-20 shadow-lg !border overflow-hidden !border-primary_color rounded-md items-center cursor-pointer hover:shadow-xl transition"
      @click="openDrawer"
    >
      <div class="text-center gap-2 py-2 bg-primary_color">
        <i class="fas fa-shopping-basket text-xl text-white"></i><br/>
        <!-- Format totalItems to 2 digits -->
        <span class="text-sm font-medium text-white">
          {{ totalCartItem.toLocaleString('en-US', { minimumIntegerDigits: 2, useGrouping: false }) }} Items
        </span>
      </div>

      <div class="cart-price mt-1 py-1 text-center text-primary_color">
        ৳{{ cartTotal }}
      </div>
    </div>
  </div>

  <!-- DrawerCart Component -->
  <DrawerCart ref="drawer" />
</template>

<script>
import { mapGetters } from "vuex";
import DrawerCart from "./DrawerCart.vue";

export default {
  components: { DrawerCart },
  data() {
    return {
      drawer: null,
    };
  },
  computed: {
    ...mapGetters("cart",["totalCartItem", "cartTotal"]),
  },
  mounted() {
    this.drawer = this.$refs.drawer;
  },
  methods: {
    openDrawer() {
      if (this.drawer) this.drawer.open();
    },
  },
};
</script>

<style scoped>
.cart-box {
  width: 100px;
  padding: 0.75rem;
}

.cart-box-top i {
  /* Optionally adjust icon size */
}
</style>
