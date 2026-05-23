<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-6 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex flex-col gap-4">
    <div class="flex items-center gap-3">
      <div :class="['w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0', iconBg]">
        <component :is="iconComponent" :class="['w-4 h-4', iconColor]" />
      </div>
      <h3 class="text-xs font-semibold text-[#344767] leading-snug">{{ title }}</h3>
    </div>
    <p class="text-[10px] text-[#8392ab] leading-relaxed">{{ description }}</p>
    <div class="mt-auto flex items-center gap-1 text-[10px] text-[#8392ab]">
      <span>Ver más</span>
      <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
      </svg>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, type Component } from 'vue'
import IconApple from '@/components/icons/IconApple.vue'
import IconPackage from '@/components/icons/IconPackage.vue'
import IconAward from '@/components/icons/IconAward.vue'

const props = withDefaults(defineProps<{
  title: string
  description?: string
  route?: string
  icon?: string
  iconBg?: string
  iconColor?: string
}>(), {
  description: '',
  route: '#',
  icon: 'package',
  iconBg: 'bg-emerald-50',
  iconColor: 'text-emerald-600',
})

const iconMap: Record<string, Component> = {
  apple: IconApple,
  package: IconPackage,
  award: IconAward,
}

const iconComponent = computed<Component>(() => iconMap[props.icon] || IconPackage)
</script>
