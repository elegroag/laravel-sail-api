<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import AuthBackgroundShapes from '@/components/ui/auth-background-shapes'

type Props = {
  msj?: string
}

const props = defineProps<Props>()

const message = props.msj || 'El sistema se encuentra en estado de actualización y mantenimiento.'
</script>

<template>
  <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
    <div class="relative hidden h-full flex-col bg-muted p-10 text-white lg:flex dark:border-r">
      <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700" />

      <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full -translate-y-16 translate-x-16 opacity-60" />
      <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-emerald-800 to-emerald-600 rounded-full translate-y-12 -translate-x-12 opacity-40" />
      <div class="absolute top-1/2 left-0 w-16 h-16 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full -translate-x-8 opacity-30" />

      <Link href="/login" class="relative z-20 flex items-center text-lg font-medium mb-8">
        <AppLogoIcon class="mr-2 size-8 fill-current text-white" />
        COMFACA
      </Link>

      <div class="relative z-10 flex-1 flex flex-col justify-center">
        <h1 class="text-4xl font-bold mb-2">FUERA DE SERVICIO</h1>
        <div class="w-16 h-0.5 bg-white mb-6" />
        <p class="text-emerald-100 text-lg mb-6">Comfaca En Línea</p>

        <div class="text-emerald-100 text-sm leading-relaxed mb-6 space-y-3">
          <p>En este momento la plataforma Comfaca En Línea se encuentra en proceso de actualización y mantenimiento para brindarte una experiencia más segura y confiable.</p>
          <p>Durante este periodo no podrás realizar trámites ni consultar información a través del portal. Te invitamos a intentarlo nuevamente más tarde.</p>
          <p>Agradecemos tu comprensión y estaremos restableciendo el servicio lo antes posible.</p>
        </div>

        <Link
          href="/login"
          class="inline-flex items-center text-emerald-200 hover:text-white transition-colors text-sm"
        >
          <span class="mr-1">←</span>
          Volver al inicio de sesión
        </Link>
      </div>
    </div>

    <div class="w-full lg:p-8 flex flex-col justify-center relative">
      <AuthBackgroundShapes />

      <div class="mx-auto flex w-full max-w-md flex-col justify-center space-y-6">
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-6">
          <div class="font-semibold text-amber-800 mb-2">
            Plataforma temporalmente fuera de servicio
          </div>
          <div class="mt-2 text-amber-900 text-sm leading-relaxed">
            <span v-html="message" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>