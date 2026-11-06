<template>
  <div
        class="w-full overflow-hidden px-[18px] py-[15px] box-border rounded-lg bg-white border border-gray-200"
      >
      <!-- Image skeleton -->
      <div class="w-full h-48 bg-gray-200 rounded-lg animate-pulse"></div>

      <!-- Body skeleton -->
      <div class="mt-3 space-y-3">
        <!-- Rating skeleton -->
        <div class="flex gap-1">
          <div v-for="i in 5" :key="i" class="w-4 h-4 bg-gray-200 rounded animate-pulse"></div>
          <div class="w-10 h-3 bg-gray-200 rounded animate-pulse"></div>
        </div>

        <!-- Title skeleton -->
        <div class="h-4 bg-gray-200 rounded w-3/4 animate-pulse"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2 animate-pulse"></div>

        <!-- Price skeleton -->
        <div class="flex gap-2">
          <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
          <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
        </div>

        <!-- Discount skeleton -->
        <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>

        <!-- Button skeleton -->
        <div class="h-10 w-full bg-gray-200 rounded-lg animate-pulse"></div>
      </div>
    </div>
</template>

<script setup>

</script>
