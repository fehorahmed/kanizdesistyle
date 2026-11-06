import axios from 'axios';

const state = {
  products: [],
  productLoading: false,
  brands: [],
  filters: [],
  filterLoading: false,
};

const mutations = {
  SET_PRODUCTS(state, data) {
    state.products = data;
  },
  SET_PRODUCT_LOADING(state, status) {
    state.productLoading = status;
  },
  SET_BRANDS(state, data) {
    state.brands = data;
  },
  SET_FILTERS(state, data) {
    state.filters = data;
  },
  SET_FILTER_LOADING(state, status) {
    state.filterLoading = status;
  }
};

const actions = {
  async fetchFilters({ commit }, params = {}) {
    commit("SET_FILTER_LOADING", true);
    try {
      const { data } = await axios.get("/api/get-filters", { params });
      console.log(data.filters);
      commit("SET_BRANDS", data.brands);
      commit("SET_FILTERS", data.filters);
    } catch (err) {
      console.error("Error fetching filters:", err);
    } finally {
      commit("SET_FILTER_LOADING", false);
    }
  },
  async fetchProductData({ commit },params) {
    commit('SET_PRODUCT_LOADING', true);
    try {
      const res = await axios.get(`/api/get-product-data`, { params });
      const { products } = res.data;
 
      commit('SET_PRODUCTS', products);
      return products;
    } catch (err) {
      console.error('Error fetching product data:', err);
    } finally {
      commit('SET_PRODUCT_LOADING', false);
    }
  },
  
};

const getters = {
  products: (state) => state.products,
  productLoading: (state) => state.productLoading,
  brands: (state) => state.brands,
  filters: (state) => state.filters,
  filterLoading: (state) => state.filterLoading,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
};
