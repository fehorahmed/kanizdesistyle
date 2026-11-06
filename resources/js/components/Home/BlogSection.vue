<template>
    <div class="container custom_container  animate__animated animate__fadeInUp">
        <div class="row">
            <div class="col-12">
                <div class="title_outer">
                    <h5 class="font-weight-bolder mb-4 d-inline-block pr-3"><span class="text-danger">latest</span> news</h5>
                </div> <!-- title_outer -->
            </div> <!-- col-12 -->
        </div> <!-- row -->

        <div class="row blog_containt">
            <div id="blog_carousel" class="owl-carousel owl-theme blog_class ">

                <div class="item">
                    <div class="col-12 blog_">
                        <div class="d-flex">
                            <div class="blog_image col-6 px-0">
                                <a href="single-blog.html"><img src="../../../../themes/frontend/assets/img/blog/blog_1.jpg" class="fst-image img-fluid rounded" alt="blog_1"></a>	
                                <div class="date text-center p-2 text-light font-weight-bold">
                                    <div>09</div>
                                    <div>oct</div>
                                </div>
                                <div class="blog_mask">	
                                    <div class="blog_mask_content mx-0 my-auto text-center">
                                        <a href="single-blog.html" class="icon d-inline-block"><i class="fas fa-search"></i></a>
                                        <a href="single-blog.html" class="icon d-inline-block"><i class="fas fa-link"></i></a>
                                    </div>
                                </div>												
                            </div>

                            <div class="text-left col-6 pr-0">
                                
                                <h6 class="font-weight-bold"><a href="single-blog.html">This is Secound Post For wbBlog</a></h6>
                                <span><i class="fas fa-comments text-muted pr-1 align-middle"></i><span class="text-muted align-middle">0 Comment</span></span>
                                <p class="text-muted mb-0 f_13">Sed elit the quamrich iaculis sed sempoersit udin vitae sed amet magna osemper the lorem osema olatiup udin...</p>
                                <a href="single-blog.html" class="btn btn-primary  f_13  ">read more</a>

                            </div>
                        </div>
                    </div>	<!-- col-12 -->
                </div> <!-- item -->

                <div class="item">
                    <div class="col-12 blog_">
                        <div class="d-flex">
                            <div class="blog_image col-6 px-0">
                                <a href="single-blog.html" class="d-block"><img src="../../../../themes/frontend/assets/img/blog/blog_2.jpg" class="fst-image img-fluid rounded"  alt="blog_2"></a>	
                                <div class="date text-center p-2 text-light font-weight-bold">
                                    <div>09</div>
                                    <div>oct</div>
                                </div>
                                <div class="blog_mask">	
                                    <div class="blog_mask_content mx-0 my-auto text-center">
                                        <a href="single-blog.html" class="icon d-inline-block"><i class="fas fa-search"></i></a>
                                        <a href="single-blog.html" class="icon d-inline-block"><i class="fas fa-link"></i></a>
                                    </div>
                                </div>												
                            </div>

                            <div class="text-left col-6 pr-0">
                                
                                <h6 class="font-weight-bold"><a href="single-blog.html">Vestibulum lobortis libero volu</a></h6>
                                <span><i class="fas fa-comments text-muted pr-1 align-middle"></i><span class="text-muted align-middle">0 Comment</span></span>
                                <p class="text-muted mb-0 f_13">Sed elit the quamrich iaculis sed sempoersit udin vitae sed amet magna osemper the lorem osema olatiup udin...</p>
                                <a href="single-blog.html" class="btn btn-primary  f_13  ">read more</a>

                            </div>
                        </div>
                    </div>	<!-- col-12 -->
                </div> <!-- item -->

                <div class="item">
                    <div class="col-12 blog_">
                        <div class="d-flex">
                            <div class="blog_image col-6 px-0">
                                <a href="single-blog.html"><img src="../../../../themes/frontend/assets/img/blog/blog_3.jpg" class="fst-image img-fluid rounded" alt="blog_3"></a>		
                                <div class="date text-center p-2 text-light font-weight-bold">
                                    <div>09</div>
                                    <div>oct</div>
                                </div>
                                <div class="blog_mask">	
                                    <div class="blog_mask_content mx-0 my-auto text-center">
                                        <a href="single-blog.html" class="icon d-inline-block"><i class="fas fa-search"></i></a>
                                        <a href="single-blog.html" class="icon d-inline-block"><i class="fas fa-link"></i></a>
                                    </div>
                                </div>												
                            </div>

                            <div class="text-left col-6 pr-0">
                                
                                <h6 class="font-weight-bold"><a href="single-blog.html">Nunc placerat augue vel port</a></h6>
                                <span><i class="fas fa-comments text-muted pr-1 align-middle"></i><span class="text-muted align-middle">0 Comment</span></span>
                                <p class="text-muted mb-0 f_13">Sed elit the quamrich iaculis sed sempoersit udin vitae sed amet magna osemper the lorem osema olatiup udin...</p>
                                <a href="single-blog.html" class="btn btn-primary  f_13  ">read more</a>

                            </div>
                        </div>
                    </div>	<!-- col-12 -->
                </div> <!-- item -->
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'BlogSection',
    data() {
        return {
            isLoading: true,
        };
    },
    mounted(){
        $('#blog_carousel').owlCarousel({
            // loop: true,  
            pagination:false,
            navigation:false,
            nav: true,
            navText: [
                "<i class='fa fa-chevron-left'></i>",
                "<i class='fa fa-chevron-right'></i>"
            ],
            autoplay: false,
            autoplayHoverPause: true,
            responsive: {
                0: {
                   items: 1
                },
                768: {
                   items: 2
                },
                992: {
                   items: 2
                },
                1200: {
                   items: 2
                },
                1410: {
                   items: 2
                },
                1850: {
                   items: 2
                }
            }
        })
    },
};
</script>
  
<style scoped>

</style>