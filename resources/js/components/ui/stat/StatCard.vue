<template>
  <div class="bg-card rounded-lg border border-border overflow-hidden hover:shadow-sm transition-shadow">
    <!-- Image container -->
    <div class="p-4 pb-2">
      <div class="h-24 w-24 rounded-lg bg-muted/50 flex items-center justify-center overflow-hidden">
        <img :src="image" :alt="title" class="h-full w-full object-contain" />
      </div>
    </div>

    <!-- Content -->
    <div class="px-4 pb-4">
      <!-- Title -->
      <h3 class="text-sm font-semibold text-foreground mb-3">{{ title }}</h3>

      <!-- Stats Grid -->
      <div class="grid grid-cols-2 gap-x-4 gap-y-2 mb-3">
        <div v-for="stat in stats" :key="stat.label" class="flex items-center gap-1.5">
          <component 
            :is="getStatIcon(stat.icon)" 
            :class="['h-3.5 w-3.5 stroke-[1.5]', getStatColor(stat.icon)]" 
          />
          <span class="text-xs text-muted-foreground">{{ stat.label }}</span>
          <span class="text-xs font-bold text-foreground">{{ stat.value }}</span>
        </div>
      </div>

      <!-- Temporales Link -->
      <div v-if="temporales !== undefined" class="flex items-center justify-between pt-2 border-t border-border">
        <Link href="#" class="text-xs text-primary hover:underline font-medium">Temporales</Link>
        <span class="text-xs font-bold text-primary">{{ temporales }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import IconClock from '@/components/icons/IconClock.vue'
import IconCheckCircle from '@/components/icons/IconCheckCircle.vue'
import IconXCircle from '@/components/icons/IconXCircle.vue'
import IconRefresh from '@/components/icons/IconRefresh.vue'

defineProps<{
  title: string
  image: string
  stats: { label: string; value: number; icon: string }[]
  temporales?: number
}>()

const iconMap: Record<string, typeof IconClock> = {
  'clock': IconClock,
  'check': IconCheckCircle,
  'x': IconXCircle,
  'refresh': IconRefresh,
}

const colorMap: Record<string, string> = {
  'clock': 'text-warning',
  'check': 'text-muted-foreground',
  'x': 'text-error',
  'refresh': 'text-info',
}

const getStatIcon = (iconName: string) => {
  return iconMap[iconName] || IconClock
}

const getStatColor = (iconName: string) => {
  return colorMap[iconName] || 'text-muted-foreground'
}
</script>