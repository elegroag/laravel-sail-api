<template>
  <div>
    <!-- Ítem con hijos: botón desplegable -->
    <button
      v-if="item.children && item.children.length > 0"
      @click="$emit('toggle')"
      :title="collapsed ? item.label : ''"
      :class="[
        'flex w-full items-center gap-2.5 rounded-md px-2.5 py-2 text-[12px] transition-all duration-150 cursor-pointer',
        'hover:bg-white/10',
        isActive ? 'bg-blue-500/80 text-white' : 'text-white/65'
      ]"
    >
      <span :class="['flex h-5 w-5 items-center justify-center rounded flex-shrink-0', getIconBgClass(item.icon)]">
        <component :is="getIcon(item.icon)" class="h-3 w-3 text-white" style="stroke-width:2" />
      </span>
      <span class="nav-label flex-1 text-left leading-tight">{{ item.label }}</span>
      <IconChevron
        v-if="!collapsed"
        :class="['h-3 w-3 flex-shrink-0 opacity-60 transition-transform duration-200', isExpanded ? 'rotate-180' : '']"
      />
    </button>

    <!-- Ítem simple: Inertia Link -->
    <Link
      v-else-if="item.route"
      :href="item.route"
      :title="collapsed ? item.label : ''"
      :class="[
        'flex w-full items-center gap-2.5 rounded-md px-2.5 py-2 text-[12px] transition-all duration-150 cursor-pointer',
        'hover:bg-white/10',
        isActive ? 'bg-blue-500 text-white shadow-sm' : 'text-white/65'
      ]"
    >
      <span :class="['flex h-5 w-5 items-center justify-center rounded flex-shrink-0', getIconBgClass(item.icon)]">
        <component :is="getIcon(item.icon)" class="h-3 w-3 text-white" style="stroke-width:2" />
      </span>
      <span class="nav-label flex-1 leading-tight">{{ item.label }}</span>
    </Link>

    <!-- Submenú colapsable -->
    <Transition name="submenu">
      <ul
        v-if="isExpanded && item.children && !collapsed"
        class="mt-0.5 mb-0.5 space-y-0.5 pl-3"
      >
        <li v-for="child in item.children" :key="child.id">
          <!-- Nivel 2: puede tener nietos -->
          <div v-if="child.children && child.children.length > 0">
            <button
              @click="$emit('toggle-child', child.id)"
              :class="[
                'flex w-full items-center gap-2 rounded-md px-2.5 py-1.5 text-[11.5px] transition-colors cursor-pointer',
                'text-white/55 hover:bg-white/10 hover:text-white/80'
              ]"
            >
              <span class="h-1.5 w-1.5 rounded-full bg-white/30 flex-shrink-0"></span>
              <span class="flex-1 text-left leading-tight">{{ child.label }}</span>
              <IconChevron
                :class="['h-3 w-3 opacity-50 transition-transform duration-200', expandedChildren.has(child.id) ? 'rotate-180' : '']"
              />
            </button>
            <!-- Nivel 3 -->
            <Transition name="submenu">
              <ul v-if="expandedChildren.has(child.id)" class="mt-0.5 space-y-0.5 pl-4">
                <li v-for="grandchild in child.children" :key="grandchild.id">
                  <Link
                    v-if="grandchild.route"
                    :href="grandchild.route"
                    :class="[
                      'flex items-center gap-2 rounded-md px-2.5 py-1.5 text-[11px] transition-colors cursor-pointer',
                      isActiveRoute(grandchild.route) ? 'text-sky-300 font-medium' : 'text-white/45 hover:bg-white/10 hover:text-white/70'
                    ]"
                  >
                    <span class="h-1 w-1 rounded-full bg-white/25 flex-shrink-0"></span>
                    <span class="leading-tight">{{ grandchild.label }}</span>
                  </Link>
                </li>
              </ul>
            </Transition>
          </div>

          <!-- Nivel 2 sin hijos -->
          <Link
            v-else-if="child.route"
            :href="child.route"
            :class="[
              'flex items-center gap-2 rounded-md px-2.5 py-1.5 text-[11.5px] transition-colors cursor-pointer',
              isActiveRoute(child.route) ? 'text-sky-300 font-medium' : 'text-white/55 hover:bg-white/10 hover:text-white/80'
            ]"
          >
            <span class="h-1.5 w-1.5 rounded-full bg-white/30 flex-shrink-0"></span>
            <span class="leading-tight">{{ child.label }}</span>
          </Link>
        </li>
      </ul>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import type { MenuItem } from '@/types'
import IconHome from '@/components/icons/IconHome.vue'
import IconSearch from '@/components/icons/IconSearch.vue'
import IconUsers from '@/components/icons/IconUsers.vue'
import IconFileText from '@/components/icons/IconFileText.vue'
import IconUser from '@/components/icons/IconUser.vue'
import IconPackage from '@/components/icons/IconPackage.vue'
import IconAlertCircle from '@/components/icons/IconAlertCircle.vue'
import IconChevron from '@/components/icons/IconChevron.vue'
import { useSidebar } from '@/composables/useSidebar'

defineProps<{
  item: MenuItem
  collapsed: boolean
  isExpanded: boolean
  isActive: boolean
}>()

defineEmits<{
  toggle: []
  'toggle-child': [id: string]
  navigate: [route: string]
}>()

const { isActiveRoute } = useSidebar()

/* Nivel 3: estado local de hijos expandidos */
const expandedChildren = ref<Set<string>>(new Set())

const iconMap: Record<string, typeof IconHome> = {
  home: IconHome,
  search: IconSearch,
  users: IconUsers,
  'file-text': IconFileText,
  user: IconUser,
  package: IconPackage,
  'alert-circle': IconAlertCircle,
}

const iconBgMap: Record<string, string> = {
  home: 'bg-[#4d9de0]',
  search: 'bg-[#3fa9a9]',
  users: 'bg-[#e07b4d]',
  'file-text': 'bg-[#a07bca]',
  user: 'bg-[#e05b8a]',
  package: 'bg-[#4db87b]',
  'alert-circle': 'bg-[#ca7b4d]',
  circle: 'bg-white/20',
}

const getIcon = (name: string) => iconMap[name] || IconHome
const getIconBgClass = (name: string) => iconBgMap[name] || 'bg-white/20'
</script>

<style scoped>
.submenu-enter-active,
.submenu-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease, max-height 0.25s ease;
  overflow: hidden;
  max-height: 400px;
}
.submenu-enter-from,
.submenu-leave-to {
  opacity: 0;
  transform: translateY(-4px);
  max-height: 0;
}
</style>
