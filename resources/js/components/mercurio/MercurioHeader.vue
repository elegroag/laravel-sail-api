<template>
  <header
    :class="[
      'flex h-14 items-center gap-3 border-b border-white/[0.05] px-4 transition-all duration-300',
      'bg-gradient-to-r from-[#1a2537] to-[#243447]'
    ]"
  >
    <!-- Toggle sidebar -->
    <button
      @click="$emit('toggle-sidebar')"
      class="flex items-center justify-center w-8 h-8 rounded-md text-white/60 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
      title="Alternar sidebar"
    >
      <IconMenu class="h-4 w-4" />
    </button>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-sm">
      <Link
        href="/mercurio-v2"
        class="text-white/40 hover:text-white/70 transition-colors cursor-pointer"
      >
        Inicio
      </Link>
      <template v-for="(crumb, index) in breadcrumbs" :key="index">
        <IconChevron class="h-3 w-3 text-white/30" />
        <Link
          :href="crumb.href"
          :class="[
            'transition-colors cursor-pointer',
            index === breadcrumbs.length - 1
              ? 'text-white font-medium'
              : 'text-white/40 hover:text-white/70'
          ]"
        >
          {{ crumb.title }}
        </Link>
      </template>
    </nav>

    <!-- Spacer -->
    <div class="flex-1" />

    <!-- Acciones derecha -->
    <div class="flex items-center gap-2">
      <!-- Notificaciones -->
      <button
        class="relative flex items-center justify-center w-9 h-9 rounded-full text-white/50 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
        title="Notificaciones"
      >
        <IconBell class="h-4 w-4" />
        <span class="absolute top-1 right-1 w-2 h-2 bg-[#f5365c] rounded-full"></span>
      </button>

      <!-- Usuario dropdown -->
      <div class="relative">
        <button
          @click="showUserMenu = !showUserMenu"
          class="flex items-center gap-2 rounded-full pl-1 pr-3 py-1 hover:bg-white/10 transition-colors cursor-pointer"
        >
          <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#22b7bd] to-[#596cff] flex items-center justify-center">
            <span class="text-white text-xs font-semibold">{{ userInitials }}</span>
          </div>
          <span class="text-white/70 text-xs hidden sm:block">{{ userName }}</span>
          <IconChevron class="h-3 w-3 text-white/40" />
        </button>

        <!-- Dropdown menu -->
        <Transition name="dropdown">
          <div
            v-if="showUserMenu"
            class="absolute right-0 mt-2 w-48 bg-[#1a2537] border border-white/[0.1] rounded-lg shadow-xl z-50 overflow-hidden"
          >
            <div class="px-3 py-2 border-b border-white/[0.05]">
              <p class="text-white text-xs font-medium">{{ userName }}</p>
              <p class="text-white/40 text-[10px]">{{ userEmail }}</p>
            </div>
            <div class="py-1">
              <Link
                href="/mercurio-v2/cuenta/perfil"
                class="flex items-center gap-2 px-3 py-2 text-xs text-white/60 hover:text-white hover:bg-white/5 transition-colors cursor-pointer"
              >
                <IconUser class="h-3.5 w-3.5" />
                Mi perfil
              </Link>
              <Link
                href="/mercurio-v2/cuenta/seguridad"
                class="flex items-center gap-2 px-3 py-2 text-xs text-white/60 hover:text-white hover:bg-white/5 transition-colors cursor-pointer"
              >
                <IconShield class="h-3.5 w-3.5" />
                Seguridad
              </Link>
              <div class="border-t border-white/[0.05] my-1"></div>
              <button
                @click="logout"
                class="flex items-center gap-2 px-3 py-2 text-xs text-[#f5365c] hover:bg-[#f5365c]/10 transition-colors cursor-pointer w-full"
              >
                <IconLogout class="h-3.5 w-3.5" />
                Cerrar sesión
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import type { Auth, BreadcrumbItem } from '@/types'
import IconMenu from '@/components/icons/IconMenu.vue'
import IconChevron from '@/components/icons/IconChevron.vue'
import IconBell from '@/components/icons/IconBell.vue'
import IconUser from '@/components/icons/IconUser.vue'
import IconShield from '@/components/icons/IconShield.vue'
import IconLogout from '@/components/icons/IconLogout.vue'

type Props = {
  breadcrumbs?: BreadcrumbItem[]
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => []
})

defineEmits<{
  'toggle-sidebar': []
}>()

const page = usePage()
const user = computed(() => (page.props.auth as Auth)?.user)
const showUserMenu = ref(false)

const userName = computed(() => user.value?.nombre || user.value?.name || 'Usuario')
const userEmail = computed(() => user.value?.email || '')
const userInitials = computed(() => {
  const name = userName.value
  const parts = name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.slice(0, 2).toUpperCase()
})

function logout() {
  window.location.href = '/logout'
}
</script>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
