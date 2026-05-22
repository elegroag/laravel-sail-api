<script setup lang="ts">
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { ChevronLeft } from 'lucide-vue-next'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { useRegisterController } from '@/composables/useRegisterController'
import { userTypes } from '@/constants/auth'

const props = defineProps<{
    Coddoc?: Record<string, string>
    Tipsoc?: Record<string, string>
    Codciu?: Record<string, string>
    errors?: Record<string, string>
}>()

const {
    state,
    step,
    dialog,
    setDialog,
    documentTypeOptions,
    cityOptions,
    societyOptions,
    companyCategoryOptions,
    events,
    validateStep
} = useRegisterController(props.Coddoc || {}, props.Tipsoc || {}, props.Codciu || {}, props.errors || {})

const isCompany = computed(() => state.selectedUserType === 'empresa')
const isWorker = computed(() => state.selectedUserType === 'trabajador')
const isNatural = computed(() => state.companyCategory === 'N')
const maxSteps = computed(() => {
    if (isCompany.value) return state.userRole === 'delegado' ? 5 : 4
    if (isWorker.value) return 3
    return 2
})

function handleUserTypeSelect(id: string) {
    if (id === 'empresa') {
        router.visit(route('register.company'))
        return
    }
    if (id === 'trabajador') {
        router.visit(route('register.worker'))
        return
    }
    events.handleUserTypeSelect(id as any)
}

function handleFieldChange(field: string, value: any) {
    ;(state as any)[field] = value
}
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
          <Link :href="route('login')" class="flex items-center text-lg font-medium mb-8">
            <AppLogoIcon class="mr-2 size-50 fill-current text-white" />
          </Link>

          <h1 class="text-4xl font-bold mb-2">REGISTRO</h1>
          <div class="w-16 h-0.5 bg-white mb-6" />
          <p class="text-emerald-100 text-lg mb-6">Únete a Comfaca En Línea</p>

          <div class="text-emerald-100 text-sm leading-relaxed mb-6 space-y-3">
            <p>Cree su cuenta y acceda a COMFACA de forma segura y eficiente para la gestión de sus trámites y servicios.</p>
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

      <!-- Right Panel - Register Form -->
      <div class="flex flex-1 flex-col justify-center px-6 py-10 lg:w-1/2 lg:px-16">
        <div class="w-full max-w-md mx-auto">
          <!-- Mobile Header -->
          <div class="flex lg:hidden items-center gap-3 mb-8">
            <img src="/img/comfaca-logo.png" alt="COMFACA" class="h-10 w-auto" />
            <span class="text-lg font-bold text-gray-900">COMFACA EN LÍNEA</span>
          </div>

          <!-- User Type Selection -->
          <div v-if="!state.selectedUserType" class="flex flex-col">
            <h1 class="text-2xl font-semibold text-gray-900 mb-1">Crear cuenta</h1>
            <p class="text-sm text-gray-500 mb-6">Selecciona tu tipo de usuario</p>

            <div class="grid grid-cols-2 gap-3 mb-6">
              <button
                v-for="ut in userTypes"
                :key="ut.id"
                type="button"
                class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition-colors text-center"
                @click="handleUserTypeSelect(ut.id)"
              >
                <component :is="ut.icon" class="mb-2 w-6 h-6 text-gray-600" />
                <span class="text-xs font-medium text-gray-700">{{ ut.label }}</span>
              </button>
            </div>
          </div>

          <!-- Register Form -->
          <form v-else @submit="(e) => { e.preventDefault(); events.handleRegister(e) }" class="space-y-4">
            <div class="flex items-center justify-between mb-4">
              <button
                type="button"
                @click="events.handleBack"
                class="flex items-center text-sm text-gray-500 hover:text-emerald-600 transition-colors"
              >
                <ChevronLeft class="w-4 h-4 mr-1" />
                Volver
              </button>
              <span class="text-sm text-gray-500">Paso {{ step }} de {{ maxSteps }}</span>
            </div>

            <div v-if="step === 1 && isCompany" class="space-y-4">
              <h3 class="text-lg font-medium">Datos de la empresa</h3>

              <div>
                <label class="block text-sm font-medium text-gray-700">Nombre de la empresa *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.companyName"
                  @input="handleFieldChange('companyName', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.companyName" class="mt-1 text-sm text-red-600">{{ state.errors.companyName }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">NIT *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.companyNit"
                  @input="handleFieldChange('companyNit', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.companyNit" class="mt-1 text-sm text-red-600">{{ state.errors.companyNit }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Tipo de documento</label>
                <select
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.companyDocumentType"
                  @change="handleFieldChange('companyDocumentType', ($event.target as HTMLSelectElement).value)"
                >
                  <option value="">Seleccione...</option>
                  <option
                    v-for="(label, value) in documentTypeOptions"
                    :key="value"
                    :value="value"
                  >
                    {{ label }}
                  </option>
                </select>
                <p v-if="state.errors.companyDocumentType" class="mt-1 text-sm text-red-600">{{ state.errors.companyDocumentType }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">No. Documento *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.companyDocument"
                  @input="handleFieldChange('companyDocument', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.companyDocument" class="mt-1 text-sm text-red-600">{{ state.errors.companyDocument }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Ciudad</label>
                <select
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.companyCity"
                  @change="handleFieldChange('companyCity', ($event.target as HTMLSelectElement).value)"
                >
                  <option value="">Seleccione...</option>
                  <option
                    v-for="(label, value) in cityOptions"
                    :key="value"
                    :value="value"
                  >
                    {{ label }}
                  </option>
                </select>
                <p v-if="state.errors.companyCity" class="mt-1 text-sm text-red-600">{{ state.errors.companyCity }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Dirección</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.companyAddress"
                  @input="handleFieldChange('companyAddress', ($event.target as HTMLInputElement).value)"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.companyPhone"
                  @input="handleFieldChange('companyPhone', ($event.target as HTMLInputElement).value)"
                />
              </div>
            </div>

            <div v-if="step === 1 && isWorker" class="space-y-4">
              <h3 class="text-lg font-medium">Datos del trabajador</h3>

              <div>
                <label class="block text-sm font-medium text-gray-700">Tipo de documento *</label>
                <select
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.documentType"
                  @change="handleFieldChange('documentType', ($event.target as HTMLSelectElement).value)"
                >
                  <option value="">Seleccione...</option>
                  <option
                    v-for="(label, value) in documentTypeOptions"
                    :key="value"
                    :value="value"
                  >
                    {{ label }}
                  </option>
                </select>
                <p v-if="state.errors.documentType" class="mt-1 text-sm text-red-600">{{ state.errors.documentType }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">No. Documento *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.documentNumber"
                  @input="handleFieldChange('documentNumber', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.documentNumber" class="mt-1 text-sm text-red-600">{{ state.errors.documentNumber }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Primer nombre *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.firstName"
                  @input="handleFieldChange('firstName', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.firstName" class="mt-1 text-sm text-red-600">{{ state.errors.firstName }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Segundo nombre</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.secondName"
                  @input="handleFieldChange('secondName', ($event.target as HTMLInputElement).value)"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Primer apellido *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.lastName"
                  @input="handleFieldChange('lastName', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.lastName" class="mt-1 text-sm text-red-600">{{ state.errors.lastName }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Segundo apellido</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.secondLastName"
                  @input="handleFieldChange('secondLastName', ($event.target as HTMLInputElement).value)"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Ciudad</label>
                <select
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.city"
                  @change="handleFieldChange('city', ($event.target as HTMLSelectElement).value)"
                >
                  <option value="">Seleccione...</option>
                  <option
                    v-for="(label, value) in cityOptions"
                    :key="value"
                    :value="value"
                  >
                    {{ label }}
                  </option>
                </select>
                <p v-if="state.errors.city" class="mt-1 text-sm text-red-600">{{ state.errors.city }}</p>
              </div>
            </div>

            <div v-if="step === 1 && !isCompany && !isWorker" class="space-y-4">
              <h3 class="text-lg font-medium">Datos personales</h3>

              <div>
                <label class="block text-sm font-medium text-gray-700">Tipo de documento *</label>
                <select
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.documentType"
                  @change="handleFieldChange('documentType', ($event.target as HTMLSelectElement).value)"
                >
                  <option value="">Seleccione...</option>
                  <option
                    v-for="(label, value) in documentTypeOptions"
                    :key="value"
                    :value="value"
                  >
                    {{ label }}
                  </option>
                </select>
                <p v-if="state.errors.documentType" class="mt-1 text-sm text-red-600">{{ state.errors.documentType }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">No. Documento *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.documentNumber"
                  @input="handleFieldChange('documentNumber', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.documentNumber" class="mt-1 text-sm text-red-600">{{ state.errors.documentNumber }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Primer nombre *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.firstName"
                  @input="handleFieldChange('firstName', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.firstName" class="mt-1 text-sm text-red-600">{{ state.errors.firstName }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Segundo nombre</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.secondName"
                  @input="handleFieldChange('secondName', ($event.target as HTMLInputElement).value)"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Primer apellido *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.lastName"
                  @input="handleFieldChange('lastName', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.lastName" class="mt-1 text-sm text-red-600">{{ state.errors.lastName }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Segundo apellido</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.secondLastName"
                  @input="handleFieldChange('secondLastName', ($event.target as HTMLInputElement).value)"
                />
              </div>
            </div>

            <div v-if="step === 2 && isCompany" class="space-y-4">
              <h3 class="text-lg font-medium">Categoría de la empresa</h3>

              <div>
                <label class="block text-sm font-medium text-gray-700">Categoría</label>
                <select
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.companyCategory"
                  @change="handleFieldChange('companyCategory', ($event.target as HTMLSelectElement).value)"
                >
                  <option value="">Seleccione...</option>
                  <option
                    v-for="(label, value) in companyCategoryOptions"
                    :key="value"
                    :value="value"
                  >
                    {{ label }}
                  </option>
                </select>
                <p v-if="state.errors.companyCategory" class="mt-1 text-sm text-red-600">{{ state.errors.companyCategory }}</p>
              </div>

              <div v-if="!isNatural">
                <label class="block text-sm font-medium text-gray-700">Tipo de empresa</label>
                <select
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.companyType"
                  @change="handleFieldChange('companyType', ($event.target as HTMLSelectElement).value)"
                >
                  <option value="">Seleccione...</option>
                  <option
                    v-for="(label, value) in societyOptions"
                    :key="value"
                    :value="value"
                  >
                    {{ label }}
                  </option>
                </select>
              </div>

              <div v-if="!isNatural">
                <label class="block text-sm font-medium text-gray-700">NIT de la empresa principal (si aplica)</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.mainCompanyNit"
                  @input="handleFieldChange('mainCompanyNit', ($event.target as HTMLInputElement).value)"
                />
              </div>
            </div>

            <div v-if="step === 2 && isWorker" class="space-y-4">
              <h3 class="text-lg font-medium">Datos del afiliado</h3>

              <div>
                <label class="block text-sm font-medium text-gray-700">Empresa donde labora *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.companyName"
                  @input="handleFieldChange('companyName', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.companyName" class="mt-1 text-sm text-red-600">{{ state.errors.companyName }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">NIT de la empresa *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.companyNit"
                  @input="handleFieldChange('companyNit', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.companyNit" class="mt-1 text-sm text-red-600">{{ state.errors.companyNit }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Ciudad</label>
                <select
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.city"
                  @change="handleFieldChange('city', ($event.target as HTMLSelectElement).value)"
                >
                  <option value="">Seleccione...</option>
                  <option
                    v-for="(label, value) in cityOptions"
                    :key="value"
                    :value="value"
                  >
                    {{ label }}
                  </option>
                </select>
                <p v-if="state.errors.city" class="mt-1 text-sm text-red-600">{{ state.errors.city }}</p>
              </div>
            </div>

            <div v-if="step === 2 && !isCompany && !isWorker" class="space-y-4">
              <h3 class="text-lg font-medium">Información adicional</h3>

              <div>
                <label class="block text-sm font-medium text-gray-700">Ciudad</label>
                <select
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.city"
                  @change="handleFieldChange('city', ($event.target as HTMLSelectElement).value)"
                >
                  <option value="">Seleccione...</option>
                  <option
                    v-for="(label, value) in cityOptions"
                    :key="value"
                    :value="value"
                  >
                    {{ label }}
                  </option>
                </select>
                <p v-if="state.errors.city" class="mt-1 text-sm text-red-600">{{ state.errors.city }}</p>
              </div>
            </div>

            <div v-if="step === 3 && isWorker" class="space-y-4">
              <h3 class="text-lg font-medium">Datos del beneficiario</h3>

              <div>
                <label class="block text-sm font-medium text-gray-700">¿Es usted mismo el beneficiario? *</label>
                <select
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.isSelfBeneficiary"
                  @change="handleFieldChange('isSelfBeneficiary', ($event.target as HTMLSelectElement).value)"
                >
                  <option value="si">Sí</option>
                  <option value="no">No</option>
                </select>
              </div>
            </div>

            <div v-if="(step === 3 && !isCompany && !isWorker) || (step === 4 && isCompany) || (step === 3 && isWorker)" class="space-y-4">
              <h3 class="text-lg font-medium">Datos de contacto</h3>

              <div>
                <label class="block text-sm font-medium text-gray-700">Correo electrónico *</label>
                <input
                  type="email"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.email"
                  @input="handleFieldChange('email', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.email" class="mt-1 text-sm text-red-600">{{ state.errors.email }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Confirmar correo electrónico *</label>
                <input
                  type="email"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.confirmEmail"
                  @input="handleFieldChange('confirmEmail', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.confirmEmail" class="mt-1 text-sm text-red-600">{{ state.errors.confirmEmail }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Celular</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.cellphone"
                  @input="handleFieldChange('cellphone', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.cellphone" class="mt-1 text-sm text-red-600">{{ state.errors.cellphone }}</p>
              </div>
            </div>

            <div v-if="step === 4 && isCompany && state.userRole !== 'delegado'" class="space-y-4">
              <h3 class="text-lg font-medium">Datos del usuario</h3>

              <div>
                <label class="block text-sm font-medium text-gray-700">Nombres completos *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.firstName"
                  @input="handleFieldChange('firstName', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.firstName" class="mt-1 text-sm text-red-600">{{ state.errors.firstName }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Apellidos completos *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.lastName"
                  @input="handleFieldChange('lastName', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.lastName" class="mt-1 text-sm text-red-600">{{ state.errors.lastName }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Tipo de documento *</label>
                <select
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.documentType"
                  @change="handleFieldChange('documentType', ($event.target as HTMLSelectElement).value)"
                >
                  <option value="">Seleccione...</option>
                  <option
                    v-for="(label, value) in documentTypeOptions"
                    :key="value"
                    :value="value"
                  >
                    {{ label }}
                  </option>
                </select>
                <p v-if="state.errors.documentType" class="mt-1 text-sm text-red-600">{{ state.errors.documentType }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">No. Documento *</label>
                <input
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.documentNumber"
                  @input="handleFieldChange('documentNumber', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.documentNumber" class="mt-1 text-sm text-red-600">{{ state.errors.documentNumber }}</p>
              </div>
            </div>

            <div v-if="(step === 5 && isCompany && state.userRole === 'delegado') || (step === 4 && !isCompany && !isWorker) || (step === 4 && isWorker)" class="space-y-4">
              <h3 class="text-lg font-medium">Configuración de clave</h3>

              <div>
                <label class="block text-sm font-medium text-gray-700">Clave *</label>
                <input
                  type="password"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.password"
                  @input="handleFieldChange('password', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.password" class="mt-1 text-sm text-red-600">{{ state.errors.password }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Confirmar clave *</label>
                <input
                  type="password"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2"
                  :value="state.confirmPassword"
                  @input="handleFieldChange('confirmPassword', ($event.target as HTMLInputElement).value)"
                />
                <p v-if="state.errors.confirmPassword" class="mt-1 text-sm text-red-600">{{ state.errors.confirmPassword }}</p>
              </div>
            </div>

            <div class="flex justify-between pt-4">
              <button
                v-if="step > 1"
                type="button"
                @click="events.handlePrevStep"
                class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50"
              >
                Anterior
              </button>
              <button
                type="button"
                @click="events.handleNextStep"
                class="px-4 py-2 text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 ml-auto"
              >
                {{ step === maxSteps ? 'Registrarse' : 'Siguiente' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Dialog -->
      <div v-if="dialog" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="setDialog(null)" />
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
          <div class="mb-4">
            <h3 :class="['text-lg font-medium', dialog.type === 'success' ? 'text-emerald-600' : 'text-red-600']">
              {{ dialog.type === 'success' ? 'Registro Exitoso' : 'Error en el Registro' }}
            </h3>
          </div>
          <div class="mb-4">
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ dialog.message }}</p>
          </div>
          <div class="flex justify-end gap-2">
            <button
              @click="setDialog(null)"
              class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50"
            >
              Cerrar
            </button>
            <button
              v-if="dialog.showLoginButton"
              @click="router.visit(route('login'))"
              class="px-4 py-2 text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700"
            >
              Ir al Login
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>