<script setup lang="ts">
</script>

<template>
  <div class="absolute inset-0 -z-10 overflow-hidden">
    <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
      <defs>
        <radialGradient id="grad1" cx="50%" cy="50%" r="50%">
          <stop offset="0%" style="stop-color:#10b981;stop-opacity:0.3" />
          <stop offset="100%" style="stop-color:#10b981;stop-opacity:0" />
        </radialGradient>
      </defs>
      <circle cx="20%" cy="80%" r="40" fill="url(#grad1)" />
      <circle cx="80%" cy="20%" r="35" fill="url(#grad1)" />
      <circle cx="70%" cy="70%" r="25" fill="url(#grad1)" />
      <circle cx="30%" cy="30%" r="20" fill="url(#grad1)" />
    </svg>
    <div class="absolute top-0 right-0 -mr-20 -mt-20 h-96 w-96 rounded-full bg-gradient-to-br from-emerald-200/30 to-transparent blur-3xl" />
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 h-96 w-96 rounded-full bg-gradient-to-tr from-teal-200/30 to-transparent blur-3xl" />
  </div>
</template>