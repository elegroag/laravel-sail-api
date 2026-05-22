<script setup lang="ts">
defineProps<{
  show?: boolean
  white?: boolean
  className?: string
  overlay?: boolean
}>()
</script>

<template>
  <div
    v-if="show"
    :class="[
      'loader',
      white ? 'loader-white' : '',
      overlay ? 'fixed inset-0' : '',
      className || ''
    ]"
  >
    <div class="loader-inner">
      <div class="loader-line-wrap">
        <div class="loader-line"></div>
      </div>
      <div class="loader-line-wrap">
        <div class="loader-line"></div>
      </div>
      <div class="loader-line-wrap">
        <div class="loader-line"></div>
      </div>
      <div class="loader-line-wrap">
        <div class="loader-line"></div>
      </div>
      <div class="loader-line-wrap">
        <div class="loader-line"></div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.loader {
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  flex-flow: row nowrap;
  -webkit-flex-flow: row nowrap;
  -ms-flex-flow: row nowrap;
  -ms-flex-align: center;
  align-items: center;
  justify-content: center;
  bottom: 0;
  left: 0;
  overflow: hidden;
  right: 0;
  top: 0;
  z-index: 99999;
  background-color: rgba(5, 5, 5, 0.8);
  transition: all 0.3s ease;
}

.loader-white {
  background-color: rgba(255, 255, 255, 0.9);
}

.loader-inner {
  height: 60px;
  width: 100px;
  position: relative;
  transition: all 0.3s ease;
}

.loader-line-wrap {
  animation: spin 1000ms cubic-bezier(0.175, 0.885, 0.32, 1.275) infinite;
  box-sizing: border-box;
  height: 50px;
  left: 0;
  overflow: hidden;
  position: absolute;
  top: 0;
  transform-origin: 50% 100%;
  width: 100px;
  transition: all 0.3s ease;
}

.loader-line {
  border: 4px solid transparent;
  border-radius: 100%;
  box-sizing: border-box;
  height: 100px;
  left: 0;
  margin: 0 auto;
  position: absolute;
  right: 0;
  top: 0;
  width: 100px;
  transition: all 0.3s ease;
}

.loader-line-wrap:nth-child(1) {
  animation-delay: -50ms;
}

.loader-line-wrap:nth-child(2) {
  animation-delay: -100ms;
}

.loader-line-wrap:nth-child(3) {
  animation-delay: -150ms;
}

.loader-line-wrap:nth-child(4) {
  animation-delay: -200ms;
}

.loader-line-wrap:nth-child(5) {
  animation-delay: -250ms;
}

.loader-line-wrap:nth-child(1) .loader-line {
  border-color: hsl(0, 80%, 60%);
  height: 90px;
  width: 90px;
  top: 7px;
}

.loader-line-wrap:nth-child(2) .loader-line {
  border-color: hsl(60, 80%, 60%);
  height: 76px;
  width: 76px;
  top: 14px;
}

.loader-line-wrap:nth-child(3) .loader-line {
  border-color: hsl(120, 80%, 60%);
  height: 62px;
  width: 62px;
  top: 21px;
}

.loader-line-wrap:nth-child(4) .loader-line {
  border-color: hsl(180, 80%, 60%);
  height: 48px;
  width: 48px;
  top: 28px;
}

.loader-line-wrap:nth-child(5) .loader-line {
  border-color: hsl(240, 80%, 60%);
  height: 34px;
  width: 34px;
  top: 35px;
}

@keyframes spin {
  0%,
  15% {
    transform: rotate(0);
  }
  100% {
    transform: rotate(360deg);
  }
}

@media (prefers-reduced-motion: reduce) {
  .loader-line-wrap {
    animation: none;
  }
}
</style>