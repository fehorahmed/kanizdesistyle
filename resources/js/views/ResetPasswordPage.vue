<template>
  <div>
    <!-- Header -->
    <Header />

    <div id="reset_page" class="forgot-page animate__animated animate__fadeInUp">
      <div class="sp_header bg-white p-3">
        <div class="container custom_container">
          <div class="row">
            <div class="col-12">
              <ul>
                <li class="d-inline-block font-weight-bolder">
                  <router-link :to="{ name: 'home' }">home</router-link>
                </li>
                <li class="d-inline-block hr_ font-weight-bolder">
                  <router-link :to="{ name: 'reset-password', params: { token: $route.params.token }, query: { email: $route.query.email } }"
                    :class="{ active: $route.name === 'reset-password' }">
                    Reset Password
                  </router-link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="container custom_container">
        <h3 class="text-center mb-4">Reset your password</h3>

        <div class="page-content card card-block p-3 p-sm-4">
          <!-- Skeleton Loading -->
          <div v-if="authLoading" class="space-y-4">
            <div v-for="n in 3" :key="n" class="h-6 w-full bg-gray-200 rounded animate-pulse"></div>
            <div v-for="n in 2" :key="'btn'+n" class="h-10 w-32 bg-gray-300 rounded animate-pulse"></div>
          </div>

          <!-- Form -->
          <form v-else @submit.prevent="handleReset" class="needs-validation" novalidate>
            <div class="form-group">
              <label class="font-weight-bolder">New Password</label>
              <input type="password" class="form-control" v-model="password" required />
              <div class="invalid-feedback">Please enter your new password.</div>
            </div>

            <div class="form-group mt-3">
              <label class="font-weight-bolder">Confirm Password</label>
              <input type="password" class="form-control" v-model="password_confirmation" required />
              <div class="invalid-feedback">Please confirm your password.</div>
            </div>

            <button type="submit" class="btn btn-primary mt-4" :disabled="authLoading">
              <span v-if="authLoading">Resetting...</span>
              <span v-else>Reset Password</span>
            </button>

            <!-- Success / Error Messages -->
            <p v-if="resetSuccess" class="text-success mt-3">{{ resetSuccess }}</p>
            <p v-if="resetError" class="text-danger mt-3">{{ resetError }}</p>
          </form>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script>
import Header from "../components/Header.vue";
import Footer from "../components/Footer.vue";
import { mapGetters, mapActions } from "vuex";

export default {
  name: "ResetPasswordPage",
  data() {
    return {
      password: "",
      password_confirmation: "",
    };
  },
  computed: {
    ...mapGetters("auth", ["authLoading", "resetError", "resetSuccess"]),
  },
  methods: {
    ...mapActions("auth", ["resetPassword"]),
    async handleReset() {
      const token = this.$route.params.token;
      const email = this.$route.query.email;

      try {
        await this.resetPassword({
          email,
          token,
          password: this.password,
          password_confirmation: this.password_confirmation,
        });
      } catch (err) {
        console.error(err);
      }
    },
  },
  components: {
    Header,
    Footer,
  },
};
</script>

<style scoped>
.page-content {
  max-width: 500px;
  margin: 0 auto;
}
</style>
