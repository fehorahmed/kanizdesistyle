import axios from '@/axios';

export default {
  namespaced: true,
  state: () => ({
    user: null,
    loading: false,
    error: null,
    forgotError: null,
    resetError: null,
    forgotSuccess: null,
    resetSuccess: null,
  }),
  mutations: {
    setUser(state, user) {
      state.user = user;
    },
    setLoading(state, status) {
      state.loading = status;
    },
    setError(state, error) {
      state.error = error;
    },
    setForgotError(state, error) {
      state.forgotError = error;
    },
    setResetError(state, error) {
      state.resetError = error;
    },
    setForgotSuccess(state, message) {
      state.forgotSuccess = message;
    },
    setResetSuccess(state, message) {
      state.resetSuccess = message;
    },
    logout(state) {
      state.user = null;
    }
  },
  actions: {
    async fetchUser({ commit }) {
      commit('setLoading', true);
      commit('setError', null);
      try {
        const res = await axios.get('/api/user');
        commit('setUser', res.data);
      } catch (err) {
        commit('setUser', null);
        commit('setError', err.response?.data?.message || 'Failed to fetch user');
      } finally {
        commit('setLoading', false);
      }
    },
    async updateUser({ commit }, form) {
      const { data } = await axios.put("/api/user", form);
      commit("setUser", data.user);
      return data.message;
    },
    async login({ dispatch }, credentials) {
      await axios.post('/api/login', credentials);
      await dispatch('fetchUser');
    },
    async register({ commit, dispatch }, form) {
      commit('setLoading', true);
      commit('setError', null);

      try {
        await axios.post('/api/register', form);

        await dispatch('fetchUser');
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Registration failed');
        throw err; 
      } finally {
        commit('setLoading', false);
      }
    },
    async logout({ commit }) {
      await axios.post('/api/logout');
      commit('logout');
    },
    async changePassword({ commit }, form) {
      commit('setLoading', true);
      commit('setError', null);
      try {
        const res = await axios.post('/api/change-password', form);
        console.log(res.data);
        return res.data.message;
      } catch (err) {
        commit('setError', err.response?.data?.message || 'Failed to change password');
        throw err;
      } finally {
        commit('setLoading', false);
      }
    },
    async forgotPassword({ commit }, email) {
      commit("setLoading", true);
      commit("setForgotError", null);
      commit("setForgotSuccess", null);
      try {
        const res = await axios.post("/api/forgot-password", { email });
        commit("setForgotSuccess", res.data.message || "Reset link sent to email!");
      } catch (err) {
        commit(
          "setForgotError",
          err.response?.data?.message || "Failed to send reset link."
        );
        throw err;
      } finally {
        commit("setLoading", false);
      }
    },

    // ----- Reset Password -----
    async resetPassword({ commit }, { email, token, password, password_confirmation }) {
      commit("setLoading", true);
      commit("setResetError", null);
      commit("setResetSuccess", null);
      try {
        const res = await axios.post("/api/reset-password", {
          email,
          token,
          password,
          password_confirmation,
        });
        commit("setResetSuccess", res.data.message || "Password reset successfully!");
      } catch (err) {
        commit(
          "setResetError",
          err.response?.data?.message ||
            (err.response?.data?.errors?.email?.[0] || "Failed to reset password.")
        );
        throw err;
      } finally {
        commit("setLoading", false);
      }
    },
  },
  getters: {
    isLoggedIn: state => !!state.user,
    loginUser: state => state?.user || [],
    userName: state => state.user?.name || '',
    userEmail: state => state.user?.email || '',
    authLoading: state => state.loading,
    authError: state => state.error,
    forgotError: (state) => state.forgotError,
    forgotSuccess: (state) => state.forgotSuccess,
    resetError: (state) => state.resetError,
    resetSuccess: (state) => state.resetSuccess,
  }
};
