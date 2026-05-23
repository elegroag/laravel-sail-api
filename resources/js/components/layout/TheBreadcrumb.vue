<template>
  <div class="px-6 py-2 border-b border-gray-200/60 bg-white flex items-center gap-1.5 min-h-[34px]">
    <Link href="/" class="flex items-center gap-1.5">
      <IconHome class="w-3 h-3 text-gray-400 flex-shrink-0" />
    </Link>
    <template v-for="(crumb, i) in crumbs" :key="crumb.path">
      <span class="text-gray-300 text-[11px]">/</span>
      <Link
        v-if="i < crumbs.length - 1"
        :href="crumb.path"
        class="text-[11px] text-sky-500 hover:underline leading-none"
      >{{ crumb.label }}</Link>
      <span
        v-else
        class="text-[11px] text-gray-500 leading-none"
      >{{ crumb.label }}</span>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import IconHome from '@/components/icons/IconHome.vue'

const page = usePage()

const labelMap: Record<string, string> = {
  '/': 'Inicio',
  '/consultas': 'Consultas',
  '/afiliacion': 'Afiliación',
  '/certificados': 'Certificados',
  '/cuenta': 'Cuenta Usuario',
  '/productos': 'Productos y Servicios',
  '/reportar': 'Reportar Errores',
}

const crumbs = computed(() => {
  const path = page.url || '/'
  const label = labelMap[path] ?? (page.props.title as string) ?? path
  if (path === '/') return [{ path: '/', label: 'Inicio' }]
  return [
    { path: '/', label: 'Inicio' },
    { path, label },
  ]
})
</script>