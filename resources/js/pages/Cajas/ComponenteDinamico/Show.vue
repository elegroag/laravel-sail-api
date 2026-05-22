<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

type DataSourceItem = { value: string; label: string }
type FormularioType = { id: number; name: string; title: string }

type Componente = {
  id: number
  name: string
  label: string
  type: string
  form_type?: string
  placeholder?: string
  default_value?: string
  help_text?: string
  is_required?: boolean
  is_disabled?: boolean
  is_readonly?: boolean
  group_id: number
  order: number
  css_classes?: string
  target?: number
  search_type?: string
  search_endpoint?: string
  date_max?: string
  number_min?: string | number
  number_max?: string | number
  number_step?: number
  data_source?: DataSourceItem[] | null
  event_config?: Record<string, unknown>
  formulario?: Pick<FormularioType, 'id' | 'name' | 'title'> | null
  created_at: string
  updated_at: string
}

type Props = {
  componente: Componente
}

const props = defineProps<Props>()

const deleting = ref(false)
const confirmOpen = ref(false)

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const handleDelete = () => {
  deleting.value = true
  router.delete(`/cajas/componente-dinamico/${props.componente.id}`, {
    onSuccess: () => router.visit('/cajas/componente-dinamico'),
    onError: () => {
      deleting.value = false
      confirmOpen.value = false
    }
  })
}
</script>

<template>
  <AppLayout :title="`Componente: ${componente.label}`">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ componente.label }}</h1>
            <p class="mt-1 text-sm text-gray-500">
              Componente dinámico{{ componente.formulario ? ` del formulario "${componente.formulario.title}"` : ' sin asignar' }}
            </p>
          </div>
          <div class="flex space-x-3">
            <Link
              href="/cajas/componente-dinamico"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Volver
            </Link>
            <Link
              :href="`/cajas/componente-dinamico/${componente.id}/edit`"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Editar
            </Link>
            <button
              @click="confirmOpen = true"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
            >
              Eliminar
            </button>
          </div>
        </div>
      </div>

      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Información del Componente</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Detalles completos del componente dinámico.</p>
        </div>
        <div class="border-t border-gray-200">
          <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Nombre</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ componente.name }}</dd>
            </div>

            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Etiqueta</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ componente.label }}</dd>
            </div>

            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Tipo</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ componente.type }}
                </span>
              </dd>
            </div>

            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Tipo de Formulario</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  {{ componente.form_type }}
                </span>
              </dd>
            </div>

            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Grupo</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ componente.group_id }}</dd>
            </div>

            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Orden</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ componente.order }}</dd>
            </div>

            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Estado</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <div class="flex space-x-2">
                  <span v-if="componente.is_disabled" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    Deshabilitado
                  </span>
                  <span v-if="componente.is_readonly" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Solo Lectura
                  </span>
                  <span v-if="!componente.is_disabled && !componente.is_readonly" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Activo
                  </span>
                </div>
              </dd>
            </div>

            <div v-if="componente.default_value" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Valor por Defecto</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ componente.default_value }}</dd>
            </div>

            <div v-if="componente.placeholder" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Placeholder</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ componente.placeholder }}</dd>
            </div>

            <div v-if="componente.help_text" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Texto de Ayuda</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ componente.help_text }}</dd>
            </div>

            <div v-if="componente.data_source && componente.data_source.length > 0" class="bg-gray-50 px-4 py-5 sm:px-6">
              <dt class="text-sm font-medium text-gray-500 mb-2">Fuente de Datos</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0">
                <div class="max-h-40 overflow-y-auto">
                  <pre class="text-xs bg-gray-100 p-2 rounded">{{ JSON.stringify(componente.data_source, null, 2) }}</pre>
                </div>
              </dd>
            </div>

            <div v-if="componente.event_config && Object.keys(componente.event_config).length > 0" class="bg-white px-4 py-5 sm:px-6">
              <dt class="text-sm font-medium text-gray-500 mb-2">Configuración de Eventos</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0">
                <div class="max-h-40 overflow-y-auto">
                  <pre class="text-xs bg-gray-100 p-2 rounded">{{ JSON.stringify(componente.event_config, null, 2) }}</pre>
                </div>
              </dd>
            </div>

            <div v-if="componente.formulario" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Formulario</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <Link
                  :href="`/cajas/formulario-dinamico/${componente.formulario.id}`"
                  class="text-blue-600 hover:text-blue-800"
                >
                  {{ componente.formulario.title }}
                </Link>
              </dd>
            </div>

            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Fechas</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <div>Creado: {{ formatDate(componente.created_at) }}</div>
                <div>Actualizado: {{ formatDate(componente.updated_at) }}</div>
              </dd>
            </div>

            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">ID</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ componente.id }}</dd>
            </div>
          </dl>
        </div>
      </div>

      <div v-if="confirmOpen" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="confirmOpen = false" />
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
          <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900">Confirmar Eliminación</h3>
            <p class="mt-2 text-sm text-gray-500">
              Esta acción eliminará definitivamente el componente "{{ componente.label }}". No podrás deshacerla.
            </p>
          </div>
          <div class="flex justify-end gap-3">
            <button
              @click="confirmOpen = false"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              :disabled="deleting"
            >
              Cancelar
            </button>
            <button
              @click="handleDelete"
              :disabled="deleting"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 disabled:opacity-50"
            >
              {{ deleting ? 'Eliminando...' : 'Eliminar' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>