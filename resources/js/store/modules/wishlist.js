import axios from '@/axios';
import { useToast } from 'vue-toastification';

export default {
  namespaced: true,
  state: () => ({
    items: [],
    loading: false,
    error: null,
  }),
  mutations: {
    setWishlist(state, items) {
      state.items = items;
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
    clearWishlist(state) {
      state.items = [];
    },
  },
  actions: {
    async fetchWishlist({ commit }) {
      const toast = useToast();
      commit('setLoading', true);
      commit('clearError');
      try {
        const { data } = await axios.get('/api/wishlist');
        commit('setWishlist', Object.values(data.wishlist));
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to fetch wishlist.');
        toast.error(err.response?.data?.message || 'Failed to fetch wishlist.');
      } finally {
        commit('setLoading', false);
      }
    },

    async addToWishlist({ dispatch, commit }, productId) {
      const toast = useToast();
      commit('setLoading', true);
      commit('clearError');
      try {
        await axios.post(`/api/wishlist/${productId}`);
        await dispatch('fetchWishlist');
        toast.success('Product added to wishlist!');
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to add to wishlist.');
        toast.error(err.response?.data?.message || 'Failed to add to wishlist.');
      } finally {
        commit('setLoading', false);
      }
    },

    async removeFromWishlist({ dispatch, commit }, productId) {
      const toast = useToast();
      commit('setLoading', true);
      commit('clearError');
      try {
        await axios.delete(`/api/wishlist/${productId}`);
        await dispatch('fetchWishlist');
        toast.success('Product removed from wishlist!');
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to remove wishlist item.');
        toast.error(err.response?.data?.message || 'Failed to remove wishlist item.');
      } finally {
        commit('setLoading', false);
      }
    },

    async clearWishlist({ commit }) {
      commit('clearWishlist');
    },
  },
  getters: {
    wishlistItems: state => state.items,
    wishlistCount: state => state.items.length,
    wishlistLoading: state => state.loading,
    wishlistError: state => state.error,
  },
};
