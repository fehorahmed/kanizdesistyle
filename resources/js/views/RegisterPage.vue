<template>
  <div>
    <Header />

    <div id="Registration_page" class="Registration-page animate__animated animate__fadeInUp">
      <div class="sp_header bg-white p-3">
        <div class="container custom_container">
          <div class="row">
            <div class="col-12">
              <ul>
                <li class="d-inline-block font-weight-bolder">
                  <router-link :to="{ name: 'home' }">home</router-link>
                </li>
                <li
                  class="d-inline-block hr_ font-weight-bolder"
                  :class="{ active: $route.name === 'register' }"
                >
                  <router-link :to="{ name: 'register' }">Registration</router-link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="container custom_container">
        <h3 class="text-center">Create your account</h3>
        <div id="Registration" class="page-content card card-block p-3 p-sm-4">
          <form @submit.prevent="handleRegister" novalidate class="space-y-4">
            <p>
              Already have an account?
              <router-link :to="{ name: 'login' }"> Log in instead!</router-link>
            </p>

            <!-- Name -->
            <div>
              <label class="block font-semibold mb-1">Name*</label>
              <input
                v-model="user.name"
                type="text"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                :class="{'border-red-500': (submitted && !user.name) || errors.name}"
              />
              <p v-if="submitted && !user.name" class="text-red-500 text-sm mt-1">
                Please enter your name.
              </p>
              <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name[0] }}</p>
            </div>

            <!-- Mobile -->
            <div>
              <label class="block font-semibold mb-1">Mobile No*</label>
              <input
                v-model="user.mobile"
                type="text"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                :class="{'border-red-500': (submitted && !user.mobile) || errors.mobile}"
              />
              <p v-if="submitted && !user.mobile" class="text-red-500 text-sm mt-1">
                Please enter your mobile number.
              </p>
              <p v-if="errors.mobile" class="text-red-500 text-sm mt-1">{{ errors.mobile[0] }}</p>
            </div>

            <!-- Email -->
            <div>
              <label class="block font-semibold mb-1">Email address</label>
              <input
                v-model="user.email"
                type="email"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                :class="{'border-red-500': errors.email}"
              />
              <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email[0] }}</p>
            </div>

            <!-- Password -->
            <div>
              <label class="block font-semibold mb-1">Password*</label>
              <input
                v-model="user.password"
                type="password"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                :class="{'border-red-500': (submitted && !user.password) || errors.password}"
              />
              <p v-if="submitted && !user.password" class="text-red-500 text-sm mt-1">
                Please enter your password.
              </p>
              <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password[0] }}</p>
            </div>

            <!-- Agree -->
            <div class="flex items-center">
              <input
                v-model="user.agree"
                type="checkbox"
                id="agree"
                class="mr-2 w-5 h-5 border-2 border-gray-400 rounded !accent-primary_color focus:ring-2 focus:ring-blue-300"
              />
              <label for="agree" class="text-sm mt-2">
                I agree to the terms and conditions and the privacy policy
              </label>
            </div>
            <p v-if="submitted && !user.agree" class="text-red-500 text-sm">
              You must agree before submitting.
            </p>

            <!-- Buttons -->
            <div class="flex justify-between mt-3">
              <button
                id="clearButton"
                type="reset"
                class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400"
                @click="resetForm"
              >
                Reset
              </button>
              <button
                type="submit"
                class="bg-primary_color text-white px-4 py-2 rounded disabled:opacity-50"
                :disabled="isSubmitting"
              >
                {{ isSubmitting ? "Registering..." : "Save" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>

<script>
import { mapActions } from "vuex";
import Header from "../components/Header.vue";
import Footer from "../components/Footer.vue";

export default {
  name: "RegisterPage",
  data() {
    return {
      user: {
        name: "",
        mobile: "",
        email: "",
        password: "",
        agree: false,
      },
      submitted: false,
      isSubmitting: false,
      errors: {}, 
    };
  },
  methods: {
    ...mapActions("auth", ["register"]),

    resetForm() {
      this.submitted = false;
      this.errors = {};
      this.user = {
        name: "",
        mobile: "",
        email: "",
        password: "",
        agree: false,
      };
    },

    async handleRegister() {
      this.submitted = true;
      this.errors = {};

      // Frontend validation
      if (!this.user.name || !this.user.mobile || !this.user.password || !this.user.agree) {
        return;
      }

      this.isSubmitting = true;

      try {
        await this.register(this.user);
        this.$router.push({ name: "account.dashboard" });
      } catch (err) {
        if (err.response?.status === 422) {
          // Server validation errors
          this.errors = err.response.data.errors;
        } else {
          this.errors = { general: [err.response?.data?.message || "Registration failed."] };
        }
      } finally {
        this.isSubmitting = false;
      }
    },
  },
  components: {
    Header,
    Footer,
  },
};
</script>
