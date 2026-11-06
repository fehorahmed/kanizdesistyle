<template>
  <div class="text-xl font-semibold">
    {{ displayText }}<span class="cursor">|</span>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const texts = ["Welcome to my site", "Explore the features", "Enjoy your stay!"];
const displayText = ref('');
const currentTextIndex = ref(0);
let charIndex = 0;
let isDeleting = false;

const typeSpeed = 100;
const deleteSpeed = 50;
const pauseBetween = 1500;

function typeLoop() {
  const fullText = texts[currentTextIndex.value];

  if (!isDeleting) {
    displayText.value = fullText.substring(0, charIndex + 1);
    charIndex++;
    if (charIndex === fullText.length) {
      isDeleting = true;
      setTimeout(typeLoop, pauseBetween);
      return;
    }
  } else {
    displayText.value = fullText.substring(0, charIndex - 1);
    charIndex--;
    if (charIndex === 0) {
      isDeleting = false;
      currentTextIndex.value = (currentTextIndex.value + 1) % texts.length;
    }
  }

  setTimeout(typeLoop, isDeleting ? deleteSpeed : typeSpeed);
}

onMounted(() => {
  typeLoop();
});
</script>

<style scoped>
.cursor {
  display: inline-block;
  width: 1ch;
  animation: blink 1s steps(1) infinite;
}

@keyframes blink {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0;
  }
}
</style>
