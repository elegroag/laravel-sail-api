<template>
  <section>
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-6">
      <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm', iconBg]">
        <component :is="iconComponent" class="w-6 h-6 text-white" />
      </div>
      <div>
        <h2 class="text-base font-bold text-gray-800 leading-tight">{{ title }}</h2>
        <p v-if="subtitle" class="text-xs text-gray-400 mt-0.5 leading-relaxed">{{ subtitle }}</p>
      </div>
    </div>

    <!-- Section Content -->
    <slot />
  </section>
</template>

<script setup lang="ts">
import { computed, type Component } from 'vue'
import IconUsersGroup from '../../icons/IconUsersGroup.vue'
import IconSearch from '../../icons/IconSearch.vue'
import IconPackage from '../../icons/IconPackage.vue'

const props = defineProps<{
  title: string
  subtitle?: string
  icon: 'users' | 'search' | 'package'
  iconBg?: string
}>()

const iconComponent = computed<Component>(() => {
  const icons: Record<string, Component> = {
    'users': IconUsersGroup,
    'search': IconSearch,
    'package': IconPackage,
  }
  return icons[props.icon] || IconUsersGroup
})
</script>