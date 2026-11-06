import axios from '@/axios';
import { useToast } from 'vue-toastification';

export default {
  namespaced: true,
  state: () => ({
    items: [],
    coupon: null,
    subtotal: 0,
    cartDiscount: 0,
    productDiscount: 0,
    couponDiscount: 0,
    tax: 0,
    total: 0,
    loading: false,
    error: null,
  }),
  mutations: {
    setCart(state, payload) {
      state.items = payload.items ? Object.values(payload.items) : [];
      state.subtotal = payload.subtotal;
      state.cartDiscount = payload.discount;
      state.productDiscount = payload.productDiscount;
      state.couponDiscount = payload.couponDiscount;
      state.tax = payload.tax;
      state.total = payload.total;
      state.coupon = payload.coupon;
    },
    setLoading(state, status) {
      state.loading = status;
    },
    setError(state, error) {
      state.error = error;
    },
    clearError(state) {
      state.error = null;
    },
    clearCart(state) {
      state.items = [];
      state.subtotal = 0;
      state.cartDiscount = 0;
      state.productDiscount = 0;
      state.couponDiscount = 0;
      state.tax = 0;
      state.total = 0;
      state.coupon = null;
    }
  },
  actions: {
    async fetchCart({ commit }) {
      commit('setLoading', true);
      commit('clearError');
      try {
        const { data } = await axios.get('/api/cart/summary');
        // console.log(data);
        commit('setCart', data);
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to fetch cart.');
      } finally {
        commit('setLoading', false);
      }
    },

    async addToCart({ dispatch, commit }, product) {
      const toast = useToast();
      commit('setLoading', true);
      commit('clearError');
      try {
        await axios.post('/api/cart/add', { product_id: product.id, quantity: 1, discount: product.discount });
        await dispatch('fetchCart');
        toast.success(`Product added to cart!`);
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to add to cart.');
      } finally {
        commit('setLoading', false);
      }
    },

    async updateCart({ dispatch, commit }, { productId, quantity }) {
      commit('setLoading', true);
      commit('clearError');
      try {
        await axios.post(`/api/cart/update/${productId}`, { quantity });
        await dispatch('fetchCart');
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to update cart.');
      } finally {
        commit('setLoading', false);
      }
    },

    async removeFromCart({ dispatch, commit }, productId) {
      commit('setLoading', true);
      commit('clearError');
      try {
        await axios.delete(`/api/cart/remove/${productId}`);
        await dispatch('fetchCart');
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to remove item.');
      } finally {
        commit('setLoading', false);
      }
    },

    async applyCoupon({ dispatch, commit }, { code, type, discount }) {
      commit('setLoading', true);
      commit('clearError');
      try {
        await axios.post('/api/cart/coupon', { code, type, discount });
        await dispatch('fetchCart');
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to apply coupon.');
      } finally {
        commit('setLoading', false);
      }
    },

    async removeCoupon({ dispatch, commit }) {
      commit('setLoading', true);
      commit('clearError');
      try {
        await axios.delete('/api/cart/coupon');
        await dispatch('fetchCart');
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to remove coupon.');
      } finally {
        commit('setLoading', false);
      }
    },
    async clearCartAfterOrder({ commit }) {
      try {
        await axios.post('/api/cart/clear'); // optional, if backend supports it
      } catch (err) {
        console.warn('Cart clear request failed on backend, clearing locally only.');
      }
      commit('clearCart');
    },
  },
  getters: {
    coupon: state => state.coupon,
    cartItems: state => state.items,
    cartSubtotal: state => state.subtotal,
    cartDiscount: state => state.cartDiscount,
    couponDiscount: state => state.couponDiscount,
    productDiscount: state => state.productDiscount,
    cartTax: state => state.tax,
    cartTotal: state => state.total,
    cartCoupon: state => state.coupon,
    cartLoading: state => state.loading,
    cartError: state => state.error,
    cartCount: state => state.items.reduce((sum, item) => sum + item.quantity, 0),
    totalCartItem: state => state.items.length,
  },
};
