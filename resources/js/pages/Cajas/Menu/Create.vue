<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const formData = ref({
    title: '',
    default_url: '',
    icon: '',
    color: '',
    nota: '',
    parent_id: '',
    codapl: 'CA',
    controller: '',
    action: ''
})

const errors = ref<Record<string, string>>({})
const processing = ref(false)

const handleChange = (e: Event) => {
    const target = e.target as HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement
    const { name, value } = target
    ;(formData.value as Record<string, string>)[name] = value
    if (errors.value[name]) {
        errors.value[name] = ''
    }
}

const handleSubmit = async (event: Event) => {
    event.preventDefault()
    processing.value = true

    try {
        const response = await fetch('/cajas/menu', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                ...formData.value,
                parent_id: formData.value.parent_id ? Number(formData.value.parent_id) : null,
            })
        })

        const data = await response.json()

        if (response.ok) {
            router.visit('/cajas/menu')
        } else {
            if (data.errors) {
                errors.value = data.errors
            } else {
                console.error('Error desconocido:', data)
            }
        }
    } catch (error) {
        console.error('Error al crear menu:', error)
    } finally {
        processing.value = false
    }
}
</script>

<template>
  <AppLayout title="Crear Menu Item">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Crear Menu Item
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Formulario para crear un nuevo item de menú
          </p>
        </div>
        <Link
          href="/cajas/menu"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          Volver
        </Link>
      </div>
      <div class="px-4 py-5 sm:px-6">
        <form @submit="handleSubmit">
          <div class="grid grid-cols-6 gap-6">
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

            <div class="col-span-6 sm:col-span-3">
              <label for="codapl" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aplicación *</label>
              <select
                name="codapl"
                id="codapl"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
                v-model="formData.codapl"
                @change="handleChange"
              >
                <option value="CA">CA</option>
              </select>
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="controller" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Controller *</label>
              <input
                type="text"
                name="controller"
                id="controller"
                required
                :class="['mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2', errors.controller ? 'border-red-300' : '']"
                v-model="formData.controller"
                @input="handleChange"
              />
              <p v-if="errors.controller" class="mt-1 text-sm text-red-600">{{ errors.controller }}</p>
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="action" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Action *</label>
              <input
                type="text"
                name="action"
                id="action"
                required
                :class="['mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2', errors.action ? 'border-red-300' : '']"
                v-model="formData.action"
                @input="handleChange"
              />
              <p v-if="errors.action" class="mt-1 text-sm text-red-600">{{ errors.action }}</p>
            </div>

            <div class="col-span-6">
              <label for="default_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL por defecto</label>
              <input
                type="text"
                name="default_url"
                id="default_url"
                :class="['mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2', errors.default_url ? 'border-red-300' : '']"
                v-model="formData.default_url"
                @input="handleChange"
              />
              <p v-if="errors.default_url" class="mt-1 text-sm text-red-600">{{ errors.default_url }}</p>
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Icono</label>
              <input
                type="text"
                name="icon"
                id="icon"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
                v-model="formData.icon"
                @input="handleChange"
              />
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color</label>
              <input
                type="text"
                name="color"
                id="color"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
                v-model="formData.color"
                @input="handleChange"
              />
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Padre</label>
              <input
                type="number"
                name="parent_id"
                id="parent_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
                v-model="formData.parent_id"
                @input="handleChange"
              />
            </div>

            <div class="col-span-6">
              <label for="nota" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nota</label>
              <textarea
                name="nota"
                id="nota"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
                v-model="formData.nota"
                @input="handleChange"
              />
            </div>
          </div>

          <div class="flex justify-end pt-6">
            <button
              type="submit"
              :disabled="processing"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ processing ? 'Guardando...' : 'Guardar Item' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>