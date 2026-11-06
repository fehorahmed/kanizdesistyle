<template>
    
    <div>
        <header>
            <div  class="header-top border-bottom bg-white ">
                <div class="container custom_container header-top-container">
                    <div class="row">
                        <div class="col-xl-2 col-lg-2 col-3">
                            <div class="text-left header-top-left pt-2"><router-link :to="{ name: 'home' }"><img src="/public/img/logo.jpg" style="width: 69px;" class="img-responsive img"  alt="Ecommerce"></router-link></div>
                        </div> 
                        <div class="col-xl-10 py-3 col-lg-10 col-9 head-search">
                            <div class="d-flex navbar row">
                                <div class="input-class text-left col-xl-9 col-lg-9 col-10">
                                    <div class="between-header p-0  rounded mb-0 head-left">                        
                                        <div class="input-group">
                                            <!-- <TypingInput/> -->
                                            <input 
                                                type="text" 
                                                v-model="searchQuery"
                                                @input="handleSearchInput"
                                                @focus="showDropdown = true"
                                                @blur="hideDropdown"
                                                :placeholder="typedPlaceholder + (showCursor ? '|' : '')" 
                                                class="!bg-[#eee] form-control !text-[15px]" 
                                                aria-label="Search products in Kaniz's Desi Style"
                                            >
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-danger w-auto text-uppercase font-weight-normal"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>

                                        <!-- Search Dropdown -->
                                        <ul 
                                        v-if="showDropdown && searchResults.length" 
                                        class="absolute z-50 w-full bg-white border rounded shadow-lg mt-1 max-h-60 overflow-y-auto"
                                        >
                                            <li 
                                                v-for="product in searchResults" 
                                                :key="product.id"
                                                @mousedown.prevent="selectProduct(product)"
                                                class="flex items-center px-3 py-2 hover:bg-gray-100 cursor-pointer"
                                            >
                                                <router-link :to="{ name: 'product_details', params: { slug: product.slug } }" class="flex items-center w-full">
                                                    <img :src="product.image" alt="product.name" class="w-10 h-10 object-cover mr-3">
                                                    <span class="text-gray-800">{{ product.name }}</span>   
                                                </router-link>
                                            </li>
                                        </ul>

                                    </div> 
                                </div>

                                <div class="col-xl-3 col-lg-3 col-2 text-right">
                                    <ul>
                                        
                                        <li class="d-lg-inline-block d-md-none d-sm-none d-none">
                                            <router-link
                                                :to="{ name: 'wishlist' }"
                                                class="relative w-10 h-10 flex items-center justify-center text-[15px] text-[#555] bg-[#f5f5f5] rounded-full transition-all duration-300 ease-linear hover:bg-primary_color hover:text-white"
                                            >
                                                <i class="fas fa-heart flex items-center justify-center"></i>
                                                <span class="absolute -top-3 left-5 w-6 h-6 text-[12px] leading-[20px] rounded-full text-center text-white bg-primary_color border-2 border-white shadow-[2px_3px_8px_rgba(0,0,0,0.1)]">{{ wishlistCount.toLocaleString('en-US', { minimumIntegerDigits: 2, useGrouping: false }) }}</span>
                                            </router-link>
                                        </li>

                                        <li class="d-lg-inline-block d-md-none d-sm-none d-none ml-2">
                                            <router-link
                                                :to="{ name: 'compare' }"
                                                class="relative w-10 h-10 flex items-center justify-center text-[15px] text-[#555] bg-[#f5f5f5] rounded-full transition-all duration-300 ease-linear hover:bg-primary_color hover:text-white"
                                            >
                                                <!-- <i class="fas fa-heart flex items-center justify-center"></i> -->
                                                <span class="material-icons flex items-center justify-center !text-[15px]">compare</span>
                                                <span class="absolute -top-3 left-5 w-6 h-6 text-[12px] leading-[20px] rounded-full text-center text-white bg-primary_color border-2 border-white shadow-[2px_3px_8px_rgba(0,0,0,0.1)]">{{ compareCount.toLocaleString('en-US', { minimumIntegerDigits: 2, useGrouping: false }) }}</span>
                                            </router-link>
                                        </li>

                                        <li class="dropdown d-inline-block !ml-0 md:!ml-2">
                                            <span class="dropdown-toggle" role="menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <a class="relative 
                                                        w-9 h-9 sm:w-10 sm:h-10 md:w-10 md:h-10 
                                                        flex items-center justify-center 
                                                        text-[13px] sm:text-[15px] md:text-[18px] 
                                                        text-[#555] bg-[#f5f5f5] rounded-full 
                                                        transition-all duration-300 ease-linear 
                                                        cursor-pointer hover:bg-primary_color hover:text-white">
                                                    <i class="fas fa-user flex items-center justify-center"></i>
                                                </a>

                                            </span>
                                            <div v-if="isLoggedIn" class="dropdown-menu r_menu dropdown-menu-right" >
                                                <router-link class="dropdown-item font-weight-bolder hover:!text-primary_color" :to="{ name: 'account.dashboard' }">dashboard</router-link>
                                                <router-link class="dropdown-item font-weight-bolder hover:!text-primary_color" :to="{ name: 'account.orders' }">orders</router-link>
                                                <router-link class="dropdown-item font-weight-bolder hover:!text-primary_color" :to="{ name: 'account.profile' }">profile</router-link>
                                                <router-link class="dropdown-item font-weight-bolder hover:!text-primary_color" :to="{ name: 'account.password' }">change password</router-link>
                                            </div>
                                            <div v-else class="dropdown-menu r_menu dropdown-menu-right" >
                                                <router-link class="dropdown-item font-weight-bolder hover:!text-primary_color" :to="{ name: 'login' }">log in</router-link>
                                                <router-link class="dropdown-item font-weight-bolder hover:!text-primary_color" :to="{ name: 'register' }">register</router-link>
                                            </div>
                                        </li>

                                    </ul>
                                </div> 

                            </div> 
                        </div> 
                    </div> 
                </div>
            </div> 

            <div id="sp_header_top" class="header_bottom shadow-sm bg-white rounded bg-white d-md-none d-sm-none d-none d-lg-block">
                <div class="container custom_container">
                    <div class="row">
                        
                        <div class="col-lg-9 text-left sp_hz_menu">
                            <ul class="main-menu navbar">
                                <li><router-link class="text-[15px] text-[#555] font-semibold font-poppins" :to="{ name: 'home' }" :class="{ active: $route.name === 'home' }">Home</router-link></li>
                                <li><router-link class="text-[15px] text-[#555] font-semibold font-poppins" :to="{ name: 'products' }" :class="{ active: $route.name === 'products' }">Products</router-link></li>
                                <!-- <li><router-link class="text-[15px] text-[#555] font-semibold font-poppins" :to="{ name: 'blog' }" :class="{ active: $route.name === 'blog' }">blog</router-link></li> -->
                                <li><router-link class="text-[15px] text-[#555] font-semibold font-poppins" :to="{ name: 'gallery' }" :class="{ active: $route.name === 'gallery' }">gallery</router-link></li>
                                <!-- <li><router-link class="text-[15px] text-[#555] font-semibold font-poppins" :to="{ name: 'about' }" :class="{ active: $route.name === 'about' }">about us</router-link></li> -->

                            </ul>
                        </div> 
                
                    </div> 
                </div> 
            </div> 

        </header>
        <div id="sp_vertical_menu" class="container custom_container menu_slider d-xl-none d-lg-none d-md-block my-0">
            <div class="row">
                <div class="col-lg-3 vertical_menu">
                    <div class="v_menu bg-white rounded">
                        <div class="cat_menu bg-primary_color rounded-top" @click="openCategoryDrawer"><a class="pe-auto text-white text-uppercase d-md-none d-sm-none d-none d-lg-block font-weight-bold md:text-[15px]" data-toggle="collapse" href="#vartical_collapse" role="button" aria-expanded="false" aria-controls="vartical_collapse"><i class="fas fa-bars"></i>top categories</a></div>
                    </div> 
                </div> 
            </div>
        </div>
    </div>
    <DrawerParent/> 
    <DrawerCategory ref="categoryDrawer"/>
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
import TypingInput from './TypingInput.vue';
import DrawerParent from './DrawerParent.vue';
import DrawerCategory from './DrawerCategory.vue';

export default {
    name: 'Header',
    data() {
        return {
            fullText: 'Search products in Kaniz\'s Desi Style',
            typedPlaceholder: '',
            index: 0,
            isDeleting: false,
            showCursor: true,
            searchQuery: '',
            searchResults: [],
            showDropdown: false,
            typingTimeout: null,
        };
    },
    created() {
        this.fetchCategories();
        this.fetchCart();
        if(this.isLoggedIn){
            this.fetchWishlist();
        }
        this.fetchCompare();
    },
    computed: {
        ...mapGetters({
            isLoggedIn: 'auth/isLoggedIn',
        }),
        ...mapGetters('home', ['categories', 'categoryLoading']),
        ...mapGetters('wishlist', ['wishlistCount', 'wishlistLoading']),
        ...mapGetters('compare', ['compareCount', 'compareLoading'])
    },
     mounted() {
        this.startTypingEffect()
        this.startCursorBlink()
    },
    methods:{
        openCategoryDrawer() {
            this.$refs.categoryDrawer.open();
        },
        handleSearchInput() {
            clearTimeout(this.typingTimeout);

            if (!this.searchQuery.trim()) {
                this.searchResults = [];
                return;
            }

            this.typingTimeout = setTimeout(async () => {
                // Replace with your API endpoint
                const response = await this.$axios.get('/api/get-search', {
                    params: { q: this.searchQuery, limit: 5 }
                });
                this.searchResults = response.data;
            }, 300);
        },

        hideDropdown() {
            setTimeout(() => { this.showDropdown = false; }, 200);
        },

        // selectProduct(product) {
        //     this.$router.push({ name: 'product.detail', params: { slug: product.slug } });
        //     this.searchQuery = '';
        //     this.searchResults = [];
        // },
        ...mapActions('home', ['fetchCategories']),
        ...mapActions('cart', ['fetchCart']),
        ...mapActions('wishlist', ['fetchWishlist']),
        ...mapActions('compare', ['fetchCompare']),
        startTypingEffect() {
      const typeEffect = () => {
        if (!this.isDeleting) {
          // Typing forward
          this.typedPlaceholder = this.fullText.slice(0, this.index)
          this.index++

          if (this.index > this.fullText.length) {
            // Pause before deleting
            this.isDeleting = true
            setTimeout(typeEffect, 1500)
            return
          }
        } else {
          // Deleting backward
          this.typedPlaceholder = this.fullText.slice(0, this.index)
          this.index--

          if (this.index < 0) {
            // Pause before re-typing
            this.isDeleting = false
            this.index = 0
            setTimeout(typeEffect, 1500)
            return
          }
        }

        const speed = this.isDeleting ? 50 : 100
        setTimeout(typeEffect, speed)
      }

      // Start after short delay to avoid flashing full text
      setTimeout(typeEffect, 500)
    },

    startCursorBlink() {
      setInterval(() => {
        this.showCursor = !this.showCursor
      }, 500)
    },
    },
    components:{
        TypingInput,
        DrawerParent,
        DrawerCategory,
    },
};
</script>
  
<style scoped>

</style>