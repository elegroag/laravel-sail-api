<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import { Input } from '@/components/ui/input'
import { SelectRadix } from '@/components/ui/select'

type Props = {
    menu_item: {
        id: number
        title: string
        default_url: string
        icon: string
        color: string
        nota: string
        parent_id: number | null
        codapl: string
        controller: string
        action: string
    }
}

const props = defineProps<Props>()

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

onMounted(() => {
    formData.value = {
        title: props.menu_item.title || '',
        default_url: props.menu_item.default_url || '',
        icon: props.menu_item.icon || '',
        color: props.menu_item.color || '',
        nota: props.menu_item.nota || '',
        parent_id: props.menu_item.parent_id ? props.menu_item.parent_id.toString() : '',
        codapl: props.menu_item.codapl || 'CA',
        controller: props.menu_item.controller || '',
        action: props.menu_item.action || ''
    }
})

const handleChange = (e: Event) => {
    const target = e.target as HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement
    const { name, value } = target
    formData.value[name as keyof typeof formData.value] = value
    if (errors.value[name]) {
        errors.value[name] = ''
    }
}

const handleSubmit = async (event: Event) => {
    event.preventDefault()
    processing.value = true

    try {
        const response = await fetch(`/cajas/menu/${props.menu_item.id}`, {
            method: 'PUT',
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
        console.error('Error al actualizar item:', error)
    } finally {
        processing.value = false
    }
}
</script>

<template>
  <AppLayout title="Editar Menu Item">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Editar Menu Item
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Modificar los datos del item de menú
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
              <label for="title" class="block text-sm font-medium text-gray-700">Título *</label>
              <Input
                name="title"
                id="title"
                v-model="formData.title"
                :class="['mt-1 w-full', errors.title ? 'border-red-300' : '']"
                required
              />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="codapl" class="block text-sm font-medium text-gray-700">Aplicación *</label>
              <SelectRadix
                v-model="formData.codapl"
                :options="[{ value: 'CA', label: 'CA' }]"
                class="mt-1 w-full"
              />
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="controller" class="block text-sm font-medium text-gray-700">Controller *</label>
              <Input
                name="controller"
                id="controller"
                v-model="formData.controller"
                :class="['mt-1 w-full', errors.controller ? 'border-red-300' : '']"
                required
              />
              <p v-if="errors.controller" class="mt-1 text-sm text-red-600">{{ errors.controller }}</p>
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="action" class="block text-sm font-medium text-gray-700">Action *</label>
              <Input
                name="action"
                id="action"
                v-model="formData.action"
                :class="['mt-1 w-full', errors.action ? 'border-red-300' : '']"
                required
              />
              <p v-if="errors.action" class="mt-1 text-sm text-red-600">{{ errors.action }}</p>
            </div>

            <div class="col-span-6">
              <label for="default_url" class="block text-sm font-medium text-gray-700">URL por defecto</label>
              <Input
                name="default_url"
                id="default_url"
                v-model="formData.default_url"
                :class="['mt-1 w-full', errors.default_url ? 'border-red-300' : '']"
              />
              <p v-if="errors.default_url" class="mt-1 text-sm text-red-600">{{ errors.default_url }}</p>
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="icon" class="block text-sm font-medium text-gray-700">Icono</label>
              <Input
                name="icon"
                id="icon"
                v-model="formData.icon"
                class="mt-1 w-full"
              />
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
              <Input
                name="color"
                id="color"
                v-model="formData.color"
                class="mt-1 w-full"
              />
            </div>

            <div class="col-span-6 sm:col-span-3">
              <label for="parent_id" class="block text-sm font-medium text-gray-700">Padre</label>
              <Input
                type="number"
                name="parent_id"
                id="parent_id"
                v-model="formData.parent_id"
                class="mt-1 w-full"
              />
            </div>

            <div class="col-span-6">
              <label for="nota" class="block text-sm font-medium text-gray-700">Nota</label>
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
              {{ processing ? 'Actualizando...' : 'Actualizar Item' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>