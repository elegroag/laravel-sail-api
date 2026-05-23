<template>
  <header class="bg-white border-b border-gray-200 px-5 flex items-center justify-between flex-shrink-0 min-h-[52px]">
    <!-- Título de página -->
    <h1 class="text-[13px] font-semibold text-gray-800 leading-none">{{ currentTitle }}</h1>

    <!-- Acciones del header -->
    <div class="flex items-center gap-1.5">
      <!-- Notificaciones -->
      <button class="p-2 rounded-lg hover:bg-gray-100 transition-colors relative" aria-label="Notificaciones">
        <IconBell class="w-4 h-4 text-gray-500" />
        <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-red-500 rounded-full"></span>
      </button>

      <!-- Grid / apps -->
      <button class="p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Aplicaciones">
        <IconGrid class="w-4 h-4 text-gray-500" />
      </button>

      <!-- Separador -->
      <div class="w-px h-5 bg-gray-200 mx-1" role="separator"></div>

      <!-- Usuario -->
      <div class="flex items-center gap-2 cursor-pointer group px-2 py-1 rounded-lg hover:bg-gray-50 transition-colors">
        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
          <span class="text-white text-[10px] font-bold select-none">{{ userInitials }}</span>
        </div>
        <span class="text-[12px] font-medium text-gray-700 hidden sm:block leading-none">
          {{ userName }}
        </span>
        <IconChevron class="w-3 h-3 text-gray-400 group-hover:text-gray-600 transition-colors" />
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import IconGrid from '@/components/icons/IconGrid.vue'
import IconBell from '@/components/icons/IconBell.vue'
import IconChevron from '@/components/icons/IconChevron.vue'

const page = usePage()

const currentTitle = computed(() => (page.props.title as string) || 'Inicio')

const userName = computed(() => {
  const user = page.props.auth?.user
  return user?.name || 'Usuario'
})

const userInitials = computed(() => {
  const name = userName.value
  const parts = name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.substring(0, 2).toUpperCase()
})
</script>