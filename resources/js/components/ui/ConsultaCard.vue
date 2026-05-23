<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-7 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex flex-col items-center gap-3 text-center">
    <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm', iconBg]">
      <component :is="iconComponent" :class="['w-5 h-5', iconColor]" />
    </div>
    <div>
      <h3 class="text-xs font-semibold text-[#344767] leading-snug">{{ title }}</h3>
      <p class="text-[10px] text-[#8392ab] mt-1">{{ description }}</p>
    </div>
    <div class="mt-auto flex items-center gap-1 text-[10px] text-[#8392ab]">
      <span>Ir</span>
      <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
      </svg>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, type Component } from 'vue'
import IconSearch from '@/components/icons/IconSearch.vue'
import IconUsersGroup from '@/components/icons/IconUsersGroup.vue'
import IconTableCells from '@/components/icons/IconTableCells.vue'
import IconBanknote from '@/components/icons/IconBanknote.vue'
import IconFileText from '@/components/icons/IconFileText.vue'
import IconPackage from '@/components/icons/IconPackage.vue'

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
  icon: 'search',
  iconBg: 'bg-sky-50',
  iconColor: 'text-sky-500',
})

const iconMap: Record<string, Component> = {
  search: IconSearch,
  users: IconUsersGroup,
  'table-cells': IconTableCells,
  banknote: IconBanknote,
  'file-text': IconFileText,
  package: IconPackage,
}

const iconComponent = computed<Component>(() => iconMap[props.icon] || IconSearch)
</script>
