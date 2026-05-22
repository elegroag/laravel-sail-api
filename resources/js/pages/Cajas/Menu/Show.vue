<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

type Props = {
    menu_item: {
        id: number
        title: string
        default_url: string | null
        icon: string | null
        color: string | null
        nota: string | null
        parent_id: number | null
        codapl: string
        controller: string
        action: string
        is_visible?: number | null
        tipo?: string | null
        position?: number | null
    }
}

const props = defineProps<Props>()
const deleting = ref(false)

const handleDelete = async () => {
    if (!confirm('¿Estás seguro de que deseas eliminar este item de menú? Esta acción no se puede deshacer.')) {
        return
    }

    deleting.value = true

    try {
        router.delete(`/cajas/menu/${props.menu_item.id}`, {
            onSuccess: () => {
                router.visit('/cajas/menu')
            },
            onError: () => {
                deleting.value = false
            }
        })
    } catch (error) {
        console.error('Error al eliminar item:', error)
        deleting.value = false
    }
}
</script>

<template>
  <AppLayout :title="`Menu Item: ${menu_item.title}`">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Detalles de Menu Item
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Información completa del item de menú
          </p>
        </div>
        <div class="flex space-x-2">
          <Link
            :href="`/cajas/menu/${menu_item.id}/edit`"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Editar
          </Link>
          <Link
            href="/cajas/menu"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            Volver al listado
          </Link>
        </div>
      </div>

      <div class="border-t border-gray-200">
        <dl>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Título</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ menu_item.title }}
            </dd>
          </div>

          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Controller / Action</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ menu_item.controller }} / {{ menu_item.action }}
            </dd>
          </div>

          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">URL por defecto</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ menu_item.default_url || 'N/A' }}
            </dd>
          </div>

          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Icono / Color</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ menu_item.icon || 'N/A' }} / {{ menu_item.color || 'N/A' }}
            </dd>
          </div>

          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Padre</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ menu_item.parent_id ?? 'N/A' }}
            </dd>
          </div>

          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Aplicación</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ menu_item.codapl }}
            </dd>
          </div>

          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Visible / Tipo / Posición</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ menu_item.is_visible ?? 'N/A' }} / {{ menu_item.tipo ?? 'N/A' }} / {{ menu_item.position ?? 'N/A' }}
            </dd>
          </div>

          <div v-if="menu_item.nota" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Nota</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ menu_item.nota }}
            </dd>
          </div>

          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">ID</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ menu_item.id }}
            </dd>
          </div>

          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Permiso</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ `${menu_item.controller}.${menu_item.action}` }}
            </dd>
          </div>
        </dl>
      </div>

      <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <div class="flex justify-between">
          <button
            @click="handleDelete"
            :disabled="deleting"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ deleting ? 'Eliminando...' : 'Eliminar Item' }}
          </button>
          <Link
            :href="`/cajas/menu/${menu_item.id}/edit`"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Editar Item
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>