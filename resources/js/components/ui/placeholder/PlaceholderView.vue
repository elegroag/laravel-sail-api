<template>
  <div class="p-8">
    <div class="max-w-sm mx-auto mt-16 text-center">
      <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
        <IconGrid class="w-8 h-8 text-gray-400" />
      </div>
      <h2 class="text-xl font-bold text-gray-700 mb-2">{{ title }}</h2>
      <p class="text-sm text-gray-400">Esta sección está en construcción.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import IconGrid from '../../icons/IconGrid.vue'

defineProps<{
  title: string
}>()
</script>