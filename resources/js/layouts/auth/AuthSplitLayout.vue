<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import AuthBackgroundShapes from '@/components/ui/auth-background-shapes'

defineProps<{
  title?: string
}>()
</script>

<template>
  <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
    <div class="relative hidden h-full flex-col bg-muted p-10 text-white lg:flex dark:border-r">
      <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700" />
      <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full -translate-y-16 translate-x-16 opacity-60" />
      <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-emerald-800 to-emerald-600 rounded-full translate-y-12 -translate-x-12 opacity-40" />

      <slot name="header">
        <Link href="/" class="relative z-20 flex items-center text-lg font-medium mb-8">
          <AppLogoIcon class="mr-2 size-8 fill-current text-white" />
          COMFACA
        </Link>
      </slot>

      <slot name="welcome" />
    </div>

    <div class="w-full lg:p-8 flex flex-col justify-center relative">
      <AuthBackgroundShapes />

      <div class="mx-auto flex w-full max-w-md flex-col justify-center space-y-6">
        <slot />
      </div>
    </div>

    <slot name="loading" />
    <slot name="dialog" />
  </div>
</template>