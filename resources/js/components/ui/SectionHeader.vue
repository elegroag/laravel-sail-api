<template>
  <div class="flex items-center gap-4 mb-6">
    <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm', iconBgClass]">
      <component :is="iconComponent" :class="['w-5 h-5', iconClass]" />
    </div>
    <div>
      <h2 class="text-base font-semibold text-[#344767]">{{ title }}</h2>
      <p class="text-xs text-[#8392ab]">{{ description }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, type Component } from 'vue'
import IconSearch from '@/components/icons/IconSearch.vue'
import IconPackage from '@/components/icons/IconPackage.vue'
import IconClock from '@/components/icons/IconClock.vue'
import IconUsersGroup from '@/components/icons/IconUsersGroup.vue'
import IconFileText from '@/components/icons/IconFileText.vue'
import IconRefresh from '@/components/icons/IconRefresh.vue'
import IconAlertCircle from '@/components/icons/IconAlertCircle.vue'

const props = withDefaults(defineProps<{
  title: string
  description: string
  icon: string
  iconBgClass?: string
  iconClass?: string
}>(), {
  iconBgClass: 'bg-gray-100',
  iconClass: 'text-gray-500',
})

const iconMap: Record<string, Component> = {
  search: IconSearch,
  package: IconPackage,
  clock: IconClock,
  users: IconUsersGroup,
  'file-text': IconFileText,
  refresh: IconRefresh,
  'alert-circle': IconAlertCircle,
}

const iconComponent = computed<Component>(() => iconMap[props.icon] || IconSearch)
</script>
