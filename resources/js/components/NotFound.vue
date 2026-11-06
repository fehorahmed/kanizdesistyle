<template>
  <div class="container vh-100 d-flex flex-column justify-content-center align-items-center text-center">
    <h1 class="display-1 fw-bold text-primary">404</h1>
    <h2 class="h4 fw-semibold text-dark mb-3">Oops! Page Not Found</h2>
    <p class="text-muted mb-4">
      The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
    </p>
    <router-link to="/" class="btn btn-primary px-4">
      ⬅️ Back to Home
    </router-link>

    <img
        src="https://storyset.com/web/404-error"
        alt="404 illustration"
        class="img-fluid mt-5"
        style="max-width: 400px;"
    />
  </div>
</template>

<script>
export default {
    name: "NotFound",
};
</script>
<style scoped>
    body {
        background-color: #f8f9fa;
    }
</style>