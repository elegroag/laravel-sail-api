<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

type Props = {
    formulario: {
        id: number
        name: string
        title: string
        description: string | null
        module: string
        endpoint: string
        method: string
        is_active: boolean
        layout_config: any
        permissions: any
        componentes?: any[]
        created_at: string
        updated_at: string
    }
}

const props = defineProps<Props>()
const deleting = ref(false)

const handleDelete = async () => {
    if (!confirm('¿Estás seguro de que deseas eliminar este formulario dinámico? Esta acción no se puede deshacer.')) {
        return
    }

    deleting.value = true

    try {
        router.delete(`/cajas/formulario-dinamico/${props.formulario.id}`, {
            onSuccess: () => {
                router.visit('/cajas/formulario-dinamico')
            },
            onError: () => {
                deleting.value = false
            }
        })
    } catch (error) {
        console.error('Error al eliminar formulario:', error)
        deleting.value = false
    }
}

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<template>
  <AppLayout :title="`Formulario: ${formulario.title}`">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">Detalles del Formulario Dinámico</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Información completa del formulario dinámico</p>
        </div>
        <div class="flex space-x-2">
          <Link :href="`/cajas/formulario-dinamico/${formulario.id}/edit`" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Editar
          </Link>
          <Link href="/cajas/formulario-dinamico" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            Volver al listado
          </Link>
        </div>
      </div>

      <div class="border-t border-gray-200">
        <dl>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Nombre</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formulario.name }}</dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Título</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formulario.title }}</dd>
          </div>
          <div v-if="formulario.description" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Descripción</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formulario.description }}</dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Módulo</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formulario.module }}</dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Endpoint</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formulario.endpoint }}</dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Método HTTP</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formulario.method }}</dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Estado</dt>
            <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
              <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', formulario.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                {{ formulario.is_active ? 'Activo' : 'Inactivo' }}
              </span>
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Fecha de creación</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formatDate(formulario.created_at) }}</dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Última actualización</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formatDate(formulario.updated_at) }}</dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">ID</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formulario.id }}</dd>
          </div>
        </dl>
      </div>

      <div v-if="formulario.componentes && formulario.componentes.length > 0" class="border-t border-gray-200">
        <div class="px-4 py-5 sm:px-6">
          <h4 class="text-lg leading-6 font-medium text-gray-900 mb-4">Componentes Asociados ({{ formulario.componentes.length }})</h4>
          <div class="space-y-3">
            <div v-for="componente in formulario.componentes" :key="componente.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
              <div>
                <div class="text-sm font-medium text-gray-900">{{ componente.label || componente.name }}</div>
                <div class="text-sm text-gray-500">{{ componente.type }} | Orden: {{ componente.order ?? 'N/A' }}</div>
              </div>
              <Link :href="`/cajas/componente-dinamico/${componente.id}`" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Ver componente</Link>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <div class="flex justify-between">
          <button
            @click="handleDelete"
            :disabled="deleting"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ deleting ? 'Eliminando...' : 'Eliminar Formulario' }}
          </button>
          <Link
            :href="`/cajas/formulario-dinamico/${formulario.id}/edit`"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Editar Formulario
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>