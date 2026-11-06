// File: layouts/AccountLayout.vue
<template>
  <div>
    <Header />
    <section class="account-area py-5">
      <div class="container">
        <div class="row">
          <div class="col-md-3">
            <div class="account-sidebar bg-white p-3 rounded shadow-sm">
              <h5 class="font-weight-bold mb-3">My Account</h5>
              <ul class="list-unstyled account-nav">
                <li><router-link :to="{ name: 'account.dashboard' }" :class="{ active: isActive('account.dashboard') }">Dashboard</router-link></li>
                <li><router-link :to="{ name: 'account.orders' }" :class="{ active: isActive('account.orders') }">Orders</router-link></li>
                <li><router-link :to="{ name: 'account.wishlist' }" :class="{ active: isActive('account.wishlist') }">Wishlist</router-link></li>
                <li><router-link :to="{ name: 'account.profile' }" :class="{ active: isActive('account.profile') }">Profile</router-link></li>
                <li><router-link :to="{ name: 'account.password' }" :class="{ active: isActive('account.password') }">Change Password</router-link></li>
                <li><a href="/logout">Logout</a></li>
              </ul>
            </div>
          </div>

          <div class="col-md-9">
            <div class="account-content bg-white p-4 rounded shadow-sm">
              <router-view />
            </div>
          </div>
        </div>
      </div>
    </section>
    <Footer />
  </div>
</template>

<script>
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';

export default {
  name: "AccountLayout",
  components: { Header, Footer },
  methods: {
    isActive(name) {
      return this.$route.name === name;
    }
  }
};
</script>

<style scoped>
.account-nav li {
  margin-bottom: 10px;
}
.account-nav a {
  color: #333;
  font-weight: 500;
  display: block;
  padding: 8px 12px;
  border-radius: 5px;
}
.account-nav a.active,
.account-nav a:hover {
  background-color: #f0f0f0;
  color: #000;
}
</style>


// File: views/account/Dashboard.vue
<template>
  <div>
    <h3>Welcome, {{ user.name }}</h3>
    <p>This is your account dashboard.</p>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: "Dashboard",
  computed: {
    ...mapState(['user'])
  },
  mounted() {
    if (!this.user.name) {
      this.$store.dispatch('fetchUser');
    }
  }
};
</script>


// File: views/account/Orders.vue
<template>
  <div>
    <h3>My Orders</h3>
    <ul v-if="orders.length">
      <li v-for="order in orders" :key="order.id">
        Order #{{ order.id }} - {{ order.status }} - {{ order.total }}
      </li>
    </ul>
    <p v-else>You haven’t placed any orders yet.</p>
  </div>
</template>

<script>
export default {
  name: "Orders",
  data() {
    return { orders: [] };
  },
  mounted() {
    this.fetchOrders();
  },
  methods: {
    async fetchOrders() {
      const res = await this.$axios.get('/api/account/orders');
      this.orders = res.data;
    }
  }
};
</script>


// File: views/account/Wishlist.vue
<template>
  <div>
    <h3>Wishlist</h3>
    <ul v-if="items.length">
      <li v-for="item in items" :key="item.id">{{ item.name }} - {{ item.price }}</li>
    </ul>
    <p v-else>Your wishlist is currently empty.</p>
  </div>
</template>

<script>
export default {
  name: "Wishlist",
  data() {
    return { items: [] };
  },
  mounted() {
    this.$axios.get('/api/account/wishlist').then(res => {
      this.items = res.data;
    });
  }
};
</script>


// File: views/account/Profile.vue
<template>
  <div>
    <h3>Profile Settings</h3>
    <form @submit.prevent="updateProfile">
      <div class="form-group">
        <label>Name</label>
        <input type="text" class="form-control" v-model="form.name" />
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" class="form-control" v-model="form.email" />
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: "Profile",
  computed: {
    ...mapState(['user'])
  },
  data() {
    return {
      form: {
        name: '',
        email: ''
      }
    };
  },
  mounted() {
    this.form.name = this.user.name;
    this.form.email = this.user.email;
  },
  methods: {
    async updateProfile() {
      const res = await this.$axios.put('/api/account/profile', this.form);
      this.$store.dispatch('fetchUser');
      alert('Profile updated successfully!');
    }
  }
};
</script>


// File: views/account/Password.vue
<template>
  <div>
    <h3>Change Password</h3>
    <form @submit.prevent="changePassword">
      <div class="form-group">
        <label>Current Password</label>
        <input type="password" class="form-control" v-model="form.current" />
      </div>
      <div class="form-group">
        <label>New Password</label>
        <input type="password" class="form-control" v-model="form.new" />
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" class="form-control" v-model="form.confirm" />
      </div>
      <button type="submit" class="btn btn-primary">Change Password</button>
    </form>
  </div>
</template>

<script>
export default {
  name: "Password",
  data() {
    return {
      form: {
        current: '',
        new: '',
        confirm: ''
      }
    };
  },
  methods: {
    async changePassword() {
      if (this.form.new !== this.form.confirm) {
        alert("Passwords do not match.");
        return;
      }
      await this.$axios.post('/api/account/change-password', this.form);
      alert("Password changed successfully!");
    }
  }
};
</script>


// Vuex Store (store/index.js)
export default {
  state: {
    user: {}
  },
  mutations: {
    setUser(state, user) {
      state.user = user;
    }
  },
  actions: {
    async fetchUser({ commit }) {
      const res = await axios.get('/api/account/user');
      commit('setUser', res.data);
    }
  }
};
