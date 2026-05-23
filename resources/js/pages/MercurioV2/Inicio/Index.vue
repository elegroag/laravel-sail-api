<script setup lang="ts">
import MercurioLayout from '@/layouts/mercurio/MercurioLayout.vue'
import SectionHeader from '@/components/ui/SectionHeader.vue'
import StatCard from '@/components/ui/StatCard.vue'
import MovementCard from '@/components/ui/MovementCard.vue'
import ConsultaCard from '@/components/ui/ConsultaCard.vue'
import ProductCard from '@/components/ui/ProductCard.vue'

interface Stats {
  total: number
  aprobadas: number
  rechazadas: number
  pendientes: number
}

interface Movement {
  id: number
  tipo: string
  nombre: string
  fecha: string
  monto: number
  saldo: number
  icon: string
}

interface Props {
  stats: Stats
  movimientos: Movement[]
  consultas: { id: number; title: string; description: string; route: string }[]
  productos: { id: number; title: string; description: string; route: string }[]
}

defineProps<Props>()
</script>

<template>
  <MercurioLayout>
    <div class="max-w-7xl mx-auto space-y-8">
      <!-- Bienvenida -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-[#344767]">Bienvenido de vuelta</h1>
          <p class="text-[#8392ab] text-sm mt-1">Resumen de tu actividad en Comfaca</p>
        </div>
        <div class="text-sm text-[#8392ab]">
          {{ new Date().toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
              <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="text-xs font-medium text-[#8392ab]">Total</span>
          </div>
          <p class="text-2xl font-bold text-[#344767]">{{ stats.total }}</p>
          <p class="text-[10px] text-[#8392ab] mt-1">Afiliaciones creadas</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center">
              <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 012.25-2.25h7.5A2.25 2.25 0 0118 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 004.5 9v.878m13.5-3A2.25 2.25 0 0119.5 9v.878m0 0a2.246 2.246 0 00-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0121 12v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6c0-.98.626-1.813 1.5-2.122"/>
              </svg>
            </div>
            <span class="text-xs font-medium text-[#8392ab]">Aprobadas</span>
          </div>
          <p class="text-2xl font-bold text-emerald-600">{{ stats.aprobadas }}</p>
          <p class="text-[10px] text-[#8392ab] mt-1">Afiliaciones aprobadas</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
              <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
              </svg>
            </div>
            <span class="text-xs font-medium text-[#8392ab]">Rechazadas</span>
          </div>
          <p class="text-2xl font-bold text-red-500">{{ stats.rechazadas }}</p>
          <p class="text-[10px] text-[#8392ab] mt-1">Afiliaciones rechazadas</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
              <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="text-xs font-medium text-[#8392ab]">Pendientes</span>
          </div>
          <p class="text-2xl font-bold text-amber-500">{{ stats.pendientes }}</p>
          <p class="text-[10px] text-[#8392ab] mt-1">Afiliaciones pendientes</p>
        </div>
      </div>

      <!-- Cards de stats (no movimientos) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <MovementCard
          title="Solicitudes cónyuges"
          icon="users"
          icon-bg="bg-emerald-50"
          icon-color="text-emerald-600"
          :stats="stats.solicitudes_conyuges"
        />
        <MovementCard
          title="Solicitudes beneficiarios"
          icon="user-plus"
          icon-bg="bg-sky-50"
          icon-color="text-sky-600"
          :stats="stats.solicitudes_beneficiarios"
        />
        <MovementCard
          title="Actualización datos"
          icon="refresh"
          icon-bg="bg-violet-50"
          icon-color="text-violet-600"
          :stats="stats.actualizacion_datos"
        />
        <MovementCard
          title="Presentar certificados"
          icon="file-text"
          icon-bg="bg-amber-50"
          icon-color="text-amber-600"
          :stats="stats.presentar_certificados"
        />
      </div>

      <!-- Consultas -->
      <div>
        <SectionHeader
          title="Consultas rápidas"
          description="Accede a los servicios de consulta"
          icon="search"
          icon-bg-class="bg-sky-50"
          icon-class="text-sky-500"
        />

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <ConsultaCard
            v-for="consulta in consultas"
            :key="consulta.id"
            :title="consulta.title"
            :description="consulta.description"
            :route="consulta.route"
          />
        </div>
      </div>

      <!-- Productos y Servicios -->
      <div>
        <SectionHeader
          title="Productos y servicios"
          description="Explora los productos disponibles"
          icon="package"
          icon-bg-class="bg-violet-50"
          icon-class="text-violet-500"
        />

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <ProductCard
            v-for="producto in productos"
            :key="producto.id"
            :title="producto.title"
            :description="producto.description"
            :route="producto.route"
          />
        </div>
      </div>
    </div>
  </MercurioLayout>
</template>
