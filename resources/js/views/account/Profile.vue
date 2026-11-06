<template>
  <div>
    <h3>Profile Settings</h3>
    <form @submit.prevent="updateProfile">
      <div class="form-group">
        <label>Name</label>
        <input type="text" class="form-control" style="border: 1px solid #ced4da;" v-model="form.name" />
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" class="form-control" style="border: 1px solid #ced4da;" v-model="form.email" />
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import { useToast } from 'vue-toastification';

const toast = useToast();
export default {
  name: "Profile",
  data() {
    return {
      form: {
        name: "",
        email: ""
      },
      message: null
    };
  },
  computed: {
    ...mapState("auth", ["user"])
  },
  mounted() {
    if (this.user) {
      this.form.name = this.user.name;
      this.form.email = this.user.email;
    } else {
      this.fetchUser().then(() => {
        if (this.user) {
          this.form.name = this.user.name;
          this.form.email = this.user.email;
        }
      });
    }
  },
  methods: {
    ...mapActions("auth", ["fetchUser", "updateUser"]),
    async updateProfile() {
      try {
        this.message = await this.updateUser(this.form);
        toast.success(this.message); 
      } catch (err) {
        console.error("Update failed", err);
      }
    }
  }
};
</script>