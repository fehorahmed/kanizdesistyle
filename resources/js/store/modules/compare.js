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
    setCompare(state, items) {
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
    clearCompare(state) {
      state.items = [];
    },
  },
  actions: {
    async fetchCompare({ commit }) {
      const toast = useToast();
      commit('setLoading', true);
      commit('clearError');
      try {
        const { data } = await axios.get('/api/compare');
        commit('setCompare', Object.values(data.compare));
      } catch (err) {
        // console.log(err);
        commit('setError', err.response?.data?.message || 'Failed to fetch compare list.');
        toast.error(err.response?.data?.message || 'Failed to fetch compare list.');
      } finally {
        commit('setLoading', false);
      }
    },

    async addToCompare({ dispatch, commit }, productId) {
      const toast = useToast();
      commit('setLoading', true);
      commit('clearError');
      try {
        await axios.post(`/api/compare/${productId}`);
        await dispatch('fetchCompare');
        toast.success('Product added to compare!');
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to add to compare.');
        toast.error(err.response?.data?.message || 'Failed to add to compare.');
      } finally {
        commit('setLoading', false);
      }
    },

    async removeFromCompare({ dispatch, commit }, productId) {
      const toast = useToast();
      commit('setLoading', true);
      commit('clearError');
      try {
        const response =await axios.delete(`/api/compare/${productId}`);
        console.log(response);
        await dispatch('fetchCompare');
        toast.success('Product removed from compare!');
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to remove compare item.');
        toast.error(err.response?.data?.message || 'Failed to remove compare item.');
      } finally {
        commit('setLoading', false);
      }
    },

    async clearCompare({ commit }) {
      commit('clearCompare');
    },
  },
  getters: {
    compareItems: state => state.items,
    compareCount: state => state.items.length,
    compareLoading: state => state.loading,
    compareError: state => state.error,
  },
};
