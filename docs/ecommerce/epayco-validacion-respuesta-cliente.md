# Validación ePayco y respuesta al cliente

Flujo de validación post-pago (API `reference` + webhook), qué decide el
backend en cada caso y qué SweetAlert ve el usuario en Mercurio Ecommerce.

> **Última revisión:** 2026-09-02  
> **Incidente motivador:** reconsulta a ePayco devolvió
> `{ "status": "error", "description": "Error de datos o conexión verifique de nuevo." }`
> tras un webhook ya aprobado; el cliente veía rechazo falso aunque la venta
> estuviera registrada.

Documentos relacionados:
- [pasarela-pago-epayco.md](./pasarela-pago-epayco.md) — flujo completo.
- [epayco-webhook-confirmation.md](./epayco-webhook-confirmation.md) — webhook.
- [epayco-estado-transaccion-db.md](./epayco-estado-transaccion-db.md) — auditoría BD.
- [pendientes-compra-en-linea.md](./pendientes-compra-en-linea.md) — backlog a implementar.

---

## 1. Quién llama a la API de validación

Todas las consultas a
`GET https://secure.epayco.co/validation/v1/reference/{ref_payco}` pasan por
[`ApiEpayco::validarReferencia()`](../app/Services/Api/ApiEpayco.php).

| Origen | Ruta Laravel | Controller | ¿Llama `validarReferencia`? |
| ------ | ------------ | ---------- | --------------------------- |
| Frontend post-`response` | `POST /mercurio/servicios/validar-pago-epayco` | `validarPagoEpayco` | **Sí** |
| Frontend tras pago aprobado | `POST /mercurio/servicios/guardar-venta` | `guardarVenta` | **Sí** (doble check) |
| ePayco server-to-server | `POST /api/epayco/confirmation` | `EpaycoConfirmationService` | **No** — usa el payload del webhook |

El JS del catálogo (`epayco.js`, `venta.js`) **no** consulta ePayco directo:
solo pega a las rutas Mercurio.

---

## 2. Secuencia típica (cliente vuelve al sitio)

```
ePayco redirect → response URL (?ref_payco=…)
       │
       ▼
verificarRespuestaEpayco()          [epayco.js]
       │  SweetAlert: "Verificando pago…"
       ▼
POST validar-pago-epayco
       │  → validarReferencia()
       ├─ aprobado → SweetAlert "Pago aprobado" + "Guardando la venta…"
       │                    │
       │                    ▼
       │              POST guardar-venta
       │                    │  → validarReferencia() otra vez
       │                    └─ éxito / ya_pagada / error (ver §4)
       │
       └─ no aprobado / error API → SweetAlert de fallo (salvo excepción PA)
```

En paralelo (o antes), el **webhook** puede haber dejado la precompra en
`PA` y haber llamado a Subsidio (`venta_registrada: true`).

---

## 3. Qué hace `validarReferencia`

### 3.1 Respuesta de transacción OK

Envelope típico: HTTP 200 + `data` con campos `x_*` (p. ej.
`x_cod_transaction_state`, `x_response`). Se normaliza a:

```php
[
  'success' => true,
  'data' => [
    'aprobado'   => ($codEstado === 1),
    'cod_estado' => $codEstado,
    'respuesta'  => …,
    'motivo'     => …,
    'ref_payco'  => …,
    'payload_raw'=> $tx,
    // …
  ],
]
```

También acepta alias (`x_cod_respuesta`, `x_respuesta`, `x_id_factura`, etc.).

### 3.2 Error de datos / conexión de ePayco (HTTP 200 engañoso)

ePayco a veces responde **HTTP 200** con un envelope de error, por ejemplo:

```json
{
  "status": false,
  "message": "Error de datos o conexión.",
  "data": {
    "status": "error",
    "description": "Error de datos o conexión verifique de nuevo."
  }
}
```

o plano:

```json
{
  "status": "error",
  "description": "Error de datos o conexión verifique de nuevo."
}
```

**Antes:** el código veía `data` presente, no encontraba
`x_cod_transaction_state` → inventaba `cod_estado: 0` / `"Sin respuesta"` /
`aprobado: false` (falso “pago no aprobado”).

**Ahora:** `esRespuestaErrorEpayco()` / `mensajeErrorEpayco()` detectan el
envelope y devuelven:

```php
[
  'success' => false,
  'errors'  => 'Error de datos o conexión verifique de nuevo.', // o message/description
]
```

También se trata como error: excepción de red, HTTP ≠ 200, JSON inválido,
`data` sin códigos de transacción.

### 3.3 Webhook vs API (misma `ref_payco`)

Misma transacción aprobada: mismo `x_ref_payco`, `x_transaction_id`,
`x_cod_transaction_state = 1`, misma `x_signature`. Diferencias habituales:

| Aspecto | Webhook | API `reference` |
| ------- | ------- | ----------------- |
| Tipos | Casi todo string | Números donde aplica |
| PII | Completos | Enmascarados |
| Extra | `x_payment_date`, `is_processable`, … | `x_type_payment`, `x_extra5_epayco`, … |

No cambia el criterio de aprobación (`cod_estado === 1`).

---

## 4. Matriz: backend → respuesta al cliente

UI en [`epayco.js`](../public/src/Mercurio/Ecommerce/epayco.js) y
[`venta.js`](../public/src/Mercurio/Ecommerce/venta.js) (SweetAlert2).

### 4.1 Query string / redirect (antes de llamar API)

| Condición | SweetAlert | Notas |
| --------- | ---------- | ----- |
| `ref_payco` presente y `x_cod_transaction_state` en URL ≠ 1 | **Pago no completado** (warning) — estado, ref, motivo; “La venta no fue registrada.” | No llama a `validarReferencia` |
| `ref_payco` presente y estado URL 1 o ausente | **Verificando pago…** → AJAX `validar-pago-epayco` | |

### 4.2 `POST validar-pago-epayco`

| Caso | Backend | SweetAlert cliente |
| ---- | ------- | ------------------ |
| API OK, `cod_estado === 1` | `success: true`, `aprobado: true` | **Pago aprobado** + “Guardando la venta…” → llama `guardarVenta` |
| API OK, `cod_estado !== 1` | `success: true`, `aprobado: false` | **Pago no completado** (warning) |
| API error (`success: false`) y precompra **ya `PA`** (webhook) | Excepción: `success: true`, `aprobado: true`, `ya_pagada: true` | **Pago aprobado** → sigue a `guardarVenta` |
| API error y precompra **no** `PA` | `success: false`, `message` = texto ePayco / error | **No se pudo verificar el pago** (error) + message + “La venta no fue registrada.” |
| Fallo AJAX (red/HTTP) | — | **Error de conexion** — “No se pudo verificar el pago con ePayco.” |

### 4.3 `POST guardar-venta`

| Caso | Backend | SweetAlert cliente |
| ---- | ------- | ------------------ |
| API OK, aprobado, precompra no PA | Llama Subsidio `guardar-venta` | **Compra exitosa** (+ message) |
| API OK, aprobado, precompra **ya `PA`** | No reenvía a Subsidio | **Compra exitosa** — “Venta ya registrada previamente” |
| API error (`success: false`) y precompra **ya `PA`** | Excepción: `success: true`, `ya_pagada`, `validacion_omitida` | **Compra exitosa** — “Venta ya registrada previamente” |
| API OK pero no aprobado, precompra **ya `PA`** | Misma excepción de idempotencia | **Compra exitosa** — “Venta ya registrada previamente” |
| API error / no aprobado y precompra **no** `PA` | `success: false`, `message` | **Error** — texto del backend (p. ej. mensaje ePayco o “El pago no fue aprobado…”) |
| Fallo AJAX | — | **Error** — “Error de conexion al guardar la venta” |
| Faltan datos de sesión (cedtra/codser/numero) | No llama backend | **Error** — “No se encontraron los datos del servicio…” |

### 4.4 Webhook (sin UI de cliente)

No muestra SweetAlert. Efectos:

1. Valida `x_signature`.
2. Audita `epayco_transacciones` (`origen=webhook`).
3. Actualiza precompra; si `cod_estado === 1` y aún no `PA` → registra venta en Subsidio.
4. Si ya estaba `PA` → no reenvía a Subsidio.

Es la fuente de verdad cuando el cliente no vuelve o cuando la API
`reference` falla en la 2.ª consulta.

---

## 5. Excepciones de idempotencia (anti rechazo falso)

Objetivo: si el webhook (u otra validación previa) ya dejó la precompra en
`PA`, **no** mostrar al cliente un rechazo por fallo/intermitencia de la
API `reference`.

### 5.1 En `validarPagoEpayco`

Si `validarReferencia` falla **y** `resolverPrecompraPorRefOId` encuentra
precompra `isPagado()` → responde como pago aprobado (`ya_pagada: true`)
para que el frontend continúe a `guardarVenta`.

### 5.2 En `guardarVenta`

1. Resuelve precompra **antes** de interpretar el resultado de ePayco.
2. Si `validarReferencia` falla **o** reporta no aprobado, pero `yaPagada`
   y no hay `FORCE_APPROVED` → `success: true` / “Venta ya registrada
   previamente” (`validacion_omitida` cuando aplica).
3. Si está `PA` y la API sí aprueba → omite Subsidio (evita doble
   `guardar-venta`).

`EPAYCO_FORCE_APPROVED` (solo non-prod) puede forzar reenvío a Subsidio
aunque ya esté `PA` (QA).

### 5.3 Caso real documentado (precompra 67)

| Hora (aprox.) | Evento | Resultado |
| ------------- | ------ | --------- |
| T0 | Webhook `ref` 384300771, estado 2 | Intento PSE abandonado (otra ref) |
| T1 | Webhook `ref` 384302186, estado 1 | `venta_registrada: true`, precompra `PA` |
| T2 | `validar-pago-epayco` → API OK estado 1 | Cliente: “Pago aprobado” |
| T3 | `guardar-venta` → API error conexión | **Antes:** Error / “pago no aprobado”. **Ahora:** “Compra exitosa” / ya registrada |

---

## 6. Código de referencia

| Pieza | Archivo |
| ----- | ------- |
| Detección error ePayco + normalización | [`ApiEpayco.php`](../app/Services/Api/ApiEpayco.php) |
| Excepciones PA en validar / guardar | [`EcommerceController.php`](../app/Http/Controllers/Mercurio/EcommerceController.php) |
| UI validación | [`epayco.js`](../public/src/Mercurio/Ecommerce/epayco.js) |
| UI guardar venta | [`venta.js`](../public/src/Mercurio/Ecommerce/venta.js) |
| Webhook | [`EpaycoConfirmationService.php`](../app/Services/Ecommerce/EpaycoConfirmationService.php) |
| Tests detección error | [`ApiEpaycoValidarReferenciaTest.php`](../tests/Unit/Ecommerce/ApiEpaycoValidarReferenciaTest.php) |

---

## 7. Checklist de soporte

- [ ] ¿Hay fila `origen=webhook` con `cod_estado=1` para esa `ref_payco`?
- [ ] ¿La precompra está `PA`? Si sí y el cliente vio error antiguo → fue el bug de reconsulta; con el fix debería ver éxito.
- [ ] ¿El log muestra `fallo validacion ePayco pero precompra ya PA` o `validacion_omitida`?
- [ ] ¿`payload_json` de una fila `validacion` tiene `status: error` / description de conexión? → fallo intermitente de ePayco, no rechazo de cobro.
- [ ] No confundir **dos `ref_payco`** sobre el mismo `invoice`/`extra4` (reintento PSE): un webhook puede ser estado 2 y otro estado 1.
