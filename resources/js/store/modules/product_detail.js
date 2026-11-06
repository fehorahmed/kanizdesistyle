import axios from '@/axios';

const state = {
  product: {},
  relatedProducts: {},
  productLoading: false,
  seo: {
    title: '',
    description: '',
    image: '',
    url: '',
  },
  summary_reviews: [],            
  reviews: [],            
  reviewLoading: false,
  reviewPage: 1,
  reviewLastPage: 1,
};

const mutations = {
  SET_PRODUCT(state, data) {
    state.product = data;
  },
  SET_RELATED_PRODUCTS(state, data) {
    state.relatedProducts = data;
  },
  SET_PRODUCT_LOADING(state, status) {
    state.productLoading = status;
  },
  SET_SEO(state, { title, description, image, url }) {
    state.seo = { title, description, image, url };
  },
  SET_SUMMARY_REVIEWS(state, data) {
    state.summary_reviews = data;
  },
  SET_REVIEWS(state, data) {
    state.reviews = data;
  },
  ADD_REVIEWS(state, data) {
    state.reviews = [...state.reviews, ...data]; // append new reviews
  },
  ADD_REVIEW(state, review) {
    state.reviews.unshift(review); // add new review at top
  },
  SET_REVIEW_LOADING(state, status) {
    state.reviewLoading = status;
  },
  SET_REVIEW_PAGE(state, page) {
    state.reviewPage = page;
  },
  SET_REVIEW_LAST_PAGE(state, lastPage) {
    state.reviewLastPage = lastPage;
  },
};

const actions = {
  async fetchProductData({ commit }, slug) {
    commit('SET_PRODUCT_LOADING', true);
    try {
      const res = await axios.get(`/api/get-product-details-data/${slug}`);
      const { product, relatedProducts } = res.data;
      
      if (!product || Object.keys(product).length === 0) {
        commit('SET_PRODUCT', null); 
        return false; 
      }
      commit('SET_PRODUCT', product);
      commit('SET_RELATED_PRODUCTS', relatedProducts || []);

      // Set SEO dynamically
      const title = product.name ? `${product.name} | Ecommerce` : 'Product | Ecommerce';
      const description = product?.short_description || 'Check out this amazing product at Ecommerce.';
      const image = product.images?.[0] || '/default-product.jpg';
      const url = window.location.href;

      commit('SET_SEO', { title, description, image, url });

    } catch (err) {
      console.error('Error fetching product data:', err);
    } finally {
      commit('SET_PRODUCT_LOADING', false);
    }
  },
  // --- REVIEWS ---
  async fetchReviews({ commit, state }, { productId, page = 1 }) {
    commit('SET_REVIEW_LOADING', true);
    try {
      const res = await axios.get(`/api/products/${productId}/reviews?page=${page}`);
      const { reviews, current_page, last_page } = res.data;
      if (page === 1) {
        commit('SET_REVIEWS', reviews);
      } else {
        commit('ADD_REVIEWS', reviews);
      }

      commit('SET_REVIEW_PAGE', current_page);
      commit('SET_REVIEW_LAST_PAGE', last_page);

      return reviews;
    } catch (err) {
      console.error('Error fetching reviews:', err);
    } finally {
      commit('SET_REVIEW_LOADING', false);
    }
  },

  async fetchSummaryReviews({ commit, state }, { productId }) {
    commit('SET_REVIEW_LOADING', true);
    try {
      const res = await axios.get(`/api/products/${productId}/summary-reviews`);
      const { reviews } = res.data;
      commit('SET_SUMMARY_REVIEWS', reviews);
    } catch (err) {
      console.error('Error fetching reviews:', err);
    } finally {
      commit('SET_REVIEW_LOADING', false);
    }
  },

  async submitReview({ commit, state }, { productId, reviewData }) {
    commit('SET_REVIEW_LOADING', true);
    try {
      const res = await axios.post(`/api/products/${productId}/reviews`, reviewData);
      commit('ADD_REVIEW', res.data.review);

      // Update product rating info
      const updatedProduct = {
        ...state.product,
        total_review: res.data.product.total_review,
        total_rating: res.data.product.total_rating,
        rating: res.data.product.rating,
      };
      commit('SET_PRODUCT', updatedProduct);

      return res.data.product;
    } catch (err) {
      console.error('Error submitting review:', err);
      throw err;
    } finally {
      commit('SET_REVIEW_LOADING', false);
    }
  },
};

const getters = {
  product: (state) => state.product,
  relatedProducts: (state) => state.relatedProducts,
  productLoading: (state) => state.productLoading,
  seo: (state) => state.seo,
  summary_reviews: (state) => state.summary_reviews,
  reviews: (state) => state.reviews,
  reviewLoading: (state) => state.reviewLoading,
  reviewPage: (state) => state.reviewPage,
  reviewLastPage: (state) => state.reviewLastPage,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
