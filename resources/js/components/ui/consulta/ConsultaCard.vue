<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-7 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex flex-col items-center gap-5 h-full">
    <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0', iconBg]">
      <component :is="iconComponent" :class="['w-8 h-8', iconColor]" />
    </div>
    <h3 class="text-[12px] font-semibold text-gray-800 text-center leading-snug">{{ title }}</h3>
  </div>
</template>

<script setup lang="ts">
import { computed, type Component } from 'vue'
import IconBanknote from '../../icons/IconBanknote.vue'
import IconUsersGroup from '../../icons/IconUsersGroup.vue'
import IconTableCells from '../../icons/IconTableCells.vue'

const props = defineProps<{
  title: string
  icon: 'banknote' | 'users' | 'table-cells'
  iconBg?: string
  iconColor?: string
}>()

const iconComponent = computed<Component>(() => {
  const icons: Record<string, Component> = {
    'banknote': IconBanknote,
    'users': IconUsersGroup,
    'table-cells': IconTableCells,
  }
  return icons[props.icon] || IconBanknote
})
</script>