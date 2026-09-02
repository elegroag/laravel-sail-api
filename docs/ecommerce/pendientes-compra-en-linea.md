# Pendientes — compra en línea (Mercurio Ecommerce / ePayco)

Lista de especificaciones **aún por implementar o verificar** en el proceso de
compra digital. Lo ya aplicado (webhook, auditoría, abandono, idempotencia
`PA`, detección error API reference, etc.) no se repite aquí; ver
[analisis-mejoras-epayco-precompras.md](./analisis-mejoras-epayco-precompras.md).

> **Fecha:** 2026-09-02  
> **Estado:** backlog activo  
> **Audiencia:** desarrollo, operaciones, QA

Documentos relacionados:
- [pasarela-pago-epayco.md](./pasarela-pago-epayco.md)
- [epayco-validacion-respuesta-cliente.md](./epayco-validacion-respuesta-cliente.md)
- [epayco-webhook-confirmation.md](./epayco-webhook-confirmation.md)
- [epayco-modal-control.md](./epayco-modal-control.md)
- [precompras-job-abandonadas.md](./precompras-job-abandonadas.md)

---

## Cómo usar este documento

| Campo | Significado |
| ----- | ----------- |
| **ID** | Identificador estable (`P-xx`) |
| **Prioridad** | P0 operativa · P1 corto plazo · P2 mediano · P3 consciente |
| **Tipo** | `ops` (verificar/desplegar) · `dev` (código) · `qa` (pruebas) |
| **Estado** | `pendiente` / `en curso` / `hecho` |

Al completar un ítem: marcar `hecho`, fecha, y mover resumen a
`analisis-mejoras-epayco-precompras.md` §4/§5.

---

## A. Operación / despliegue (P0)

Verificar en **cada entorno** (sobre todo producción). No son features; sin
esto el flujo híbrido cliente+webhook queda incompleto.

### P-01 — Credenciales de firma del webhook

| | |
| --- | --- |
| **Prioridad** | P0 |
| **Tipo** | ops |
| **Estado** | hecho (2026-09-02) |

**Especificación**
- Configurar `EPAYCO_CUSTOMER_ID` (o `EPAYCO_P_CUST_ID_CLIENTE`) y
  `EPAYCO_P_KEY` en `.env`.
- Sin ellas el webhook responde `503` y no confirma pagos server-side.
- El código lee `config('app.epayco.customer_id')` con fallback
  `EPAYCO_P_CUST_ID_CLIENTE` → `EPAYCO_CUSTOMER_ID`, y
  `config('app.epayco.p_key')` ← `EPAYCO_P_KEY`.

**Criterio de aceptación**
- [x] Variables presentes en el `.env` del proyecto
      (`EPAYCO_P_CUST_ID_CLIENTE` + `EPAYCO_P_KEY`).
- [x] Webhook en uso real (confirmaciones `200` / precompras a `PA`
      observadas en operación).
- [ ] Revalidar que el **mismo par** esté en el `.env` de cada host
      productivo al desplegar (no asumir solo el entorno local).

---

### P-02 — URL pública HTTPS del webhook

| | |
| --- | --- |
| **Prioridad** | P0 |
| **Tipo** | ops |
| **Estado** | pendiente |

**Especificación**
- `POST {APP_URL}/api/epayco/confirmation` debe ser alcanzable desde
  Internet (HTTPS válido).
- Registrada / coherente con la URL `confirmation` enviada en el checkout.

**Criterio de aceptación**
- [ ] ePayco panel / logs muestran deliveries `200`.
- [ ] Pago sandbox con navegador cerrado → precompra pasa a `PA`.

---

### P-03 — Migración `epayco_transacciones`

| | |
| --- | --- |
| **Prioridad** | P0 |
| **Tipo** | ops |
| **Estado** | hecho (2026-09-02) |

**Especificación**
- Ejecutar migración
  `2026_08_14_160000_create_epayco_transacciones_table` (y dependientes)
  en el entorno.

**Criterio de aceptación**
- [x] Tabla aplicada en **producción** — no requiere más acciones.
- [x] Tras pagos aparecen filas `origen=webhook` y/o `validacion`
      (operación confirmada).

---

### P-04 — Scheduler / job de precompras abandonadas

| | |
| --- | --- |
| **Prioridad** | P0 |
| **Tipo** | ops |
| **Estado** | pendiente |

**Especificación**
- Cron o `schedule:work` activo para `precompras:marcar-abandonadas`
  (típicamente 02:00).
- Guía: [precompras-job-abandonadas.md](./precompras-job-abandonadas.md).

**Criterio de aceptación**
- [ ] `php artisan schedule:list` muestra el comando.
- [ ] Dry-run / run en staging marca `PE` antiguas → `AB`.

---

### P-05 — `EPAYCO_FORCE_APPROVED=false` en producción

| | |
| --- | --- |
| **Prioridad** | P0 |
| **Tipo** | ops |
| **Estado** | pendiente |

**Especificación**
- La bandera solo es válida en non-prod. En producción debe estar `false`
  o ausente.

**Criterio de aceptación**
- [ ] Confirmado en `.env` / config cache de prod.
- [ ] Un pago rechazado real **no** se fuerza a aprobado vía API.

---

### P-06 — Despliegue de bundles JS (Ecommerce / ComprasPendientes)

| | |
| --- | --- |
| **Prioridad** | P0 |
| **Tipo** | ops |
| **Estado** | pendiente |

**Especificación**
- Tras cambios en `public/src/Mercurio/Ecommerce/` (p. ej. mensajes) y
  backend de validación, regenerar y desplegar bundles del módulo.
- Incluye el fix de error API + idempotencia `PA` (backend) + docs.

**Criterio de aceptación**
- [ ] Assets desplegados en el host que sirve Mercurio.
- [ ] Escenario: webhook `PA` + fallo simulado de `reference` → cliente
      ve “Compra exitosa” / ya registrada (no rechazo falso).

---

### P-07 — Prueba end-to-end WebView Flutter Android

| | |
| --- | --- |
| **Prioridad** | P0 |
| **Tipo** | qa |
| **Estado** | pendiente |

**Especificación**
- Recorrer pago en app Android (Smart Checkout v2 `type: standard`).
- Casos: pago OK, rechazo/abandono PSE, cierre modal, cierre app a mitad.

**Criterio de aceptación**
- [ ] Pago OK → `PA` + venta (webhook y/o response).
- [ ] Cierre sin pagar → `AB` o job posterior.
- [ ] Sin crash / loop de modal documentado en [epayco-modal-control.md](./epayco-modal-control.md).

---

## B. Desarrollo corto plazo (P1)

### P-08 — TLS configurable en `APIClient` (Subsidio y otros)

| | |
| --- | --- |
| **Prioridad** | P1 |
| **Tipo** | dev |
| **Estado** | pendiente |

**Especificación**
- Hoy [`APIClient`](../app/Library/APIClient/APIClient.php) usa
  `withoutVerifying()` de forma fija.
- Aplicar el mismo patrón que `ApiEpayco` (`*_HTTP_VERIFY_SSL`, default
  `true` en prod).
- Afecta llamadas a Subsidio (`guardar-venta`, tarifas, etc.) en el flujo
  de compra.

**Criterio de aceptación**
- [ ] Flag en `.env.example` + `config`.
- [ ] Prod con verify ON; QA puede desactivar si hay proxy/CA roto.
- [ ] `guardar-venta` a Subsidio sigue funcionando con TLS verificado.

---

### P-09 — Alertas operativas del webhook

| | |
| --- | --- |
| **Prioridad** | P1 |
| **Tipo** | dev |
| **Estado** | pendiente |

**Especificación**
- Notificar (email / Slack / canal interno) cuando:
  - Webhook responde `400` (firma inválida).
  - Webhook responde `503` (credenciales de firma faltantes).
- Evitar spam: rate-limit o agregación por ventana (p. ej. 1 alerta / 15 min
  por tipo).

**Criterio de aceptación**
- [ ] Alerta disparada en staging con firma inválida a propósito.
- [ ] Alerta con credenciales vacías.
- [ ] Documentado en [epayco-webhook-confirmation.md](./epayco-webhook-confirmation.md).

---

### P-10 — Tests Feature del webhook y validación

| | |
| --- | --- |
| **Prioridad** | P1 |
| **Tipo** | dev / qa |
| **Estado** | pendiente |

**Especificación**
Cubrir con Feature / Http::fake (o DB de prueba):

1. Webhook firma OK → `PA` + fila `origen=webhook`.
2. Webhook firma KO → `400`, sin cambio de estado.
3. Idempotencia: segunda notification con precompra ya `PA` no reenvía
   Subsidio.
4. Resolución de precompra por `extra4` / `x_id_invoice`.
5. `validarReferencia` con envelope error conexión → `success: false`.
6. `guardarVenta` con precompra `PA` + API error → `success: true` /
   `ya_pagada`.

**Criterio de aceptación**
- [ ] Suite verde en CI / `php artisan test --filter=Epayco` (o equivalente).
- [ ] No dependen de red real a ePayco.

---

## C. Desarrollo mediano plazo (P2)

### P-11 — Whitelist / blacklist de medios de pago

| | |
| --- | --- |
| **Prioridad** | P2 |
| **Tipo** | dev |
| **Estado** | pendiente |

**Especificación**
- Usar parámetros ePayco `methods` / `methodsDisable` en el open del
  checkout (v1 y v2 / Apify session según aplique).
- Configurable por `.env` o config (lista de franquicias/medios permitidos).
- Detalle: [epayco-modal-control.md](./epayco-modal-control.md) §3.

**Criterio de aceptación**
- [ ] En QA solo aparecen los medios configurados (p. ej. PSE + Nequi).
- [ ] Documentado en `.env.example` y pasarela.

---

### P-12 — Cola asíncrona para `guardar-venta` desde el webhook

| | |
| --- | --- |
| **Prioridad** | P2 |
| **Tipo** | dev |
| **Estado** | pendiente |

**Especificación**
- Si la llamada a Subsidio desde el webhook supera ~2–3 s, ePayco puede
  reintentar el `confirmation`.
- Encolar un Job (`ShouldQueue`) que registre la venta tras marcar
  intención / estado local, y responder `200` rápido al webhook.
- Mantener idempotencia: no duplicar venta si ya `PA` o job ya corrió.

**Criterio de aceptación**
- [ ] Webhook responde en &lt; 1–2 s aunque Subsidio tarde.
- [ ] Fallo del job reintentable; alerta si agota intentos.
- [ ] Sin doble venta en reintentos ePayco.

---

### P-13 — Cerrar carrera residual cliente + webhook (ambos leen `PE`)

| | |
| --- | --- |
| **Prioridad** | P2 |
| **Tipo** | dev |
| **Estado** | pendiente |

**Especificación**
- Hoy, si `guardarVenta` (cliente) y el webhook leen `PE` a la vez, ambos
  pueden llamar a Subsidio.
- Mitigar con uno o más de:
  - Lock atómico al promover `PE` → `PA` (DB row lock / `update … where
    estado = PE`).
  - Idempotencia explícita en API Subsidio por `ref_payco` / invoice.
  - Flag `venta_enviada_at` en precompra.

**Criterio de aceptación**
- [ ] Test de concurrencia (o simulación) no genera dos ventas.
- [ ] Documentado el mecanismo elegido.

---

### P-14 — UX de reintento cuando falla API y aún no hay `PA`

| | |
| --- | --- |
| **Prioridad** | P2 |
| **Tipo** | dev |
| **Estado** | pendiente |

**Especificación**
- Si `validarReferencia` falla por conexión y la precompra **no** está
  `PA` (webhook lento o ausente), hoy el cliente ve error y “venta no
  registrada”.
- Propuesta:
  - SweetAlert con **Reintentar verificación** (mismo `ref_payco`).
  - O polling corto (2–3 intentos) antes de mostrar error definitivo.
  - Mensaje: el cobro puede estar en proceso; no pedir pagar de nuevo sin
    revisar “Compras pendientes” / soporte.

**Criterio de aceptación**
- [ ] Botón o auto-retry implementado en `epayco.js`.
- [ ] Copy actualizado en
      [epayco-validacion-respuesta-cliente.md](./epayco-validacion-respuesta-cliente.md).

---

## D. Consciente / baja prioridad (P3)

### P-15 — Branding / textos del modal ePayco

| | |
| --- | --- |
| **Prioridad** | P3 |
| **Tipo** | dev |
| **Estado** | pendiente |

**Especificación**
- Parámetros `title`, `titleButtonPay`, etc. (impacto UX bajo; CSS interno
  de ePayco limitado).

---

### P-16 — Validar `x_signature` también en `validarReferencia`

| | |
| --- | --- |
| **Prioridad** | P3 |
| **Tipo** | dev |
| **Estado** | pendiente |

**Especificación**
- Solo si ePayco envía firma de forma fiable en el endpoint `reference`.
- Hoy la confianza del path API es el propio host ePayco; el webhook ya
  valida firma.

---

### P-17 — Campos opcionales de checkout (address, tax, ico)

| | |
| --- | --- |
| **Prioridad** | P3 |
| **Tipo** | dev |
| **Estado** | pendiente |

**Especificación**
- Hoy `tax` / `ico` van en 0; address de facturación puede no enviarse
  completa.
- Evaluar solo si negocio/contabilidad lo exige.

---

## E. Resumen visual

| ID | Título | Prioridad | Tipo | Estado |
| -- | ------ | --------- | ---- | ------ |
| P-01 | Credenciales firma webhook | P0 | ops | hecho |
| P-02 | URL HTTPS webhook | P0 | ops | pendiente |
| P-03 | Migración `epayco_transacciones` | P0 | ops | hecho |
| P-04 | Scheduler job abandonadas | P0 | ops | pendiente |
| P-05 | `FORCE_APPROVED=false` en prod | P0 | ops | pendiente |
| P-06 | Bundles JS desplegados | P0 | ops | pendiente |
| P-07 | E2E WebView Android | P0 | qa | pendiente |
| P-08 | TLS en `APIClient` | P1 | dev | pendiente |
| P-09 | Alertas webhook 400/503 | P1 | dev | pendiente |
| P-10 | Tests Feature ePayco | P1 | dev/qa | pendiente |
| P-11 | Whitelist medios de pago | P2 | dev | pendiente |
| P-12 | Cola async `guardar-venta` webhook | P2 | dev | pendiente |
| P-13 | Lock / idempotencia carrera PE | P2 | dev | pendiente |
| P-14 | UX reintento si API falla sin PA | P2 | dev | pendiente |
| P-15 | Branding modal ePayco | P3 | dev | pendiente |
| P-16 | Firma en `validarReferencia` | P3 | dev | pendiente |
| P-17 | tax/ico/address opcionales | P3 | dev | pendiente |

---

## Orden sugerido de ataque

1. **P-02, P-04 → P-07** (ops/QA restantes; P-01 firma y P-03 migración ya hechos).
2. **P-08, P-09, P-10** (hardening + observabilidad + regresión).
3. **P-13, P-12, P-14** (carrera, latencia webhook, UX error sin PA).
4. **P-11** y luego P3 según negocio.
