<template>
    <div class="container custom_container t_pro_container  animate__animated animate__fadeInUp">
        <div class="row">
            <div class="col-12 text-center">
                <div>
                    <h3 class="text-[20px] md:text-[38px] leading-[20px] md:leading-[50px] text-[#39404a] block mb-0">Pre Owned Laptops</h3>
                    <p class="text-[13px] md:text-[18px]">Check & Get Your Desired Product!</p>
                </div> 
            </div> 
        </div> <!-- row -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
            <ProductCardTwo v-if="!homeLoading" :products="products" />
            <ProductCardSkelton v-else v-for="n in 6" :key="n"/>
        </div>
    </div>
</template>
<script>
import ProductCardSkelton from '../ProductCardSkelton.vue';
import ProductCardTwo from '../ProductCardTwo.vue';

export default {
    name: 'UsedProduct',
    props:{
        homeLoading: {
            type: Boolean,
            required: true
        },
        products: {
            type: Array,
            required: true
        },
    },
    data() {
        return {
            isLoading: true,
        };
    },
    components:{
        ProductCardTwo,
        ProductCardSkelton,
    }
};
</script>