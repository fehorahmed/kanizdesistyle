import axios from '@/axios';

const state = {
  banners: [],
  features: [],
  categories: [],
  //For New Products
  new_products: [],
  newProductsLoading: false,
  newProductsPage: 1,
  newProductsPerPage: 13,
  newProductsTotal: 0,

  trending_products: [],
  featured_products: [],
  used_products: [],
  testimonials: [],
  loading: false,
  categoryLoading: false,
  categories: [],
  f_categories: [],
  s_categories: [],
  galleryLoading: false,
  galleries: [],
  pageLoading: false,
  pages: [],
  page: {},
  brands: [],
  setting: {},

  //Top Niche
  top_sales_products: [],
  top_rating_products: [],
  top_discount_products: [],
  topNicheLoading: false,
};

const mutations = {
  //For New Products
  SET_NEW_PRODUCTS(state, data) {
    state.new_products = data;
  },
  APPEND_NEW_PRODUCTS(state, data) {
    state.new_products = [...state.new_products, ...data];
  },
  SET_NEW_PRODUCTS_LOADING(state, status) {
    state.newProductsLoading = status;
  },
  SET_NEW_PRODUCTS_PAGE(state, page) {
    state.newProductsPage = page;
  },
  SET_NEW_PRODUCTS_TOTAL(state, total) {
    state.newProductsTotal = total;
  },


  SET_BANNERS(state, data) {
    state.banners = data;
  },
  SET_FEATURES(state, data) {
    state.features = data;
  },
  SET_CATEGORIES(state, data) {
    state.categories = data;
  },
  SET_BRANDS(state, data) {
    state.brands = data;
  },
  SET_TRENDING_PRODUCTS(state, data) {
    state.trending_products = data;
  },
  SET_FEATURED_PRODUCTS(state, data) {
    state.featured_products = data;
  },
  SET_USED_PRODUCTS(state, data) {
    state.used_products = data;
  },
  SET_TESTIMONIALS(state, data) {
    state.testimonials = data;
  },
  SET_LOADING(state, status) {
    state.loading = status;
  },
  SET_CATEGORY_LOADING(state, status) {
    state.categoryLoading = status;
  },
  SET_F_CATEGORY(state, data) {
    state.f_categories = data;
  },
  SET_S_CATEGORY(state, data) {
    state.s_categories = data;
  },
  SET_CATEGORY(state, data) {
    state.categories = data;
  },
  SET_GALLERY_LOADING(state, status) {
    state.galleryLoading = status;
  },
  SET_GALLERY(state, data) {
    state.galleries = data;
  },
  SET_PAGE_LOADING(state, status) {
    state.pageLoading = status;
  },
  SET_PAGES(state, data) {
    state.pages = data;
  },
  SET_PAGE(state, data) {
    state.page = data;
  },
  SET_SETTING(state, data) {
    state.setting = data;
  },
  //Top Niche
  SET_TOP_SALES_PRODUCTS(state, data) {
    state.top_sales_products = data;
  },
  SET_TOP_RATING_PRODUCTS(state, data) {
    state.top_rating_products = data;
  },
  SET_TOP_DISCOUNT_PRODUCTS(state, data) {
    state.top_discount_products = data;
  },
  SET_TOP_NICHE_LOADING(state, status) {
    state.topNicheLoading = status;
  },
};

const actions = {
  async fetchNewProducts({ commit, state }, { reset = false } = {}) {
    if (reset) {
      commit("SET_NEW_PRODUCTS_PAGE", 1);
      commit("SET_NEW_PRODUCTS", []);
    }
    commit("SET_NEW_PRODUCTS_LOADING", true);
    try {
      const res = await axios.get("/api/get-new-products", {
        params: {
          limit: state.newProductsPerPage,
          offset: (state.newProductsPage - 1) * state.newProductsPerPage,
        },
      });
      // console.log(res.data);
      const { products, total } = res.data;
      if (reset) {
        commit("SET_NEW_PRODUCTS", products);
      } else {
        commit("APPEND_NEW_PRODUCTS", products);
      }
      commit("SET_NEW_PRODUCTS_TOTAL", total);
      commit("SET_NEW_PRODUCTS_PAGE", state.newProductsPage + 1);
    } catch (err) {
      console.error("Error fetching new products:", err);
    } finally {
      commit("SET_NEW_PRODUCTS_LOADING", false);
    }
  },
  async fetchHomeData({ commit }) {
    commit('SET_LOADING', true);
    try {
      const res = await axios.get('/api/get-home-data');
      const { banners, new_products, trending_products, featured_products, used_products, setting } = res.data;

      commit('SET_BANNERS', banners);
      commit('SET_TRENDING_PRODUCTS', trending_products);
      commit('SET_FEATURED_PRODUCTS', featured_products);
      commit('SET_USED_PRODUCTS', used_products);
      commit('SET_SETTING', setting);
    } catch (err) {
      console.error('Error fetching homepage data:', err);
    } finally {
      commit('SET_LOADING', false);
    }
  },
  async fetchCategories({ commit }) {
    commit('SET_CATEGORY_LOADING', true);
    try {
      const res = await axios.get('/api/get-categories');
      const { f_categories, s_categories, categories, brands } = res.data;
      // console.log(categories);  
      commit('SET_F_CATEGORY', f_categories);
      commit('SET_S_CATEGORY', s_categories);
      commit('SET_CATEGORY', categories);
      commit('SET_BRANDS', brands);
    } catch (err) {
      console.error('Error fetching categories data:', err);
    } finally {
      commit('SET_CATEGORY_LOADING', false);
    }
  },
  async fetchGalleries({ commit }) {
    commit('SET_GALLERY_LOADING', true);
    try {
      const res = await axios.get('/api/get-galleries');
      const { galleries } = res.data;
      // console.log(galleries);  
      commit('SET_GALLERY', galleries);
    } catch (err) {
      console.error('Error fetching galleries data:', err);
    } finally {
      commit('SET_GALLERY_LOADING', false);
    }
  },
  async fetchPages({ commit }) {
    commit('SET_PAGE_LOADING', true);
    try {
      const res = await axios.get('/api/get-pages');
      const { pages } = res.data;
 
      commit('SET_PAGES', pages);
    } catch (err) {
      console.error('Error fetching pages data:', err);
    } finally {
      commit('SET_PAGE_LOADING', false);
    }
  },
   async fetchPage({ commit }, slug) {
    commit("SET_PAGE_LOADING", true);
    try {
      const res = await axios.get(`/api/get-page/${slug}`);
      console.log(res);
      commit("SET_PAGE", res.data.page);
    } catch (err) {
      console.error("Error fetching page:", err);
    } finally {
      commit("SET_PAGE_LOADING", false);
    }
  },
  async fetchTopNiche({ commit }) {
    commit("SET_TOP_NICHE_LOADING", true);
    try {
      const res = await axios.get("/api/get-top-niche-products");
      
      const { top_sale_products, top_rating_products, top_discount_products } = res.data; // API should return separate lists
      commit("SET_TOP_SALES_PRODUCTS", top_sale_products);
      commit("SET_TOP_RATING_PRODUCTS", top_rating_products);
      commit("SET_TOP_DISCOUNT_PRODUCTS", top_discount_products);
    } catch (err) {
      console.error("Error fetching top niche products:", err);
    } finally {
      commit("SET_TOP_NICHE_LOADING", false);
    }
  },
};

const getters = {
  banners: (state) => state.banners,
  features: (state) => state.features,

  new_products: (state) => state.new_products,
  newProductsLoading: (state) => state.newProductsLoading,
  newProductsTotal: (state) => state.newProductsTotal,

  trending_products: (state) => state.trending_products,
  featured_products: (state) => state.featured_products,
  used_products: (state) => state.used_products,
  testimonials: (state) => state.testimonials,
  homeLoading: (state) => state.loading,
  categoryLoading: (state) => state.categoryLoading,
  f_categories: (state) => state.f_categories,
  s_categories: (state) => state.s_categories,
  categories: (state) => state.categories,
  galleryLoading: (state) => state.galleryLoading,
  galleries: (state) => state.galleries,
  pageLoading: (state) => state.pageLoading,
  pages: (state) => state.pages,
  page: (state) => state.page,
  brands: (state) => state.brands,
  setting: (state) => state.setting,

  //Top Niche
  topSalesProducts: (state) => state.top_sales_products,
  topRatingProducts: (state) => state.top_rating_products,
  topDiscountProducts: (state) => state.top_discount_products,
  topNicheLoading: (state) => state.topNicheLoading,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
};
