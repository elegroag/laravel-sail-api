# Análisis y recomendaciones — mejoras ePayco / precompras (Mercurio)

> **Fecha:** 2026-08-14  
> **Alcance:** cambios del módulo de pasarela ePayco y precompras de servicios
> (commit `e64ad053` + trabajo posterior de webhook, TLS, Admservicios).  
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

1. **Trazabilidad** — historial append-only de respuestas ePayco.
2. **Resiliencia** — abandono explícito (`onClose` + job) y confirmación
   server-side (webhook + firma).
3. **Operación / seguridad** — visibilidad en Admservicios y verificación TLS
   configurable.

El flujo deja de depender solo del navegador del usuario: aunque cierre la
modal, la pestaña o la WebView, el backend puede confirmar el pago y auditarlo.

---

## 2. Inventario de cambios aplicados

### 2.1 Auditoría de transacciones (`epayco_transacciones`)

| Pieza | Detalle |
| ----- | ------- |
| Migración | `2026_08_14_160000_create_epayco_transacciones_table.php` |
| Modelo | `EpaycoTransaccion` + `PrecompraServicio::transaccionesEpayco()` |
| Escritura | Cada `validarReferencia` exitosa → fila nueva (`origen=validacion`) |
| Payload | Campos clave + `payload_json` crudo |

**Mejora:** se puede reconstruir el pago (monto, banco, franquicia, firma,
IDs) sin depender solo de `ref_payco` / `motivo_epayco` en la precompra.

### 2.2 Abandonos de checkout

| Pieza | Detalle |
| ----- | ------- |
| Estado | `EstadoPrecompra::ABANDONADA` (`AB`) — no retomable |
| Endpoint | `POST /mercurio/servicios/abandonar-precompra` |
| Cliente | `onCloseModal` en Ecommerce y ComprasPendientes (+ SweetAlert) |
| Salvaguarda | Flag `__epaycoPagoEnValidacion` + delay 2.5s; `AB`→`PA` si el pago llega tarde |

**Mejora:** deja de haber “pendientes eternos” silenciosos cuando el usuario
cierra la pasarela con la pestaña abierta.

### 2.3 Job de limpieza de `PE` huérfanas

| Pieza | Detalle |
| ----- | ------- |
| Job | `MarcarPrecomprasAbandonadas` |
| Comando | `precompras:marcar-abandonadas --dias=7` |
| Schedule | Diario 02:00 en `routes/console.php` |
| Motivo | `ABANDONO_INACTIVIDAD` |
| Guía | [precompras-job-abandonadas.md](./precompras-job-abandonadas.md) |

**Mejora:** cubre cierre de navegador / WebView / pérdida de red donde
`onClose` no corre.

### 2.4 Webhook `confirmation` + firma `x_signature`

| Pieza | Detalle |
| ----- | ------- |
| Ruta | `POST /api/epayco/confirmation` (pública) |
| Validador | `EpaycoSignatureValidator` (fórmula oficial ePayco) |
| Servicio | `EpaycoConfirmationService` (audita, actualiza precompra, `guardar-venta`) |
| Checkout | Envía `confirmation` + `extra4` (precompra_id) |
| Env | `EPAYCO_CUSTOMER_ID`, `EPAYCO_P_KEY` |
| Tests | `EpaycoSignatureValidatorTest` (3 casos) |
| Guía | [epayco-webhook-confirmation.md](./epayco-webhook-confirmation.md) |

**Mejora:** confirmación confiable aunque el cliente no vuelva; anti-fraude
básico vía firma; idempotencia si la precompra ya estaba `PA`.

### 2.5 Visibilidad operativa (Admservicios)

| Pieza | Detalle |
| ----- | ------- |
| Relación | `ultimaTransaccionEpayco()` (`latestOfMany`) |
| UI / CSV | Columnas `Transaction ID` y `Approval code` |

**Mejora:** soporte puede conciliar con ePayco sin consultar SQL a mano.
*(Fuera de alcance: `ReporteComprasServicios`.)*

### 2.6 TLS en `validarReferencia`

| Pieza | Detalle |
| ----- | ------- |
| Flag | `EPAYCO_HTTP_VERIFY_SSL` (default **true**) |
| Escape | `false` solo en dev con CA/proxy roto |
| Error | Fallos de conexión/SSL se reportan como mensaje controlado |

**Mejora:** endurece la validación del pago contra MITM, sin romper entornos
problemáticos.

### 2.7 Documentación

Se consolidó documentación operativa y de arquitectura del flujo ePayco
(pasarela, modal, estado en BD, webhook, job).

---

## 3. Diagnóstico del estado actual

### 3.1 Madurez del flujo

```text
Antes                         Ahora
─────                         ─────
Precompra PE + redirect       Precompra + validación API
Sin historial ePayco          epayco_transacciones (append-only)
Abandono = PE huérfana        AB vía onClose + job TTL
Confirmación solo cliente     + webhook firmado
Admin: solo ref_payco         + transaction_id / approval_code
TLS desactivado siempre       TLS on por defecto (flag)
```

### 3.2 Fortalezas

- Defensa en profundidad: cliente (`response`) + API reference + webhook.
- Auditoría append-only útil para disputas y soporte.
- Estados de negocio claros (`PE` / `PA` / `RE` / `DE` / `AB`).
- Job y webhook reducen dependencia de la WebView/navegador.
- Documentación alineada con el código.

### 3.3 Riesgos y huecos residuales

| # | Riesgo | Severidad | Notas |
| - | ------ | --------- | ----- |
| R1 | Credenciales de firma no configuradas en prod | Alta | Webhook responde `503` y ePayco reintenta; hay que setear `EPAYCO_CUSTOMER_ID` / `EPAYCO_P_KEY`. |
| R2 | Scheduler no activo en el servidor | Media | El job de abandono no corre sin cron/`schedule:work`. |
| R3 | Migración `epayco_transacciones` no aplicada | Alta | Escrituras de auditoría fallan (se loguean; no deben tumbar el pago). |
| R4 | Doble `guardar-venta` (cliente + webhook) | Media | Idempotencia local evita re-llamada si ya `PA`; depende de que subsidio tolere reintentos si hay carrera. |
| R5 | Checkout ePayco en WebView Android | Media | Iframe/`onClose`/storage frágiles; el webhook mitiga, pero hay que probar en dispositivo real. |
| R6 | `APIClient` (Subsidio u otros) sigue con `withoutVerifying()` | Baja–Media | Deuda TLS fuera de ePayco reference. |
| R7 | Firma no se valida en `validarReferencia` (solo webhook) | Baja | La API de ePayco es el origen de confianza en ese path. |
| R8 | Reporte de compras Cajas sin `transaction_id` | Baja | Decisión consciente (opción B); CSV de Admservicios sí los trae. |
| R9 | Whitelist de medios de pago / branding del modal | Baja | Sigue pendiente en la doc de modal-control. |
| R10 | Bundles JS deben regenerarse al desplegar | Media | Cambios en `src/Mercurio/...` requieren `APP=mercurio npx gulp ...`. |

### 3.4 Dependencias de puesta en marcha (checklist corto)

- [ ] Migración `epayco_transacciones` ejecutada.
- [ ] `.env`: `EPAYCO_CUSTOMER_ID`, `EPAYCO_P_KEY`, `EPAYCO_HTTP_VERIFY_SSL=true` (prod).
- [ ] URL pública HTTPS de ` /api/epayco/confirmation` registrada / alcanzable.
- [ ] Cron de `schedule:run` (job 02:00).
- [ ] Bundles `Ecommerce.js` / `ComprasPendientes.js` desplegados.
- [ ] Prueba sandbox: pago OK, rechazo, cierre modal, cierre app (WebView).

---

## 4. Mejoras aplicadas (por valor)

| Mejora | Valor de negocio | Valor técnico |
| ------ | ---------------- | ------------- |
| Historial `epayco_transacciones` | Disputas / auditoría | Trazabilidad |
| Webhook + firma | Menos ventas “pagadas en ePayco, no en Mercurio” | Seguridad + resiliencia |
| `onClose` → `AB` | Métricas reales; menos pendientes falsos | UX + datos limpios |
| Job TTL 7 días | Limpieza automática | Operación |
| Admservicios: IDs ePayco | Soporte más rápido | Observabilidad |
| TLS con flag | Cumplimiento / hardening | Seguridad |

---

## 5. Recomendaciones

### 5.1 Inmediatas (esta semana / al desplegar)

1. **Completar variables de firma en cada entorno** y verificar con un POST
   de prueba firmado ([guía webhook](./epayco-webhook-confirmation.md)).
2. **Confirmar cron del scheduler** ([guía job](./precompras-job-abandonadas.md)).
3. **Probar el flujo completo en WebView Flutter Android** (abrir checkout,
   pagar, cancelar, matar app a mitad): validar que el webhook deje la
   precompra en `PA` y la venta en subsidio.
4. **Monitorear logs** las primeras 48 h:
   - `ePayco confirmation`
   - `MarcarPrecomprasAbandonadas`
   - `Error registrando epayco_transacciones`

### 5.2 Corto plazo (1–2 sprints)

5. **Prueba de carrera cliente + webhook:** dos `guardar-venta` casi
   simultáneos; documentar comportamiento de ApiSubsidio y, si hace falta,
   un flag `venta_registrada` en precompra.
6. **Extender TLS configurable a `APIClient`** (mismo patrón
   `*_HTTP_VERIFY_SSL`) o al menos a llamadas críticas de subsidio.
7. **Incluir `transaction_id` / `approval_code` en `ReporteComprasServicios`**
   si finanzas lo pide (opción C descartada antes; reabrir si hay demanda).
8. **Alerta operativa** (email/Slack) cuando el webhook reciba `400` de firma
   o `503` por credenciales faltantes.

### 5.3 Mediano plazo

9. **Whitelist de medios de pago** (`methods` / `methodsDisable` en checkout).
10. **Cola asíncrona para `guardar-venta` desde el webhook** si la API de
    subsidio supera ~2–3 s (ePayco espera respuesta rápida).
11. **Panel de detalle de precompra** en Admservicios con historial completo
    de `epayco_transacciones` (no solo la última).
12. **Tests Feature** del webhook (firma OK/KO, idempotencia `PA`, resolución
    por `extra4`) con Http::fake / DB de prueba.

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
| Cierra modal sin pagar | `onCloseModal` → abandonar | `AB` + Swal |
| Cierra app / pierde red sin `onClose` | Job a los 7 días | `PE` → `AB` |
| Pago llega después de `AB` | Validación / webhook | Puede pasar a `PA` |
| Disputa con ePayco | Admservicios / SQL `epayco_transacciones` | `transaction_id`, `approval_code`, payload |

---

## 7. Conclusión

El módulo pasó de un checkout “cliente-céntrico” a un diseño **híbrido
cliente + servidor** con auditoría, abandono controlado y confirmación
firmada. Las mejoras ya aplicadas cubren los hallazgos de mayor valor de la
documentación de diagnóstico.

El éxito en producción depende menos de más código y más de **configuración
y pruebas de despliegue**: firma ePayco, scheduler, migración, TLS y un
recorrido real en WebView Android.

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
| Admin | `app/Http/Controllers/Cajas/AdmserviciosController.php` |
| Checkout JS | `public/src/Mercurio/Ecommerce/main.js`, `ComprasPendientes/main.js` |
