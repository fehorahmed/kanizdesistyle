import { createStore } from 'vuex';
import cart from './modules/cart';
import auth from './modules/auth';
import home from './modules/home';
import product from './modules/product';
import product_detail from './modules/product_detail';
import order from './modules/order';
import wishlist from './modules/wishlist';
import compare from './modules/compare';

const store = createStore({
    modules: {
        cart,
        wishlist,
        compare,
        auth,
        home,
        product,
        product_detail,
        order,
    },
});

export default store;
