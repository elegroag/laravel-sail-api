<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import { X } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { SelectRadix } from '@/components/ui/select'

type Props = {
    formularios_dinamicos: {
        data: any[]
        meta: {
            total_formularios: number
            pagination?: {
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

const { data, meta } = props.formularios_dinamicos

const selectedId = ref<number | null>(null)
const children = ref<any[]>([])
const loadingChildren = ref(false)
const childrenError = ref<string | null>(null)

const addOpen = ref(false)
const options = ref<Array<{ id: number; title: string; module: string }>>([])
const optionsLoading = ref(false)
const optionsError = ref<string | null>(null)
const selectedChildId = ref('')
const searchOption = ref('')
const attaching = ref(false)
const toast = ref<{ type: 'success' | 'error'; message: string } | null>(null)

const childOptions = computed(() =>
  options.value.map(opt => ({ value: String(opt.id), label: `${opt.title} (${opt.module})` }))
)

const searchParams = new URLSearchParams(window.location.search)
const q = ref(searchParams.get('q') || '')
const module = ref(searchParams.get('module') || '')
const isActive = ref(searchParams.get('is_active') || '')
const perPage = computed(() => meta.pagination?.per_page || 10)
const safePagination = computed(() => meta.pagination ?? { current_page: 1, last_page: 1, per_page: 10, from: 0, to: 0, total: 0 })

onMounted(() => {
    const sp = new URLSearchParams(window.location.search)
    q.value = sp.get('q') || ''
    module.value = sp.get('module') || ''
    isActive.value = sp.get('is_active') || ''
})

const currentFilterParams = computed(() => ({
    q: q.value || undefined,
    module: module.value || undefined,
    is_active: isActive.value || undefined,
    per_page: perPage.value
}))

const applyFilters = () => {
    router.get('/cajas/formulario-dinamico', { ...currentFilterParams.value, page: 1 }, { preserveState: true, preserveScroll: true })
}

const clearFilters = () => {
    q.value = ''
    module.value = ''
    isActive.value = ''
    router.get('/cajas/formulario-dinamico', { per_page: perPage.value, page: 1 }, { preserveState: true, preserveScroll: true })
}

const handleDelete = async (_id: number, title: string) => {
    if (!confirm(`¿Estás seguro de que deseas eliminar el formulario "${title}"? Esta acción no se puede deshacer.`)) {
        return
    }

    try {
        router.delete(`/cajas/formulario-dinamico/${_id}`, {
            onError: () => {
                alert('Error al eliminar el formulario.')
            }
        })
    } catch (error) {
        console.error('Error al eliminar formulario:', error)
        alert('Error al eliminar el formulario.')
    }
}

const handleDetail = async (_id: number) => {
    try {
        selectedId.value = _id
        loadingChildren.value = true
        childrenError.value = null
        const res = await fetch(`/cajas/formulario-dinamico/${_id}/children`, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
        if (!res.ok) {
            throw new Error('No fue posible cargar los componentes hijos')
        }
        const json = await res.json()
        children.value = Array.isArray(json.data) ? json.data : []
    } catch (e: any) {
        children.value = []
        childrenError.value = e?.message || 'Error desconocido'
    } finally {
        loadingChildren.value = false
    }
}

const openAddChild = async () => {
    if (!selectedId.value) return
    addOpen.value = true
    await loadOptions('')
}

const loadOptions = async (search: string) => {
    if (!selectedId.value) return
    try {
        optionsLoading.value = true
        optionsError.value = null
        const url = new URL(window.location.origin + `/cajas/formulario-dinamico/options`)
        if (search) url.searchParams.set('q', search)

        const res = await fetch(url.toString(), {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin',
            method: 'POST',
            body: JSON.stringify({ q: search })
        })
        if (!res.ok) throw new Error('No fue posible cargar opciones')
        const json = await res.json()
        options.value = Array.isArray(json.data) ? json.data : []
    } catch (e: any) {
        options.value = []
        optionsError.value = e?.message || 'Error desconocido'
    } finally {
        optionsLoading.value = false
    }
}

const attachChild = async () => {
    if (!selectedId.value || !selectedChildId.value) return
    try {
        attaching.value = true
        const res = await fetch(`/cajas/formulario-dinamico/attach-child`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin',
            body: JSON.stringify({ id: selectedId.value, child_id: Number(selectedChildId.value) })
        })
        if (!res.ok) {
            const err = await res.json().catch(() => ({}))
            throw new Error(err.message || 'No fue posible agregar el componente')
        }
        await handleDetail(selectedId.value)
        addOpen.value = false
        selectedChildId.value = ''
        searchOption.value = ''
        toast.value = { type: 'success', message: 'Componente agregado correctamente' }
    } catch (e: any) {
        toast.value = { type: 'error', message: e?.message || 'Error desconocido al agregar' }
    } finally {
        attaching.value = false
    }
}

const toggleActive = async (id: number) => {
    try {
        const res = await fetch(`/cajas/formulario-dinamico/${id}/toggle-active`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
        if (!res.ok) throw new Error('No fue posible cambiar el estado')
        window.location.reload()
    } catch (e: any) {
        alert(e?.message || 'Error al cambiar estado')
    }
}

const changePage = (page: number) => {
    router.get('/cajas/formulario-dinamico', { page, ...currentFilterParams.value }, { preserveState: true, preserveScroll: true })
}
</script>

<template>
  <AppLayout title="Formularios Dinámicos">
    <div class="bg-white shadow overflow-hidden sm:rounded-md m-2">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">Formularios Dinámicos Registrados</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Lista de todos los formularios dinámicos en el sistema</p>
        </div>
        <Link href="/cajas/formulario-dinamico/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
          Nuevo Formulario Dinámico
        </Link>
      </div>

      <div class="px-4 sm:px-6 pb-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
          <div class="sm:col-span-2">
            <label for="q" class="block text-sm font-medium text-gray-700">Buscar</label>
            <Input
              id="q"
              v-model="q"
              class="mt-1 w-full"
              placeholder="Nombre, título, descripción..."
              @keydown.enter="applyFilters"
            />
          </div>
          <div>
            <label for="module" class="block text-sm font-medium text-gray-700">Módulo</label>
            <Input
              id="module"
              v-model="module"
              class="mt-1 w-full"
              placeholder="Ej: auth, creditos"
            />
          </div>
          <div class="flex items-end gap-2">
            <button @click="applyFilters" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">Filtrar</button>
            <button @click="clearFilters" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">Limpiar</button>
          </div>
        </div>
      </div>

      <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div class="text-center">
            <div class="text-2xl font-bold text-indigo-600">{{ meta.total_formularios }}</div>
            <div class="text-sm text-gray-500">Total Formularios</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ data.filter(f => f.is_active).length }}</div>
            <div class="text-sm text-gray-500">Activos</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-red-600">{{ data.filter(f => !f.is_active).length }}</div>
            <div class="text-sm text-gray-500">Inactivos</div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
          <ul class="divide-y divide-gray-200">
            <li v-for="formulario in data" :key="formulario.id">
              <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center cursor-pointer" @click="handleDetail(formulario.id)">
                        <span class="text-sm font-medium text-white">{{ formulario.title.charAt(0).toUpperCase() }}</span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-900">{{ formulario.title }}</div>
                        <span :class="['ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', formulario.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                          {{ formulario.is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                      </div>
                      <div class="text-sm text-gray-500">{{ formulario.module }} | {{ formulario.endpoint }}</div>
                      <div class="text-sm text-gray-500">{{ formulario.method }}</div>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <button
                      @click="toggleActive(formulario.id)"
                      :class="['inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium', formulario.is_active ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200']"
                    >
                      {{ formulario.is_active ? 'Desactivar' : 'Activar' }}
                    </button>
                    <div class="flex space-x-2">
                      <Link :href="`/cajas/formulario-dinamico/${formulario.id}/show`" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Ver</Link>
                      <Link :href="`/cajas/componente-dinamico/${formulario.id}/listar-por-formulario`" class="text-green-600 hover:text-green-900 text-sm font-medium">Componentes ({{ formulario.components_count || 0 }})</Link>
                      <Link :href="`/cajas/formulario-dinamico/${formulario.id}/edit`" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Editar</Link>
                      <button @click="handleDelete(formulario.id, formulario.title)" class="text-red-600 hover:text-red-900 text-sm font-medium">Eliminar</button>
                    </div>
                  </div>
                </div>
                <div v-if="formulario.description" class="mt-2">
                  <p class="text-sm text-gray-600">{{ formulario.description }}</p>
                </div>
              </div>
            </li>
          </ul>
        </div>

        <aside class="lg:col-span-1 m-2">
          <div class="sticky top-4 bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 flex items-center justify-between border-b">
              <div>
                <h4 class="text-sm font-semibold text-gray-900">Detalle del Formulario</h4>
                <p v-if="selectedId" class="text-xs text-gray-500">Formulario seleccionado: <span class="font-medium">#{{ selectedId }}</span></p>
                <p v-else class="text-xs text-gray-500">Selecciona un formulario para ver sus componentes</p>
              </div>
              <div v-if="selectedId" class="flex items-center gap-2">
                <span class="text-xs text-gray-500">{{ children.length }} componente{{ children.length === 1 ? '' : 's' }}</span>
                <button @click="openAddChild" class="inline-flex items-center h-8 px-2.5 rounded-md border border-gray-300 text-xs font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">Agregar</button>
              </div>
            </div>
            <div class="px-4 py-5 sm:px-6 max-h-[70vh] overflow-auto">
              <div v-if="loadingChildren" class="space-y-2">
                <div class="h-3 w-1/2 bg-gray-200 rounded animate-pulse" />
                <div class="h-3 w-2/3 bg-gray-200 rounded animate-pulse" />
                <div class="h-3 w-1/3 bg-gray-200 rounded animate-pulse" />
              </div>
              <div v-else-if="childrenError" class="rounded-md border border-red-200 bg-red-50 text-red-700 text-sm px-3 py-2">{{ childrenError }}</div>
              <template v-else-if="selectedId">
                <div v-if="children.length === 0" class="text-sm text-gray-500">Este formulario no tiene componentes.</div>
                <ul v-else class="divide-y divide-gray-200">
                  <li v-for="componente in children" :key="componente.id" class="px-4 py-4 sm:px-6">
                    <div class="flex items-start gap-3">
                      <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white text-sm font-semibold">
                        {{ componente.label?.charAt(0)?.toUpperCase() || componente.name?.charAt(0)?.toUpperCase() }}
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                          <div class="truncate text-sm font-medium text-gray-900" :title="componente.label || componente.name">{{ componente.label || componente.name }}</div>
                          <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium', componente.type === 'input' ? 'bg-blue-50 text-blue-700' : componente.type === 'select' ? 'bg-green-50 text-green-700' : componente.type === 'textarea' ? 'bg-purple-50 text-purple-700' : componente.type === 'date' ? 'bg-orange-50 text-orange-700' : componente.type === 'number' ? 'bg-red-50 text-red-700' : 'bg-gray-50 text-gray-700']">
                            {{ componente.type }}
                          </span>
                        </div>
                        <div class="mt-0.5 text-xs text-gray-500 truncate" :title="componente.name">{{ componente.name }}</div>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                          <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700 ring-1 ring-inset ring-gray-200">Grupo: {{ componente.group_id ?? 'N/A' }}</span>
                          <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700 ring-1 ring-inset ring-gray-200">Orden: {{ componente.order ?? '—' }}</span>
                          <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 ring-inset', componente.is_required ? 'bg-red-50 text-red-700 ring-red-200' : 'bg-green-50 text-green-700 ring-green-200']">
                            {{ componente.is_required ? 'Requerido' : 'Opcional' }}
                          </span>
                        </div>
                        <div v-if="componente.placeholder" class="mt-2 text-[11px] text-gray-500 break-all">Placeholder: {{ componente.placeholder }}</div>
                      </div>
                    </div>
                  </li>
                </ul>
              </template>
            </div>
          </div>
        </aside>
      </div>

      <div v-if="addOpen" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="addOpen = false" />
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
          <div class="px-4 py-3 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Agregar componente al formulario #{{ selectedId }}</h3>
            <button @click="addOpen = false" class="text-gray-500 hover:text-gray-700">
              <X class="w-4 h-4" />
            </button>
          </div>
          <div class="p-4 space-y-3">
            <div>
              <label class="block text-sm font-medium text-gray-700">Buscar componentes</label>
              <div class="mt-1 flex gap-2">
                <Input
                  v-model="searchOption"
                  class="flex-1"
                  placeholder="Nombre, etiqueta, tipo..."
                  @keydown.enter="loadOptions(searchOption)"
                />
                <button @click="loadOptions(searchOption)" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300">Buscar</button>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Seleccionar componente</label>
              <SelectRadix
                v-model="selectedChildId"
                :options="childOptions"
                placeholder="— Selecciona —"
                class="mt-1 w-full"
              />
              <p v-if="optionsLoading" class="mt-1 text-xs text-gray-500">Cargando componentes…</p>
              <p v-if="optionsError" class="mt-1 text-xs text-red-600">{{ optionsError }}</p>
            </div>
          </div>
          <div class="px-4 py-3 border-t flex justify-end gap-2">
            <button @click="addOpen = false" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
            <button @click="attachChild" :disabled="!selectedChildId || attaching" class="inline-flex items-center h-9 px-3 rounded-md border border-transparent text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50">{{ attaching ? 'Agregando…' : 'Agregar' }}</button>
          </div>
        </div>
      </div>

      <div v-if="toast" :class="toast.type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'" class="fixed bottom-4 right-4 z-50 min-w-[260px] max-w-[360px] px-4 py-3 rounded shadow-lg text-sm transition-all">
        {{ toast.message }}
        <button type="button" class="ml-3 underline text-white/90 hover:text-white" @click="toast = null">Cerrar</button>
      </div>

      <div v-if="meta.pagination" class="bg-white px-4 py-3 border-t border-gray-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
          <div class="text-sm text-gray-700">Mostrando {{ meta.pagination.from || 0 }}–{{ meta.pagination.to || 0 }} de {{ meta.pagination.total }}</div>
        </div>
        <div class="inline-flex items-center gap-2">
          <button @click="changePage(1)" :disabled="meta.pagination.current_page === 1" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 disabled:opacity-50 disabled:cursor-not-allowed">Primera</button>
          <button @click="changePage(Math.max(1, meta.pagination.current_page - 1))" :disabled="meta.pagination.current_page === 1" class="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900">Anterior</button>
          <button
            v-for="(num, idx) in Array.from({ length: Math.min(meta.pagination?.last_page ?? 1, (meta.pagination?.current_page ?? 1) + 2) - Math.max(1, (meta.pagination?.current_page ?? 1) - 2) + 1 }, (_, i) => Math.max(1, (meta.pagination?.current_page ?? 1) - 2) + i)" :key="num + '-' + idx"
            @click="changePage(num)"
            :class="['inline-flex items-center h-9 px-3 rounded-md border text-sm font-medium', num === meta.pagination.current_page ? 'bg-indigo-600 text-gray border-indigo-600' : 'text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-300']"
          >{{ num }}</button>
          <button @click="changePage(Math.min(meta.pagination.last_page, meta.pagination.current_page + 1))" :disabled="meta.pagination.current_page === meta.pagination.last_page" class="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900">Siguiente</button>
          <button @click="changePage(meta.pagination.last_page)" :disabled="meta.pagination.current_page === meta.pagination.last_page" class="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900">Última</button>
        </div>
      </div>

      <div v-if="data.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay formularios dinámicos</h3>
        <p class="mt-1 text-sm text-gray-500">Comienza creando un nuevo formulario dinámico.</p>
        <div class="mt-6">
          <Link href="/cajas/formulario-dinamico/create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Nuevo Formulario Dinámico</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>