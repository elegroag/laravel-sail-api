<script setup lang="ts">
import { ref } from 'vue'
import MercurioSidebar from '@/components/mercurio/MercurioSidebar.vue'
import MercurioHeader from '@/components/mercurio/MercurioHeader.vue'
import type { BreadcrumbItem } from '@/types'

defineProps<{
  breadcrumbs?: BreadcrumbItem[]
}>()

const sidebarCollapsed = ref(false)

function toggleSidebar() {
  sidebarCollapsed.value = !sidebarCollapsed.value
}
</script>

<template>
  <div class="flex h-screen w-full overflow-hidden bg-[rgb(250,244,232)]">
    <!-- Sidebar -->
    <MercurioSidebar
      :collapsed="sidebarCollapsed"
      @toggle-collapse="sidebarCollapsed = !sidebarCollapsed"
    />

    <!-- Main content -->
    <div class="flex flex-1 flex-col overflow-hidden">
      <!-- Header -->
      <MercurioHeader
        :breadcrumbs="breadcrumbs || []"
        @toggle-sidebar="toggleSidebar"
      />

      <!-- Page content -->
      <main class="flex-1 overflow-y-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>
