<script setup lang="ts">
import AppLayout from '@/layouts/AppLayoutTemplate.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import { SelectRadix } from '@/components/ui/select'
import { Input } from '@/components/ui/input'

type MenuItem = {
    id: number
    title: string
    controller: string
    action: string
    default_url: string
    codapl: string
    tipo: string
    is_visible: boolean
    position: number
}

type TipFun = {
    tipfun: string
    destipfun: string
}

type Permission = {
    id: number
    menu_item: number
    tipfun: string
    can_view: boolean
    opciones: string | null
    tipfun_details?: TipFun
}

type Props = {
    menu_items: {
        data: MenuItem[]
        meta: {
            total_menu_items: number
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

const selectedItem = ref<MenuItem | null>(null)
const permissions = ref<Permission[]>([])
const tiposFuncionarios = ref<TipFun[]>([])
const loadingPermissions = ref(false)
const permissionsError = ref<string | null>(null)
const saving = ref(false)

const searchParams = new URLSearchParams(window.location.search)
const q = ref(searchParams.get('q') || '')
const tipo = ref(searchParams.get('tipo') || '')
const codapl = ref(searchParams.get('codapl') || '')
const perPage = computed(() => meta.pagination?.per_page || 10)

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
    router.get('/cajas/menu-permission', { ...currentFilterParams.value, page: 1 }, { preserveState: true, preserveScroll: true })
}

const clearFilters = () => {
    q.value = ''
    tipo.value = ''
    codapl.value = ''
    router.get('/cajas/menu-permission', { per_page: perPage.value, page: 1 }, { preserveState: true, preserveScroll: true })
}

const tipoOptions = [
    { value: 'A', label: 'Administrador' },
    { value: 'E', label: 'Empresa' },
    { value: 'P', label: 'Particular' },
    { value: 'T', label: 'Trabajador' },
    { value: 'F', label: 'Foniñez' },
]

const codaplOptions = [
    { value: 'CA', label: 'CA' },
    { value: 'ME', label: 'ME' },
]

const handleSelectItem = async (item: MenuItem) => {
    selectedItem.value = item
    loadingPermissions.value = true
    permissionsError.value = null
    try {
        const res = await fetch(`/cajas/menu-permission/${item.id}/permissions`, {
            headers: {
                'Accept': 'application/json',
            },
        })
        if (!res.ok) {
            throw new Error('No fue posible cargar los permisos')
        }
        const json = await res.json()
        permissions.value = json.permissions || []
        tiposFuncionarios.value = json.tipos_funcionarios || []
    } catch (e: any) {
        permissions.value = []
        permissionsError.value = e?.message || 'Error desconocido'
    } finally {
        loadingPermissions.value = false
    }
}

const handlePermissionChange = (tipfun: string, field: 'can_view' | 'opciones', value: any) => {
    const existingPermissionIndex = permissions.value.findIndex(p => p.tipfun === tipfun)
    const updatedPermissions = [...permissions.value]

    if (existingPermissionIndex > -1) {
        updatedPermissions[existingPermissionIndex] = {
            ...updatedPermissions[existingPermissionIndex],
            [field]: value
        }
    } else {
        const newPermission: Permission = {
            id: 0,
            menu_item: selectedItem.value!.id,
            tipfun: tipfun,
            can_view: field === 'can_view' ? value : false,
            opciones: field === 'opciones' ? value : null,
        }
        updatedPermissions.push(newPermission)
    }
    permissions.value = updatedPermissions
}

const savePermissions = async () => {
    if (!selectedItem.value) return
    saving.value = true
    try {
        for (const p of permissions.value) {
            if (p.menu_item !== selectedItem.value!.id) continue

            await fetch(`/cajas/menu-permission/ajax`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    menu_item: selectedItem.value!.id,
                    tipfun: p.tipfun,
                    can_view: p.can_view,
                    opciones: p.opciones
                })
            })
        }
        await handleSelectItem(selectedItem.value!)
    } catch (error) {
        console.error('Error saving permissions', error)
    } finally {
        saving.value = false
    }
}
</script>

<template>
  <AppLayout title="Permisos de Menú">
    <div class="bg-white shadow overflow-hidden sm:rounded-md m-2">
      <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Permisos de Menú
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
          Administra los permisos para cada item del menú por tipo de funcionario.
        </p>
      </div>

      <div class="px-4 sm:px-6 pb-4">
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
          <div class="sm:col-span-2">
            <label for="q" class="block text-sm font-medium text-gray-700">Buscar Item</label>
            <Input
              id="q"
              v-model="q"
              class="mt-1 w-full"
              placeholder="Título, controller, action..."
              @keydown.enter="applyFilters"
            />
          </div>
          <div>
            <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo Menú</label>
            <SelectRadix
              v-model="tipo"
              :options="tipoOptions"
              placeholder="Todos"
              class="mt-1 w-full"
            />
          </div>
          <div>
            <label for="codapl" class="block text-sm font-medium text-gray-700">Aplicación</label>
            <SelectRadix
              v-model="codapl"
              :options="codaplOptions"
              placeholder="Todas"
              class="mt-1 w-full"
            />
          </div>
          <div class="flex items-end gap-2">
            <button @click="applyFilters" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">Filtrar</button>
            <button @click="clearFilters" class="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">Limpiar</button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-1">
          <ul class="divide-y divide-gray-200 h-[75vh] overflow-y-auto">
            <li
              v-for="menu_item in data"
              :key="menu_item.id"
              @click="handleSelectItem(menu_item)"
              :class="['cursor-pointer hover:bg-gray-50', selectedItem?.id === menu_item.id ? 'bg-indigo-50' : '']"
            >
              <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                  <div class="text-sm font-medium text-indigo-600 truncate">{{ menu_item.title }}</div>
                  <div class="ml-2 flex-shrink-0 flex">
                    <p :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', menu_item.codapl === 'CA' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                      {{ menu_item.codapl }}
                    </p>
                  </div>
                </div>
                <div class="mt-2 sm:flex sm:justify-between">
                  <div class="sm:flex">
                    <p class="flex items-center text-sm text-gray-500">
                      {{ menu_item.controller }}
                    </p>
                    <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                      {{ menu_item.action }}
                    </p>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>

        <aside class="lg:col-span-2 m-2">
          <div class="sticky top-4 rounded-xl bg-white shadow-md ring-1 ring-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
              <div>
                <h4 class="text-sm font-semibold text-gray-900">Permisos del Item</h4>
                <p v-if="selectedItem" class="text-xs text-gray-500">Item: <span class="font-medium">{{ selectedItem.title }}</span></p>
                <p v-else class="text-xs text-gray-500">Selecciona un item para ver sus permisos</p>
              </div>
              <button
                v-if="selectedItem"
                @click="savePermissions"
                :disabled="saving"
                class="inline-flex items-center h-8 px-2.5 rounded-md border border-transparent text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
              >
                {{ saving ? 'Guardando...' : 'Guardar Cambios' }}
              </button>
            </div>
            <div class="p-4 max-h-[70vh] overflow-auto">
              <div v-if="loadingPermissions"><p>Cargando permisos...</p></div>
              <div v-else-if="permissionsError" class="text-red-500">{{ permissionsError }}</div>
              <table v-else-if="selectedItem" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Tipo Funcionario
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Puede Ver
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Opciones Adicionales
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="tf in tiposFuncionarios" :key="tf.tipfun">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ tf.destipfun }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <input
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                        :checked="permissions.find(p => p.tipfun === tf.tipfun)?.can_view || false"
                        @change="handlePermissionChange(tf.tipfun, 'can_view', ($event.target as HTMLInputElement).checked)"
                      />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <input
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600 p-2"
                        :value="permissions.find(p => p.tipfun === tf.tipfun)?.opciones || ''"
                        @input="handlePermissionChange(tf.tipfun, 'opciones', ($event.target as HTMLInputElement).value)"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </AppLayout>
</template>