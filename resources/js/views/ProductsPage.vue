<template>
  <div>
    <!-- header area -->
    <Header></Header>

    <div id="shop_page" class="shop-page animate__animated animate__fadeInUp">
      <div class="sp_header bg-white p-3">
        <div class="container custom_container">
          <div class="row">
            <div class="col-12">
              <ul>
                <li class="d-inline-block font-weight-bolder">
                  <router-link :to="{ name: 'home' }">home</router-link>
                </li>
                <li class="d-inline-block hr_ font-weight-bolder">
                  <router-link
                    :to="{ name: 'products' }"
                    :class="{ active: $route.name === 'products' }"
                  >products</router-link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="container custom_container">
        <div class="row">
          <!-- Sidebar Filters -->
          <div class="col-lg-3 col-md-4">
            <div class="shop_sidebar border card-body rounded bg-white">
              <h2 class="text-uppercase card-title font-weight-bold d-md-block d-none">
                filter by
              </h2>
			  
              <!-- Price Range -->
              <div class="s_filter mt-0">
                <h6 class="text-uppercase border-bottom font-weight-bold !text-primary_color">
                  Price
                  <button class="toggle collapsed" data-toggle="collapse" data-target="#shop_range_collapse"></button>
                </h6>
                <div class="collapse" id="shop_range_collapse">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-6 pr-1">
							<input type="number" class="w-full h-[40px] rounded-[5px] bg-[#f5f5f5] text-center outline-none border border-transparent font-normal text-[15px] text-[#555] custom-number mb-2" placeholder="Min Price" v-model="minPrice" />
						</div>
						<div class="col-md-6 col-sm-6 col-6 pl-1">
							<input type="number" class="w-full h-[40px] rounded-[5px] bg-[#f5f5f5] text-center outline-none border border-transparent font-normal text-[15px] text-[#555] custom-number mb-2" placeholder="Max Price" v-model="maxPrice" />
						</div>
						<div class="col-sm-12 text-center">
							<button class="w-full flex items-center justify-center gap-2 text-[#39404a] text-[16px] font-rubik font-normal px-[6px] py-[10px] rounded-lg border border-[#e8e8e8] bg-[#e8e8e8] outline-none transition-all duration-300 hover:text-white hover:bg-primary_color" @click="applyPriceFilter"><i class="fas fa-search"></i> Search</button>
						</div>
					</div>
                </div>
              </div>

              <!-- Color Filter -->
              <!-- <div class="s_filter form">
                <h6 class="text-uppercase border-bottom font-weight-bold !text-primary_color">
                  color
                  <button class="toggle collapsed" data-toggle="collapse" data-target="#shop_color_collapse"></button>
                </h6>
                <div class="collapse" id="shop_color_collapse">
                  <label class="form-check" v-for="color in colors" :key="color">
                    <input class="form-check-input" type="checkbox" :value="color" v-model="selectedColors" @change="updateFilters"/>
                    <span class="form-check-label font-weight-bolder">{{ color }}</span>
                  </label>
                </div>
              </div> -->

              <!-- Dynamic Category Filters -->
            <div v-for="filter in filters" :key="filter.id" class="s_filter">
              <h6 class="text-uppercase border-bottom font-weight-bold !text-primary_color">
                {{ filter.name }}
                <button class="toggle collapsed" :data-target="'#filter_' + filter.id" data-toggle="collapse"></button>
              </h6>
              <div class="collapse" :id="'filter_' + filter.id">
                <label v-for="option in filter.options" :key="option.id" class="flex items-center gap-2 cursor-pointer select-none mb-2">
                  <input type="checkbox"
                        :value="option.id"
                        v-model="selectedFilters"
                        @change="updateFilters"
                        class="absolute opacity-0 w-0 h-0 peer" />
                  <div class="w-5 h-5 border-2 border-gray-400 rounded flex items-center justify-center
                              peer-checked:bg-primary_color peer-checked:border-primary_color
                              transition-all duration-200">
                    <svg v-show="selectedFilters.includes(option.id)" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <span class="font-semibold text-[#777] text-[16px] font-[400] hover:text-primary_color">{{ option.value }}</span>
                </label>
              </div>
            </div>

              <!-- Brand Filter -->
              <div class="s_filter">
                <h6 class="text-uppercase border-bottom font-weight-bold !text-primary_color">
                  brand
                  <button class="toggle collapsed" data-toggle="collapse" data-target="#shop_brand_collapse"></button>
                </h6>
                <div class="collapse" id="shop_brand_collapse">
                  <div v-for="brand in brands" :key="brand.id" class="mb-2">
                    <label class="flex items-center justify-between gap-2 cursor-pointer select-none mb-2">
                      <div class="flex items-center gap-2">
                        <input type="checkbox"
                              :value="brand.id"
                              v-model="selectedBrands"
                              @change="updateFilters"
                              class="absolute opacity-0 w-0 h-0 peer" />

                        <div class="w-5 h-5 border-2 border-gray-400 rounded flex items-center justify-center
                                    peer-checked:bg-primary_color peer-checked:border-primary_color
                                    transition-all duration-200">
                          <svg v-show="selectedBrands.includes(brand.id)" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                          </svg>
                        </div>

                        <span class="font-semibold text-[#777] text-[16px] font-[400] hover:text-primary_color">{{ brand.name }}</span>
                      </div>

                      <!-- <span class="text-gray-500">({{ brand.product_count || 0 }})</span> -->
                    </label>

                  </div>
                </div>
              </div>

              <!-- Size Filter -->
              <!-- <div class="s_filter">
                <h6 class="text-uppercase border-bottom font-weight-bold !text-primary_color">
                  size
                  <button class="toggle collapsed" data-toggle="collapse" data-target="#shop_size_collapse"></button>
                </h6>
                <div class="collapse" id="shop_size_collapse">
                  <label class="form-check" v-for="size in sizes" :key="size">
                    <input class="form-check-input" type="checkbox" :value="size" v-model="selectedSizes" @change="updateFilters"/>
                    <span class="form-check-label font-weight-bolder">{{ size }}</span>
                  </label>
                </div>
              </div> -->

              <button class="btn btn-primary clear_btn" @click="clearAllFilters">Clear all</button>
            </div>
          </div>

          <!-- Products List -->
          <div class="col-lg-9 col-md-8">
            <div class="flex justify-between items-center bg-white p-2 px-3 mb-3 rounded shadow-sm">
              <!-- Show dropdown -->
              <div class="flex items-center gap-2">
                <span class="font-medium">Show:</span>
                <select v-model="perPage" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                  <option value="10">10</option>
                  <option value="20">20</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                  <option value="200">200</option>
                </select>
              </div>

              <!-- Sort dropdown -->
              <div class="flex items-center gap-2">
                <span class="font-medium">Sort By:</span>
                <select v-model="sortBy" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                  <option value="relevance">Relevance</option>
                  <option value="name_asc">Name, A to Z</option>
                  <option value="name_desc">Name, Z to A</option>
                  <option value="price_low_high">Price, low to high</option>
                  <option value="price_high_low">Price, high to low</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
              <ProductCard :products="products" v-if="!productLoading" />
              <ProductCardSkelton v-else v-for="n in 6" :key="n"/>
            </div>

            <div class="text-center mt-6">
              <button v-if="!allLoaded && !productLoading" @click="loadMore" class="px-6 py-2 rounded-lg bg-primary_color text-white font-medium shadow-md hover:bg-[#528c27] focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1 transition duration-200 ease-in-out">Load More</button>
              <span v-else-if="allLoaded" class="text-gray-500">No more products</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- footer -->
    <Footer></Footer>
  </div>
</template>

<script>
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';
import { mapGetters, mapActions } from 'vuex';
import ProductCard from '../components/ProductCard.vue';
import ProductCardSkelton from '../components/ProductCardSkelton.vue';

export default {
  name: "ShopPage",
  props: ['slug'],
  data() {
    return {
      breadcrumbTitle: "All Products",
      perPage: 10,
      sortBy: "relevance",
      minPrice: null,
      maxPrice: null,
      selectedBrands: [],
      selectedFilters: [],
      page: 1,
      allLoaded: false,
      productList: []
    };
  },
  computed:{
    ...mapGetters('product', ['productLoading','brands','filters', 'filterLoading']),
    products() {
      return this.productList;
    }
  },
  watch: {
    '$route.query': {
      immediate: true,
      handler() {
        this.applyFiltersFromQuery();
        this.fetchFiltersBySlug(this.slug);
      }
    },
    slug(newSlug) {
      this.resetAll();
      this.fetchProductsBySlug(newSlug);
      this.fetchFiltersBySlug(newSlug);
    },
    perPage() {
      this.resetAll();
      this.loadProducts();
    },
    sortBy() {
      this.resetAll();
      this.loadProducts();
    },
  },
  created() {
    this.fetchProductsBySlug(this.slug);
  },
  methods:{
    ...mapActions('product', ['fetchFilters','fetchProductData']),

    fetchProductsBySlug(slug) {
      this.breadcrumbTitle = slug ? slug.replace('-', ' ') : 'All Products';
      this.updateMeta(slug);
      this.resetAll();
      this.loadProducts();
    },

    async fetchFiltersBySlug(slug) {
      const params = { slug }; 
      await this.fetchFilters(params);
    },

    updateMeta(slug) {
      document.title = slug
        ? `${this.breadcrumbTitle} | Products | Ecommerce`
        : `Products | Ecommerce`;
      const metaDescription = document.querySelector('meta[name="description"]');
      if (metaDescription) {
        metaDescription.setAttribute(
          'content',
          slug
            ? `Browse ${this.breadcrumbTitle} products in our catalog.`
            : 'Explore our product catalog.'
        );
      }
    },

    applyPriceFilter() {
      if(this.minPrice && this.maxPrice && this.minPrice > this.maxPrice) {
        alert("Min Price should be less than Max Price");
        return;
      }
      const query = {
        ...(this.selectedBrands.length && { brand: this.selectedBrands.join(',') }),
        ...(this.minPrice && { minPrice: this.minPrice }),
        ...(this.maxPrice && { maxPrice: this.maxPrice }),
        ...(this.selectedFilters.length && { filters: this.selectedFilters.join(',') })
      };

      this.$router.push({ name: 'products', params: { slug: this.slug }, query });
    },
    // Updates the query params in URL
    updateFilters() {
      const query = {
        ...(this.selectedBrands.length && { brand: this.selectedBrands.join(',') }),
        ...(this.minPrice && { minPrice: this.minPrice }),
        ...(this.maxPrice && { maxPrice: this.maxPrice }),
        ...(this.selectedFilters.length && { filters: this.selectedFilters.join(',') })
      };

      this.$router.push({ name: 'products', params: { slug: this.slug }, query });
    },

    applyFiltersFromQuery() {
      const q = this.$route.query;
      this.selectedBrands = q.brand ? q.brand.split(',').map(Number) : [];
      this.selectedFilters = q.filters ? q.filters.split(',').map(Number) : [];
      this.minPrice = q.minPrice || null;
      this.maxPrice = q.maxPrice || null;
      this.resetAll();
      this.loadProducts();
    },

    resetAll() {
      this.page = 1;
      this.allLoaded = false;
      this.productList = [];
    },

    async loadProducts() {
      if (this.allLoaded || this.productLoading) return;

      const params = {
        slug: this.slug,
        brands: this.selectedBrands,
        filters: this.selectedFilters,
        minPrice: this.minPrice,
        maxPrice: this.maxPrice,
        perPage: this.perPage,
        sortBy: this.sortBy,
        page: this.page
      };
      // console.log('Selected Brands:', this.selectedBrands);
      const newProducts = await this.fetchProductData(params);

      if (newProducts && newProducts.length) {
        this.productList = [...this.productList, ...newProducts];
      }

      if (!newProducts || newProducts.length < this.perPage) {
        this.allLoaded = true;
      } else {
        this.page += 1;
      }
    },

    loadMore() {
      this.loadProducts();
    },

    clearAllFilters() {
      this.selectedBrands = [];
      this.selectedFilters = [];
      this.minPrice = null;
      this.maxPrice = null;
      this.perPage = 10;
      this.sortBy = "relevance";
      this.updateFilters();
    }
  },
  mounted() {
    if (window.innerWidth > 991) {
      $("#sp_vertical_menu .vertical_menu").insertBefore("#sp_header_top .col-lg-9.text-left");
    }
  },
  components: {
    Footer,
    Header,
    ProductCard,
    ProductCardSkelton,
  },
};
</script>
<style scoped>
.custom-number::-webkit-outer-spin-button,
.custom-number::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.custom-number {
    -moz-appearance: textfield; /* Firefox */
}
</style>