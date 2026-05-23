import { ref, computed } from 'vue'
import type { MenuItem } from '@/types'

const expandedMenus = ref<Set<string>>(new Set(['inicio']))
const activeRoute = ref('/mercurio-v2')

export function useSidebar() {
  const menuItems: MenuItem[] = [
    {
      id: 'inicio',
      label: 'Inicio',
      icon: 'home',
      route: '/mercurio-v2'
    },
    {
      id: 'consultas',
      label: 'Consultas',
      icon: 'search',
      children: [
        { id: 'consulta-giro', label: 'Consulta de giro', icon: 'circle', route: '/mercurio-v2/consultas/giro' },
        { id: 'consulta-nucleo', label: 'Consulta núcleo familiar', icon: 'circle', route: '/mercurio-v2/consultas/nucleo' },
        { id: 'consulta-planilla', label: 'Consulta planilla', icon: 'circle', route: '/mercurio-v2/consultas/planilla' }
      ]
    },
    {
      id: 'afiliacion',
      label: 'Afiliación',
      icon: 'users',
      children: [
        { id: 'nueva-afiliacion', label: 'Nueva afiliación', icon: 'circle', route: '/mercurio-v2/afiliacion/nueva' },
        { id: 'estado-afiliacion', label: 'Estado de afiliación', icon: 'circle', route: '/mercurio-v2/afiliacion/estado' },
        { id: 'historial', label: 'Historial', icon: 'circle', route: '/mercurio-v2/afiliacion/historial' }
      ]
    },
    {
      id: 'certificados',
      label: 'Certificados',
      icon: 'file-text',
      children: [
        { id: 'solicitar-cert', label: 'Solicitar certificado', icon: 'circle', route: '/mercurio-v2/certificados/solicitar' },
        { id: 'mis-certificados', label: 'Mis certificados', icon: 'circle', route: '/mercurio-v2/certificados/mis-certificados' }
      ]
    },
    {
      id: 'cuenta',
      label: 'Cuenta usuario',
      icon: 'user',
      children: [
        { id: 'perfil', label: 'Mi perfil', icon: 'circle', route: '/mercurio-v2/cuenta/perfil' },
        { id: 'seguridad', label: 'Seguridad', icon: 'circle', route: '/mercurio-v2/cuenta/seguridad' },
        { id: 'notificaciones', label: 'Notificaciones', icon: 'circle', route: '/mercurio-v2/cuenta/notificaciones' }
      ]
    },
    {
      id: 'productos',
      label: 'Productos y servicios',
      icon: 'package',
      children: [
        { id: 'catalogo', label: 'Catálogo', icon: 'circle', route: '/mercurio-v2/productos/catalogo' },
        { id: 'mis-productos', label: 'Mis productos', icon: 'circle', route: '/mercurio-v2/productos/mis-productos' }
      ]
    },
    {
      id: 'reportar',
      label: 'Reportar errores del siste...',
      icon: 'alert-circle',
      route: '/mercurio-v2/reportar'
    }
  ]

  const isExpanded = (menuId: string) => expandedMenus.value.has(menuId)

  const toggleMenu = (menuId: string) => {
    if (expandedMenus.value.has(menuId)) {
      expandedMenus.value.delete(menuId)
    } else {
      expandedMenus.value.add(menuId)
    }
  }

  const setActiveRoute = (route: string) => {
    activeRoute.value = route
  }

  const isActiveRoute = computed(() => (route: string) => {
    return activeRoute.value === route || route.startsWith(activeRoute.value)
  })

  return {
    menuItems,
    expandedMenus,
    activeRoute,
    isExpanded,
    toggleMenu,
    setActiveRoute,
    isActiveRoute
  }
}