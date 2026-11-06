import axios from '@/axios';

export default {
  namespaced: true,
  state: () => ({
    list: [],
    loading: false,
    error: null,
  }),
  mutations: {
    setOrders(state, orders) {
      state.list = orders;
    },
    setLoading(state, status) {
      state.loading = status;
    },
    setError(state, error) {
      state.error = error;
    },
    clearOrders(state) {
      state.list = [];
    }
  },
  actions: {
    async fetchOrders({ commit }) {
      commit('setLoading', true);
      commit('setError', null);
      try {
        const { data } = await axios.get('/api/orders'); // your API endpoint
        commit('setOrders', data);
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to fetch orders.');
      } finally {
        commit('setLoading', false);
      }
    }
  },
  getters: {
    ordersList: state => state.list,
    ordersLoading: state => state.loading,
    ordersError: state => state.error,
    ordersCount: state => state.list.length,
  }
};
