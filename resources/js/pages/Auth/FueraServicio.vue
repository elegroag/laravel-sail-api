<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { ChevronLeft } from 'lucide-vue-next'
import AppLogoIcon from '@/components/AppLogoIcon.vue'

type Props = {
  msj?: string
}

const props = defineProps<Props>()

const message = props.msj || 'El sistema se encuentra en estado de actualización y mantenimiento.'
</script>

<template>
  <div class="min-h-dvh w-full bg-white">
    <div class="flex min-h-dvh flex-col lg:flex-row lg:relative">
      <!-- Left Panel - Welcome Section (Desktop only) -->
      <div class="hidden lg:block lg:w-1/2 lg:min-h-dvh lg:relative lg:overflow-hidden">
        <!-- Absolute background layer -->
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700" />

        <!-- Absolute decorative shapes -->
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full opacity-60 -translate-y-1/2 translate-x-1/4 pointer-events-none" />
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-emerald-800 to-emerald-600 rounded-full opacity-40 -translate-y-1/3 -translate-x-1/4 pointer-events-none" />
        <div class="absolute top-1/2 left-0 w-20 h-20 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full opacity-30 -translate-y-1/2 -translate-x-1/2 pointer-events-none" />

        <!-- Content layer -->
        <div class="relative z-10 flex flex-col justify-center h-full p-10 text-white">
          <Link href="/" class="flex items-center text-lg font-medium mb-8">
            <AppLogoIcon class="mr-2 size-8 fill-current text-white" />
            COMFACA
          </Link>

          <h1 class="text-4xl font-bold mb-2">FUERA DE SERVICIO</h1>
          <div class="w-16 h-0.5 bg-white mb-6" />
          <p class="text-emerald-100 text-lg mb-6">Comfaca En Línea</p>

          <div class="text-emerald-100 text-sm leading-relaxed mb-6 space-y-3">
            <p>En este momento la plataforma Comfaca En Línea se encuentra en proceso de actualización y mantenimiento para brindarte una experiencia más segura y confiable.</p>
            <p>Durante este periodo no podrás realizar trámites ni consultar información a través del portal. Te invitamos a intentarlo nuevamente más tarde.</p>
            <p>Agradecemos tu comprensión y estaremos restableciendo el servicio lo antes posible.</p>
          </div>

          <Link
            :href="route('login')"
            class="inline-flex items-center text-emerald-200 hover:text-white transition-colors text-sm mb-4"
          >
            <ChevronLeft class="w-4 h-4 mr-1" />
            Volver al inicio de sesión
          </Link>
        </div>
      </div>

      <!-- Right Panel - Message -->
      <div class="flex flex-1 flex-col justify-center px-6 py-10 lg:w-1/2 lg:px-16">
        <div class="w-full max-w-md mx-auto">
          <!-- Mobile Header -->
          <div class="flex lg:hidden items-center gap-3 mb-8">
            <img src="/img/comfaca-logo.png" alt="COMFACA" class="h-10 w-auto" />
            <span class="text-lg font-bold text-gray-900">COMFACA EN LÍNEA</span>
          </div>

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
  </div>
</template>