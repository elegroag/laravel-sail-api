<template>
  <div class="flex items-center justify-between gap-1">
    <div class="flex items-center gap-1 min-w-0">
      <component :is="iconComponent" :class="['w-3 h-3 flex-shrink-0', iconClass]" />
      <span class="text-[10px] text-gray-500 whitespace-nowrap">{{ label }}</span>
    </div>
    <span class="text-[11px] font-bold text-gray-700 flex-shrink-0">{{ value ?? 0 }}</span>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import IconClock from '@/components/icons/IconClock.vue'
import IconCheckCircle from '@/components/icons/IconCheckCircle.vue'
import IconXCircle from '@/components/icons/IconXCircle.vue'
import IconRefresh from '@/components/icons/IconRefresh.vue'

const props = withDefaults(defineProps<{
  icon: 'clock' | 'check' | 'x' | 'refresh'
  label: string
  value?: number
  iconClass?: string
}>(), {
  value: 0
})

const iconComponent = computed(() => {
  const icons = {
    clock: IconClock,
    check: IconCheckCircle,
    x: IconXCircle,
    refresh: IconRefresh,
  }
  return icons[props.icon]
})
</script>
