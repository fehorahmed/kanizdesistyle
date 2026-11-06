<!-- SkeletonLoader.vue -->
<template>
  <div class="skeleton-carousel">
    <div v-for="n in 3" :key="n" class="skeleton-slide"></div>
  </div>
</template>

<style scoped>
.skeleton-carousel {
  display: flex;
  gap: 10px;
}

.skeleton-slide {
  flex: 1;
  height: 250px;
  border-radius: 8px;
  background: linear-gradient(90deg, #e0e0e0 25%, #f5f5f5 37%, #e0e0e0 63%);
  background-size: 400% 100%;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}
</style>
