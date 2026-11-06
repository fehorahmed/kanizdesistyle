<template>
  <div>
    <!-- header area -->                                                                    <!-- first row -->
    <Header></Header>

    <div id="right_blog_sidebar_page" class="right-blog-page blog-page animate__animated animate__fadeInUp">

		<div class="sp_header bg-white p-3   animate__animated animate__fadeInUp">
			<div class="container custom_container ">	
				<div class="row ">
					<div class="col-12 ">
						<ul>
							<li class="d-inline-block font-weight-bolder"><router-link :to="{ name: 'home' }">home</router-link></li>
							<li class="d-inline-block hr_ font-weight-bolder"><router-link :to="{ name: 'blog' }" :class="{ active: $route.name === 'blog' }">blog</router-link></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container custom_container">
			
			<div class="row">
				<div class="col-md-8 col-lg-8 col-xl-9 col-sm-12 b_left_blog mb-3 mb-md-0">
					<div class="row sh_page rounded">
						<div class="col-lg-6 col-md-12">
							<div class="card mb-4  animate__animated animate__fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.4s">
								<a href="single-blog.html"><img src="/public/themes/frontend/assets/img/blog/blog_1.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="blog_1"> </a>
								<div class="card-body p-0 mt-3">
									<div class="blog_subtitle">
										<span class="font-weight-bolder pr-2"><i class="far fa-calendar-alt mr-2"></i>Oct 27 2020</span>
										<span class="font-weight-bolder border-secondary border-left border-right px-2"><i class="far fa-comments mr-2"></i>0 comments</span>
										<span class="font-weight-bolder pl-2"><i class="fas fa-user mr-2"></i>author</span>
									</div>
									<h2 class="card-title mt-3 font-weight-bold"><a href="single-blog.html">Nunc placerat augue vel porta elementum consequat velit sit amet.</a></h2>
									<p class="card-text text-muted f_13">
										Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula, a scelerisque elit interdum non. Integer bibendum lacus sit amet ullamcorper vestibulum.
									</p>
									<a href="single-blog.html" class="btn btn-primary mt-2 f_13 px-3 py-2">read more</a>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-12">
							<div class="card mb-4  animate__animated animate__fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.4s">
								<a href="single-blog.html"><img src="/public/themes/frontend/assets/img/blog/blog_3.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="blog_3"></a>
								<div class="card-body p-0 mt-3">
									<div class="blog_subtitle">
										<span class="font-weight-bolder pr-2"><i class="far fa-calendar-alt mr-2"></i>Oct 27 2020</span>
										<span class="font-weight-bolder border-secondary border-left border-right px-2"><i class="far fa-comments mr-2"></i>0 comments</span>
										<span class="font-weight-bolder pl-2"><i class="fas fa-user mr-2"></i>author</span>
									</div>
									<h2 class="card-title mt-3 font-weight-bold"><a href="single-blog.html">Nunc placerat augue vel porta elementum consequat velit sit amet.</a></h2>
									<p class="card-text text-muted f_13">
										Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula, a scelerisque elit interdum non. Integer bibendum lacus sit amet ullamcorper vestibulum.
									</p>
									<a href="single-blog.html" class="btn btn-primary mt-2 f_13 px-3 py-2">read more</a>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-12">
							<div class="card mb-4  animate__animated animate__fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.4s">
								<a href="single-blog.html"><img src="/public/themes/frontend/assets/img/blog/blog_4.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="blog_4"></a>
								<div class="card-body p-0 mt-3">
									<div class="blog_subtitle">
										<span class="font-weight-bolder pr-2"><i class="far fa-calendar-alt mr-2"></i>Oct 27 2020</span>
										<span class="font-weight-bolder border-secondary border-left border-right px-2"><i class="far fa-comments mr-2"></i>0 comments</span>
										<span class="font-weight-bolder pl-2"><i class="fas fa-user mr-2"></i>author</span>
									</div>
									<h2 class="card-title mt-3 font-weight-bold"><a href="single-blog.html">Nunc placerat augue vel porta elementum consequat velit sit amet.</a></h2>
									<p class="card-text text-muted f_13">
										Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula, a scelerisque elit interdum non. Integer bibendum lacus sit amet ullamcorper vestibulum.
									</p>
									<a href="single-blog.html" class="btn btn-primary mt-2 f_13 px-3 py-2">read more</a>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-12">
							<div class="card mb-4  animate__animated animate__fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.4s">
								<a href="single-blog.html"><img src="/public/themes/frontend/assets/img/blog/blog_5.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="blog_5"></a>
								<div class="card-body p-0 mt-3">
									<div class="blog_subtitle">
										<span class="font-weight-bolder pr-2"><i class="far fa-calendar-alt mr-2"></i>Oct 27 2020</span>
										<span class="font-weight-bolder border-secondary border-left border-right px-2"><i class="far fa-comments mr-2"></i>0 comments</span>
										<span class="font-weight-bolder pl-2"><i class="fas fa-user mr-2"></i>author</span>
									</div>
									<h2 class="card-title mt-3 font-weight-bold"><a href="single-blog.html">Nunc placerat augue vel porta elementum consequat velit sit amet.</a></h2>
									<p class="card-text text-muted f_13">
										Aliquam pharetra enim eget sollicitudin cursus. Phasellus quis lorem mi. Vestibulum luctus velit sit amet malesuada molestie. Mauris aliquam lacinia ligula, a scelerisque elit interdum non. Integer bibendum lacus sit amet ullamcorper vestibulum.
									</p>
									<a href="single-blog.html" class="btn btn-primary mt-2 f_13 px-3 py-2">read more</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4 col-lg-4 col-xl-3 col-sm-12 b_left_side">
					<div class="bl_sticky">
						<div class="row sh_page rounded mb-3">
							<div class="col-12">

								<div class="title_outer">
		                            <h5 class="font-weight-bolder mb-4 d-inline-block pr-3 bg-white"><span class="text-danger">recent blog</span></h5>
		                        </div> <!-- title_outer -->

								<ul class="bl-sidebar ">

							    	<li class=" d-block border-bottom">
							    		<div class="media pb-3">
							    			<div class="pr-2"><a href="single-blog.html"><img src="/public/themes/frontend/assets/img/blog/blog_1.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="blog_1"></a></div>
											<div class="media-body">
											    <h6 class="mt-0 f_15 font-weight-bold"><a href="single-blog.html">Nullam in neque vitae arcu auctor dignissim feugiat erat.</a></h6>
											    <p><span class="font-weight-bolder pr-2"><i class="far fa-calendar-alt mr-2"></i>Oct 27 2020</span></p>
											</div>
										</div>
							    	</li>
							    	<li class=" d-block border-bottom ">
							    		<div class="media py-3">
							    			<div class="pr-2"><a href="single-blog.html"><img src="/public/themes/frontend/assets/img/blog/blog_3.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="blog_3"></a></div>
										    <div class="media-body">
											    <h6 class="mt-0 f_15 font-weight-bold"><a href="single-blog.html">Vestibulum lobortis bibendum sollicitudin lectus.</a></h6>
											    <p><span class="font-weight-bolder pr-2"><i class="far fa-calendar-alt mr-2"></i>Oct 27 2020</span></p>
										  	</div>
										</div>
							    	</li>
							    	<li class=" d-block border-bottom">
							    		<div class="media py-3">
							    			<div class="pr-2"><a href="single-blog.html"><img src="/public/themes/frontend/assets/img/blog/blog_4.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="blog_4"></a></div>
											<div class="media-body">
											    <h6 class="mt-0 f_15 font-weight-bold"> <a href="single-blog.html">eget sollicitudin cursus Phasellus quis lorem mi Vestibulum</a></h6>
											    <p><span class="font-weight-bolder pr-2"><i class="far fa-calendar-alt mr-2"></i>Oct 27 2020</span></p>
											</div>
										</div>
							    	</li>
							    	<li class="d-block ">
							    		<div class="media pt-3">
							    			<div class="pr-2"><a href="single-blog.html"><img src="/public/themes/frontend/assets/img/blog/blog_5.jpg" class="fst-image mx-auto d-block img-fluid rounded" alt="blog_5"></a></div>
											<div class="media-body">
											    <h6 class="mt-0 f_15 font-weight-bold"><a href="single-blog.html">bibendum lacus sit amet ullamcorper vestibulum.</a> </h6>
											    <p><span class="font-weight-bolder pr-2"><i class="far fa-calendar-alt mr-2"></i>Oct 27 2020</span></p>
											</div>
										</div>
							    	</li>
							    	
							    </ul>
							</div>
						</div>

					<div class="row sh_page rounded mb-3">
						<div class="col-12">

							<div class="title_outer">
	                            <h5 class="font-weight-bolder mb-4 d-inline-block pr-3 bg-white"><span class="text-danger">category</span></h5>
	                        </div> <!-- title_outer -->

							<ul class="bl_cat">

						    	<li class="font-weight-bold pt-0"><a href="blog.html">fashion</a></li>
						    	<li class="font-weight-bold"><a href="blog.html">sports</a></li>
						    	<li class="font-weight-bold"><a href="blog.html">life style</a></li>
						    	<li class="font-weight-bold"><a href="blog.html">business</a></li>
						    	<li class="font-weight-bold"><a href="blog.html">vedeo</a></li>
						    	<li class="font-weight-bold"><a href="blog.html">travel</a></li>
						    	
						    </ul>
						</div>
					</div>

					<div class="row sh_page rounded">
						<div class="col-12">

							<div class="title_outer">
	                            <h5 class="font-weight-bolder mb-4 d-inline-block pr-3 bg-white"><span class="text-danger">comment</span></h5>
	                        </div> <!-- title_outer -->

							<ul class="bl_cat">

						    	<li class="font-weight-bold pt-0"><a href="#">Vestibulum luctus velit sit amet malesuada molestie</a></li>
						    	<li class="font-weight-bold"><a href="#">hasellus quis lorem mi Vestibulum luctus velit sit.</a></li>
						    	<li class="font-weight-bold"><a href="#">Integer bibendum lacus sit amet ullamcorper vestibulum.</a></li>
						    	<li class="font-weight-bold"><a href="#">Phasellus quis lorem mi Vestibulum luctus velit sit amet malesuada molestie.</a></li>
						    	<li class="font-weight-bold"><a href="#">Vestibulum luctus velit sit amet malesuada molestie Mauris</a></li>
						    	
						    </ul>
						</div>
					</div>
				</div>
				</div>

			</div>

		</div> <!-- container custom_container -->

	</div> 

    <!-- footer -->
    <Footer></Footer>
    
  </div>
</template>

<script>
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';
export default {
  name: "BlogPage",
  data() {
      return {
          isLoading: true,
          breadcrumbTitle: "Blog",
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
