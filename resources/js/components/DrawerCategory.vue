<template>
  <!-- Overlay -->
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 z-40"
    @click="close"
  ></div>

  <!-- Drawer -->
  <div
    :class="[
      'fixed top-0 left-0 h-full w-80 bg-white shadow-lg z-50 transform transition-transform duration-300 ease-in-out flex flex-col',
      isOpen ? 'translate-x-0' : '-translate-x-full'
    ]"
  >
    <!-- Header -->
    <div class="flex justify-between items-center p-3 border-b">
      <h3 class="text-lg font-semibold flex items-center gap-2 m-0 text-primary_color">
        <i class="fas fa-align-left"></i> Categories
      </h3>
      <button v-if="isOpen" @click="close" class="hover:bg-primary_color hover:text-white cursor-pointer w-8 h-8 bg-[#fff] flex items-center justify-center rounded-full text-[#555] text-[15px] font-normal absolute top-2 right-[-10px] shadow-[0_2px_3px_rgba(0,0,0,0.03)] transition-all duration-300 ease-linear">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Category List -->
    <div class="flex-1 overflow-y-auto">
      <ul class="p-[12px] !pt-[0px] space-y-2">
        <li v-for="cat in localCategories" :key="cat.name" class="border-b border-[#e8e8e8] text-[17px] mt-0 py-1">
          <div v-if="cat.sub_categories.length > 0" class="flex justify-between items-center rounded hover:bg-gray-100 cursor-pointer group"
               @click="toggleSub(cat.name)">
            <a class="w-full p-2 text-[#555]" >{{ cat.name }}</a>
            <i class="fa fa-angle-right text-[#555] mr-2 group-hover:text-primary_color" :class="{ 'rotate-90': cat.open }"></i>
          </div>
          <div v-else class="flex justify-between items-center rounded hover:bg-gray-100 cursor-pointer">
            <router-link :to="{ name: 'products', params: { slug: cat.slug } }" class="w-full p-2 text-[#555]">{{ cat.name }}</router-link>
          </div>
          <ul v-if="cat.sub_categories && cat.open" class="pl-2 mt-1 space-y-1">
            <li class="hover:bg-gray-100 cursor-pointer rounded">
              <router-link :to="{ name: 'products', params: { slug: cat.slug } }" class="block w-full p-2 text-[#555]"><i class="fas fa-long-arrow-alt-right"></i> {{ cat.name }}(C)</router-link>
            </li>
            <li v-for="sub in cat.sub_categories" :key="sub" class="hover:bg-gray-100 cursor-pointer rounded">
              <router-link :to="{ name: 'products', params: { slug: sub.slug } }" class="block w-full p-2 text-[#555]"><i class="fas fa-long-arrow-alt-right"></i> {{ sub.name }}</router-link>
            </li>
          </ul>
        </li>
      </ul>
    </div>

    <!-- Footer -->
    <div class="p-3 border-t text-sm text-gray-500">
      <p class="m-0">
        All Rights Reserved by
        <a href="https://soft360.tech" target="_blank" class="text-primary_color hover:underline">Soft360</a>
      </p>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
  name: "DrawerCategory",
  data() {
    return {
      isOpen: false,
      localCategories: [], 
    };
  },
  computed: {
    ...mapGetters('home', ['categories', 'categoryLoading']),
  },
  watch: {
    categories: {
      immediate: true,
      handler(newCategories) {
        // clone Vuex categories and add `open` field
        this.localCategories = newCategories.map((c) => ({
          ...c,
          open: false,
        }));
      },
    },
    $route() {
      this.close();
    },
  },
  methods: {
    open() {
      this.isOpen = true;
    },
    close() {
      this.isOpen = false;
    },
    toggleSub(name) {
      this.localCategories.forEach(cat => {
        if (cat.name === name) {
          cat.open = !cat.open; 
        } else {
          cat.open = false; 
        }
      });
    },
  },
};
</script>

<style scoped>
.rotate-90 {
  transform: rotate(90deg);
  transition: transform 0.3s;
}
</style>
