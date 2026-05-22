<script setup lang="ts">
import AppLayoutTemplate from '@/layouts/app/AppSidebarLayout.vue'
import type { BreadcrumbItem } from '@/types'

defineProps<{
  title: string
  breadcrumbs?: BreadcrumbItem[]
}>()
</script>

<template>
  <AppLayoutTemplate :breadcrumbs="$props.breadcrumbs">
    <slot />
  </AppLayoutTemplate>
</template>