<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

type PermissionData = {
    id: number
    menu_item: number
    tipfun: string
    can_view: boolean
    opciones: string | null
    menu_item_data: {
        id: number
        title: string
    }
    tipfun_data: {
        tipfun: string
        destipfun: string
    }
}

type Props = {
    permission: PermissionData
    errors: Record<string, string>
}

const props = defineProps<Props>()

const formData = ref({
    can_view: false,
    opciones: '',
})

const processing = ref(false)

onMounted(() => {
    formData.value = {
        can_view: props.permission.can_view,
        opciones: props.permission.opciones || '',
    }
})

const handleSubmit = (e: Event) => {
    e.preventDefault()
    processing.value = true
    router.put(`/cajas/menu-permission/${props.permission.id}`, {
        can_view: formData.value.can_view,
        opciones: formData.value.opciones,
    }, {
        onFinish: () => { processing.value = false }
    })
}
</script>

<template>
  <AppLayout title="Editar Permiso">
    <div class="bg-white shadow overflow-hidden sm:rounded-md m-2">
      <div class="px-4 py-5 sm:px-6 border-b">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Editar Permiso
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
          Modifica los detalles del permiso.
        </p>
      </div>
      <form @submit="handleSubmit" class="p-4 sm:p-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Item del Menú</label>
            <p class="mt-1 text-sm text-gray-900 p-2 bg-gray-100 rounded-md">{{ permission.menu_item_data.title }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Tipo de Funcionario</label>
            <p class="mt-1 text-sm text-gray-900 p-2 bg-gray-100 rounded-md">{{ permission.tipfun_data.destipfun }}</p>
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
            {{ processing ? 'Actualizando...' : 'Actualizar Permiso' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>