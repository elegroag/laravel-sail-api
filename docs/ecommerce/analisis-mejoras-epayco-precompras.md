# Análisis y recomendaciones — mejoras ePayco / precompras (Mercurio)

> **Fecha original:** 2026-08-14  
> **Última revisión:** 2026-08-24  
> **Alcance:** pasarela ePayco y precompras de servicios (webhook, TLS,
> Admservicios, modularización JS, `EPAYCO_FORCE_APPROVED`).  
> **Audiencia:** desarrollo, operaciones y soporte de Cajas/Mercurio.

Documentos relacionados:

- [pasarela-pago-epayco.md](./pasarela-pago-epayco.md)
- [epayco-modal-control.md](./epayco-modal-control.md)
- [epayco-estado-transaccion-db.md](./epayco-estado-transaccion-db.md)
- [epayco-webhook-confirmation.md](./epayco-webhook-confirmation.md)
- [precompras-job-abandonadas.md](./precompras-job-abandonadas.md)

---

## 1. Resumen ejecutivo

Se reforzó el ciclo de vida de la compra digital en tres ejes:

1. **Trazabilidad** — historial append-only de respuestas ePayco + modal de
   detalle en Admservicios.
2. **Resiliencia** — abandono explícito (`onClose` / `onClosed` + job) y
   confirmación server-side (webhook + firma).
3. **Operación / seguridad** — TLS configurable, visibilidad operativa y
   bandera de QA `EPAYCO_FORCE_APPROVED` (solo non-prod).

El flujo no depende solo del navegador: aunque cierre la modal, la pestaña
o la WebView, el backend puede confirmar el pago y auditarlo.

---

## 2. Inventario de cambios aplicados

### 2.1 Auditoría de transacciones (`epayco_transacciones`)

| Pieza | Detalle |
| ----- | ------- |
| Migración | `2026_08_14_160000_create_epayco_transacciones_table.php` |
| Modelo | `EpaycoTransaccion` + `PrecompraServicio::transaccionesEpayco()` |
| Escritura | `validarReferencia` → `origen=validacion`; webhook → `origen=webhook` |
| Payload | Campos clave + `payload_json` crudo |

### 2.2 Abandonos de checkout

| Pieza | Detalle |
| ----- | ------- |
| Estado | `EstadoPrecompra::ABANDONADA` (`AB`) — no retomable |
| Endpoint | `POST /mercurio/servicios/abandonar-precompra` |
| Cliente | `onCloseModal` / `onClosed` en Ecommerce y ComprasPendientes (+ Swal) |
| Salvaguarda | Flag `__epaycoPagoEnValidacion` + delay 2.5s; `AB`→`PA` si el pago llega tarde |

### 2.3 Job de limpieza de `PE` huérfanas

| Pieza | Detalle |
| ----- | ------- |
| Job | `MarcarPrecomprasAbandonadas` |
| Comando | `precompras:marcar-abandonadas --dias=7` |
| Schedule | Diario 02:00 en `routes/console.php` |
| Motivo | `ABANDONO_INACTIVIDAD` |
| Guía | [precompras-job-abandonadas.md](./precompras-job-abandonadas.md) |

### 2.4 Webhook `confirmation` + firma `x_signature`

| Pieza | Detalle |
| ----- | ------- |
| Ruta | `POST /api/epayco/confirmation` (pública) |
| Validador | `EpaycoSignatureValidator` |
| Servicio | `EpaycoConfirmationService` |
| Checkout | Envía `confirmation` + `extra4` (precompra_id) |
| Env | `EPAYCO_CUSTOMER_ID` / `EPAYCO_P_CUST_ID_CLIENTE`, `EPAYCO_P_KEY` |
| Guía | [epayco-webhook-confirmation.md](./epayco-webhook-confirmation.md) |

### 2.5 Visibilidad operativa (Admservicios)

| Pieza | Detalle |
| ----- | ------- |
| Relación | `ultimaTransaccionEpayco()` (`latestOfMany`) |
| Tabla / CSV | Columnas `Transaction ID` y `Approval code` |
| Detalle | `POST /cajas/admservicios/detalle/{id}` — modal con historial completo de `epayco_transacciones` |

*(Fuera de alcance aún: `ReporteComprasServicios`.)*

### 2.6 TLS en `ApiEpayco`

| Pieza | Detalle |
| ----- | ------- |
| Flag | `EPAYCO_HTTP_VERIFY_SSL` (default **true**) |
| Alcance | `validarReferencia`, Apify login / sesión Smart Checkout |

### 2.7 Checkout modular + versión v1/v2

| Pieza | Detalle |
| ----- | ------- |
| JS catálogo | Módulos en `public/src/Mercurio/Ecommerce/` (`pago.js`, `epayco.js`, `venta.js`, …) |
| JS pendientes | `public/src/Mercurio/ComprasPendientes/` (`pago.js`, `utils.js`, …) |
| Env | `EPAYCO_CHECKOUT_VERSION` (`1` Standard / `2` Smart + Apify) |

### 2.8 QA: forzar aprobación (non-prod)

| Pieza | Detalle |
| ----- | ------- |
| Flag | `EPAYCO_FORCE_APPROVED` (default **false**) |
| Efecto | En `ApiEpayco::validarReferencia()`, fuerza `aprobado=true` / `cod_estado=1` |
| Guardrail | Solo si `APP_ENV` y `APP_MODE` **no** son `production` |
| Uso | Probar `guardar-venta` sin un pago realmente aceptado en QA/dev |
| No aplica | Al webhook (sigue exigiendo firma y payload real de ePayco) |

### 2.9 Documentación

Set operativo en `docs/ecommerce/` (pasarela, modal, estado BD, webhook, job,
este análisis).

---

## 3. Diagnóstico del estado actual

### 3.1 Madurez del flujo

```text
Antes                         Ahora
─────                         ─────
Precompra PE + redirect       Precompra + validación API + webhook
Sin historial ePayco          epayco_transacciones (append-only)
Abandono = PE huérfana        AB vía onClose + job TTL
Confirmación solo cliente     + webhook firmado
Admin: solo ref_payco         + IDs en tabla/CSV + modal historial
TLS desactivado siempre       TLS on por defecto (flag)
Checkout monolítico main.js   Módulos + Standard/Smart Checkout
```

### 3.2 Fortalezas

- Defensa en profundidad: cliente (`response`) + API reference + webhook.
- Auditoría append-only útil para disputas y soporte.
- Estados de negocio claros (`PE` / `PA` / `RE` / `DE` / `AB`).
- Job y webhook reducen dependencia de la WebView/navegador.
- Admservicios puede inspeccionar el historial sin SQL manual.

### 3.3 Riesgos y huecos residuales

| # | Riesgo | Severidad | Notas |
| - | ------ | --------- | ----- |
| R1 | Credenciales de firma no configuradas en prod | Alta | Webhook responde `503`; setear `EPAYCO_CUSTOMER_ID` / `EPAYCO_P_KEY`. |
| R2 | Scheduler no activo en el servidor | Media | El job de abandono no corre sin cron/`schedule:work`. |
| R3 | Migración `epayco_transacciones` no aplicada | Alta | Escrituras de auditoría fallan (logueadas; no deben tumbar el pago). |
| R4 | Doble `guardar-venta` (cliente + webhook) | Baja | Mitigado en ambos lados si la precompra ya estaba `PA` antes de actualizar. Carrera simultánea residual (ambos leen `PE`) depende de idempotencia en Subsidio. |
| R5 | Checkout ePayco en WebView Android | Baja | Mitigado: en móvil/WebView se fuerza Smart Checkout v2 con `type: standard` (entorno seguro ePayco). Webhook + retorno `response` siguen confirmando. |
| R6 | `APIClient` (Subsidio u otros) sigue con `withoutVerifying()` | Baja–Media | Deuda TLS fuera de `ApiEpayco`. |
| R7 | Firma no se valida en `validarReferencia` (solo webhook) | Baja | La API de ePayco es el origen de confianza en ese path. |
| R8 | ~~Reporte de compras Cajas sin `transaction_id`~~ | — | Hecho: columnas Transaction ID / Approval code en `ReporteComprasServicios`. |
| R9 | Whitelist de medios de pago / branding del modal | Baja | Pendiente; ver [modal-control](./epayco-modal-control.md). |
| R10 | Bundles JS deben regenerarse al desplegar | Media | Cambios en `src/Mercurio/...` requieren gulp del módulo. |
| R11 | `EPAYCO_FORCE_APPROVED=true` mal configurado | Media | Guardrail non-prod; nunca activar en production. |

### 3.4 Dependencias de puesta en marcha (checklist corto)

- [ ] Migración `epayco_transacciones` ejecutada.
- [ ] `.env`: `EPAYCO_CUSTOMER_ID` (o `EPAYCO_P_CUST_ID_CLIENTE`), `EPAYCO_P_KEY`, `EPAYCO_HTTP_VERIFY_SSL=true` (prod).
- [ ] `EPAYCO_FORCE_APPROVED=false` en todo entorno productivo.
- [ ] URL pública HTTPS de `/api/epayco/confirmation` registrada / alcanzable.
- [ ] Cron de `schedule:run` (job 02:00).
- [ ] Bundles `Ecommerce` / `ComprasPendientes` / `Admservicios` desplegados.
- [ ] Prueba sandbox: pago OK, rechazo, cierre modal, cierre app (WebView).

---

## 4. Mejoras aplicadas (por valor)

| Mejora | Valor de negocio | Valor técnico |
| ------ | ---------------- | ------------- |
| Historial `epayco_transacciones` | Disputas / auditoría | Trazabilidad |
| Webhook + firma | Menos ventas “pagadas en ePayco, no en Mercurio” | Seguridad + resiliencia |
| `onClose` → `AB` | Métricas reales; menos pendientes falsos | UX + datos limpios |
| Job TTL 7 días | Limpieza automática | Operación |
| Admservicios: IDs + modal detalle | Soporte más rápido | Observabilidad |
| TLS con flag | Cumplimiento / hardening | Seguridad |
| Checkout v1/v2 + módulos JS | Mantenibilidad | Arquitectura frontend |
| `EPAYCO_FORCE_APPROVED` | QA de `guardar-venta` sin cobro real | Testing (non-prod) |

---

## 5. Recomendaciones

### 5.1 Inmediatas (despliegue / operación)

1. Completar variables de firma y verificar con POST de prueba
   ([guía webhook](./epayco-webhook-confirmation.md)).
2. Confirmar cron del scheduler
   ([guía job](./precompras-job-abandonadas.md)).
3. Probar flujo completo en WebView Flutter Android.
4. Monitorear logs las primeras 48 h: `ePayco confirmation`,
   `MarcarPrecomprasAbandonadas`, errores de `epayco_transacciones`.
5. Verificar `EPAYCO_FORCE_APPROVED=false` en prod.

### 5.2 Corto plazo (1–2 sprints) — aún pendientes

5. ~~**Prueba de carrera cliente + webhook / omitir reenvío si ya `PA`**~~ —
   **hecho en cliente:** `EcommerceController::guardarVenta` captura
   `yaPagada` antes de actualizar y no llama a Subsidio si ya estaba `PA`
   (mismo criterio que el webhook). Carrera simultánea residual: depende
   de idempotencia de Subsidio o de un lock futuro.
6. **Extender TLS configurable a `APIClient`** (mismo patrón `*_HTTP_VERIFY_SSL`).
7. ~~**Incluir `transaction_id` / `approval_code` en `ReporteComprasServicios`~~ —
   **hecho** (columnas en tabla + JSON del consultar; última tx vía
   `ultimaTransaccionEpayco`).
8. **Alerta operativa** (email/Slack) cuando el webhook reciba `400` de firma
   o `503` por credenciales faltantes.

### 5.3 Mediano plazo — aún pendientes

9. **Whitelist de medios de pago** (`methods` / `methodsDisable`).
10. **Cola asíncrona para `guardar-venta` desde el webhook** si Subsidio
    supera ~2–3 s.
11. ~~**Panel de detalle de precompra** en Admservicios~~ — **hecho**
    (`detalle/{id}` + historial).
12. **Tests Feature** del webhook (firma OK/KO, idempotencia `PA`,
    resolución por `extra4`) con Http::fake / DB de prueba.

### 5.4 No prioritario / consciente

- Customizar textos del modal ePayco (bajo impacto).
- Validar `x_signature` también en `validarReferencia` (solo si ePayco la
  envía de forma fiable en ese endpoint).

---

## 6. Matriz de cobertura del ciclo de compra

| Evento usuario | Mecanismo | Resultado esperado |
| -------------- | --------- | ------------------ |
| Paga y vuelve al sitio | `validar-pago-epayco` + `guardar-venta` | `PA` + venta + filas auditoría |
| Paga y cierra el navegador | Webhook `confirmation` | `PA` + venta (si no estaba `PA`) |
| Cierra modal sin pagar | `onClose` → abandonar | `AB` + Swal |
| Cierra app / pierde red sin `onClose` | Job a los 7 días | `PE` → `AB` |
| Pago llega después de `AB` | Validación / webhook | Puede pasar a `PA` |
| Disputa con ePayco | Admservicios detalle / SQL | Historial `epayco_transacciones` |
| QA sin cobro real (non-prod) | `EPAYCO_FORCE_APPROVED=true` | `validarReferencia` simula aprobado |

---

## 7. Conclusión

El módulo está en un diseño **híbrido cliente + servidor** con auditoría,
abandono controlado, confirmación firmada y visibilidad en Admservicios.
Las mejoras de mayor valor del diagnóstico original ya están aplicadas.

El éxito en producción depende sobre todo de **configuración y pruebas de
despliegue**: firma ePayco, scheduler, migración, TLS, `FORCE_APPROVED`
apagado, y un recorrido real en WebView Android.

---

## 8. Referencias de código clave

| Área | Ubicación |
| ---- | --------- |
| Validación reference | `app/Services/Api/ApiEpayco.php` |
| Firma | `app/Services/Ecommerce/EpaycoSignatureValidator.php` |
| Webhook | `app/Http/Controllers/Api/EpaycoWebhookController.php` |
| Confirmación | `app/Services/Ecommerce/EpaycoConfirmationService.php` |
| Abandono / sync precompra | `app/Http/Controllers/Mercurio/EcommerceController.php` |
| Job | `app/Jobs/MarcarPrecomprasAbandonadas.php` |
| Admin listado / CSV | `app/Http/Controllers/Cajas/AdmserviciosController.php` |
| Admin detalle | `POST /cajas/admservicios/detalle/{id}` + `_detalle.blade.php` |
| Checkout JS | `public/src/Mercurio/Ecommerce/{pago,epayco,venta}.js` |
| Pendientes JS | `public/src/Mercurio/ComprasPendientes/pago.js` |
