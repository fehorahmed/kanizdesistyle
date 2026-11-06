<template>
  <div>
    <!-- header area -->                                                                    <!-- first row -->
    <Header></Header>

    <div id="forgot_page" class="forgot-page animate__animated animate__fadeInUp">
		<div class="sp_header bg-white p-3">
			<div class="container custom_container">	
				<div class="row ">
					<div class="col-12 ">
						<ul>
							<li class="d-inline-block font-weight-bolder"><router-link :to="{ name: 'home' }">home</router-link></li>
							<li class="d-inline-block hr_ font-weight-bolder"><router-link :to="{ name: 'forgot-password' }" :class="{ active: $route.name === 'forgot-password' }">Forgot password</router-link></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container custom_container ">
			<h3 class="text-center">Forgot your password?  </h3>
			<div id="forgot" class="page-content card card-block p-3 p-sm-4">
				<form @submit.prevent="handleSubmit" class="needs-validation" novalidate>
					<div class="form-group">
						<p class="renew_pass text-muted">
						Please enter the email address you used to register. You will receive a temporary link to reset your password.
						</p>
						<label class="font-weight-bolder">Email address</label>
						<input 
						type="email" 
						class="form-control" 
						v-model="email" 
						required 
						/>
						<div class="invalid-feedback">Please enter your email address.</div>
					</div>

					<button 
						type="submit" 
						class="btn btn-primary mt-3" 
						:disabled="authLoading"
					>
						<span v-if="authLoading">Sending...</span>
						<span v-else>Send</span>
					</button>

					<p v-if="forgotSuccess" class="text-success mt-3">{{ forgotSuccess }}</p>
					<p v-if="forgotError" class="text-danger mt-3">{{ forgotError }}</p>
				</form>
			</div>
		</div>
	</div>

    <!-- footer -->
    <Footer></Footer>
    
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Header from "../components/Header.vue";
import Footer from "../components/Footer.vue";

export default {
  name: "ForgotPage",
  data() {
    return {
      email: "",
    };
  },
  computed: {
    ...mapGetters("auth", ["authLoading", "forgotError", "forgotSuccess"]),
  },
  methods: {
    ...mapActions("auth", ["forgotPassword"]),
    async handleSubmit() {
      try {
        await this.forgotPassword(this.email);
      } catch (err) {
        console.error(err);
      }
    },
  },
  components: {
    Footer,
    Header,
  },
};
</script>

<style>

</style>
