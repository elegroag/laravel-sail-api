<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { router, Link } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'

type Componente = {
  id: number
  name: string
  label: string
  type: string
  form_type?: string
  group_id: number
  order: number
  is_disabled: boolean
  is_readonly: boolean
  is_required?: boolean
  placeholder?: string
}

type Props = {
  componentes_dinamicos: {
    data: Componente[]
    meta: {
      total_componentes: number
      pagination: {
        current_page: number
        last_page: number
        per_page: number
        from: number | null
        to: number | null
        total: number
      }
    }
  }
}

const props = defineProps<Props>()

const loading = ref(false)
const searchValue = ref('')
const confirmOpen = ref(false)
const pendingDelete = ref<{ id: number; name: string } | null>(null)

const filters = ref({
  type: '',
  group_id: '',
  has_validation: ''
})

const currentPage = ref(props.componentes_dinamicos.meta.pagination.current_page)
const perPage = ref(props.componentes_dinamicos.meta.pagination.per_page)
const totalItems = ref(props.componentes_dinamicos.meta.total_componentes)
const totalPages = computed(() => Math.ceil(totalItems.value / perPage.value))
const from = computed(() => props.componentes_dinamicos.meta.pagination.from)
const to = computed(() => props.componentes_dinamicos.meta.pagination.to)

const list = ref(props.componentes_dinamicos)

const filterOptions = [
  {
    key: 'type',
    label: 'Tipo',
    value: '',
    options: [
      { value: '', label: 'Todos los tipos' },
      { value: 'input', label: 'Campo de Texto' },
      { value: 'select', label: 'Lista Desplegable' },
      { value: 'textarea', label: 'Área de Texto' },
      { value: 'date', label: 'Campo de Fecha' },
      { value: 'number', label: 'Campo Numérico' },
      { value: 'dialog', label: 'Diálogo' }
    ]
  }
]

const hasActiveFilters = computed(() =>
  filters.value.type !== '' ||
  filters.value.group_id !== '' ||
  filters.value.has_validation !== '' ||
  searchValue.value.trim() !== ''
)

const getTypeColor = (type: string) => {
  switch (type) {
    case 'input': return 'bg-blue-100 text-blue-800'
    case 'select': return 'bg-green-100 text-green-800'
    case 'textarea': return 'bg-purple-100 text-purple-800'
    case 'date': return 'bg-orange-100 text-orange-800'
    case 'number': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}

const getTypeLabel = (type: string) => {
  switch (type) {
    case 'input': return 'Texto'
    case 'select': return 'Select'
    case 'textarea': return 'Área'
    case 'date': return 'Fecha'
    case 'number': return 'Número'
    case 'dialog': return 'Diálogo'
    default: return type
  }
}

const handleFilterChange = (key: string, value: string) => {
  filters.value = { ...filters.value, [key]: value }
  setTimeout(performSearch, 300)
}

const clearFilters = () => {
  filters.value = { type: '', group_id: '', has_validation: '' }
  searchValue.value = ''
  performSearch()
}

const performSearch = () => {
  setLoading(true)
  const params: Record<string, any> = {
    page: 1,
    per_page: perPage.value
  }

  if (filters.value.type) params.type = filters.value.type
  if (filters.value.group_id) params.group_id = filters.value.group_id
  if (filters.value.has_validation) params.has_validation = filters.value.has_validation
  if (searchValue.value.trim()) params.q = searchValue.value

  const searchParams = new URLSearchParams(window.location.search)
  const formularioId = searchParams.get('formulario_id')
  if (formularioId) params.formulario_id = formularioId

  router.get('/cajas/componente-dinamico', params, {
    preserveState: true,
    onFinish: () => setLoading(false)
  })
}

const handlePageChange = (page: number) => {
  currentPage.value = page
  setLoading(true)
  const params: Record<string, any> = {
    page,
    per_page: perPage.value
  }

  if (filters.value.type) params.type = filters.value.type
  if (filters.value.group_id) params.group_id = filters.value.group_id
  if (filters.value.has_validation) params.has_validation = filters.value.has_validation
  if (searchValue.value.trim()) params.q = searchValue.value

  const searchParams = new URLSearchParams(window.location.search)
  const formularioId = searchParams.get('formulario_id')
  if (formularioId) params.formulario_id = formularioId

  router.get('/cajas/componente-dinamico', params, {
    preserveState: true,
    onFinish: () => setLoading(false)
  })
}

const handlePerPageChange = (newPerPage: number) => {
  perPage.value = newPerPage
  setLoading(true)
  const params: Record<string, any> = {
    page: 1,
    per_page: newPerPage
  }

  if (filters.value.type) params.type = filters.value.type
  if (filters.value.group_id) params.group_id = filters.value.group_id
  if (filters.value.has_validation) params.has_validation = filters.value.has_validation
  if (searchValue.value.trim()) params.q = searchValue.value

  router.get('/cajas/componente-dinamico', params, {
    preserveState: true,
    onFinish: () => setLoading(false)
  })
}

const setLoading = (value: boolean) => {
  loading.value = value
}

const handleEdit = (id: number) => {
  router.visit(`/cajas/componente-dinamico/${id}/edit`)
}

const handleDelete = (id: number) => {
  const comp = list.value.data.find(c => c.id === id)
  pendingDelete.value = { id, name: comp?.label || 'componente' }
  confirmOpen.value = true
}

const confirmDelete = async () => {
  if (!pendingDelete.value) return
  setLoading(true)
  try {
    router.delete(`/cajas/componente-dinamico/${pendingDelete.value.id}`, {
      onFinish: () => {
        confirmOpen.value = false
        pendingDelete.value = null
        setLoading(false)
      }
    })
  } catch {
    setLoading(false)
  }
}

const handleShow = (id: number) => {
  router.visit(`/cajas/componente-dinamico/${id}/show`)
}

const handleDuplicate = (id: number) => {
  router.post(`/cajas/componente-dinamico/${id}/duplicate`, {}, {
    onSuccess: () => router.reload()
  })
}

onMounted(() => {
  totalItems.value = list.value.meta.total_componentes
})
</script>

<template>
  <AppLayout title="Componentes Dinámicos">
    <div class="bg-white shadow overflow-hidden sm:rounded-md m-2">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">Componentes Dinámicos</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Gestiona los componentes reutilizables del sistema</p>
        </div>
        <Link
          href="/cajas/componente-dinamico/create"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          Nuevo Componente
        </Link>
      </div>

      <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div class="text-center">
            <div class="text-2xl font-bold text-indigo-600">{{ list.meta.total_componentes }}</div>
            <div class="text-sm text-gray-500">Total Componentes</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ list.data.length }}</div>
            <div class="text-sm text-gray-500">Visibles</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-orange-600">{{ list.data.filter(c => c.type === 'select').length }}</div>
            <div class="text-sm text-gray-500">Selects</div>
          </div>
        </div>
      </div>

      <div class="px-4 py-5 sm:px-6">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
          <div class="p-4">
            <div class="flex items-center space-x-4">
              <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <input
                  type="text"
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Buscar..."
                  v-model="searchValue"
                  @keydown.enter="performSearch"
                />
              </div>
              <button
                @click="performSearch"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Buscar
              </button>
              <select
                v-model="filters.type"
                @change="handleFilterChange('type', filters.type)"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="">Todos los tipos</option>
                <option value="input">Campo de Texto</option>
                <option value="select">Lista Desplegable</option>
                <option value="textarea">Área de Texto</option>
                <option value="date">Campo de Fecha</option>
                <option value="number">Campo Numérico</option>
                <option value="dialog">Diálogo</option>
              </select>
              <button
                v-if="hasActiveFilters"
                @click="clearFilters"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Limpiar
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="px-4 py-5 sm:px-6">
        <div v-if="loading" class="flex justify-center items-center py-12">
          <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="ml-2 text-gray-600">Cargando componentes...</span>
        </div>

        <div v-else-if="list.data.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay componentes</h3>
          <p class="mt-1 text-sm text-gray-500">Comienza creando tu primer componente dinámico.</p>
        </div>

        <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
          <div class="hidden sm:grid gap-4 px-6 py-3 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider sm:grid-cols-[30%_8%_8%_8%_8%_40%]">
            <div>Componente</div>
            <div>Nombre</div>
            <div>Grupo</div>
            <div>Orden</div>
            <div>Tipo</div>
            <div>Acciones</div>
          </div>
          <ul class="divide-y divide-gray-200">
            <li v-for="componente in list.data" :key="componente.id" class="px-6 py-4">
              <div class="grid grid-cols-1 gap-4 items-start sm:items-center sm:grid-cols-[30%_8%_8%_8%_8%_40%]">
                <div class="min-w-0">
                  <div class="flex items-center justify-between sm:justify-start sm:space-x-3">
                    <div class="min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">{{ componente.label }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                      <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', getTypeColor(componente.type)]">
                        {{ getTypeLabel(componente.type) }}
                      </span>
                      <span v-if="componente.is_disabled" class="inline-flex items-center rounded-full bg-gray-100 text-gray-800 px-2 py-0.5 text-xs font-medium">
                        Desh
                      </span>
                      <span v-if="componente.is_readonly" class="inline-flex items-center rounded-full bg-gray-100 text-gray-800 px-2 py-0.5 text-xs font-medium">
                        Solo Lect
                      </span>
                    </div>
                  </div>
                </div>
                <div class="text-sm text-gray-700 truncate">{{ componente.name }}</div>
                <div class="text-sm text-gray-700">{{ componente.group_id }}</div>
                <div class="text-sm text-gray-700">{{ componente.order }}</div>
                <div class="text-sm text-gray-700">{{ getTypeLabel(componente.form_type || componente.type) }}</div>
                <div class="flex flex-wrap gap-2 mt-2 sm:mt-0">
                  <button @click="handleShow(componente.id)" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Ver
                  </button>
                  <button @click="handleEdit(componente.id)" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Editar
                  </button>
                  <button @click="handleDuplicate(componente.id)" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Duplicar
                  </button>
                  <button @click="handleDelete(componente.id)" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    Eliminar
                  </button>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center gap-4">
            <div class="text-sm text-gray-700">
              Mostrando {{ from || 0 }}–{{ to || 0 }} de {{ totalItems }}
            </div>
            <div class="flex items-center gap-2">
              <label for="per_page" class="text-sm text-gray-600">Por página</label>
              <select
                id="per_page"
                class="rounded-md border border-gray-300 px-2 py-1 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                :value="perPage"
                @change="handlePerPageChange(Number(($event.target as HTMLSelectElement).value))"
              >
                <option v-for="n in [10, 15, 25, 50, 100]" :key="n" :value="n">{{ n }}</option>
              </select>
            </div>
          </div>
          <div class="inline-flex items-center gap-2">
            <button
              @click="handlePageChange(1)"
              :disabled="currentPage === 1"
              class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Primera
            </button>
            <button
              @click="handlePageChange(Math.max(1, currentPage - 1))"
              :disabled="currentPage === 1"
              class="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900"
            >
              Anterior
            </button>
            <span class="px-3 py-1 text-sm text-gray-700">
              {{ currentPage }} de {{ totalPages }}
            </span>
            <button
              @click="handlePageChange(Math.min(totalPages, currentPage + 1))"
              :disabled="currentPage === totalPages"
              class="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900"
            >
              Siguiente
            </button>
            <button
              @click="handlePageChange(totalPages)"
              :disabled="currentPage === totalPages"
              class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Última
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="confirmOpen" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="confirmOpen = false" />
      <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
        <div class="mb-4">
          <h3 class="text-lg font-medium text-gray-900">Confirmar eliminación</h3>
          <p class="mt-2 text-sm text-gray-500">
            Esta acción eliminará definitivamente el componente "{{ pendingDelete?.name }}". No podrás deshacerla.
          </p>
        </div>
        <div class="flex justify-end gap-3">
          <button
            @click="confirmOpen = false"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            @click="confirmDelete"
            :disabled="loading"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 disabled:opacity-50"
          >
            {{ loading ? 'Eliminando...' : 'Eliminar' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>