<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-6 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex flex-col gap-4">
    <h3 class="text-[11px] font-semibold text-gray-700 leading-snug">{{ title }}</h3>
    <div class="flex justify-center">
      <div :class="['w-14 h-14 rounded-xl flex items-center justify-center', iconBg]">
        <component :is="iconComponent" :class="['w-7 h-7', iconColor]" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, type Component } from 'vue'
import IconApple from '../../icons/IconApple.vue'

const props = defineProps<{
  title: string
  icon: string
  iconBg?: string
  iconColor?: string
}>()

const iconComponent = computed<Component>(() => {
  const icons: Record<string, Component> = {
    'apple': IconApple,
  }
  return icons[props.icon] || IconApple
})
</script>