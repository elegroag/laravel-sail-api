<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import { Input } from '@/components/ui/input'
import { SelectRadix } from '@/components/ui/select'

type Props = {
    formulario?: { id: number; name: string; title: string }
    formularios?: Array<{ id: number; name: string; title: string }>
}

const props = defineProps<Props>()
const { props: pageProps } = usePage()

const loading = ref(false)
const successOpen = ref(false)
const successMsg = ref('')
const errors = ref<Record<string, string>>({})
const formPickerOpen = ref(false)
const formPickerQuery = ref('')
const formPickerModule = ref('')
const selectedFormulario = ref(props.formulario ?? null)
const rows = ref<Array<{ id: number; name: string; title: string }>>(props.formularios || [])
const loadingPicker = ref(false)
const page = ref(1)
const pager = ref<{ current_page: number; last_page: number; per_page: number; total: number } | null>(null)

const typeOptions = [
    { value: 'input', label: 'Input' },
    { value: 'select', label: 'Select' },
    { value: 'textarea', label: 'Textarea' },
    { value: 'checkbox', label: 'Checkbox' },
    { value: 'radio', label: 'Radio' },
    { value: 'date', label: 'Date' },
    { value: 'number', label: 'Number' },
    { value: 'email', label: 'Email' },
    { value: 'password', label: 'Password' },
]

const formTypeOptions = [
    { value: 'text', label: 'Text' },
    { value: 'email', label: 'Email' },
    { value: 'number', label: 'Number' },
    { value: 'password', label: 'Password' },
    { value: 'date', label: 'Date' },
    { value: 'tel', label: 'Telephone' },
]

onMounted(() => {
    const flash = (pageProps.value as any)?.flash
    const msg = flash?.success as string | undefined
    if (msg && typeof msg === 'string') {
        successMsg.value = msg
        successOpen.value = true
    }
})

const formData = ref({
    name: '',
    label: '',
    type: 'input',
    form_type: 'text',
    placeholder: '',
    default_value: '',
    help_text: '',
    is_required: false,
    is_disabled: false,
    is_readonly: false,
    group_id: null as number | null,
    order: null as number | null,
    data_source: null as string | null,
    event_config: null as string | null
})

const handleSubmit = async () => {
    loading.value = true
    errors.value = {}

    try {
        await router.post('/cajas/componente-dinamico', {
            ...formData.value,
            formulario_id: selectedFormulario.value ? selectedFormulario.value.id : null
        })
    } catch (error: any) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors
        }
    } finally {
        loading.value = false
    }
}

const handleCancel = () => {
    router.visit('/cajas/componente-dinamico')
}
</script>

<template>
  <AppLayout title="Crear Componente Dinámico">
    <div class="bg-white shadow overflow-hidden sm:rounded-md m-2">
      <div class="px-4 py-5 sm:px-6">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Crear Nuevo Componente</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Define las propiedades y configuración del componente dinámico</p>
            <p v-if="selectedFormulario" class="mt-2 text-sm text-blue-600">Para el formulario: <strong>{{ selectedFormulario.title }}</strong></p>
            <p v-else class="mt-2 text-sm text-gray-500">Sin formulario asignado</p>
          </div>
          <div class="flex gap-2">
            <button @click="handleCancel" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
              Cancelar
            </button>
            <button @click="formPickerOpen = true" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
              {{ selectedFormulario ? 'Cambiar Formulario' : 'Elegir Formulario' }}
            </button>
          </div>
        </div>
      </div>

      <div class="px-4 py-5 sm:px-6">
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Nombre *</label>
              <Input id="name" v-model="formData.name" class="mt-1 w-full" required />
              <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
            </div>
            <div>
              <label for="label" class="block text-sm font-medium text-gray-700">Etiqueta *</label>
              <Input id="label" v-model="formData.label" class="mt-1 w-full" required />
              <p v-if="errors.label" class="mt-1 text-xs text-red-600">{{ errors.label }}</p>
            </div>
            <div>
              <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
              <SelectRadix v-model="formData.type" :options="typeOptions" class="mt-1 w-full" />
            </div>
            <div>
              <label for="form_type" class="block text-sm font-medium text-gray-700">Tipo de Formulario</label>
              <SelectRadix v-model="formData.form_type" :options="formTypeOptions" class="mt-1 w-full" />
            </div>
            <div>
              <label for="placeholder" class="block text-sm font-medium text-gray-700">Placeholder</label>
              <Input id="placeholder" v-model="formData.placeholder" class="mt-1 w-full" />
            </div>
            <div>
              <label for="default_value" class="block text-sm font-medium text-gray-700">Valor por Defecto</label>
              <Input id="default_value" v-model="formData.default_value" class="mt-1 w-full" />
            </div>
            <div>
              <label for="group_id" class="block text-sm font-medium text-gray-700">Grupo</label>
              <Input id="group_id" v-model="formData.group_id" type="number" class="mt-1 w-full" />
            </div>
            <div>
              <label for="order" class="block text-sm font-medium text-gray-700">Orden</label>
              <Input id="order" v-model="formData.order" type="number" class="mt-1 w-full" />
            </div>
          </div>

          <div>
            <label for="help_text" class="block text-sm font-medium text-gray-700">Texto de Ayuda</label>
            <Input id="help_text" v-model="formData.help_text" class="mt-1 w-full" />
          </div>

          <div class="flex items-center gap-6">
            <label class="inline-flex items-center">
              <input type="checkbox" v-model="formData.is_required" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
              <span class="ml-2 text-sm text-gray-700">Requerido</span>
            </label>
            <label class="inline-flex items-center">
              <input type="checkbox" v-model="formData.is_disabled" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
              <span class="ml-2 text-sm text-gray-700">Deshabilitado</span>
            </label>
            <label class="inline-flex items-center">
              <input type="checkbox" v-model="formData.is_readonly" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
              <span class="ml-2 text-sm text-gray-700">Solo Lectura</span>
            </label>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button type="submit" :disabled="loading" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50">
              {{ loading ? 'Guardando...' : 'Guardar Componente' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>