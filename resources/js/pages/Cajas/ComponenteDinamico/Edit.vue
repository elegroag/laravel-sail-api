<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'
import { X } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { SelectRadix } from '@/components/ui/select'

type DataSourceItem = { value: string; label: string }

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
  formulario_id?: number
}

type Props = {
  componente: Componente
}

const props = defineProps<Props>()
const { props: pageProps } = usePage()

const formData = ref({
  name: '',
  type: 'text',
  label: '',
  placeholder: '',
  form_type: 'input',
  group_id: 1,
  order: 1,
  default_value: '',
  is_disabled: false,
  is_readonly: false,
  data_source: [] as DataSourceItem[],
  css_classes: '',
  help_text: '',
  target: -1,
  event_config: {} as Record<string, string | number | boolean | null>,
  search_type: '',
  search_endpoint: '',
  date_max: '',
  number_min: '',
  number_max: '',
  number_step: 1,
  formulario_id: undefined as number | undefined
})

const processing = ref(false)
const errors = ref<Record<string, string>>({})
const successOpen = ref(false)
const successMsg = ref('')

const typeOptions = [
    { value: 'text', label: 'Texto' },
    { value: 'number', label: 'Número' },
    { value: 'date', label: 'Fecha' },
    { value: 'hidden', label: 'Oculto' },
    { value: 'phone', label: 'Teléfono' },
    { value: 'email', label: 'Email' },
]

const formTypeOptions = [
    { value: 'input', label: 'Input' },
    { value: 'select', label: 'Select' },
    { value: 'textarea', label: 'Textarea' },
    { value: 'date', label: 'Date' },
    { value: 'dialog', label: 'Dialog' },
    { value: 'radio', label: 'Radio' },
    { value: 'checkbox', label: 'Checkbox' },
    { value: 'address', label: 'Dirección' },
]

const searchTypeOptions = [
    { value: '', label: 'Seleccione' },
    { value: 'ninguno', label: 'Ninguno' },
    { value: 'local', label: 'Local' },
    { value: 'ajax', label: 'Ajax' },
    { value: 'collection', label: 'Collection' },
]

const showSearchType = computed(() =>
  formData.value.form_type === 'select' || formData.value.form_type === 'dialog'
)
const showAjaxEndpoint = computed(() =>
  (formData.value.form_type === 'select' || formData.value.form_type === 'dialog') &&
  formData.value.search_type === 'ajax'
)
const showDataSource = computed(() =>
  formData.value.form_type === 'select' && formData.value.search_type === 'local'
)
const showDateConfig = computed(() => formData.value.form_type === 'date')
const showNumberConfig = computed(() =>
  formData.value.form_type === 'input' && formData.value.type === 'number'
)

onMounted(() => {
  formData.value = {
    name: props.componente.name || '',
    type: (props.componente.type || 'text').toString().toLowerCase(),
    label: props.componente.label || '',
    placeholder: props.componente.placeholder || '',
    form_type: (props.componente.form_type || 'input').toString().toLowerCase(),
    group_id: props.componente.group_id || 1,
    order: props.componente.order || 1,
    default_value: props.componente.default_value || '',
    is_disabled: props.componente.is_disabled || false,
    is_readonly: props.componente.is_readonly || false,
    data_source: (props.componente.data_source as DataSourceItem[] | null) || [],
    css_classes: props.componente.css_classes || '',
    help_text: props.componente.help_text || '',
    target: props.componente.target || -1,
    event_config: (props.componente.event_config as Record<string, string | number | boolean | null>) || {},
    search_type: (props.componente.search_type as string) || '',
    search_endpoint: (props.componente.search_endpoint as string) || '',
    date_max: props.componente.date_max || '',
    number_min: props.componente.number_min?.toString() || '',
    number_max: props.componente.number_max?.toString() || '',
    number_step: props.componente.number_step || 1,
    formulario_id: props.componente.formulario_id
  }

  const msg = (pageProps.value as any)?.flash?.success as string | undefined
  if (msg) {
    successMsg.value = msg
    successOpen.value = true
  }
})

const handleChange = (e: Event) => {
  const target = e.target as HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement
  const { name, value, type } = target

  if (type === 'checkbox') {
    const checked = (target as HTMLInputElement).checked
    ;(formData.value as any)[name] = checked
  } else if (type === 'number') {
    ;(formData.value as any)[name] = Number(value)
  } else {
    ;(formData.value as any)[name] = value
  }

  if (errors.value[name]) {
    errors.value = { ...errors.value, [name]: '' }
  }
}

const handleDataSourceChange = (index: number, field: 'value' | 'label', value: string) => {
  const list = [...formData.value.data_source]
  list[index] = { ...list[index], [field]: value }
  formData.value.data_source = list
}

const addDataSourceItem = () => {
  formData.value.data_source = [...formData.value.data_source, { value: '', label: '' }]
}

const removeDataSourceItem = (index: number) => {
  formData.value.data_source = formData.value.data_source.filter((_, i) => i !== index)
}

const validate = (): boolean => {
  const v: Record<string, string> = {}
  const typeAllowed = ['text', 'number', 'date', 'email', 'phone', 'hidden']
  const formTypeAllowed = ['input', 'select', 'textarea', 'date', 'dialog', 'radio', 'checkbox', 'address']

  if (!formData.value.name.trim()) v.name = 'El nombre es obligatorio.'
  if (!formData.value.label.trim()) v.label = 'La etiqueta es obligatoria.'
  if (!typeAllowed.includes(formData.value.type)) v.type = 'Propiedad type del componente inválido.'
  if (!formTypeAllowed.includes(formData.value.form_type)) v.form_type = 'Tipo de formulario inválido.'
  if (!formData.value.group_id || Number(formData.value.group_id) < 1) v.group_id = 'Grupo debe ser un entero ≥ 1.'
  if (!formData.value.order || Number(formData.value.order) < 1) v.order = 'Orden debe ser un entero ≥ 1.'

  if (showSearchType.value) {
    const allowedSearch = ['ninguno', 'local', 'ajax', 'collection', '']
    if (!allowedSearch.includes(formData.value.search_type)) {
      v.search_type = 'Tipo de búsqueda inválido.'
    }
    if (formData.value.search_type === 'ajax') {
      if (!formData.value.search_endpoint || formData.value.search_endpoint.trim().length < 160) {
        v.search_endpoint = 'Debe especificar un endpoint (mínimo 160 caracteres).'
      }
    }
  }

  if (formData.value.form_type === 'select' && Array.isArray(formData.value.data_source) && formData.value.data_source.length > 0) {
    const invalid = formData.value.data_source.find((it) => !it.value.trim() || !it.label.trim())
    if (invalid) v.data_source = 'Todas las opciones deben tener valor y etiqueta.'
  }

  if (formData.value.type === 'number' && formData.value.form_type === 'input') {
    const step = Number(formData.value.number_step)
    if (!(step > 0)) v.number_step = 'El incremento debe ser mayor que 0.'
    const min = formData.value.number_min !== '' ? Number(formData.value.number_min) : null
    const max = formData.value.number_max !== '' ? Number(formData.value.number_max) : null
    if (min !== null && Number.isNaN(min)) v.number_min = 'Número mínimo inválido.'
    if (max !== null && Number.isNaN(max)) v.number_max = 'Número máximo inválido.'
    if (min !== null && max !== null && min > max) v.number_min = 'El mínimo no puede ser mayor que el máximo.'
  }

  if (formData.value.type === 'date' && formData.value.date_max) {
    const ts = Date.parse(formData.value.date_max)
    if (Number.isNaN(ts)) v.date_max = 'Fecha máxima inválida.'
  }

  errors.value = v
  return Object.keys(v).length === 0
}

const handleSubmit = async () => {
  if (!validate()) return

  processing.value = true

  const payload = {
    ...formData.value,
    search_type: formData.value.search_type === 'ninguno' || formData.value.search_type === '' ? null : formData.value.search_type,
    data_source: formData.value.form_type === 'select' && formData.value.search_type === 'local' ? formData.value.data_source : null,
    search_endpoint: formData.value.search_type === 'ajax' ? formData.value.search_endpoint : null,
    event_config: Object.keys(formData.value.event_config).length > 0 ? formData.value.event_config : null,
    date_max: formData.value.type === 'date' && formData.value.date_max ? formData.value.date_max : null,
    number_min: formData.value.type === 'number' && formData.value.number_min ? Number(formData.value.number_min) : null,
    number_max: formData.value.type === 'number' && formData.value.number_max ? Number(formData.value.number_max) : null,
    number_step: formData.value.type === 'number' ? Number(formData.value.number_step) : 1
  }

  try {
    router.put(`/cajas/componente-dinamico/${props.componente.id}`, payload, {
      preserveState: true,
      onSuccess: () => {
        successMsg.value = (pageProps.value as any)?.flash?.success || 'Componente actualizado correctamente.'
        successOpen.value = true
      },
      onError: (err) => {
        errors.value = err as Record<string, string>
      },
      onFinish: () => {
        processing.value = false
      }
    })
  } catch {
    processing.value = false
  }
}
</script>

<template>
  <AppLayout :title="`Editar Componente: ${componente.label}`">
    <div v-if="successOpen" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="successOpen = false" />
      <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
        <div class="mb-4">
          <h3 class="text-lg font-medium text-emerald-600">Actualización exitosa</h3>
          <p class="mt-2 text-sm text-gray-500">{{ successMsg || 'Cambios guardados correctamente.' }}</p>
        </div>
        <div class="flex justify-end">
          <button
            @click="successOpen = false"
            class="px-4 py-2 text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>

    <div class="bg-white shadow sm:rounded-md m-2 overflow-hidden">
      <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">Editar Componente Dinámico</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Modificar los datos del componente dinámico</p>
        </div>
        <div class="space-x-2 flex">
          <Link
            :href="`/cajas/componente-dinamico?formulario_id=${componente.formulario_id}`"
            class="px-4 py-2 text-sm font-medium rounded-md text-white bg-indigo-400 hover:bg-indigo-400 inline-flex items-center border border-transparent"
          >
            Volver con formulario
          </Link>
          <Link
            href="/cajas/componente-dinamico"
            class="px-4 py-2 text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 inline-flex items-center border border-transparent"
          >
            Volver
          </Link>
        </div>
      </div>

      <div class="px-4 py-5 sm:px-6">
        <form @submit.prevent="handleSubmit">
          <div class="gap-6 grid grid-cols-6">
            <div class="sm:col-span-3 col-span-6">
              <label for="name" class="text-sm font-medium text-gray-700 block">
                Nombre único *
              </label>
              <Input
                name="name"
                id="name"
                v-model="formData.name"
                :class="['mt-1 w-full', errors.name ? 'border-red-300' : '']"
                required
              />
              <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
              <p class="mt-1 text-xs text-gray-500">Identificador único para el componente</p>
            </div>

            <div class="sm:col-span-3 col-span-6">
              <label for="type" class="text-sm font-medium text-gray-700 block">
                Tipo *
              </label>
              <SelectRadix
                v-model="formData.type"
                :options="typeOptions"
                class="mt-1 w-full"
              />
            </div>

            <div class="sm:col-span-3 col-span-6">
              <label for="form_type" class="text-sm font-medium text-gray-700 block">
                Tipo de formulario *
              </label>
              <SelectRadix
                v-model="formData.form_type"
                :options="formTypeOptions"
                class="mt-1 w-full"
              />
            </div>

            <div v-if="showSearchType" class="sm:col-span-3 col-span-6">
              <label for="search_type" class="text-sm font-medium text-gray-700 block">
                Tipo de búsqueda
              </label>
              <SelectRadix
                v-model="formData.search_type"
                :options="searchTypeOptions"
                class="mt-1 w-full"
              />
              <p v-if="errors.search_type" class="mt-1 text-sm text-red-600">{{ errors.search_type }}</p>
            </div>

            <div v-if="showAjaxEndpoint" class="col-span-6">
              <label for="search_endpoint" class="text-sm font-medium text-gray-700 block">
                Endpoint de búsqueda (AJAX)
              </label>
              <Input
                name="search_endpoint"
                id="search_endpoint"
                v-model="formData.search_endpoint"
                :class="['mt-1 w-full', errors.search_endpoint ? 'border-red-300' : '']"
                placeholder="https://api.midominio.com/recurso?param1=... (mínimo 160 caracteres)"
                minLength="160"
              />
              <p v-if="errors.search_endpoint" class="mt-1 text-sm text-red-600">{{ errors.search_endpoint }}</p>
            </div>

            <div class="sm:col-span-3 col-span-6">
              <label for="label" class="text-sm font-medium text-gray-700 block">
                Etiqueta *
              </label>
              <Input
                name="label"
                id="label"
                v-model="formData.label"
                :class="['mt-1 w-full', errors.label ? 'border-red-300' : '']"
                required
              />
              <p v-if="errors.label" class="mt-1 text-sm text-red-600">{{ errors.label }}</p>
            </div>

            <div class="sm:col-span-3 col-span-6">
              <label for="placeholder" class="text-sm font-medium text-gray-700 block">
                Placeholder
              </label>
              <Input
                name="placeholder"
                id="placeholder"
                v-model="formData.placeholder"
                class="mt-1 w-full"
              />
            </div>

            <div class="sm:col-span-2 col-span-6">
              <label for="group_id" class="text-sm font-medium text-gray-700 block">
                Grupo *
              </label>
              <Input
                type="number"
                name="group_id"
                id="group_id"
                v-model.number="formData.group_id"
                :class="['mt-1 w-full', errors.group_id ? 'border-red-300' : '']"
                min="1"
                required
              />
              <p v-if="errors.group_id" class="mt-1 text-sm text-red-600">{{ errors.group_id }}</p>
            </div>

            <div class="sm:col-span-2 col-span-6">
              <label for="order" class="text-sm font-medium text-gray-700 block">
                Orden *
              </label>
              <Input
                type="number"
                name="order"
                id="order"
                v-model.number="formData.order"
                :class="['mt-1 w-full', errors.order ? 'border-red-300' : '']"
                min="1"
                required
              />
              <p v-if="errors.order" class="mt-1 text-sm text-red-600">{{ errors.order }}</p>
            </div>

            <div class="sm:col-span-2 col-span-6">
              <div class="space-y-3">
                <div>
                  <label class="inline-flex items-center">
                    <input
                      type="checkbox"
                      name="is_disabled"
                      class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      v-model="formData.is_disabled"
                      @change="handleChange"
                    />
                    <span class="ml-2 text-sm text-gray-700">Deshabilitado</span>
                  </label>
                </div>
                <div>
                  <label class="inline-flex items-center">
                    <input
                      type="checkbox"
                      name="is_readonly"
                      class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      v-model="formData.is_readonly"
                      @change="handleChange"
                    />
                    <span class="ml-2 text-sm text-gray-700">Solo lectura</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="sm:col-span-2 col-span-6">
              <label for="target" class="text-sm font-medium text-gray-700 block">
                Objetivo
              </label>
              <Input
                type="number"
                name="target"
                id="target"
                v-model.number="formData.target"
                class="mt-1 w-full"
              />
            </div>

            <div class="sm:col-span-3 col-span-6">
              <label for="default_value" class="text-sm font-medium text-gray-700 block">
                Valor por defecto
              </label>
              <Input
                name="default_value"
                id="default_value"
                v-model="formData.default_value"
                class="mt-1 w-full"
              />
            </div>

            <div class="col-span-6">
              <label for="help_text" class="text-sm font-medium text-gray-700 block">
                Texto de ayuda
              </label>
              <textarea
                name="help_text"
                id="help_text"
                rows="2"
                class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-900 p-2 block w-full"
                v-model="formData.help_text"
                @input="handleChange"
              />
            </div>

            <div class="col-span-6">
              <label for="css_classes" class="text-sm font-medium text-gray-700 block">
                Clases CSS
              </label>
              <Input
                name="css_classes"
                id="css_classes"
                v-model="formData.css_classes"
                class="mt-1 w-full"
              />
            </div>

            <div v-if="showDataSource" class="col-span-6">
              <div class="border-gray-200 pt-6 border-t">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Opciones del Select</h4>
                <div class="space-y-3">
                  <div v-for="(item, index) in formData.data_source" :key="index" class="gap-3 flex items-end">
                    <div class="flex-1">
                      <label class="text-sm font-medium text-gray-700 block">Valor</label>
                      <Input
                        :modelValue="item.value"
                        @update:modelValue="handleDataSourceChange(index, 'value', String($event))"
                        class="mt-1 w-full"
                      />
                    </div>
                    <div class="flex-1">
                      <label class="text-sm font-medium text-gray-700 block">Etiqueta</label>
                      <Input
                        :modelValue="item.label"
                        @update:modelValue="handleDataSourceChange(index, 'label', String($event))"
                        class="mt-1 w-full"
                      />
                    </div>
                    <button
                      type="button"
                      @click="removeDataSourceItem(index)"
                      class="h-9 px-3 rounded-md border border-red-300 text-sm font-medium text-red-700 hover:bg-red-50 inline-flex items-center"
                    >
                      <X class="w-4 h-4" />
                    </button>
                  </div>
                  <button
                    type="button"
                    @click="addDataSourceItem"
                    class="px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 inline-flex items-center border"
                  >
                    + Agregar Opción
                  </button>
                </div>
              </div>
            </div>

            <div v-if="showDateConfig" class="sm:col-span-3 col-span-6">
              <label for="date_max" class="text-sm font-medium text-gray-700 block">
                Fecha máxima
              </label>
              <Input
                type="date"
                name="date_max"
                id="date_max"
                v-model="formData.date_max"
                :class="['mt-1 w-full', errors.date_max ? 'border-red-300' : '']"
              />
              <p v-if="errors.date_max" class="mt-1 text-sm text-red-600">{{ errors.date_max }}</p>
            </div>

            <template v-if="showNumberConfig">
              <div class="sm:col-span-2 col-span-6">
                <label for="number_min" class="text-sm font-medium text-gray-700 block">
                  Valor mínimo
                </label>
                <Input
                  type="number"
                  step="any"
                  name="number_min"
                  id="number_min"
                  v-model="formData.number_min"
                  :class="['mt-1 w-full', errors.number_min ? 'border-red-300' : '']"
                />
                <p v-if="errors.number_min" class="mt-1 text-sm text-red-600">{{ errors.number_min }}</p>
              </div>
              <div class="sm:col-span-2 col-span-6">
                <label for="number_max" class="text-sm font-medium text-gray-700 block">
                  Valor máximo
                </label>
                <Input
                  type="number"
                  step="any"
                  name="number_max"
                  id="number_max"
                  v-model="formData.number_max"
                  :class="['mt-1 w-full', errors.number_max ? 'border-red-300' : '']"
                />
                <p v-if="errors.number_max" class="mt-1 text-sm text-red-600">{{ errors.number_max }}</p>
              </div>
              <div class="sm:col-span-2 col-span-6">
                <label for="number_step" class="text-sm font-medium text-gray-700 block">
                  Incremento *
                </label>
                <Input
                  type="number"
                  step="any"
                  name="number_step"
                  id="number_step"
                  v-model.number="formData.number_step"
                  :class="['mt-1 w-full', errors.number_step ? 'border-red-300' : '']"
                  min="0.01"
                  required
                />
                <p v-if="errors.number_step" class="mt-1 text-sm text-red-600">{{ errors.number_step }}</p>
              </div>
            </template>
          </div>

          <div class="pt-6 border-t border-gray-200 mt-6 flex justify-end">
            <button
              type="submit"
              :disabled="processing"
              class="px-4 py-2 text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 inline-flex items-center border border-transparent focus:ring-2 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
            >
              {{ processing ? 'Actualizando...' : 'Actualizar Componente' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>