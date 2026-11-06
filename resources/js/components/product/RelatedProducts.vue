<template>
    <div class="related-products mt-0">
        <h2
            class="text-2xl font-bold text-center mb-6 border-b border-gray-200"
        >
            Related Products
        </h2>
        <Swiper
            :modules="modules"
            :breakpoints="breakpoints"
            :space-between="20"
            navigation
        >
            <!-- Show skeletons while loading -->
            <SwiperSlide
                v-if="productLoading"
                v-for="n in 6"
                :key="'skelton-' + n"
            >
                <ProductCardSkelton />
            </SwiperSlide>

            <!-- Show products when loaded -->
            <SwiperSlide
                v-else
                v-for="(product, index) in products"
                :key="index"
            >
                <ProductCardSingle :product="product" />
            </SwiperSlide>
        </Swiper>
    </div>
</template>

<script>
import { Navigation, Pagination } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";
import ProductCardSingle from "../ProductCardSingle.vue";
import ProductCardSkelton from "../ProductCardSkelton.vue";

// Import Swiper styles
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

export default {
    name: "RelatedProducts",
    components: {
        Swiper,
        SwiperSlide,
        ProductCardSingle,
        ProductCardSkelton,
    },
    props: {
        products: {
            type: Array,
            default: () => [],
        },
        productLoading: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            modules: [Navigation, Pagination],
            breakpoints: {
                0: { slidesPerView: 2 }, // mobile
                640: { slidesPerView: 2 }, // sm
                768: { slidesPerView: 3 }, // md
                1024: { slidesPerView: 5 }, // lg
            },
        };
    },
};
</script>

<style scoped>
.related-products {
    margin-top: 2rem;
}
:deep(.swiper-button-prev),
:deep(.swiper-button-next) {
    background-color: var(--primary-color);
    border-radius: 50%;
    width: 40px;
    height: 40px;
}
:deep(.swiper-button-prev:after),
:deep(.swiper-button-next:after) {
    font-size: 16px;
    color: white;
    font-weight: bold;
}
:deep(.swiper-button-disabled) {
    opacity: 0.5;
}
</style>
