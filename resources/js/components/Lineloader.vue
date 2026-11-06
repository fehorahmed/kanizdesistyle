<template>
  <div class="loader-container">
    <div class="loader" :style="{ animationDuration: `${duration}s` }"></div>
  </div>
</template>

<script>
export default {
  name: "LineLoader",
  props: {
    duration: {
      type: Number,
      default: 3,
    },
  },
};
</script>

<style scoped>
/* Line loader CSS */
.loader-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background-color: #f3f3f3;
  z-index: 9999;
}

.loader {
  height: 100%;
  background-color: #3498db;
  width: 0%;
  animation: load 3s ease-in-out infinite;
}

@keyframes load {
  0% {
    width: 0%;
  }
  50% {
    width: 60%;
  }
  100% {
    width: 100%;
  }
}
</style>
