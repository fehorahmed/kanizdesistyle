<template>
  <div>
    <!-- header area -->                                                                    <!-- first row -->
    <Header></Header>

    <div id="compare_page" class="compare-page animate__animated animate__fadeInUp">

		<div class="sp_header bg-white p-3 ">
			<div class="container custom_container ">	
				<div class="row ">
					<div class="col-12 ">
						<ul>
							<li class="d-inline-block font-weight-bolder"><router-link :to="{ name: 'home' }">home</router-link></li>
							<li class="d-inline-block hr_ font-weight-bolder"><router-link :to="{ name: 'compare' }" :class="{ active: $route.name === 'compare' }">compare</router-link></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container custom_container">
			
			<div v-if="compareItems.length">
				<div class="table-responsive d-none d-md-block">
					<table class="table border text-center align-middle table-compare">
						<thead class="bg-light">
						<tr>
							<th class="font-weight-bold text-start w-25">Action</th>
							<td v-for="(item, index) in compareItems" :key="index" class="w-25">
							<button
								type="button"
								class="btn btn-outline-danger btn-sm"
								@click="removeCompare(item.product_id)"
							>
								<i class="fas fa-trash-alt"></i> Remove
							</button>
							</td>
						</tr>
						</thead>
						<tbody>
						<tr>
							<th class="text-start w-25">Product Name</th>
							<td v-for="item in compareItems" :key="item.id" class="w-25">
							{{ item.name }}
							</td>
						</tr>
						<tr>
							<th class="text-start w-25">Product Image</th>
							<td v-for="item in compareItems" :key="item.id" class="w-25 text-center">
								<img
									:src="item.image"
									alt="product"
									class="img-fluid rounded shadow-sm mb-2 m-auto"
									style="max-width: 120px;"
								/>
								<div class="fw-bold mt-1">Best Price: ৳{{ item.price }}</div>
								<router-link
									:to="{ name: 'product_details', params: { slug: item.slug } }"
									class="flex items-center justify-center gap-2.5 px-[30px] py-[7px] bg-primary_color/80 text-[#39404a] text-base font-normal border border-transparent rounded-[10px] transition-all duration-300 ease-linear hover:bg-primary_color hover:text-[#fff] "
								>
									<i class="fas fa-shopping-basket"></i> Add
								</router-link>
							</td>
						</tr>
						<tr>
							<th class="text-start w-25">Description</th>
							<td v-for="item in compareItems" :key="item.id" class="w-25 text-start">
								<div v-html="item.description"></div>
							</td>
						</tr>
						<tr>
							<th class="text-start w-25">Availability</th>
							<td v-for="item in compareItems" :key="item.id" class="w-25">
							<span class="badge bg-success">In Stock</span>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="d-block d-md-none">
					<div
						v-for="(item, index) in compareItems"
						:key="index"
						class="border rounded p-3 mb-3 shadow-sm"
					>
						<div class="flex justify-center">
						<img
							:src="item.image"
							:alt="item.name"
							class="img-fluid rounded shadow-sm w-32 h-32 object-cover"
						/>
						</div>
						<div class="mt-3">
						<h6 class="fw-bold">{{ item.name }}</h6>
							<div v-html="item.description"></div>
							<p class="fw-bold mt-2">Best Price: ${{ item.price }}</p>
							<p class="text-success fw-bold">In Stock</p>
						</div>
						<div class="d-flex justify-between mt-3">
							<router-link
								:to="{ name: 'product_details', params: { slug: item.slug } }"
								class="flex items-center justify-center gap-2.5 px-[30px] py-[7px] bg-primary_color/80 text-[#39404a] text-base font-normal border border-transparent rounded-[10px] transition-all duration-300 ease-linear hover:bg-primary_color hover:text-[#fff] "
							>
								<i class="fas fa-shopping-basket"></i> Add
							</router-link>
							<button
								class="btn btn-outline-danger btn-sm"
								@click="removeCompare(item.product_id)"
							>
								<i class="fas fa-trash-alt"></i> Remove
							</button>
						</div>
					</div>
				</div>
			</div>
			<div v-else>
				<p class="text-center">No items to compare</p>
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
import { mapActions, mapGetters } from 'vuex';
export default {
  name: "ComparePage",
  data() {
      return {
          isLoading: true,
          breadcrumbTitle: "Compare",
      };
  },
  computed:{
	...mapGetters('compare', ['compareItems', 'compareLoading']),
  },
  mounted() {
    if (window.innerWidth > 991) {
      $("#sp_vertical_menu .vertical_menu").insertBefore("#sp_header_top .col-lg-9.text-left");
    }
  },
  methods:{
	...mapActions("compare", ["removeFromCompare"]),
	removeCompare(productId) {
	  this.removeFromCompare(productId);
	},
  },
  components: {
    Footer,
    Header,
  },
};
</script>

<style>

</style>
