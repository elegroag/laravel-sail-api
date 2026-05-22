<script setup lang="ts">
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { ChevronLeft } from 'lucide-vue-next'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { Input } from '@/components/ui/input'
import { SelectRadix } from '@/components/ui/select'
import { useRecoveryController } from '@/composables/useRecoveryController'
import { userTypes } from '@/constants/auth'

const props = defineProps<{
    Coddoc: Record<string, string>
}>()

const {
    formState,
    selectedUserType,
    documentTypeOptions,
    handleUserTypeSelect,
    handleBack,
    handleFieldChange,
    handleSubmit
} = useRecoveryController(props.Coddoc)

const showDeliveryFields = computed(() => formState.delivery_method === 'email' || formState.delivery_method === 'whatsapp')
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
        <div class="relative z-10 flex flex-col justify-center h-full p-5 text-white max-w-md mx-auto items-center">
          <Link href="/" class="flex items-center justify-center text-lg font-medium mb-2">
            <AppLogoIcon class="size-100 fill-current text-white" />
          </Link>

          <h1 class="text-4xl font-bold mb-2">RECUPERAR</h1>
          <div class="w-16 h-0.5 bg-white mb-6" />
          <p class="text-emerald-100 text-lg mb-6">Comfaca En Línea</p>

          <div class="text-emerald-100 text-sm leading-relaxed mb-6 space-y-3">
            <p>Ingresa tu información para recibir las instrucciones de recuperación de clave en tu correo electrónico.</p>
          </div>

          <Link
            :href="route('login')"
            class="inline-flex items-center text-emerald-200 hover:text-white transition-colors text-sm mb-4"
          >
            <ChevronLeft class="w-4 h-4 mr-1" />
            ¿Ya tienes cuenta? Inicia sesión
          </Link>
        </div>
      </div>

      <!-- Right Panel - Recovery Form -->
      <div class="flex flex-1 flex-col justify-center px-6 py-10 lg:w-1/2 lg:px-16">
        <div class="w-full max-w-md mx-auto">
          <!-- Mobile Header -->
          <div class="flex lg:hidden items-center gap-3 mb-8">
            <img src="/img/comfaca-logo.png" alt="COMFACA" class="h-10 w-auto" />
            <span class="text-lg font-bold text-gray-900">COMFACA EN LÍNEA</span>
          </div>

          <!-- User Type Selection -->
          <div v-if="!selectedUserType" class="flex flex-col">
            <h1 class="text-2xl font-semibold text-gray-900 mb-1">Recuperar clave</h1>
            <p class="text-sm text-gray-500 mb-6">Selecciona tu tipo de usuario</p>

            <div class="grid grid-cols-2 gap-3 mb-6">
              <button
                v-for="ut in userTypes"
                :key="ut.id"
                type="button"
                class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition-colors text-center"
                @click="handleUserTypeSelect(ut.id as any)"
              >
                <component :is="ut.icon" class="mb-2 w-6 h-6 text-gray-600" />
                <span class="text-xs font-medium text-gray-700">{{ ut.label }}</span>
              </button>
            </div>
          </div>

          <!-- Recovery Form -->
          <form v-else @submit="handleSubmit" class="space-y-4">
            <button
              type="button"
              @click="handleBack"
              class="flex items-center text-sm text-gray-500 hover:text-emerald-600 transition-colors mb-4"
            >
              <ChevronLeft class="w-4 h-4 mr-1" />
              Volver
            </button>

            <div>
              <label class="block text-sm font-medium text-gray-700">Tipo de documento</label>
              <SelectRadix
                :modelValue="formState.documentType"
                @update:modelValue="(v) => handleFieldChange('documentType', String(v))"
                :options="documentTypeOptions"
                placeholder="Seleccione"
                class="mt-1 w-full"
                required
              />
              <p v-if="formState.errors.documentType" class="mt-1 text-sm text-red-600">{{ formState.errors.documentType }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Número de identificación</label>
              <Input
                :modelValue="formState.identification"
                @update:modelValue="(v) => handleFieldChange('identification', String(v))"
                class="mt-1 w-full"
                placeholder="Ingresa tu número de identificación"
                required
              />
              <p v-if="formState.errors.identification" class="mt-1 text-sm text-red-600">{{ formState.errors.identification }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Método de entrega</label>
              <SelectRadix
                :modelValue="formState.delivery_method"
                @update:modelValue="(v) => handleFieldChange('delivery_method', String(v))"
                :options="[{ value: 'email', label: 'Correo electrónico' }, { value: 'whatsapp', label: 'WhatsApp' }]"
                placeholder="Seleccione"
                class="mt-1 w-full"
              />
            </div>

            <div v-if="formState.delivery_method === 'email'">
              <label class="block text-sm font-medium text-gray-700">Correo electrónico</label>
              <Input
                :modelValue="formState.email"
                @update:modelValue="(v) => handleFieldChange('email', String(v))"
                class="mt-1 w-full"
                placeholder="correo@ejemplo.com"
                required
              />
              <p v-if="formState.errors.email" class="mt-1 text-sm text-red-600">{{ formState.errors.email }}</p>
            </div>

            <div v-if="formState.delivery_method === 'whatsapp'">
              <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
              <Input
                :modelValue="formState.whatsapp"
                @update:modelValue="(v) => handleFieldChange('whatsapp', String(v))"
                class="mt-1 w-full"
                placeholder="+57 300 123 4567"
                required
              />
              <p v-if="formState.errors.whatsapp" class="mt-1 text-sm text-red-600">{{ formState.errors.whatsapp }}</p>
            </div>

            <div class="pt-2">
              <button
                type="submit"
                :disabled="formState.isSubmitting"
                class="w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50"
              >
                {{ formState.isSubmitting ? 'Enviando...' : 'Recuperar contraseña' }}
              </button>
            </div>

            <div class="text-center">
              <Link :href="route('login')" class="text-sm text-gray-500 hover:text-emerald-600 flex items-center justify-center">
                <ChevronLeft class="w-4 h-4 mr-1" />
                Volver al inicio de sesión
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>