<template>
  <div>
    <div v-if="isLoggedIn">
      <h3>Welcome, {{ user.name }}</h3>
      <p>This is your account dashboard.</p>
    </div>
    <div v-else>
      <p>Please log in to access your account.</p>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex';

export default {
  name: "Dashboard",

  computed: {
    ...mapState('auth', ['user']),
    ...mapGetters('auth', ['isLoggedIn']),
  },

  mounted() {
    // If not logged in and user not loaded, fetch user
    if (!this.user) {
      this.fetchUser();
    }
    // Optional: Redirect to login if not logged in
    // if (!this.isLoggedIn) {
    //   this.$router.push({ name: 'login' });
    // }
  },

  methods: {
    ...mapActions('auth', ['fetchUser']),
  }
};
</script>
