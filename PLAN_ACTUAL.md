
# Plan de mejora UX para Formularios, Componentes y Validaciones

A continuación un plan por fases, con mejores prácticas, uso de componentes existentes, tipado TypeScript y patrones con Inertia + React. El objetivo es estandarizar UI/UX, robustecer formularios controlados y optimizar rendimiento y accesibilidad.

## Fase 0 — Auditoría y lineamientos (rápida)
- **[revisar-estado-actual]** Estructura de páginas, consistencia de cards, headers, paddings, barras de estadísticas.
- **[inventario-componentes]** Identificar y reutilizar componentes existentes: `AppLayout`, `FilterBar`, `PaginationControls`, `ActionButtons`, `ComponentList`, toasts.
- **[definir-guías]** Lineamientos de UI: card `bg-white shadow overflow-hidden sm:rounded-md`, headers `px-4 py-5 sm:px-6`, listas con `divide-y`, barra de estadísticas `bg-gray-50 px-4 py-3 border-b`.

## Fase 1 — Estandarización visual (UI)
- **[cards/headers]** Unificar `className` en todas las páginas de los 3 módulos.
- **[barras-estadísticas]** Replicar el patrón de Formularios: grid 3-4 métricas, estilos consistentes.
- **[paddings]** Alinear paddings en filtros, listas y paginación.
- **[acciones]** Homogeneizar botones primarios/secundarios (`ActionButtons` o `Link` con variantes).

## Fase 2 — Tipos compartidos (TypeScript)
- **[types]** Crear tipos en `resources/js/types`:
  - `Formulario`, `FormularioLayoutConfig`, `Permissions`.
  - [Componente](cci:2://file:///home/edwin-tics/proyectos/comfaca-enlinea/laravel/resources/js/pages/Cajas/ComponenteDinamico/Index.tsx:7:0-20:1), `DataSourceItem`, `ComponenteState`.
  - `Validacion`, `ReglasPersonalizadas`, `ErroresValidacion`.
- **[utils-json]** Utilidades para parseo/serialización segura de campos JSON (p.e. `layout_config`, `permissions`, `event_config`), con defaults sólidos.

## Fase 3 — Formularios controlados y validaciones
- **[inputs-controlados]** Garantizar que `value/checked` nunca sea `undefined`. Usar `??` y defaults en state y bindings.
- **[validación-front]** Integrar esquema tipado (Zod o Yup si ya está aprobado) para validar previo a submit.
- **[errores-inertia]** Mapear `errors` de Inertia al formulario limpiamente. Limpiar error al escribir.
- **[checkboxes/selects]** Asegurar checkboxes `checked={…}` y selects con `value` siempre definido.

## Fase 4 — Filtros, búsqueda y paginación
- **[FilterBar/PaginationControls]** Centralizar filtros en `FilterBar`, paginación en `PaginationControls` (ya existen).
- **[query-params]** Sincronizar filtros con query params (`URLSearchParams`) y recordar estado.
- **[Inertia]** Usar `router.get` con `preserveState`, `preserveScroll`. En listados pesados, `only` para renders parciales.
- **[debounce]** Añadir debounce en búsqueda y carga de opciones remotas.

## Fase 5 — UX de modales y relaciones
- **[modal-agregar-hijos]** En FormularioDinamico, mejorar modal de “Agregar Componente”:
  - Skeletons de carga, estados `loading/error/empty`.
  - Búsqueda con debounce y contador de resultados.
  - Deshabilitar botón mientras adjunta; toasts consistentes.
- **[editor-data-source]** En ComponenteDinamico, mejorar UX de `data_source` (añadir/eliminar, validación instantánea, placeholders claros).
- **[preview]** En ComponenteValidacion, vista previa de reglas aplicadas al componente.

## Fase 6 — Accesibilidad (a11y)
- **[roles/aria]** Roles y `aria-*` correctos en modales, botones y controles.
- **[foco]** Trampas de foco en modales, retorno de foco al cerrar, navegación por teclado.
- **[labels]** `label htmlFor` consistente y textos auxiliares legibles.

## Fase 7 — Rendimiento
- **[memo]** `useMemo`/`useCallback` para listas grandes y handlers.
- **[split]** Split de código en páginas pesadas (lazy + suspense si aplica).
- **[peticiones]** Peticiones parciales de Inertia (`only`) y `preserveState`.
- **[listas]** Si crece el dataset, considerar virtualización o paginación lado servidor estricta.

## Fase 8 — Pruebas
- **[unitarias]** Tests de componentes UI (React Testing Library) para FilterBar, PaginationControls, formularios.
- **[integración]** Flujos críticos: crear/editar, adjuntar hijo, validar reglas, manejo de errores Inertia.
- **[tipos]** Tests de utilidades (parseo JSON, defaults) para evitar `undefined`.

## Fase 9 — Documentación
- **[guía-rápida]** README corto en cada módulo:
  - Patrones de layout.
  - Uso de tipos y utils.
  - Ejemplos de FilterBar/PaginationControls/Toasts.
- **[contribución]** Lista de verificación UX antes de PR.

# Aplicación por módulo

- **FormularioDinamico**
  - Estandarizar lista + panel lateral (ya avanzado).
  - Inputs controlados en edición y JSON (layout/permissions) con defaults.
  - Modal de agregar componente con debounce, skeleton y toasts comunes.

- **ComponenteDinamico**
  - Listado con el card estándar y paddings (ya aplicado).
  - Crear/Editar con formularios tipados; mejorar editor de `data_source`.
  - Botones de acciones con `ActionButtons` y confirmación consistente.

- **ComponenteValidacion**
  - UI de reglas: chips/checkboxes/selects con ayudas contextuales.
  - Vista previa de validación aplicada a un componente ejemplo.
  - Persistencia tipada y serialización/parseo seguro.

# Mejores prácticas Inertia + React
- **router.get/post/put/delete** con `preserveState`, `preserveScroll` y `onError/onFinish`.
- **Partial reload** con `only: ['data','meta']` en listados.
- **Progresos** aprovechar `createInertiaApp.progress`, feedback visual y toasts.
- **Estados locales** con `useState` y recordar filtro por URL. Evitar `window.location.reload()` salvo toggle trivial; preferir recargas parciales.

# Componentes existentes a reutilizar
- **Layout**: `AppLayout`.
- **Barras/acciones**: `FilterBar`, `PaginationControls`, `ActionButtons`.
- **Listas**: `ComponentList`.
- **Feedback**: toasts unificados (centralizar si hay variantes duplicadas).
- **Utils**: crear `parseJsonSafe`, normalizadores de defaults.

# Roadmap sugerido (2–3 sprints)
- **Sprint 1**: Fases 1–3 (UI base, tipos, formularios controlados).
- **Sprint 2**: Fases 4–6 (filtros/paginación, modales, accesibilidad).
- **Sprint 3**: Fases 7–9 (rendimiento, pruebas, docs).

# Tareas planificadas (seguimiento)
- He creado un plan en la lista de tareas con los entregables por fase y por módulo.
- Puedo empezar por Fase 1–3 en ComponenteDinamico y FormularioDinamico, y luego extender a ComponenteValidacion.

Resumen: Definí un plan por fases para estandarizar UI/UX, fortalecer tipado y formularios controlados, optimizar navegación con Inertia y preparar pruebas y documentación. ¿Quieres que inicie por Fase 1–3 en un módulo específico primero?