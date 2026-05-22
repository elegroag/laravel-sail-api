<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import { X } from 'lucide-vue-next'

type MenuItem = {
  id: number
  title: string
  controller: string | null
  action: string | null
  tipo: string
  codapl: string
  is_visible: string
  position: number
  default_url: string | null
}

type Props = {
  menu_items: {
    data: MenuItem[]
    meta: {
      total_menu_items: number
      menu_permisos: any[]
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

const { data, meta } = props.menu_items

const selectedId = ref<number | null>(null)
const children = ref<any[]>([])
const loadingChildren = ref(false)
const childrenError = ref<string | null>(null)

const addOpen = ref(false)
const options = ref<Array<{ id: number; title: string; controller: string | null; action: string | null }>>([])
const optionsLoading = ref(false)
const optionsError = ref<string | null>(null)
const selectedChildId = ref('')
const searchOption = ref('')
const attaching = ref(false)
const toast = ref<{ type: 'success' | 'error'; message: string } | null>(null)

const searchParams = new URLSearchParams(window.location.search)
const q = ref(searchParams.get('q') || '')
const tipo = ref(searchParams.get('tipo') || '')
const codapl = ref(searchParams.get('codapl') || '')
const perPage = computed(() => meta.pagination?.per_page || 10)
const safePagination = computed(() => meta.pagination ?? { current_page: 1, last_page: 1, per_page: 10, from: null, to: null, total: 0 })

onMounted(() => {
  const sp = new URLSearchParams(window.location.search)
  q.value = sp.get('q') || ''
  tipo.value = sp.get('tipo') || ''
  codapl.value = sp.get('codapl') || ''
})

const currentFilterParams = computed(() => ({
  q: q.value || undefined,
  tipo: tipo.value || undefined,
  codapl: codapl.value || undefined,
  per_page: perPage.value
}))

const applyFilters = () => {
  router.get('/cajas/menu', { ...currentFilterParams.value, page: 1 }, { preserveState: true, preserveScroll: true })
}

const clearFilters = () => {
  q.value = ''
  tipo.value = ''
  codapl.value = ''
  router.get('/cajas/menu', { per_page: perPage.value, page: 1 }, { preserveState: true, preserveScroll: true })
}

const handleDelete = async (_id: number, title: string) => {
  if (!confirm(`¿Estás seguro de que deseas eliminar el menu "${title}"? Esta acción no se puede deshacer.`)) {
    return
  }

  try {
    router.delete(`/cajas/menu/${_id}`, {
      onSuccess: () => {},
      onError: () => {
        alert('Error al eliminar el menu. Por favor, inténtalo de nuevo.')
      }
    })
  } catch (error) {
    console.error('Error al eliminar menu:', error)
    alert('Error al eliminar el menu. Por favor, inténtalo de nuevo.')
  }
}

const handleDetail = async (_id: number) => {
  try {
    selectedId.value = _id
    loadingChildren.value = true
    childrenError.value = null
    const res = await fetch(`/cajas/menu/children`, {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin',
      method: 'POST',
      body: JSON.stringify({ id: _id, tipo: tipo.value, codapl: codapl.value })
    })
    if (!res.ok) {
      throw new Error('No fue posible cargar los items hijos')
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
    const url = new URL(window.location.origin + `/cajas/menu/options`)
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
      body: JSON.stringify({ q: search, id: selectedId.value, tipo: tipo.value, codapl: codapl.value })
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
    const res = await fetch(`/cajas/menu/attach-child`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin',
      body: JSON.stringify({ id: selectedId.value, child_id: Number(selectedChildId.value), tipo: tipo.value, codapl: codapl.value })
    })
    if (!res.ok) {
      const err = await res.json().catch(() => ({}))
      throw new Error(err.message || 'No fue posible agregar el hijo')
    }
    await handleDetail(selectedId.value)
    addOpen.value = false
    selectedChildId.value = ''
    searchOption.value = ''
    toast.value = { type: 'success', message: 'Hijo agregado correctamente' }
  } catch (e: any) {
    toast.value = { type: 'error', message: e?.message || 'Error desconocido al agregar' }
  } finally {
    attaching.value = false
  }
}

const changePage = (page: number) => {
  router.get('/cajas/menu', { page, ...currentFilterParams.value }, { preserveState: true, preserveScroll: true })
}

const changePerPage = (value: number) => {
  router.get('/cajas/menu', { page: 1, per_page: value, q: q.value || undefined, tipo: tipo.value || undefined, codapl: codapl.value || undefined }, { preserveState: true, preserveScroll: true })
}
</script>

<template>
  <AppLayout title="Menu">
    <div class="bg-white shadow overflow-hidden sm:rounded-md m-2">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Menu Registrado
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Lista de todos los menu en el sistema
          </p>
        </div>
        <Link
          href="/cajas/menu/create"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          Nuevo item Menu
        </Link>
      </div>

      <div class="px-4 sm:px-6 pb-4">
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
          <div class="sm:col-span-2">
            <label for="q" class="block text-sm font-medium text-gray-700">Buscar</label>
            <input
              id="q"
              type="text"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600 p-2"
              placeholder="Título, controller, action, URL..."
              v-model="q"
              @keydown.enter="applyFilters"
            />
          </div>
          <div>
            <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
            <select
              id="tipo"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-600 p-2"
              v-model="tipo"
            >
              <option value="">Todos</option>
              <option value="A">Administrador</option>
              <option value="E">Empresa</option>
              <option value="P">Particular</option>
              <option value="T">Trabajador</option>
              <option value="F">Foniñez</option>
            </select>
          </div>
          <div>
            <label for="codapl" class="block text-sm font-medium text-gray-700">Aplicación</label>
            <select
              id="codapl"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-600 p-2"
              v-model="codapl"
            >
              <option value="">Todas</option>
              <option value="CA">CA</option>
              <option value="ME">ME</option>
            </select>
          </div>
          <div class="flex items-end gap-2">
            <button @click="applyFilters" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">Filtrar</button>
            <button @click="clearFilters" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">Limpiar</button>
          </div>
        </div>
      </div>

      <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-indigo-600">{{ meta.total_menu_items }}</div>
            <div class="text-sm text-gray-500">Total Menu</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ meta.menu_permisos.length }}</div>
            <div class="text-sm text-gray-500">Permisos</div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
          <ul class="divide-y divide-gray-200">
            <li v-for="menu_item in data" :key="menu_item.id">
              <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center cursor-pointer" @click="handleDetail(menu_item.id)">
                        <span class="text-sm font-medium text-white">
                          {{ menu_item.title.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-900">
                          {{ menu_item.title }}
                        </div>
                        <span :class="menu_item.codapl === 'CA' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                          {{ menu_item.codapl }}
                        </span>
                      </div>
                      <div class="text-sm text-gray-500">
                        TIPO: {{ menu_item.tipo }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ menu_item.controller }} | {{ menu_item.action }}
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <div class="text-right">
                      <div class="text-sm font-medium text-gray-900">
                        {{ menu_item.is_visible }} Es visible
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ menu_item.position }} Posición
                      </div>
                    </div>
                    <div class="flex space-x-2">
                      <Link
                        :href="`/cajas/menu/${menu_item.id}/show`"
                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                      >
                        Ver
                      </Link>
                      <Link
                        :href="`/cajas/menu/${menu_item.id}/edit`"
                        class="text-gray-600 hover:text-gray-900 text-sm font-medium"
                      >
                        Editar
                      </Link>
                      <button
                        @click="handleDelete(menu_item.id, menu_item.title)"
                        class="text-red-600 hover:text-red-900 text-sm font-medium"
                      >
                        Eliminar
                      </button>
                    </div>
                  </div>
                </div>
                <div v-if="menu_item.default_url" class="mt-2">
                  <p class="text-sm text-gray-600">{{ menu_item.default_url }}</p>
                </div>
              </div>
            </li>
          </ul>
        </div>

        <aside class="lg:col-span-1 m-2">
          <div class="sticky top-4 rounded-xl bg-white shadow-md ring-1 ring-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
              <div>
                <h4 class="text-sm font-semibold text-gray-900">Detalle del Item</h4>
                <p v-if="selectedId" class="text-xs text-gray-500">Item seleccionado: <span class="font-medium">#{{ selectedId }}</span></p>
                <p v-else class="text-xs text-gray-500">Selecciona un item para ver sus hijos</p>
              </div>
              <div v-if="selectedId" class="flex items-center gap-2">
                <span class="text-xs text-gray-500">
                  {{ children.length }} hijo{{ children.length === 1 ? '' : 's' }}
                </span>
                <button
                  @click="openAddChild"
                  class="inline-flex items-center h-8 px-2.5 rounded-md border border-gray-300 text-xs font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                  Agregar
                </button>
              </div>
            </div>
            <div class="p-4 max-h-[70vh] overflow-auto">
              <div v-if="loadingChildren" class="space-y-2">
                <div class="h-3 w-1/2 bg-gray-200 rounded animate-pulse" />
                <div class="h-3 w-2/3 bg-gray-200 rounded animate-pulse" />
                <div class="h-3 w-1/3 bg-gray-200 rounded animate-pulse" />
              </div>
              <div v-else-if="childrenError" class="rounded-md border border-red-200 bg-red-50 text-red-700 text-sm px-3 py-2">
                {{ childrenError }}
              </div>
              <template v-else-if="selectedId">
                <div v-if="children.length === 0" class="text-sm text-gray-500">Este item no tiene hijos.</div>
                <ul v-else class="space-y-3">
                  <li v-for="child in children" :key="child.id" class="rounded-lg border border-gray-200 p-3 hover:border-indigo-200 transition-colors">
                    <div class="flex items-start gap-3">
                      <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white text-sm font-semibold">
                        {{ child.title?.charAt(0)?.toUpperCase() }}
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                          <div class="truncate text-sm font-medium text-gray-900" :title="child.title">{{ child.title }}</div>
                          <Link
                            :href="`/cajas/menu/${child.id}/edit`"
                            class="text-indigo-600 hover:text-indigo-800 text-xs font-medium shrink-0"
                          >
                            Editar
                          </Link>
                        </div>
                        <div class="mt-0.5 text-xs text-gray-500 truncate" :title="`${child.controller || '—'} | ${child.action || '—'}`">
                          {{ child.controller || '—' }} | {{ child.action || '—' }}
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                          <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700 ring-1 ring-inset ring-gray-200">
                            Tipo: {{ child.tipo ?? 'N/A' }}
                          </span>
                          <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700 ring-1 ring-inset ring-gray-200">
                            Pos: {{ child.position ?? '—' }}
                          </span>
                          <span :class="child.is_visible ? 'bg-green-50 text-green-700 ring-green-200' : 'bg-red-50 text-red-700 ring-red-200'" class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 ring-inset">
                            {{ child.is_visible ? 'Visible' : 'Oculto' }}
                          </span>
                        </div>
                        <div v-if="child.default_url" class="mt-2 text-[11px] text-gray-500 break-all">
                          {{ child.default_url }}
                        </div>
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
            <h3 class="text-sm font-semibold text-gray-900">Agregar hijo al item #{{ selectedId }}</h3>
            <button @click="addOpen = false" class="text-gray-500 hover:text-gray-700">
              <X class="w-4 h-4" />
            </button>
          </div>
          <div class="p-4 space-y-3">
            <div>
              <label class="block text-sm font-medium text-gray-700">Buscar</label>
              <div class="mt-1 flex gap-2">
                <input
                  type="text"
                  class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600"
                  placeholder="Título, controller, action"
                  v-model="searchOption"
                  @keydown.enter="loadOptions(searchOption)"
                />
                <button @click="loadOptions(searchOption)" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300">Buscar</button>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Seleccionar item</label>
              <select
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600 p-2"
                v-model="selectedChildId"
              >
                <option value="">— Selecciona —</option>
                <option v-for="opt in options" :key="opt.id" :value="opt.id">
                  {{ opt.title }}
                </option>
              </select>
              <p v-if="optionsLoading" class="mt-1 text-xs text-gray-500">Cargando opciones…</p>
              <p v-if="optionsError" class="mt-1 text-xs text-red-600">{{ optionsError }}</p>
            </div>
          </div>
          <div class="px-4 py-3 border-t flex justify-end gap-2">
            <button @click="addOpen = false" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
            <button @click="attachChild" :disabled="!selectedChildId || attaching" class="inline-flex items-center h-9 px-3 rounded-md border border-transparent text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50">
              {{ attaching ? 'Agregando…' : 'Agregar' }}
            </button>
          </div>
        </div>
      </div>

      <div
        v-if="toast"
        :class="toast.type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'"
        class="fixed bottom-4 right-4 z-50 min-w-[260px] max-w-[360px] px-4 py-3 rounded shadow-lg text-sm transition-all"
      >
        {{ toast.message }}
        <button
          type="button"
          class="ml-3 underline text-white/90 hover:text-white"
          @click="toast = null"
        >
          Cerrar
        </button>
      </div>

      <div v-if="meta.pagination" class="bg-white px-4 py-3 border-t border-gray-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
          <div class="text-sm text-gray-700">
            Mostrando {{ meta.pagination.from || 0 }}–{{ meta.pagination.to || 0 }} de {{ meta.pagination.total }}
          </div>
          <div class="text-sm text-gray-700 flex items-center gap-2">
            <label for="per_page" class="text-gray-600">Por página</label>
            <select
              id="per_page"
              class="rounded-md border border-gray-300 px-2 py-1 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              :value="meta.pagination.per_page"
              @change="changePerPage(Number(($event.target as HTMLSelectElement).value))"
            >
              <option v-for="n in [10,25,50,100]" :key="n" :value="n">{{ n }}</option>
            </select>
          </div>
        </div>
        <div class="inline-flex items-center gap-2">
          <button
            @click="changePage(1)"
            :disabled="meta.pagination.current_page === 1"
            class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Primera
          </button>
          <button
            @click="changePage(Math.max(1, meta.pagination.current_page - 1))"
            :disabled="meta.pagination.current_page === 1"
            class="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900"
          >
            Anterior
          </button>
          <template v-for="(num, idx) in Array.from({ length: Math.min(safePagination.last_page, safePagination.current_page + 2) - Math.max(1, safePagination.current_page - 2) + 1 }, (_, i) => Math.max(1, safePagination.current_page - 2) + i)" :key="'page-' + num">
            <button
              @click="changePage(num)"
              :class="num === meta.pagination.current_page ? 'bg-indigo-600 text-gray border-indigo-600' : 'text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-300'"
              class="inline-flex items-center h-9 px-3 rounded-md border text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              {{ num }}
            </button>
          </template>
          <button
            @click="changePage(Math.min(meta.pagination.last_page, meta.pagination.current_page + 1))"
            :disabled="meta.pagination.current_page === meta.pagination.last_page"
            class="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900"
          >
            Siguiente
          </button>
          <button
            @click="changePage(meta.pagination.last_page)"
            :disabled="meta.pagination.current_page === meta.pagination.last_page"
            class="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900"
          >
            Última
          </button>
        </div>
      </div>

      <div v-if="data.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay items de menu</h3>
        <p class="mt-1 text-sm text-gray-500">Comienza creando un nuevo item de menu.</p>
        <div class="mt-6">
          <Link
            href="/cajas/menu/create"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            Nuevo Item Menu
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>