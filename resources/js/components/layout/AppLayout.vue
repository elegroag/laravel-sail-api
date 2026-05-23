<script setup lang="ts">
import { ref } from 'vue'
import TheHeader from '@/components/layout/TheHeader.vue'
import TheSidebar from '@/components/layout/TheSidebar.vue'
import TheFooter from '@/components/layout/TheFooter.vue'
import TheBreadcrumb from '@/components/layout/TheBreadcrumb.vue'

const sidebarCollapsed = ref(false)

function toggleSidebar() {
  sidebarCollapsed.value = !sidebarCollapsed.value
}
</script>

<template>
  <div class="flex h-screen w-full overflow-hidden bg-background">
    <!-- Sidebar -->
    <TheSidebar :collapsed="sidebarCollapsed" @toggle="toggleSidebar" />

    <!-- Main area -->
    <div class="flex flex-1 flex-col overflow-hidden">
      <!-- Header -->
      <TheHeader />

      <!-- Breadcrumb -->
      <TheBreadcrumb />

      <!-- Content -->
      <main class="flex-1 overflow-y-auto p-6">
        <slot />
      </main>

      <!-- Footer -->
      <TheFooter />
    </div>
  </div>
</template>