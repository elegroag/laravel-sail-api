<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

type SelectOption = {
    id?: string | number
    value: string
    label: string
}

type Props = {
    menu_items: Array<{ id: number; title: string }>
    tipos_funcionarios: Array<{ tipfun: string; destipfun: string }>
    errors: Record<string, string>
}

const props = defineProps<Props>()

const formData = ref({
    menu_item: '',
    tipfun: '',
    can_view: false,
    opciones: '',
})

const processing = ref(false)

const menuItemOptions: SelectOption[] = props.menu_items.map(item => ({
    value: String(item.id),
    label: item.title,
}))

const tipFunOptions: SelectOption[] = props.tipos_funcionarios.map(tf => ({
    value: tf.tipfun,
    label: tf.destipfun,
}))

const handleSubmit = (e: Event) => {
    e.preventDefault()
    processing.value = true
    router.post('/cajas/menu-permission', {
        menu_item: formData.value.menu_item,
        tipfun: formData.value.tipfun,
        can_view: formData.value.can_view,
        opciones: formData.value.opciones,
    }, {
        onFinish: () => { processing.value = false }
    })
}
</script>

<template>
  <AppLayout title="Crear Permiso">
    <div class="bg-white shadow overflow-hidden sm:rounded-md m-2">
      <div class="px-4 py-5 sm:px-6 border-b">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Crear Nuevo Permiso
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
          Asigna un permiso a un item del menú para un tipo de funcionario.
        </p>
      </div>
      <form @submit="handleSubmit" class="p-4 sm:p-6">
        <div class="space-y-4">
          <div>
            <label for="menu_item" class="block text-sm font-medium text-gray-700">Item del Menú</label>
            <select
              id="menu_item"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-600 p-2"
              v-model="formData.menu_item"
              required
            >
              <option value="">-- Seleccione un item --</option>
              <option v-for="option in menuItemOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
            </select>
            <p v-if="props.errors.menu_item" class="mt-1 text-xs text-red-600">{{ props.errors.menu_item }}</p>
          </div>

          <div>
            <label for="tipfun" class="block text-sm font-medium text-gray-700">Tipo de Funcionario</label>
            <select
              id="tipfun"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-600 p-2"
              v-model="formData.tipfun"
              required
            >
              <option value="">-- Seleccione un tipo --</option>
              <option v-for="option in tipFunOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
            </select>
            <p v-if="props.errors.tipfun" class="mt-1 text-xs text-red-600">{{ props.errors.tipfun }}</p>
          </div>

          <div>
            <label for="opciones" class="block text-sm font-medium text-gray-700">Opciones Adicionales</label>
            <input
              id="opciones"
              type="text"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600 p-2"
              v-model="formData.opciones"
            />
            <p v-if="props.errors.opciones" class="mt-1 text-xs text-red-600">{{ props.errors.opciones }}</p>
          </div>

          <div class="flex items-start">
            <div class="flex items-center h-5">
              <input
                id="can_view"
                type="checkbox"
                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                v-model="formData.can_view"
              />
            </div>
            <div class="ml-3 text-sm">
              <label for="can_view" class="font-medium text-gray-700">Puede Ver</label>
              <p class="text-gray-500">Indica si el tipo de funcionario puede ver este item en el menú.</p>
            </div>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <Link href="/cajas/menu-permission" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancelar
          </Link>
          <button
            type="submit"
            :disabled="processing"
            class="inline-flex items-center h-9 px-3 rounded-md border border-transparent text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
          >
            {{ processing ? 'Creando...' : 'Crear Permiso' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>