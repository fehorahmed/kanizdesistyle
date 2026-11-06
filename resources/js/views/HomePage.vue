<template>
    <div>
        <!-- header area -->
        <!-- first row -->
        <Header></Header>
        <!-- header area end -->

        <div id="home_vertical_menu" class="container custom_container mt-3">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Left Side (Banner Section) -->
                <div
                    v-if="setting.banner_image_status_4"
                    class="w-full md:w-9/12"
                >
                    <BannerSection />
                </div>
                <div v-else class="w-full">
                    <BannerSection />
                </div>

                <!-- Right Side (Sidebar Banner) -->
                <div
                    v-if="setting.banner_image_status_4"
                    class="hidden md:block w-full md:w-3/12"
                >
                    <div class="right-banner">
                        <div v-if="!homeLoading" class="banner">
                            <a
                                :href="setting.banner_image_link_4"
                                target="_blank"
                            >
                                <img
                                    :src="'/' + setting.banner_image_4"
                                    loading="lazy"
                                    class="rounded w-full animate__animated animate__fadeInUp"
                                    data-wow-duration="0.8s"
                                    data-wow-delay="0.4s"
                                    alt="ban2"
                                />
                            </a>
                        </div>
                        <div v-else class="banner">
                            <div
                                class="w-full h-[300px] bg-gray-200 animate-pulse rounded"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="setting.banner_image_status_5" class="flex flex-col">
                <div class="ad_area">
                    <img
                        v-if="!homeLoading"
                        :src="'/' + setting.banner_image_5"
                        class="img-fluid rounded w-full mt-4 animate__animated animate__fadeInUp"
                        alt="add"
                    />
                    <div
                        v-else
                        class="w-full h-[100px] bg-gray-200 animate-pulse rounded mt-4"
                    ></div>
                </div>
            </div>
        </div>

        <!-- <div class="container custom_container">
            <div class="service-area py-8">
                <div>
                    <div
                        class="grid grid-cols-2 grid-cols-1 md:grid-cols-4 gap-6"
                    >
                        <div
                            class="service-details flex items-center bg-[#fff] rounded-[6px] space-x-4 p-4 md:p-4 group"
                        >
                            <div
                                class="w-12 h-12 flex items-center justify-center rounded-full text-center text-primary_color bg-[#fff] border-[3px] border-double border-primary_color shadow-[0_15px_35px_rgba(0,0,0,0.1)] text-lg transition-all duration-300 ease-linear group-hover:bg-primary_color group-hover:text-white"
                            >
                                <i class="fas fa-laptop"></i>
                            </div>
                            <div class="service-text">
                                <h3 class="text-sm md:text-lg text-[#39404a]">
                                    Laptop Finder
                                </h3>
                            </div>
                        </div>

                        <div
                            class="service-details flex items-center bg-[#fff] rounded-[6px] space-x-4 p-4 md:p-4 group"
                        >
                            <div
                                class="w-12 h-12 flex items-center justify-center rounded-full text-center text-primary_color bg-[#fff] border-[3px] border-double border-primary_color shadow-[0_15px_35px_rgba(0,0,0,0.1)] text-lg transition-all duration-300 ease-linear group-hover:bg-primary_color group-hover:text-white"
                            >
                                <i class="fas fa-comment"></i>
                            </div>
                            <div class="service-text">
                                <h3 class="text-sm md:text-lg text-[#39404a]">
                                    Raise a Complain
                                </h3>
                            </div>
                        </div>

                        <div
                            class="service-details flex items-center bg-[#fff] rounded-[6px] space-x-4 p-4 md:p-4 group"
                        >
                            <div
                                class="w-12 h-12 flex items-center justify-center rounded-full text-center text-primary_color bg-[#fff] border-[3px] border-double border-primary_color shadow-[0_15px_35px_rgba(0,0,0,0.1)] text-lg transition-all duration-300 ease-linear group-hover:bg-primary_color group-hover:text-white"
                            >
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="service-text">
                                <h3 class="text-sm md:text-lg text-[#39404a]">
                                    Home Service
                                </h3>
                            </div>
                        </div>

                        <div
                            class="service-details flex items-center bg-[#fff] rounded-[6px] space-x-4 p-4 md:p-4 group"
                        >
                            <div
                                class="w-12 h-12 flex items-center justify-center rounded-full text-center text-primary_color bg-[#fff] border-[3px] border-double border-primary_color shadow-[0_15px_35px_rgba(0,0,0,0.1)] text-lg transition-all duration-300 ease-linear group-hover:bg-primary_color group-hover:text-white"
                            >
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="service-text">
                                <h3 class="text-sm md:text-lg text-[#39404a]">
                                    Service Center
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <FeaturedCategory />

        <NewArrivalProduct />

        <div
            v-if="
                setting.banner_image_status_1 || setting.banner_image_status_2
            "
            class="container custom_container banner_container"
        >
            <div class="row bnr_row">
                <div
                    v-if="setting.banner_image_status_1"
                    class="col-md-6 col-12 bnr_col"
                >
                    <div v-if="!homeLoading" class="banner">
                        <a :href="setting.banner_image_link_1" target="_blank">
                            <img
                                :src="'/' + setting.banner_image_1"
                                loading="lazy"
                                class="rounded float-left img-fluid animate__animated animate__fadeInUp"
                                data-wow-duration="0.8s"
                                data-wow-delay="0.2s"
                                alt="ban1"
                            />
                        </a>
                    </div>
                    <div v-else class="banner">
                        <div
                            class="w-full h-[200px] bg-gray-200 animate-pulse rounded"
                        ></div>
                    </div>
                </div>
                <div
                    v-if="setting.banner_image_status_2"
                    class="col-md-6 col-12 bnr_col"
                >
                    <div v-if="!homeLoading" class="banner">
                        <a :href="setting.banner_image_link_2" target="_blank">
                            <img
                                :src="'/' + setting.banner_image_2"
                                loading="lazy"
                                class="rounded float-right img-fluid animate__animated animate__fadeInUp"
                                data-wow-duration="0.8s"
                                data-wow-delay="0.4s"
                                alt="ban2"
                            />
                        </a>
                    </div>
                    <div v-else class="banner">
                        <div
                            class="w-full h-[200px] bg-gray-200 animate-pulse rounded"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <TrendingProduct
            :homeLoading="homeLoading"
            :products="trending_products"
        />

        <!-- <UsedProduct :homeLoading="homeLoading" :products="used_products" /> -->

        <div
            v-if="setting.banner_image_status_3"
            class="container custom_container main_banner animate__animated animate__fadeInUp"
        >
            <div class="row">
                <div class="col-12">
                    <div v-if="!homeLoading" class="banner">
                        <a :href="setting.banner_image_link_3" target="_blank">
                            <img
                                :src="'/' + setting.banner_image_3"
                                class="rounded img-fluid"
                                alt="full_banner"
                            />
                        </a>
                    </div>
                    <div v-else class="banner">
                        <div
                            class="w-full h-[200px] bg-gray-200 animate-pulse rounded"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <FeaturedProduct
            :homeLoading="homeLoading"
            :products="featured_products"
        />

        <TopNicheSection />

        <SmartLifestyle />

        <!-- <BlogSection></BlogSection> -->
        <Footer></Footer>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import FeaturedCategory from "../components/FeaturedCategory.vue";
import Footer from "../components/Footer.vue";
import Header from "../components/Header.vue";
import BannerSection from "../components/Home/BannerSection.vue";
import BlogSection from "../components/Home/BlogSection.vue";
import FeaturedProduct from "../components/Home/FeaturedProduct.vue";
import NewArrivalProduct from "../components/Home/NewArrivalProduct.vue";
import SmartLifestyle from "../components/Home/SmartLifestyle.vue";
import TrendingProduct from "../components/Home/TrendingProduct.vue";
import UsedProduct from "../components/Home/UsedProduct.vue";
import ProductCard from "../components/ProductCard.vue";
import ProductCardSkelton from "../components/ProductCardSkelton.vue";
import ShopByBrand from "../components/ShopByBrand.vue";
import TopNicheSection from "../components/TopNicheSection.vue";

export default {
    name: "HomePage",
    data() {
        return {
            isLoading: true,
            breadcrumbTitle: "All Products",
        };
    },
    computed: {
        ...mapGetters("home", ["banners", "homeLoading"]),
        ...mapGetters("home", ["categories", "categoryLoading"]),
        ...mapGetters("home", [
            "new_products",
            "trending_products",
            "featured_products",
            "used_products",
            "homeLoading",
            "setting",
        ]),
    },
    created() {
        this.fetchHomeData();
    },
    mounted() {
        if (window.innerWidth > 991) {
            $("#sp_vertical_menu .vertical_menu").insertBefore(
                "#sp_header_top .col-lg-9.text-left"
            );
        }
    },
    methods: {
        ...mapActions("home", ["fetchHomeData"]),
    },
    components: {
        Footer,
        Header,
        NewArrivalProduct,
        TrendingProduct,
        FeaturedProduct,
        UsedProduct,
        BlogSection,
        BannerSection,
        FeaturedCategory,
        ShopByBrand,
        ProductCard,
        ProductCardSkelton,
        TopNicheSection,
        SmartLifestyle,
    },
};
</script>

<style>
/* Add global styles for the homepage if needed */
</style>
