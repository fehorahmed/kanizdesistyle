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
							<li class="d-inline-block hr_ font-weight-bolder"><router-link :to="{ name: 'categories' }" :class="{ active: $route.name === 'categories' }">categories</router-link></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container custom_container">
			<div
				v-if="!categoryLoading"
				class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5"
				>
				<div
					v-for="category in f_categories"
					:key="category.id"
					class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-[#70c332]/50 p-4 flex flex-col items-center justify-center cursor-pointer overflow-hidden"
				>
					<!-- Gradient Hover Effect -->
					<div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#70c332]/5 opacity-0 group-hover:opacity-100 transition duration-500"></div>

					<!-- Category Image -->
					<router-link :to="{ name: 'products', params: { slug: category.slug } }" class="flex flex-col items-center z-10">
						<div class="bg-gray-50 rounded-full p-4 group-hover:bg-[#70c332]/10 transition duration-300">
							<img
							:src="'/' + category.image"
							alt="category"
							class="mx-auto h-12 w-12 object-contain transform group-hover:scale-110 transition duration-300"
							/>
						</div>

						<!-- Category Name -->
						<p class="mt-3 text-center text-[12px] md:text-[14px] font-medium text-gray-700 group-hover:text-[#70c332] transition duration-300 uppercase tracking-wide">
							{{ category.name }}
						</p>
					</router-link>
				</div>
			</div>

				<!-- Skeleton Loader -->
			<div
			v-else
			class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5"
			>
				<div
					v-for="n in 10"
					:key="n"
					class="bg-white rounded-xl shadow p-4 animate-pulse flex flex-col items-center justify-center"
				>
					<div class="bg-gray-300 w-16 h-16 rounded-full"></div>
					<div class="bg-gray-300 h-3 w-2/3 mx-auto mt-3 rounded"></div>
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
import { mapGetters } from 'vuex';
export default {
  name: "CategoryPage",
  data() {
      return {
          isLoading: true,
          breadcrumbTitle: "Compare",
      };
  },
  mounted() {
    if (window.innerWidth > 991) {
      $("#sp_vertical_menu .vertical_menu").insertBefore("#sp_header_top .col-lg-9.text-left");
    }
  },
  computed: {
    ...mapGetters("home", ["f_categories", "categoryLoading"]),
  },
  components: {
    Footer,
    Header,
  },
};
</script>

<style>

</style>
