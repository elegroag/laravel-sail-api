
# Pasos para implementar enlace y filtro de Validaciones desde Componentes

- **[backend/index] Añadir conteo de validación**
  - **Acción**: En `ComponenteDinamicoController@index`, agregar `withCount('validacion')`.
  - **Motivo**: Poder filtrar y mostrar si un componente ya tiene validación.

- **[backend/index] Filtro has_validation**
  - **Acción**: Aceptar query `has_validation` con valores `'1' | '0' | ''`.
  - **Lógica**:
    - `has_validation=1` → `whereHas('validacion')`.
    - `has_validation=0` → `whereDoesntHave('validacion')`.
    - Vacío → no filtra.
  - **Salida**: Mantener paginación existente. Opcional: exponer `validacion_count` en el `data`.

- **[frontend/Componentes Index] Agregar filtro en FilterBar**
  - **Acción**: Agregar filtro `has_validation` con opciones:
    - “Todos” (valor `''`).
    - “Con validación” (valor `'1'`).
    - “Sin validación” (valor `'0'`).
  - **Lógica**: Incluir `has_validation` en `getQueryParams()` y en todas las llamadas `router.get` de búsqueda, paginación y `per_page`.

- **[frontend/Componentes Index] Botón/enlace “Validaciones”**
  - **Acción**: En cada fila/acción del componente agregar botón “Validaciones”.
  - **Navegación**:
    - Ver listados filtrados: `router.visit('/cajas/componente-validacion?componente_id=' + id)`.
    - Opcional: Atajo a crear validación: `router.visit('/cajas/componente-validacion/create?componente_id=' + id)`.

- **[frontend/Validaciones Index] Aceptar `componente_id`**
  - **Acción**: Leer `componente_id` desde query al cargar.
  - **Lógica**:
    - Si viene, filtrar el listado por ese componente.
    - Mostrar chip “Filtrando por: <componente>” con opción de limpiar filtro.
    - Mantener `preserveState` y paginación.

- **[frontend/Validaciones Create] Preselección**
  - **Acción**: Si llega `componente_id` por query:
    - Preseleccionar en `select` y en el modal picker.
    - Permitir cambiar usando el picker.

- **[UI/UX] Visibilidad**
  - **Acción**: En la lista de Componentes, mostrar un indicador (p. ej., badge) si `validacion_count > 0`.
  - **Motivo**: Rápida identificación de componentes con validación.

- **[pruebas] Validación de flujo**
  - Crear componente SIN validación → botón “Validaciones” → abrir Validaciones/Index filtrado → “Nueva validación” con `componente_id` preseleccionado → guardar → volver a Componentes y verificar badge/contador y filtro “Con validación”.

# Tareas en curso (TODO)
- **Agregar acción/enlace “Validaciones” en Componentes/Index**.
- **Backend: filtro `has_validation` en index**.
- **Frontend: filtro “Con/Sin validación” en Componentes/Index**.
- **Validaciones: aceptar `componente_id` en Index y Create**.

¿Procedo con el backend primero (filtro `has_validation`) y luego el frontend (filtro + botón “Validaciones”)?