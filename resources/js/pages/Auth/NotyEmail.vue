<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { ChevronLeft } from 'lucide-vue-next'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { useNotyEmailController } from '@/composables/useNotyEmailController'

const props = defineProps<{
  errors?: { message?: string }
}>()

const {
  alertMessage,
  successMessage,
  processing,
  tipoAfiliado,
  setTipoAfiliado,
  documentType,
  setDocumentType,
  documento,
  setDocumento,
  nombre,
  setNombre,
  telefono,
  setTelefono,
  email,
  setEmail,
  novedad,
  setNovedad,
  documentTypeOptions,
  tipoAfiliadoOptions,
  handleSubmit,
  handleNewRequest
} = useNotyEmailController(props.errors)
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
          <Link :href="route('register')" class="flex items-center text-lg font-medium mb-8">
            <AppLogoIcon class="mr-2 size-50 fill-current text-white" />
          </Link>

          <h1 class="text-4xl font-bold mb-2">CAMBIO DE CORREO</h1>
          <div class="w-16 h-0.5 bg-white mb-6" />
          <p class="text-emerald-100 text-lg mb-6">Comfaca En Línea</p>

          <div class="text-emerald-100 text-sm leading-relaxed mb-6 space-y-3">
            <p>Diligencia el siguiente formulario para solicitar el cambio del correo electrónico registrado en tu cuenta. Esta solicitud será revisada por nuestro equipo antes de aplicar el cambio.</p>
            <p>Por seguridad, es importante que la información ingresada sea veraz y esté actualizada.</p>
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

      <!-- Right Panel - Form -->
      <div class="flex flex-1 flex-col justify-center px-6 py-10 lg:w-1/2 lg:px-16">
        <div class="w-full max-w-md mx-auto">
          <!-- Mobile Header -->
          <div class="flex lg:hidden items-center gap-3 mb-8">
            <img src="/img/comfaca-logo.png" alt="COMFACA" class="h-10 w-auto" />
            <span class="text-lg font-bold text-gray-900">COMFACA EN LÍNEA</span>
          </div>

          <form @submit="handleSubmit" class="space-y-4" :class="{ 'hidden': successMessage }">
            <div>
              <label class="block text-sm font-medium text-gray-700">Tipo de afiliado</label>
              <select
                class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                :value="tipoAfiliado"
                @change="setTipoAfiliado(($event.target as HTMLSelectElement).value as any)"
                required
              >
                <option value="">Seleccione una opción</option>
                <option v-for="opt in tipoAfiliadoOptions" :key="opt.value" :value="opt.value">
                  {{ opt.label }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Tipo de documento</label>
              <select
                class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                :value="documentType"
                @change="setDocumentType(($event.target as HTMLSelectElement).value)"
                required
              >
                <option value="">Seleccione una opción</option>
                <option v-for="opt in documentTypeOptions" :key="opt.value" :value="opt.value">
                  {{ opt.label }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Documento</label>
              <input
                type="number"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                :value="documento"
                @input="setDocumento(($event.target as HTMLInputElement).value)"
                placeholder="Número de documento"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
              <input
                type="text"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                :value="nombre"
                @input="setNombre(($event.target as HTMLInputElement).value)"
                placeholder="Nombre y apellidos"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Celular de contacto</label>
              <input
                type="number"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                :value="telefono"
                @input="setTelefono(($event.target as HTMLInputElement).value)"
                placeholder="Número de celular"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Correo electrónico actual / real</label>
              <input
                type="email"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                :value="email"
                @input="setEmail(($event.target as HTMLInputElement).value)"
                placeholder="correo@ejemplo.com"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Novedad a reportar</label>
              <textarea
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                rows="4"
                :value="novedad"
                @input="setNovedad(($event.target as HTMLTextAreaElement).value)"
                placeholder="Describe la novedad o motivo del cambio de correo"
                required
              />
            </div>

            <div class="pt-2">
              <button
                type="submit"
                :disabled="processing"
                class="inline-flex w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70"
              >
                {{ processing ? 'Enviando solicitud...' : 'Solicitar cambio de correo' }}
              </button>
            </div>
          </form>

          <div v-if="successMessage" class="rounded-lg border border-emerald-200 bg-emerald-50 p-6">
            <div class="font-semibold text-emerald-800 mb-2">Solicitud registrada</div>
            <div class="text-gray-700 space-y-2">
              <p>{{ successMessage }}</p>
              <p class="text-sm text-gray-600">
                Hemos recibido tu solicitud con la siguiente información:<br />
                Documento: <span class="font-medium">{{ documento }}</span><br />
                Correo reportado: <span class="font-medium">{{ email }}</span>
              </p>
              <button
                type="button"
                @click="handleNewRequest"
                class="mt-2 inline-flex items-center justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700"
              >
                Registrar otra solicitud
              </button>
            </div>
          </div>

          <div v-if="alertMessage" class="rounded-lg border border-red-200 bg-red-50 p-4">
            <div class="text-sm text-red-800">{{ alertMessage }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>