# Devoluciones de servicios (bandeja Cajas)

> **Última revisión:** 2026-11-09  
> **Audiencia:** operadores Cajas y desarrolladores ecommerce.

## Objetivo

Listar y gestionar las solicitudes de devolución creadas por afiliados en Mercurio
cuando una precompra queda **vencida (`VE`)** (pago aprobado sin registro en Subsidio).

## Ruta

| Método | URL | Acción |
| ------ | --- | ------ |
| GET | `/cajas/devolucion-servicios/index` | Bandeja |
| POST | `/cajas/devolucion-servicios/buscar` | Listado filtrado |
| POST | `/cajas/devolucion-servicios/{id}/en-tramite` | SOLICITADA → EN_TRAMITE |
| POST | `/cajas/devolucion-servicios/{id}/aprobar` | → APROBADO + precompra `DV` |
| POST | `/cajas/devolucion-servicios/{id}/rechazar` | → RECHAZADA |

## Menú

Pendiente de registrar en el menú de Cajas (igual que conciliación):

- **URL:** `/cajas/devolucion-servicios/index`
- **Etiqueta sugerida:** Devoluciones servicios

## Estados

| Código | Significado |
| ------ | ----------- |
| `SOLICITADA` | Creada por el afiliado en Mercurio |
| `EN_TRAMITE` | Tomada por operador |
| `APROBADO` | Aprobada; precompra pasa a `DV` |
| `RECHAZADA` | Rechazada; precompra sigue `VE` |
