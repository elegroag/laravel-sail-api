# Conciliación: precompras pagadas vs ventas Subsidio

> **Última revisión:** 2026-11-09  
> **Audiencia:** operadores Cajas y desarrolladores del módulo ecommerce.

Documento relacionado: [pasarela-pago-epayco.md](./pasarela-pago-epayco.md).

---

## 1. Objetivo

Identificar diferencias entre:

1. Precompras locales en estado **pagado (`PA`)** en `precompras_servicios`.
2. Ventas registradas en Subsidio (`servi232` + `servi250.refpago`).

Para marcar `CONCILIADA` / `VALOR_DIFERENTE` deben cumplirse:

```
1) servi250.refpago  ===  precompras_servicios.ref_payco
2) existe epayco_transacciones.ref_payco igual, con esa misma precompra_id
3) servi232.estado === 'A' (venta aprobada)
```

Si la venta solo coincide con una `ref_payco` de `epayco_transacciones` distinta a la de la precompra, **no** se concilia.

Si venta y precompra coinciden pero falta la tx ePayco con esa ref → `SIN_TX_EPAYCO`.  
Si refs OK pero `venta.estado != 'A'` → `VENTA_NO_APROBADA`.

En el reporte, `ref_payco` prioriza el de la venta (`servi250.refpago`); `ref_payco_precompra` muestra el valor en `precompras_servicios`.

---

## 2. Estados de conciliación

| Código | Significado | Acción |
| ------ | ----------- | ------ |
| `CONCILIADA` | PA con venta (misma `ref_payco`), tx ePayco con esa ref, y valores iguales (±0.01) | Ninguna |
| `PAGADA_SIN_VENTA` | PA sin venta cuya `ref_payco` coincida | Botón **Registrar en Subsidio** (`admservicios.registrar-subsidio/{id}`) |
| `VALOR_DIFERENTE` | Cumple ambas refs pero el valor no coincide | Solo reporte (revisión manual) |
| `VENTA_SIN_PRECOMPRA_PA` | Venta con refpago sin precompra PA (puede existir en otro estado) | Solo reporte |
| `ACEPTADA_EPAYCO_NO_PA` | Precompra **no PA** (excluye `VE`/`DV`) con `epayco_transacciones.cod_estado = 1` (filtro por `fecha_precompra`) | Revisar / **Registrar en Subsidio** si no hay venta |
| `SIN_TX_EPAYCO` | Venta y precompra coinciden en `ref_payco`, pero no hay `epayco_transacciones` con esa ref | Solo reporte |
| `VENTA_NO_APROBADA` | PA con venta (refs OK) pero `servi232.estado != 'A'` | Solo reporte |

**Exclusiones:** precompras en estado **Vencida (`VE`)** o **Devolución (`DV`)** no entran al universo de conciliación. Las ventas cuya `refpago` apunta a una precompra `VE`/`DV` **y** además `servi232.estado != 'A'` se omiten (no salen como `VENTA_SIN_PRECOMPRA_PA`). Si la venta está `A`, sí se reporta como huérfana (sin enlazar la precompra `VE`/`DV`).

---

## 3. Endpoint API Subsidio

Ruta: `POST /api/movil/conciliacion-ventas`  
Controlador: `MovilController::conciliacionVentas` (comfaca-api).

Parámetros:

- `fecha_inicio`, `fecha_fin` (`Y-m-d`, obligatorio)
- `page` (default 1), `per_page` (default 500, máx. 500)

Respuesta: `flag`, `total`, `page`, `per_page`, `paginas`, `data[]` con `marca`, `documento`, `fecha`, `hora`, `estado`, `valpago`, `cedtra_titular`, `refpago`, `valor_forma_pago`, `items_count`.

La consulta filtra primero por `servi232.fecha` (indexable) y exige `refpago` no vacío.

---

## 4. Módulo Cajas (enlinea)

- URL: `/cajas/conciliacion-servicios/index`
- Controlador: `ConciliacionServiciosController`
- Servicio: `App\Services\Ecommerce\ConciliacionServiciosService`
- Bundle JS: `cajas/build/ConciliacionServicios.js` (fuente `public/src/Cajas/ConciliacionServicios/main.js`)

Ventana de comparación:

- Precompras: `fecha_pago` dentro del rango elegido.
- Subsidio: mismo rango **ampliado ±1 día** (desfases de medianoche).

### Menú

Registrar el ítem en el menú administrable de Cajas (pantalla de menú existente), por ejemplo:

- Título: Conciliación servicios
- Ruta / controlador: `conciliacion-servicios/index`

No hay migración automática del menú.

### Build frontend

```bash
cd www/public
APP=cajas MODULE=ConciliacionServicios npx vite build --config vite.config.js
```

---

## 5. Procedimiento operativo

1. Abrir Conciliación servicios e indicar rango de fechas.
2. Pulsar **Conciliar** y revisar el resumen por estado.
3. Para cada `PAGADA_SIN_VENTA`, usar **Registrar en Subsidio** (valida ePayco + `guardar-venta`).
4. Exportar CSV si se requiere auditoría.
5. Los casos `VALOR_DIFERENTE` y `VENTA_SIN_PRECOMPRA_PA` se investigan a mano (Admservicios detalle / SQL Subsidio).

---

## 6. Fuera de alcance

- No se concilia contra ePayco (solo Mercurio vs Subsidio).
- No se corrigen automáticamente diferencias de valor ni ventas huérfanas.
- No se modifica el flujo de pago ni `guardar-venta`.
