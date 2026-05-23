<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex flex-col h-full">
    <!-- Cabecera de la tarjeta -->
    <div class="flex items-center gap-3 mb-5">
      <div :class="['w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0', iconBg]">
        <component :is="iconComponent" :class="['w-5 h-5', iconColor]" />
      </div>
      <h3 class="text-[12px] font-semibold text-gray-800 leading-snug">{{ title }}</h3>
    </div>

    <!-- Estadísticas 2×2 -->
    <div class="grid grid-cols-2 gap-x-6 gap-y-3 mb-4 flex-1">
      <StatPill icon="clock" label="Pendientes" :value="stats.pendientes" icon-class="text-amber-500" />
      <StatPill icon="check" label="Aprobados" :value="stats.aprobados" icon-class="text-emerald-500" />
      <StatPill icon="x" label="Rechazados" :value="stats.rechazados" icon-class="text-red-500" />
      <StatPill icon="refresh" label="Devueltos" :value="stats.devueltos" icon-class="text-gray-400" />
    </div>

    <!-- Fila Temporales -->
    <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
      <div class="flex items-center gap-1.5">
        <IconHourglass class="w-3.5 h-3.5 text-gray-400" />
        <span class="text-[11px] text-gray-500">Temporales</span>
      </div>
      <span class="text-[11px] font-bold text-blue-500">{{ stats.temporales }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, type Component } from 'vue'
import StatPill from './StatPill.vue'
import IconHourglass from '@/components/icons/IconHourglass.vue'
import IconUsersGroup from '@/components/icons/IconUsersGroup.vue'
import IconUserPlus from '@/components/icons/IconUserPlus.vue'
import IconRefresh from '@/components/icons/IconRefresh.vue'
import IconFileText from '@/components/icons/IconFileText.vue'

interface Stats {
  pendientes: number
  aprobados: number
  rechazados: number
  devueltos: number
  temporales: number
}

const props = withDefaults(defineProps<{
  title: string
  icon: 'users' | 'user-plus' | 'refresh' | 'file-text'
  iconBg?: string
  iconColor?: string
  stats?: Stats
}>(), {
  stats: () => ({
    pendientes: 0,
    aprobados: 0,
    rechazados: 0,
    devueltos: 0,
    temporales: 0,
  })
})

const iconComponent = computed<Component>(() => {
  const icons: Record<string, Component> = {
    'users': IconUsersGroup,
    'user-plus': IconUserPlus,
    'refresh': IconRefresh,
    'file-text': IconFileText,
  }
  return icons[props.icon] || IconUsersGroup
})
</script>
