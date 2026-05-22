<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const formData = ref({
    name: '',
    title: '',
    description: '',
    module: '',
    endpoint: '',
    method: 'POST',
    is_active: true,
    layout_config: {
        columns: 1,
        spacing: 'md',
        theme: 'default'
    },
    permissions: {
        public: false,
        roles: [] as string[]
    }
})

const errors = ref<Record<string, string>>({})
const processing = ref(false)

const handleChange = (e: Event) => {
    const target = e.target as HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement
    const { name, value, type } = target
    const checked = (target as HTMLInputElement).checked

    if (name.startsWith('permissions.') || name.startsWith('layout_config.')) {
        const [parent, child] = name.split('.')
        if (parent === 'permissions') {
            (formData.value.permissions as any)[child] = type === 'checkbox' ? checked : value
        } else if (parent === 'layout_config') {
            (formData.value.layout_config as any)[child] = value
        }
    } else {
        (formData.value as any)[name] = type === 'checkbox' ? checked : value
    }

    if (errors.value[name]) {
        errors.value[name] = ''
    }
}

const handleSubmit = async (event: Event) => {
    event.preventDefault()
    processing.value = true
    router.post(
        '/cajas/formulario-dinamico',
        {
            ...formData.value,
            layout_config: JSON.stringify(formData.value.layout_config),
            permissions: JSON.stringify(formData.value.permissions),
        },
        {
            onError: (errs: Record<string, string>) => {
                errors.value = errs
            },
            onFinish: () => {
                processing.value = false
            },
        }
    )
}
</script>

<template>
  <AppLayout title="Crear Formulario Dinámico">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Crear Formulario Dinámico
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Formulario para crear un nuevo formulario dinámico
          </p>
        </div>
        <Link
          href="/cajas/formulario-dinamico"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          Volver
        </Link>
      </div>
      <div class="px-4 py-5 sm:px-6">
        <form @submit="handleSubmit">
          <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6 sm:col-span-3">
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre único *</label>
              <input
                type="text"
                name="name"
                id="name"
                required
                :class="['mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2', errors.name ? 'border-red-300' : '']"
                v-model="formData.name"
                @input="handleChange"
              />
              <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
              <p class="mt-1 text-xs text-gray-500">Identificador único para el formulario</p>
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título *</label>
              <input
                type="text"
                name="title"
                id="title"
                required
                :class="['mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2', errors.title ? 'border-red-300' : '']"
                v-model="formData.title"
                @input="handleChange"
              />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
            </div>

            <div class="col-span-6">
              <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
              <textarea
                name="description"
                id="description"
                rows="3"
                :class="['mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2', errors.description ? 'border-red-300' : '']"
                v-model="formData.description"
                @input="handleChange"
              />
              <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
            </div>

            <div class="col-span-6 sm:col-span-2">
              <label for="module" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Módulo *</label>
              <input
                type="text"
                name="module"
                id="module"
                required
                :class="['mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2', errors.module ? 'border-red-300' : '']"
                v-model="formData.module"
                @input="handleChange"
              />
              <p v-if="errors.module" class="mt-1 text-sm text-red-600">{{ errors.module }}</p>
            </div>

            <div class="col-span-6 sm:col-span-2">
              <label for="endpoint" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Endpoint *</label>
              <input
                type="text"
                name="endpoint"
                id="endpoint"
                required
                :class="['mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2', errors.endpoint ? 'border-red-300' : '']"
                v-model="formData.endpoint"
                @input="handleChange"
              />
              <p v-if="errors.endpoint" class="mt-1 text-sm text-red-600">{{ errors.endpoint }}</p>
            </div>

            <div class="col-span-6 sm:col-span-2">
              <label for="method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Método HTTP *</label>
              <select
                name="method"
                id="method"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
                v-model="formData.method"
                @change="handleChange"
              >
                <option value="GET">GET</option>
                <option value="POST">POST</option>
                <option value="PUT">PUT</option>
                <option value="PATCH">PATCH</option>
                <option value="DELETE">DELETE</option>
              </select>
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
              <div class="mt-1">
                <label class="inline-flex items-center">
                  <input
                    type="checkbox"
                    name="is_active"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    v-model="formData.is_active"
                    @change="handleChange"
                  />
                  <span class="ml-2 text-sm text-gray-600">Formulario activo</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex justify-end pt-6">
            <button
              type="submit"
              :disabled="processing"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ processing ? 'Guardando...' : 'Guardar Formulario' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>