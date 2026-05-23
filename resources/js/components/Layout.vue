<script setup lang="ts">
import AppLayout from '@/components/layout/AppLayout.vue'

defineProps<{
  title?: string
}>()
</script>

<template>
  <AppLayout>
    <slot />
  </AppLayout>
</template>