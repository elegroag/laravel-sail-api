<template>
  <aside
    :class="[collapsed ? 'sidebar-collapsed' : 'sidebar-expanded', 'sidebar-transition']"
    class="flex flex-col flex-shrink-0 h-screen overflow-hidden"
    style="background-color: #2d4a5a;"
  >
    <!-- Logo / Toggle -->
    <div class="flex items-center justify-between px-3 py-4 border-b border-white/10 min-h-[60px]">
      <div v-if="!collapsed" class="flex items-center gap-2 overflow-hidden">
        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
          <IconGrid class="w-4 h-4 text-white" />
        </div>
        <span class="text-white text-[10px] font-bold tracking-widest leading-tight uppercase nav-label">
          COMFACA<br>EN LINEA
        </span>
      </div>
      <button
        @click="$emit('toggle')"
        class="p-1.5 rounded-md hover:bg-white/10 transition-colors flex-shrink-0 ml-auto"
        aria-label="Colapsar menú"
      >
        <IconMenu class="w-4 h-4 text-white/70" />
      </button>
    </div>

    <!-- Info de usuario -->
    <div v-if="!collapsed" class="px-4 py-2.5 border-b border-white/10">
      <p class="text-[11px] text-white/50 mb-0.5">
        Tipo: <span class="text-sky-400 font-semibold">{{ userType }}</span>
      </p>
      <p class="text-[11px] text-white/50">
        Estado: <span class="text-emerald-400 font-semibold">{{ userStatus }}</span>
      </p>
    </div>

    <!-- Navegación -->
    <nav class="flex-1 py-3 overflow-y-auto overflow-x-hidden space-y-0.5 px-2">
      <SidebarMenuItem
        v-for="item in menuItems"
        :key="item.id"
        :item="item"
        :collapsed="collapsed"
        :is-expanded="isExpanded(item.id)"
        :is-active="isActiveMenuItem(item)"
        @toggle="toggleMenu(item.id)"
        @navigate="handleNavigate"
      />
    </nav>

    <!-- Pie del sidebar -->
    <div class="px-4 py-3 border-t border-white/10">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 bg-emerald-400 rounded-full flex-shrink-0 animate-pulse"></span>
        <span v-if="!collapsed" class="text-[11px] text-white/40 nav-label">Plataforma Online</span>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import type { MenuItem } from '@/types'
import { useSidebar } from '@/composables/useSidebar'
import SidebarMenuItem from '@/components/layout/SidebarMenuItem.vue'
import IconGrid from '@/components/icons/IconGrid.vue'
import IconMenu from '@/components/icons/IconMenu.vue'

defineProps<{ collapsed: boolean }>()
defineEmits<{ toggle: [] }>()

const page = usePage()
const { menuItems, isExpanded, toggleMenu, setActiveRoute } = useSidebar()

const userType = computed(() => page.props.auth?.user?.tipo || 'Trabajador')
const userStatus = computed(() => page.props.auth?.user?.estado || 'ACTIVO')

/** Un ítem está activo si la ruta actual coincide con él o con algún hijo */
const isActiveMenuItem = computed(() => (item: MenuItem): boolean => {
  const currentPath = page.url
  if (item.route) return currentPath === item.route || currentPath.startsWith(item.route + '?')
  if (item.children) return item.children.some(c => c.route && currentPath.startsWith(c.route))
  return false
})

function handleNavigate(to: string) {
  setActiveRoute(to)
}
</script>

<style scoped>
.sidebar-transition {
  transition: width 0.2s ease;
}
.sidebar-expanded {
  width: 240px;
}
.sidebar-collapsed {
  width: 56px;
}
.nav-label {
  transition: opacity 0.15s ease;
}
</style>