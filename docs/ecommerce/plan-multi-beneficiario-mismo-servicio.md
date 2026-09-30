# Plan — Multi-beneficiario mismo servicio (flujo secuencial)

**Estado:** F0–F3 listos (API + Mercurio); F4 pendiente de QA manual  
**Fecha:** 2026-09-21 (actualizado)  
**Repos:** Mercurio (`comfaca-enlinea/www`) + API Subsidio (`comfaca-api/api-clisisu`)

## Requisito

N beneficiarios para el **mismo** `codser` / `numero`, 1 factura / 1 `refpago`, N líneas `servi233`.

### UX secuencial (decisión)

1. Beneficiario → servicio → `validar-tarifa` (primera captura).
2. Sumar más beneficiarios eligiendo otro del núcleo (mismo servicio); revalidar con `validar-tarifas`.
3. Acumular ítems → total → un pago.
4. **Cambio de servicio:** revalidar en secuencia cada ítem; los que fallen se excluyen; se recalcula el total.

No multi-select masivo. Una compra = un solo `codser`/`numero` a la vez.

## Checklist de implementación

- [x] Doc: flujo secuencial documentado
- [x] **F0** Contrato `items[].codben`; reglas N ≤ cupos; tarifa por ben
- [x] **F1** API (`api-clisisu`): `GuardarVentaRequest` + loop `servi233` + totales + cupos
- [x] **F2** Mercurio: precompra `items` JSON; checkout suma; `guardarVenta` / webhook envían items
- [x] **F3** UI: `store.items[]`; resumen/total; pendientes/ver-compras con detalle modal
- [ ] **F4** QA: 1 ben, 2+, cupo, ya compró, pago OK/fail

## Alineación Mercurio ↔ API

| Capa | Contrato |
| --- | --- |
| Request | `items[].codben` (+ opcional `nombre`/`valser` en Mercurio); `codben` singular = legacy |
| `guardar-venta` | Mercurio y webhook envían `items` + `codben` del primero |
| Persistencia API | N filas `servi233` (`sec` 1..N), totales sumados en `servi232`/`servi250` |

## Modelo objetivo

| Pieza | Comportamiento |
| --- | --- |
| Contrato | `items[].codben` opcional; `codben` singular = legacy 1 ítem |
| `servi233` | N filas `sec` 1..N |
| Totales | Suma de `valser` / `valsub` por beneficiario |
| Cupos | `N ≤ cupos_disponibles` |
| Precompra | `items` JSON + `valor` = suma |
| ePayco | Un `precompra_id` / un pago |

## Archivos clave

### Mercurio

- `public/src/Mercurio/Ecommerce/{beneficiarios,store,venta,tarifa,panelCompra,pago}.js`
- `public/src/Mercurio/ComprasPendientes/*`
- `resources/views/mercurio/ecommerce/{index,pendientes,ver_compras}.blade.php`
- `app/Models/PrecompraServicio.php` (+ migración `items`)
- `app/Http/Controllers/Mercurio/EcommerceController.php`
- `app/Services/Ecommerce/EpaycoConfirmationService.php`

### API

- `app/Http/Requests/GuardarVentaRequest.php`
- `app/Http/Controllers/MovilController.php` (`guardarVenta`)

## Riesgos

- Tarifas distintas por edad → total = suma de `valser` individuales.
- App móvil: no romper `codben` singular.
- `valmes='S'`: validar por cada `codben`.
