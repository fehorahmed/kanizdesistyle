<template>
  <div>
    <!-- header area -->                                                                    <!-- first row -->
    <Header></Header>

    <div id="about_us_page" class="about-us-page animate__animated animate__fadeInUp">

		<div class="sp_header bg-white p-3 ">
			<div class="container custom_container ">	
				<div class="row ">
					<div class="col-12 ">
						<ul>
							<li class="d-inline-block font-weight-bolder"><router-link :to="{ name: 'home' }">home</router-link></li>
							<li class="d-inline-block hr_ font-weight-bolder"><router-link :to="{ name: 'about' }" :class="{ active: $route.name === 'about' }">about us</router-link></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="bg-white rounded py-3">
			<div class="container custom_container">
				<div>
					<div class="row">
						<div class="col-12">
							<img src="../../../themes/frontend/assets/img/about_us/about_us.jpg" class="mx-auto rounded d-block img-fluid" alt="about_us">
						</div>
					</div>
				</div>
			</div>

			<div class="container custom_container">
				<div class="row">
					<div class="col-12">
						<div class="mt-3 mt-lg-5">
							<h4 class="font-weight-bold">Nunc placerat augue vel porta elementum consequat velit sit amet.</h4>
							<p class="font-weight-bolder text-muted mt-3 f_13">Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula, a scelerisque elit interdum non. Integer bibendum lacus sit amet ullamcorper vestibulum a scelerisque elit interdum non. Integer bibendum lacus sit amet ullamcorper vestibulum Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula, a scelerisque elit interdum non. Integer bibendum lacus sit amet ullamcorper vestibulum.</p>

							<p class="font-weight-bolder text-muted f_13">Nullam in neque vitae arcu auctor dignissim feugiat erat. Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula, a scelerisque elit interdum non. Integer bibendum lacus sit amet ullamcorper vestibulum Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi.</p>

							<p class="font-weight-bolder text-muted f_13">a scelerisque elit interdum non. Integer bibendum lacus sit amet ullamcorper vestibulum Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula, a scelerisque elit interdum non. Integer bibendum lacus sit amet ullamcorper vestibulum.</p>
						</div>
					</div>
				</div>
			</div>

			<div class="container custom_container about_testimonials">
				<div class="row">
					<div class="col-12">
						<div class="title_outer">
							<h5 class="font-weight-bolder mb-4 d-inline-block pr-3 bg-white"><span class="text-danger">testimonials</span></h5>
						</div> <!-- title_outer -->
					</div> <!-- col-12 -->
				</div> <!-- row -->
					<div class="row ">
						<div id="ab_customer" class="owl-carousel owl-theme ">

							<div class="item ">
							    <div class="col-12">
							    	 <div class=" rounded p-md-4 p-3 cust_">
										<div class="d-flex">
											<div class="col-xl-3 col-lg-4 col-md-2 col-sm-3 col-12 text-center pr-0">
												<a href="#"><img src="../../../themes/frontend/assets/img/about_us/testi1.png" class=" mx-auto d-block img-fluid p-1 " alt="testi1"></a>
												<span class="font-weight-bold d-inline-block pt-2">Sed ultrices</span>
												<span class="font-weight-bolder text-muted">designer</span>
											</div>							
											<div class="col-xl-8 col-lg-8 col-md-10 col-sm-9 col-12 pr-0">									
												<p class="font-weight-bolder f_13 text-muted c_des">Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula</p>							
											</div>
										</div>
									</div>
								</div>
							</div> <!-- item -->

							<div class="item ">
							    <div class="col-12">
							    	 <div class=" rounded p-md-4 p-3 cust_">
										<div class="d-flex">
											<div class="col-xl-3 col-lg-4 col-md-2 col-sm-3 col-12 text-center pr-0">
												<a href="#"><img src="../../../themes/frontend/assets/img/about_us/testi2.png" class=" mx-auto d-block img-fluid p-1 " alt="testi2"></a>
												<span class="font-weight-bold d-inline-block pt-2">Sed ultrices</span>
												<span class="font-weight-bolder text-muted">designer</span>
											</div>							
											<div class="col-xl-8 col-lg-8 col-md-10 col-sm-9 col-12 pr-0">									
												<p class="font-weight-bolder f_13 text-muted c_des">Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula</p>							
											</div>
										</div>
									</div>
								</div>
							</div> <!-- item -->

							<div class="item ">
							    <div class="col-12">
							    	 <div class=" rounded p-md-4 p-3 cust_">
										<div class="d-flex">
											<div class="col-xl-3 col-lg-4 col-md-2 col-sm-3 col-12 text-center pr-0">
												<a href="#"><img src="../../../themes/frontend/assets/img/about_us/testi3.png" class=" mx-auto d-block img-fluid p-1 " alt="testi3"></a>
												<span class="font-weight-bold d-inline-block pt-2">Sed ultrices</span>
												<span class="font-weight-bolder text-muted">designer</span>
											</div>							
											<div class="col-xl-8 col-lg-8 col-md-10 col-sm-9 col-12 pr-0">									
												<p class="font-weight-bolder f_13 text-muted c_des">Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula</p>							
											</div>
										</div>
									</div>
								</div>
							</div> <!-- item -->

						</div>
					</div>
			</div> <!-- container custom_container -->

			<div class="container custom_container">
				<div class="row">
					<div class="col-lg-12 col-xl-8">
						<img src="../../../themes/frontend/assets/img/about_us/fullsc3.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="fullsc3">
					</div>
					<div class="col-lg-12 col-xl-4 pt-3 pt-xl-0">
						<p class="font-weight-bolder ab_bnr_text">
							Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula, a scelerisque elit interdum non. Integer bibendum lacus sit amet ullamcorper vestibulum.
						</p>
					</div>
				</div>
			</div>

			<div class="container custom_container ab_services_container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="service-item border">
                            <span class="number float-right p-2">01</span>
                            <div><svg width="80px" height="80px"><use xlink:href="#support"></use></svg></div>
                            <h3 class="font-weight-bold pt-3">fashion</h3>
                            <p class="font-weight-bolder text-muted mb-0">Phasellus quis Vestibulum luctus velit sit amet malesuada molestie. </p>
                        </div>
                    </div>
                    <div class="col-md-4 py-3 py-md-0">
                        <div class="service-item border">
                            <span class="number float-right">02</span>
                            <div><svg width="80px" height="80px"><use xlink:href="#secure"></use></svg></div>
                            <h3 class="font-weight-bold pt-3">sports</h3>
                            <p class="font-weight-bolder text-muted mb-0">Phasellus luctus quis Vestibulum luctus velit  Nulla facilisi. </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="service-item border">
                            <span class="number float-right">03</span>
                            <div><svg width="80px" height="80px"><use xlink:href="#gift"></use></svg></div>
                            <h3 class="font-weight-bold pt-3">travel</h3>
                            <p class="font-weight-bolder text-muted mb-0">Aenean vehicula non mauris maximus elementum. Nulla facilisi. </p>
                        </div>
                    </div>
                </div>
            </div> <!-- container custom_container -->

            <div class="container custom_container">
            	

            	<div class="row ">
					<div id="ab_team" class="owl-carousel owl-theme ">

						<div class="item">
						    <div class="col-12">
						    	 <div class="ab_thumb text-center">
						    	 	<img src="../../../themes/frontend/assets/img/about_us/team_1.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="team_1">
						    	 	<h5 class="font-weight-bold pt-3">Ivet Houze</h5>
						    	 	<div class="font-weight-bolder">designer</div>
						    	 </div>
							</div>
						</div> <!-- item -->

						<div class="item">
						    <div class="col-12">
						    	 <div class="ab_thumb text-center">
						    	 	<img src="../../../themes/frontend/assets/img/about_us/team_2.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="team_2">
						    	 	<h5 class="font-weight-bold pt-3">Ivet Houze</h5>
						    	 	<div class="font-weight-bolder">designer</div>
						    	 </div>
							</div>
						</div> <!-- item -->

						<div class="item">
						    <div class="col-12">
						    	 <div class="ab_thumb text-center">
						    	 	<img src="../../../themes/frontend/assets/img/about_us/team_3.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="team_3">
						    	 	<h5 class="font-weight-bold pt-3">Ivet Houze</h5>
						    	 	<div class="font-weight-bolder">designer</div>
						    	 </div>
							</div>
						</div> <!-- item -->

						<div class="item">
						    <div class="col-12">
						    	 <div class="ab_thumb text-center">
						    	 	<img src="../../../themes/frontend/assets/img/about_us/team_4.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="team_4">
						    	 	<h5 class="font-weight-bold pt-3">Ivet Houze</h5>
						    	 	<div class="font-weight-bolder">designer</div>
						    	 </div>
							</div>
						</div> <!-- item -->

						<div class="item">
						    <div class="col-12">
						    	 <div class="ab_thumb text-center">
						    	 	<img src="../../../themes/frontend/assets/img/about_us/team_5.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="team_5">
						    	 	<h5 class="font-weight-bold pt-3">Ivet Houze</h5>
						    	 	<div class="font-weight-bolder">designer</div>
						    	 </div>
							</div>
						</div> <!-- item -->

						<div class="item">
						    <div class="col-12">
						    	 <div class="ab_thumb text-center">
						    	 	<img src="../../../themes/frontend/assets/img/about_us/team_6.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="team_6">
						    	 	<h5 class="font-weight-bold pt-3">Ivet Houze</h5>
						    	 	<div class="font-weight-bolder">designer</div>
						    	 </div>
							</div>
						</div> <!-- item -->

					</div>
				</div>
            </div>

		</div> <!-- bg-white rounded p-3 -->
	</div> 

    <!-- footer -->
    <Footer></Footer>
    
  </div>
</template>

<script>
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';
export default {
  name: "AboutPage",
  data() {
      return {
          isLoading: true,
          breadcrumbTitle: "About",
      };
  },
  mounted() {
    if (window.innerWidth > 991) {
      $("#sp_vertical_menu .vertical_menu").insertBefore("#sp_header_top .col-lg-9.text-left");
    }
  },
  methods:{
        
  },
  components: {
    Footer,
    Header,
  },
};
</script>

<style>

</style>
