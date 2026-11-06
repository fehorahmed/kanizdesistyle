<template>
  <div>
    <!-- header area -->                                                                    <!-- first row -->
    <Header></Header>

    <div id="shop_page" class="shop-page animate__animated animate__fadeInUp">
		<div class="sp_header bg-white p-3">
			<div class="container custom_container ">	
				<div class="row ">
					<div class="col-12 ">
						<ul>
							<li class="d-inline-block font-weight-bolder"><router-link :to="{ name: 'home' }">home</router-link></li>
							<li class="d-inline-block hr_ font-weight-bolder"><router-link :to="{ name: 'products' }" :class="{ active: $route.name === 'products' }">products</router-link></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container custom_container">
			<div class="row">
				<div class="col-lg-3 col-md-4">
					<div class="shop_sidebar border card-body rounded bg-white">

						<h2 class="text-uppercase card-title  font-weight-bold d-md-block d-none">filter by</h2>
						<div class="s_filter mt-0">
							<h6 class="text-uppercase  border-bottom font-weight-bold">Range<button class="toggle collapsed" data-toggle="collapse" data-target="#shop_range_collapse"></button></h6>
							<div class="collapse " id="shop_range_collapse">
								<div class="col-md-6 col-sm-12 pl-0 pr-md-2 pr-sm-0 pr-0">
									<label class="font-weight-bolder">min</label>
									<input type="number" class="form-control"  placeholder="$0">
								</div>
								<div class="col-md-6 col-sm-12 pr-0 pl-md-2 pl-sm-0 pl-0 ">
									<label class="font-weight-bolder">max</label>
									<input type="number" class="form-control"  placeholder="$0">
								</div>
							</div>
						</div>

						<div class="s_filter form">
							<h6 class="text-uppercase  border-bottom font-weight-bold">color<button class="toggle collapsed" data-toggle="collapse" data-target="#shop_color_collapse"></button></h6>
							<div class="collapse" id="shop_color_collapse">
								<label class="form-check">
			                        <input class="form-check-input" type="checkbox" value="">
			                        <span class="form-check-label font-weight-bolder">pink</span>
			                        <span class="text-right float-right font-weight-bolder">(1)</span>
			                    </label>
								<label class="form-check">
			                        <input class="form-check-input " type="checkbox" value="">
			                        <span class="form-check-label font-weight-bolder">yellow</span>
			                        <span class="text-right float-right font-weight-bolder">(1)</span>
			                    </label>
								<label class="form-check">
			                        <input class="form-check-input" type="checkbox" value="">
			                        <span class="form-check-label font-weight-bolder">white</span>
			                        <span class="text-right float-right font-weight-bolder">(1)</span>
			                    </label>
								<label class="form-check">
			                        <input class="form-check-input" type="checkbox" value="">
			                        <span class="form-check-label font-weight-bolder">black</span>
			                        <span class="text-right float-right font-weight-bolder">(1)</span>
			                    </label>
			                </div>
						</div>

						<div class="s_filter">
							<h6 class="text-uppercase border-bottom font-weight-bold">brand<button class="toggle collapsed" data-toggle="collapse" data-target="#shop_brand_collapse"></button></h6>
							<div class="collapse" id="shop_brand_collapse">
								<label class="form-check">
			                        <input class="form-check-input" type="checkbox" value="">
			                        <span class="form-check-label font-weight-bolder">brand1</span>
			                        <span class="text-right float-right font-weight-bolder">(1)</span>
			                    </label>
								<label class="form-check">
			                        <input class="form-check-input" type="checkbox" value="">
			                        <span class="form-check-label font-weight-bolder">brand1</span>
			                        <span class="text-right float-right font-weight-bolder">(1)</span>
			                    </label>
								<label class="form-check">
			                        <input class="form-check-input" type="checkbox" value="">
			                        <span class="form-check-label font-weight-bolder">another brand</span>
			                        <span class="text-right float-right font-weight-bolder">(1)</span>
			                    </label>
			                </div>
						</div>	

						<div class="s_filter">
							<h6 class="text-uppercase border-bottom font-weight-bold">size<button class="toggle collapsed" data-toggle="collapse" data-target="#shop_size_collapse"></button></h6>
							<div class="collapse" id="shop_size_collapse">
								<label class="form-check">
			                        <input class="form-check-input" type="checkbox" value="">
			                        <span class="form-check-label font-weight-bolder">s</span>
			                        <span class="text-right float-right font-weight-bolder">(1)</span>
			                    </label>
								<label class="form-check">
			                        <input class="form-check-input" type="checkbox" value="">
			                        <span class="form-check-label font-weight-bolder">m</span>
			                        <span class="text-right float-right font-weight-bolder">(1)</span>
			                    </label>
								<label class="form-check">
			                        <input class="form-check-input" type="checkbox" value="">
			                        <span class="form-check-label font-weight-bolder">l</span>
			                        <span class="text-right float-right font-weight-bolder">(1)</span>
			                    </label>
								<label class="form-check">
			                        <input class="form-check-input" type="checkbox" value="">
			                        <span class="form-check-label font-weight-bolder">xl</span>
			                        <span class="text-right float-right font-weight-bolder">(1)</span>
			                    </label>
			                </div>
			            </div>
			            
						<button data-search-url="#" class="btn btn-primary clear_btn ">Clear all</button>
					</div>
				</div>

				<div class="col-lg-9 col-md-8">
					<div class="flex justify-between items-center bg-white p-2 px-3 mb-3 rounded shadow-sm">
						<!-- Show dropdown (Left) -->
						<div class="flex items-center gap-2">
							<span class="font-medium">Show:</span>
							<select
								v-model="perPage"
								class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
							>
								<option value="10">10</option>
								<option value="20">20</option>
								<option value="50">50</option>
								<option value="100">100</option>
								<option value="200">200</option>
							</select>
						</div>

						<!-- Sort By dropdown (Right) -->
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

					<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
						<ProductCard v-if="!productLoading" :products="products" />
						<ProductCardSkelton v-else v-for="n in 6" :key="n"/>
					</div>

				</div>

			</div>
		</div>

		<div class="container custom_container">
			<div class="row">
				<div class="col-12">
					<nav class="float-right mt-5 mt-col-3">
					  <ul class="pagination">
					    <li class="page-item">
					      <a class="page-link" href="#">
					        <span aria-hidden="true">&laquo;</span>
					      </a>
					    </li>
					    <li class="page-item"><a class="page-link" href="#">1</a></li>
					    <li class="page-item"><a class="page-link" href="#">2</a></li>
					    <li class="page-item"><a class="page-link" href="#">3</a></li>
					    <li class="page-item">
					      <a class="page-link" href="#">
					        <span aria-hidden="true">&raquo;</span>
					      </a>
					    </li>
					  </ul>
					</nav>
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
          isLoading: true,
          breadcrumbTitle: "All Products",
		  perPage: 10,       
          sortBy: "relevance", 
      };
  },
  computed:{
    ...mapGetters('product', ['products', 'productLoading']),
  },
  created() {
    this.fetchProductData(this.slug);
  },
  mounted() {
    if (window.innerWidth > 991) {
      $("#sp_vertical_menu .vertical_menu").insertBefore("#sp_header_top .col-lg-9.text-left");
    }
  },
  methods:{
    ...mapActions('product', ['fetchProductData']),
	fetchProductsBySlug(slug) {
      // Update breadcrumb title
      this.breadcrumbTitle = slug ? slug.replace('-', ' ') : 'All Products';

      // Update meta title and description dynamically for SEO
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

      // Fetch products via Vuex action
      this.fetchProductData(slug);
    },
  },
  components: {
    Footer,
    Header,
	ProductCard,
	ProductCardSkelton,
  },
};
</script>

<style>

</style>
