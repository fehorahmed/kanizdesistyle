<template>
  <div>
    <!-- header area -->                                                                    <!-- first row -->
    <Header></Header>

    <div id="login_page" class="login-page animate__animated animate__fadeInUp">

		<div class="sp_header bg-white p-3 ">
			<div class="container custom_container">	
				<div class="row ">
					<div class="col-12 ">
						<ul>
							<li class="d-inline-block font-weight-bolder"><router-link :to="{ name: 'home' }">home</router-link></li>
							<li class="d-inline-block hr_ font-weight-bolder"><router-link :to="{ name: 'login' }" :class="{ active: $route.name === 'login' }">login</router-link></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container custom_container ">
			<h3 class="text-center">Log in to your account </h3>
			<div id="login" class="page-content card card-block p-3 p-sm-4 ">
				<form @submit.prevent="handleLogin" class="needs-validation" novalidate>
					<div class="form-group">
						<label for="user_email" class="font-weight-bolder">Email address</label>
						<input v-model="user.email" type="email" class="form-control" id="user_email" required>
					</div>

					<div class="form-group">
						<label for="user_password" class="font-weight-bolder">Password</label>
						<input v-model="user.password" type="password" class="form-control" id="user_password" required>
					</div>

					<button
						class="px-6 py-2 mb-3 rounded-lg text-white font-semibold 
								bg-primary_color transition-colors duration-300 ease-in-out 
								hover:bg-primary_color/90 disabled:opacity-50 disabled:cursor-not-allowed"
						:disabled="isSubmitting"
						>
						<span v-if="isSubmitting">Logging in...</span>
						<span v-else>Submit</span>
					</button>

					<p v-if="error" class="text-danger">{{ error }}</p>

					<div class="pass_acc border-top pt-3">
						<span class="forgot_password font-weight-bolder">
						<router-link :to="{ name: 'forgot-password' }">Forgot your password?</router-link>
						</span>
						<span class="no_account float-right font-weight-bolder">
						<router-link :to="{ name: 'register' }">No account? Register here</router-link>
						</span>
					</div>
				</form>
			</div>
		</div>
	         
	</div> 

    <!-- footer -->
    <Footer></Footer>
    
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';
export default {
  name: "LoginPage",
  data() {
      return {
          	isLoading: true,
          	user:{
				email: '',
				password: '',
			},
			isSubmitting: false,
			error: null,
      };
  },
  mounted() {
    if (window.innerWidth > 991) {
      $("#sp_vertical_menu .vertical_menu").insertBefore("#sp_header_top .col-lg-9.text-left");
    }
  },
  methods:{
    ...mapActions('auth', ['login', 'fetchUser']),

    async handleLogin() {
      this.isSubmitting = true;
      this.error = null;

      try {
        // 2. Submit login
        await this.login(this.user);

        // 3. Fetch user data to Vuex
        await this.fetchUser();

        // 4. Redirect to account/dashboard
        // this.$router.push({ name: 'account.dashboard' });
		this.$router.push(this.$route.query.redirect || { name: 'account.dashboard' });
      } catch (err) {
        this.error = err.response?.data?.message || 'Login failed.';
      } finally {
        this.isSubmitting = false;
      }
    }  
  },
  components: {
    Footer,
    Header,
  },
};
</script>

<style>

</style>
