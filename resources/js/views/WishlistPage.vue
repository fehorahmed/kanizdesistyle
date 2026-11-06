<template>
  <div>
    <!-- header area -->                                                                    <!-- first row -->
    <Header></Header>

    <div id="wishlist_page" class="wishlist-page animate__animated animate__fadeInUp">

		<div class="sp_header bg-white p-3 ">
			<div class="container custom_container ">	
				<div class="row ">
					<div class="col-12 ">
						<ul>
							<li class="d-inline-block font-weight-bolder"><router-link :to="{ name: 'home' }">home</router-link></li>
							<li class="d-inline-block hr_ font-weight-bolder"><router-link :to="{ name: 'wishlist' }" :class="{ active: $route.name === 'wishlist' }">wishlist</router-link></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div v-if="wishlistItems.length" class="container mx-auto py-6">
			<!-- Header Row -->
			<div class="hidden md:grid grid-cols-12 gap-4 border-b pb-2 text-sm font-semibold text-gray-700">
				<div class="col-span-6 grid grid-cols-12">
					<div class="col-span-4">Image</div>
					<div class="col-span-8">Product</div>
				</div>
				<div class="col-span-6 grid grid-cols-12 text-center">
					<div class="col-span-4">Price</div>
					<div class="col-span-4">Availability</div>
					<div class="col-span-4 text-right">Action</div>
				</div>
			</div>

			<!-- Wishlist Items -->
			<div class="divide-y">
				<div
				v-for="item in wishlistItems"
				:key="item.id"
				class="grid grid-cols-12 gap-4 py-4 items-center"
				>
				<!-- Image + Product -->
				<div class="col-span-12 md:col-span-6 flex items-center">
					<div class="w-24 h-24 flex-shrink-0">
						<img
							:src="item?.product?.image"
							:alt="item.name"
							class="w-full h-full object-cover rounded-lg shadow"
						/>
					</div>
					<div class="ml-4">
						<h6 class="font-semibold text-gray-900">
							<router-link :to="`/product/${item.slug}`" class="hover:text-indigo-600">
							{{ item?.product?.name }}
							</router-link>
						</h6>
						<ul class="text-sm text-gray-500 mt-1 space-y-0.5">
							<li v-if="item?.product?.category">Category: <span class="text-gray-800">{{ item?.product?.category?.name }}</span></li>
							<li v-if="item?.product?.subcategory">SubCategory: <span class="text-gray-800">{{ item?.product?.subcategory?.name }}</span></li>
							<li v-if="item?.product?.brand">Brand: <span class="text-gray-800">{{ item?.product?.brand?.name }}</span></li>
						</ul>
					</div>
				</div>

				<!-- Price + Availability + Action -->
				<div class="col-span-12 md:col-span-6 grid grid-cols-12 items-center mt-3 md:mt-0">
					<div class="col-span-4 font-semibold text-gray-900 text-center">৳{{ item?.product?.selling_price }}</div>
						<div class="col-span-4 text-center">
							<span
								:class="[
								'px-2 py-1 text-xs rounded bg-green-100 text-green-700',
								]"
							>
								{{ 'In Stock' }}
							</span>
						</div>
						<div class="col-span-4 text-right">
							<button
								@click="remove(item.product_id)"
								class="px-2 py-2 text-red-500 hover:text-red-700 rounded-lg bg-red-100 hover:bg-red-100"
							>
								<i class="fas fa-trash-alt"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div v-else class="container mx-auto py-6">
			<p class="text-center text-gray-500">Your wishlist is empty.</p>
		</div>
	</div>

    <!-- footer -->
    <Footer></Footer>
    
  </div>
</template>

<script>
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';
import { mapGetters } from 'vuex';
export default {
  name: "WishlistPage",
  data() {
      return {
          isLoading: true,
          breadcrumbTitle: "Wishlist",
      };
  },
  computed:{
	...mapGetters('wishlist', ['wishlistItems', 'wishlistLoading']),
  },
  mounted() {
    if (window.innerWidth > 991) {
      $("#sp_vertical_menu .vertical_menu").insertBefore("#sp_header_top .col-lg-9.text-left");
    }
  },
  methods:{
	remove(productId) {
	  this.$store.dispatch('wishlist/removeFromWishlist', productId);
	}
  },
  components: {
    Footer,
    Header,
  },
};
</script>

<style>

</style>
