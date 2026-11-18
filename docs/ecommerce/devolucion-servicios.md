# Devoluciones de servicios (bandeja Cajas)

> **Última revisión:** 2026-11-18  
> **Audiencia:** operadores Cajas y desarrolladores ecommerce.

## Objetivo

Listar y gestionar las solicitudes de devolución creadas por afiliados en Mercurio
cuando una precompra queda **vencida (`VE`)** (pago aprobado sin registro en Subsidio).

## Stack

Inertia + React (`resources/js/pages/Cajas/DevolucionServicios/Index.tsx`).
Filtros y paginación por query string en `GET /index`.

## Ruta

| Método | URL | Acción |
| ------ | --- | ------ |
| GET | `/cajas/devolucion-servicios/index` | Bandeja (Inertia) |
| POST | `/cajas/devolucion-servicios/{id}/en-tramite` | SOLICITADA → EN_TRAMITE |
| POST | `/cajas/devolucion-servicios/{id}/aprobar` | → APROBADO + precompra `DV` |
| POST | `/cajas/devolucion-servicios/{id}/rechazar` | → RECHAZADA |

Query params del index: `estado`, `documento`, `q`, `per_page`, `page`.

## Menú

Pendiente de registrar en el menú de Cajas (igual que conciliación):

- **URL:** `/cajas/devolucion-servicios/index`
- **Etiqueta sugerida:** Devoluciones servicios

Prefijo Inertia ya registrado en `CajasNavMenu`.

## Estados

| Código | Significado |
| ------ | ----------- |
| `SOLICITADA` | Creada por el afiliado en Mercurio |
| `EN_TRAMITE` | Tomada por operador |
| `APROBADO` | Aprobada; precompra pasa a `DV` |
| `RECHAZADA` | Rechazada; precompra sigue `VE` |
