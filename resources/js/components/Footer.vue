<template>
    <BottomNav />
    <div
        class="footer animate__animated animate__fadeInUp !mb-0 [@media(max-width:991px)]:!mb-[60px]"
    >
        <div
            class="first_footer block w-full h-auto box-border py-[70px] px-[10px] relative z-10 mt-[60px] bg-cover bg-center"
            :style="{ backgroundImage: `url('/img/newsletter.jpg')` }"
        >
            <!-- Gradient overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-r from-[rgba(6,23,56,0.8)] to-[rgba(0,0,0,0.8)]"
            ></div>
            <div class="container custom_container">
                <div class="row">
                    <div
                        class="col-lg-6 col-md-7 col-12 d-none d-md-flex items-center"
                    >
                        <div class="newsletter ml-3">
                            <h4
                                class="block text-white text-[35px] font-rubik font-medium not-italic leading-[45px] mb-[5px] tracking-[0.3px]"
                            >
                                Get Latest Newsletter
                            </h4>
                            <p
                                class="text-white font-rubik not-italic text-[18px] font-light leading-[24px]"
                            >
                                Feel Free To Subscribe Here
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-5 col-sm-12 col-12">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control w-full px-5 text-[#555] font-normal !text-[15px] h-[47px] border-0 outline-none box-border"
                                placeholder="Enter Your Phone Number"
                                aria-label="Enter Your Phone Number"
                                aria-describedby="button-addon2"
                            />
                            <div class="input-group-append">
                                <button
                                    class="btn btn-outline-secondary text-uppercase !bg-primary_color text-white"
                                    type="button"
                                    id="button-addon2"
                                >
                                    <i class=""></i>Subscribe
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="">
            <div class="service-area bg-[#f8fffa] py-6">
                <div class="container-fluid mx-auto">
                    <div class="grid grid-cols-2 grid-cols-1 md:grid-cols-4 gap-6">
                    
                        <div class="service-details flex items-center space-x-4 p-2 md:p-4 group">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full text-center text-primary_color bg-[#fff] border-[3px] border-double border-primary_color shadow-[0_15px_35px_rgba(0,0,0,0.1)] text-lg transition-all duration-300 ease-linear group-hover:bg-primary_color group-hover:text-white">
                                <i class="fas fa-laptop"></i>
                            </div>
                            <div class="service-text">
                                <h3 class="text-sm md:text-lg text-[#39404a]">Laptop Finder</h3>
                            </div>
                        </div>

                        <div class="service-details flex items-center space-x-4 p-2 md:p-4 group">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full text-center text-primary_color bg-[#fff] border-[3px] border-double border-primary_color shadow-[0_15px_35px_rgba(0,0,0,0.1)] text-lg transition-all duration-300 ease-linear group-hover:bg-primary_color group-hover:text-white">
                                <i class="fas fa-comment"></i>
                            </div>
                            <div class="service-text">
                                <h3 class="text-sm md:text-lg text-[#39404a]">Raise a Complain</h3>
                            </div>
                        </div>

                        <div class="service-details flex items-center space-x-4 p-2 md:p-4 group">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full text-center text-primary_color bg-[#fff] border-[3px] border-double border-primary_color shadow-[0_15px_35px_rgba(0,0,0,0.1)] text-lg transition-all duration-300 ease-linear group-hover:bg-primary_color group-hover:text-white">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="service-text">
                                <h3 class="text-sm md:text-lg text-[#39404a]">Home Service</h3>
                            </div>
                        </div>

                        <div class="service-details flex items-center space-x-4 p-2 md:p-4 group">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full text-center text-primary_color bg-[#fff] border-[3px] border-double border-primary_color shadow-[0_15px_35px_rgba(0,0,0,0.1)] text-lg transition-all duration-300 ease-linear group-hover:bg-primary_color group-hover:text-white">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="service-text">
                                <h3 class="text-sm md:text-lg text-[#39404a]">Service Center</h3>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>  -->

        <div class="!bg-[#f5f6f7]">
            <footer
                class="bg-gray-100 border-t border-gray-200 container-fluid pt-4"
            >
                <div class="row">
                    <!-- Support -->
                    <div class="footer-block contact-us col-12 col-md-3 mb-4">
                        <h4
                            class="text-lg font-semibold mb-4 !text-left [@media(max-width:767px)]:!text-center"
                        >
                            Support
                        </h4>

                        <a
                            href="#"
                            class="flex items-center gap-3 bg-[#fff] shadow-sm rounded-[50px] p-2 mb-4 hover:shadow-md transition hover:[box-shadow:inset_0_-70px_0_rgba(0,0,0,0.2)] border hover:!border-primary_color hover:no-underline"
                        >
                            <div
                                class="ic text-[var(--s-f-c2)] float-left text-[36px] leading-[40px] px-[10px] border-r border-[#333]"
                            >
                                <i class="material-icons">phone</i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 m-0">
                                    9 AM - 8 PM
                                </p>
                                <h5
                                    class="text-lg font-semibold m-0 text-primary_color"
                                >
                                    01688153733
                                </h5>
                            </div>
                        </a>

                        <a
                            href="#"
                            class="flex items-center gap-3 bg-[#fff] shadow-sm rounded-[50px] p-2 hover:shadow-md transition hover:[box-shadow:inset_0_-70px_0_rgba(0,0,0,0.2)] border hover:!border-primary_color hover:no-underline"
                        >
                            <div
                                class="ic text-[var(--s-f-c2)] float-left text-[36px] leading-[40px] px-[10px] border-r border-[#333]"
                            >
                                <i class="material-icons">place</i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 m-0">
                                    Store Locator
                                </p>
                                <h5
                                    class="text-lg font-semibold m-0 text-primary_color"
                                >
                                    Find Our Stores
                                </h5>
                            </div>
                        </a>
                    </div>

                    <!-- About Us -->
                    <div
                        class="footer-block about-us col-12 col-md-6 mb-4 !text-left [@media(max-width:767px)]:!text-center"
                    >
                        <h4 class="text-lg font-semibold mb-4">About Us</h4>
                        <ul
                            class="space-y-2 xl:space-y-0 xl:flex xl:flex-wrap xl:gap-2"
                        >
                            <li
                                v-for="page in pages"
                                class="inline-block !w-auto md:!w-[48%] lg:!w-[32%] leading-5 m-0"
                            >
                                <router-link
                                    :to="{
                                        name: 'dynamic_pages',
                                        params: {
                                            slug: page.slug || 'default-slug',
                                        },
                                    }"
                                    class="!text-[14px] text-[var(--s-tertiary)] whitespace-nowrap hover:underline"
                                    >{{ page.title }}</router-link
                                >
                            </li>
                        </ul>
                    </div>

                    <!-- Org Info -->
                    <div
                        class="footer-block org-info col-12 col-md-3 mb-4 !text-left [@media(max-width:767px)]:!text-center"
                    >
                        <h4 class="text-lg font-semibold mb-4">
                            Stay Connected
                        </h4>
                        <p class="mb-3">
                            <b class="store-name font-bold font-gelica"
                                >Kaniz's Desi Style</b
                            ><br />
                            U-4, Noorjahan Road, Mohammadpur, Dhaka
                        </p>
                        <p>
                            <b class="font-gelica">Email:</b><br />
                            <a
                                href="mailto:info@kaniz.com.bd"
                                class="hover:underline normal-case !text-primary_color !text-[14px]"
                            >
                                info@kaniz.com.bd
                            </a>
                        </p>
                    </div>
                </div>
            </footer>
        </div>
        <div
            class="bg-gray-100 p-2 md:p-6 flex flex-col md:flex-row justify-between items-center gap-6 border-t border-gray-200"
        >
            <!-- App Links -->
            <div class="flex flex-col md:flex-row items-center gap-4">
                <span class="text-gray-700 font-medium">
                    Experience Ecommerce App on your mobile:
                </span>
                <div class="flex gap-3">
                    <a
                        href="#"
                        target="_blank"
                        rel="noopener"
                        class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg shadow hover:shadow-md transition"
                    >
                        <span
                            class="text-[25px] bg-playstore fab fa-google-play"
                        ></span>
                        <span class="flex flex-col text-sm text-gray-700">
                            <span class="text-[10px] mb-[-5px]"
                                >Download on</span
                            >
                            <span class="font-semibold">Google Play</span>
                        </span>
                    </a>

                    <a
                        href="#"
                        target="_blank"
                        rel="noopener"
                        class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg shadow hover:shadow-md transition"
                    >
                        <span class="text-[25px] fab fa-app-store-ios"></span>
                        <span class="flex flex-col text-sm text-gray-700">
                            <span class="text-[10px] mb-[-5px]"
                                >Download on</span
                            >
                            <span class="font-semibold">App Store</span>
                        </span>
                    </a>
                </div>
            </div>

            <!-- Social Links -->
            <div class="flex gap-4">
                <ul class="flex space-x-4 items-center">
                    <li>
                        <a
                            target="_blank"
                            href="https://www.facebook.com/ecommerce"
                            class="w-10 h-10 flex items-center justify-center rounded-full shadow-[0_15px_35px_#0000001a] !text-primary_color bg-[#fff] hover:bg-primary_color text-base font-semibold transition-all duration-300 ease-linear group"
                        >
                            <i
                                class="fab fa-facebook-f group-hover:text-white"
                            ></i>
                        </a>
                    </li>
                    <li>
                        <a
                            target="_blank"
                            href="https://www.linkedin.com/company/ecommerce"
                            class="w-10 h-10 flex items-center justify-center rounded-full shadow-[0_15px_35px_#0000001a] !text-primary_color bg-[#fff] hover:bg-primary_color text-base font-semibold transition-all duration-300 ease-linear group"
                        >
                            <i
                                class="fab fa-linkedin group-hover:text-white"
                            ></i>
                        </a>
                    </li>
                    <li>
                        <a
                            target="_blank"
                            href="https://www.instagram.com/ecommerce"
                            class="w-10 h-10 flex items-center justify-center rounded-full shadow-[0_15px_35px_#0000001a] !text-primary_color bg-[#fff] hover:bg-primary_color text-base font-semibold transition-all duration-300 ease-linear group"
                        >
                            <i
                                class="fab fa-instagram group-hover:text-white"
                            ></i>
                        </a>
                    </li>
                    <li>
                        <a
                            target="_blank"
                            href="https://www.youtube.com/@ecommerce"
                            class="w-10 h-10 flex items-center justify-center rounded-full shadow-[0_15px_35px_#0000001a] !text-primary_color bg-[#fff] hover:bg-primary_color text-base font-semibold transition-all duration-300 ease-linear group"
                        >
                            <i
                                class="fab fa-youtube group-hover:text-white"
                            ></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="fifth_footer !bg-[#193123] rounded-t-lg rounded-b-none">
            <div class="container custom_container">
                <div class="row">
                    <div class="col-12">
                        <div class="text-center d-block text-white">
                            2020-{{ currentYear }} @ All rights reserved Powered
                            by
                            <a
                                href=""
                                target="_blank"
                                class="text-white hover:text-white hover:border-b hover:border-white"
                                >ecommerce</a
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import BottomNav from "./BottomNav.vue";
export default {
    name: "Footer",
    data() {
        return {
            isLoading: true,
            currentYear: new Date().getFullYear(),
        };
    },
    components: {
        BottomNav,
    },
    computed: {
        ...mapGetters("home", ["pages", "pageLoading"]),
    },
    created() {
        this.fetchPages();
    },
    methods: {
        ...mapActions("home", ["fetchPages"]),
    },
};
</script>

<style scoped>
@media (max-width: 767px) {
    footer ul li {
        width: auto;
        padding: 0 10px;
        margin-bottom: 10px;
        position: relative;
    }
    footer ul li:after {
        content: "";
        display: inline-block;
        width: 5px;
        height: 5px;
        border-radius: 3px;
        background: #848383;
        position: absolute;
        left: -4px;
        top: 12px;
    }
}
</style>
