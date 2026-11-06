<template>
  <div>
    <h3>Change Password</h3>
    <form @submit.prevent="handleChangePassword">
      <div class="form-group">
        <label>Current Password</label>
        <input type="password" class="form-control" style="border: 1px solid #ced4da;" v-model="form.current" />
      </div>
      <div class="form-group">
        <label>New Password</label>
        <input type="password" class="form-control" style="border: 1px solid #ced4da;" v-model="form.new" />
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" class="form-control" style="border: 1px solid #ced4da;" v-model="form.confirm" />
      </div>
      <button type="submit" class="btn btn-primary">Change Password</button>
    </form>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import { useToast } from 'vue-toastification';

const toast = useToast();
export default {
  name: "Password",
  data() {
    return {
      form: {
        current: "",
        new: "",
        confirm: ""
      }
    };
  },
  methods: {
    ...mapActions('auth', ['changePassword']),
    async handleChangePassword() {
      if (this.form.new !== this.form.confirm) {
        alert("Passwords do not match.");
        return;
      }

      try {
        const message = await this.changePassword({
          current_password: this.form.current,
          new_password: this.form.new,
          new_password_confirmation: this.form.confirm
        });
        toast.success(message || "Password changed successfully!");
        this.form = { current: '', new: '', confirm: '' };
      } catch (error) {
        toast.error(this.$store.state.user.error || "Error changing password");
      }
    }
  }
};
</script>
