<template>
  <article class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">

    <!-- Top bar: estado badge alineado a la derecha -->
    <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-gray-100">
      <div class="flex items-center gap-2 min-w-0">
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
          <IconUser class="w-4 h-4 text-slate-500" />
        </div>
        <div class="min-w-0">
          <p class="text-[12px] font-semibold text-gray-800 truncate leading-tight">{{ afiliacion.nombre_completo }}</p>
          <p class="text-[11px] text-gray-400 font-mono">{{ afiliacion.cedula }}</p>
        </div>
      </div>
      <!-- Badge estado actual -->
      <span :class="['ml-3 flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold tracking-wide', estadoBadge.bg, estadoBadge.text]">
        <span :class="['w-1.5 h-1.5 rounded-full', estadoBadge.dot]"></span>
        {{ afiliacion.estado }}
      </span>
    </div>

    <!-- Body: campos en grid responsive -->
    <div class="px-5 py-4 grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3">

      <div class="flex flex-col gap-0.5">
        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Fecha Solicitud</span>
        <span class="text-[12px] text-gray-700 font-medium">{{ formatDate(afiliacion.fecha_solicitud) }}</span>
      </div>

      <div class="flex flex-col gap-0.5">
        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Fecha Inicio</span>
        <span class="text-[12px] text-gray-700 font-medium">{{ formatDate(afiliacion.fecha_inicio) }}</span>
      </div>

      <div class="flex flex-col gap-0.5">
        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">NIT</span>
        <span class="text-[12px] text-gray-700 font-mono">{{ afiliacion.nit }}</span>
      </div>

      <div class="col-span-2 flex flex-col gap-0.5">
        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Razón Social</span>
        <span class="text-[12px] text-gray-700 font-medium truncate">{{ afiliacion.razon_social }}</span>
      </div>

      <div class="flex flex-col gap-0.5">
        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Estado Previo</span>
        <span v-if="afiliacion.estado_previo"
          :class="['inline-flex items-center gap-1 text-[10px] font-semibold w-fit px-2 py-0.5 rounded-full', estadoPrevioBadge.bg, estadoPrevioBadge.text]">
          {{ afiliacion.estado_previo }}
        </span>
        <span v-else class="text-[11px] text-gray-300 italic">— Sin estado previo</span>
      </div>

    </div>

  </article>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { EstadoAfiliacion } from '@/types'
import IconUser from '../../icons/IconUser.vue'

export interface Afiliacion {
  id?: number
  nombre_completo: string
  cedula: string
  estado: EstadoAfiliacion
  fecha_solicitud: string
  fecha_inicio: string
  nit: string
  razon_social: string
  estado_previo?: EstadoAfiliacion | null
}

const props = defineProps<{ afiliacion: Afiliacion }>()

const estadoConfig: Record<EstadoAfiliacion, { bg: string; text: string; dot: string }> = {
  PENDIENTE:   { bg: 'bg-amber-50',   text: 'text-amber-600',  dot: 'bg-amber-400' },
  APROBADO:    { bg: 'bg-emerald-50', text: 'text-emerald-600',dot: 'bg-emerald-400' },
  RECHAZADO:   { bg: 'bg-red-50',     text: 'text-red-600',    dot: 'bg-red-400' },
  DEVUELTO:    { bg: 'bg-slate-100',  text: 'text-slate-500',  dot: 'bg-slate-400' },
  TEMPORAL:    { bg: 'bg-blue-50',    text: 'text-blue-600',   dot: 'bg-blue-400' },
  EN_REVISION: { bg: 'bg-violet-50',  text: 'text-violet-600', dot: 'bg-violet-400' },
}

const estadoBadge = computed(() => estadoConfig[props.afiliacion.estado])
const estadoPrevioBadge = computed(() =>
  props.afiliacion.estado_previo ? estadoConfig[props.afiliacion.estado_previo] : estadoConfig.PENDIENTE
)

const formatDate = (iso: string) => {
  if (!iso) return '—'
  const [y, m, d] = iso.split('-')
  return `${d}/${m}/${y}`
}
</script>