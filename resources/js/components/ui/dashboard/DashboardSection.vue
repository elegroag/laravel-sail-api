<template>
  <section class="mb-8">
    <!-- Section Header -->
    <div class="flex items-start gap-3 mb-5">
      <div :class="['flex h-10 w-10 items-center justify-center rounded-lg', iconBgClass]">
        <slot name="icon" />
      </div>
      <div>
        <h2 class="text-lg font-bold text-foreground leading-tight">{{ title }}</h2>
        <p class="text-sm text-muted-foreground mt-0.5">{{ description }}</p>
      </div>
    </div>

    <!-- Section Content -->
    <slot />
  </section>
</template>

<script setup lang="ts">
defineProps<{
  title: string
  description: string
  iconBgClass?: string
}>()
</script>