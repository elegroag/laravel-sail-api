<template>
  <aside
    :class="[
      'relative flex flex-col bg-[#1a2537] shadow-xl transition-all duration-300 ease-in-out overflow-hidden',
      'border-r border-white/[0.05]',
      collapsed ? 'w-[70px]' : 'w-60'
    ]"
  >
    <!-- Logo header -->
    <div class="flex items-center h-16 px-4 border-b border-white/[0.05]">
      <div class="flex items-center gap-3 min-w-0">
        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br from-[#22b7bd] to-[#596cff] flex items-center justify-center shadow-md">
          <span class="text-white font-bold text-sm">M</span>
        </div>
        <Transition name="fade">
          <div v-if="!collapsed" class="min-w-0">
            <h1 class="text-white font-bold text-base leading-tight truncate">Mercurio</h1>
            <p class="text-white/40 text-[10px] truncate">Comfaca v2.0</p>
          </div>
        </Transition>
      </div>
    </div>

    <!-- Usuario -->
    <div class="px-3 py-3 border-b border-white/[0.05]">
      <div
        :class="[
          'flex items-center gap-2.5 rounded-lg p-2 transition-colors cursor-pointer',
          'hover:bg-white/5'
        ]"
      >
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-[#22b7bd] to-[#596cff] flex items-center justify-center">
          <span class="text-white text-xs font-semibold">{{ userInitials }}</span>
        </div>
        <div v-if="!collapsed" class="min-w-0 flex-1">
          <p class="text-white text-xs font-medium truncate">{{ userName }}</p>
          <p class="text-white/40 text-[10px] truncate">{{ userType }}</p>
        </div>
      </div>
    </div>

    <!-- Navegación -->
    <nav class="flex-1 overflow-y-auto px-2 py-4 space-y-1 scrollbar-thin">
      <SidebarMenuItem
        v-for="item in menuItems"
        :key="item.id"
        :item="item"
        :collapsed="collapsed"
        :is-expanded="isExpanded(item.id)"
        :is-active="isActiveRoute(item.route || '')"
        @toggle="toggleMenu(item.id)"
        @toggle-child="toggleChild"
        @navigate="setActiveRoute"
      />
    </nav>

    <!-- Footer con collapse toggle -->
    <div class="px-2 py-3 border-t border-white/[0.05]">
      <button
        @click="$emit('toggle-collapse')"
        :class="[
          'flex w-full items-center gap-2.5 rounded-md px-2.5 py-2 text-[12px] transition-all duration-150 cursor-pointer',
          'text-white/40 hover:text-white/70 hover:bg-white/5'
        ]"
        :title="collapsed ? 'Expandir' : 'Colapsar'"
      >
        <IconMenu
          :class="[
            'h-4 w-4 flex-shrink-0 transition-transform duration-300',
            collapsed ? 'rotate-180' : ''
          ]"
        />
        <span v-if="!collapsed" class="text-xs">Colapsar</span>
      </button>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import SidebarMenuItem from './SidebarMenuItem.vue'
import IconMenu from '@/components/icons/IconMenu.vue'
import { useSidebar } from '@/composables/useSidebar'
import { usePage } from '@inertiajs/vue3'
import type { Auth } from '@/types'

defineEmits<{
  'toggle-collapse': []
}>()

const { menuItems, isExpanded, toggleMenu, isActiveRoute, setActiveRoute } = useSidebar()
const page = usePage()
const user = computed(() => (page.props.auth as Auth)?.user)

const userName = computed(() => {
  const u = user.value
  return u?.nombre || u?.name || 'Usuario'
})

const userType = computed(() => {
  const u = user.value
  return u?.tipo || 'Miembro'
})

const userInitials = computed(() => {
  const name = userName.value
  const parts = name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.slice(0, 2).toUpperCase()
})

function toggleChild(id: string) {
  // placeholder para nivel 3
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
