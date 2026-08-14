# Estado real de la transacción ePayco en base de datos

Diagnóstico complementario a
[pasarela-pago-epayco.md](./pasarela-pago-epayco.md) y
[epayco-modal-control.md](./epayco-modal-control.md).

> **Pregunta evaluada:** ¿El estado real que emite ePayco se almacena en
> base de datos, o solo el mapeo local de `EstadoPrecompra`?
>
> **Respuesta corta:** Se almacenan **ambos**. El resumen vive en
> `precompras_servicios`; el historial completo de respuestas ePayco vive
> en `epayco_transacciones`.

---

## 1. Resumen en `precompras_servicios`

Campos rellenados por
[`EcommerceController::actualizarPrecompraDesdePago()`](../app/Http/Controllers/Mercurio/EcommerceController.php):

| Columna             | Origen ePayco                           |
| ------------------- | --------------------------------------- |
| `ref_payco`         | `x_ref_payco`                           |
| `cod_estado_epayco` | `x_cod_transaction_state`               |
| `motivo_epayco`     | `x_response_reason_text` / `x_response` |
| `estado`            | Mapeo `EstadoPrecompra::desdeCodigoEpayco()` |

Esto es el **estado operativo** de la compra (último snapshot útil).

---

## 2. Auditoría en `epayco_transacciones`

Tabla nueva (migración
`2026_08_14_160000_create_epayco_transacciones_table.php`).

Cada llamada exitosa a `ApiEpayco::validarReferencia()` (vía
`validar-pago-epayco` o `guardar-venta`) inserta **una fila** — no
sobrescribe — con:

| Columna          | Contenido                                      |
| ---------------- | ---------------------------------------------- |
| `precompra_id`   | FK a `precompras_servicios` (nullable)         |
| `ref_payco`      | `x_ref_payco`                                  |
| `transaction_id` | `x_transaction_id`                             |
| `invoice`        | `x_id_invoice`                                 |
| `approval_code`  | `x_approval_code`                              |
| `cod_estado`     | `x_cod_transaction_state`                      |
| `respuesta`      | `x_response`                                   |
| `motivo`         | `x_response_reason_text`                       |
| `amount`         | `x_amount`                                     |
| `currency`       | `x_currency_code`                              |
| `bank_name`      | `x_bank_name`                                  |
| `franchise`      | `x_franchise`                                  |
| `card_mask`      | `x_card_number` (enmascarado)                  |
| `quotas`         | `x_quotas`                                     |
| `signature`      | `x_signature`                                  |
| `fecha_epayco`   | `x_date` (texto original)                      |
| `origen`         | `validacion` (futuro: `webhook`, `manual`)     |
| `payload_json`   | Objeto `data` crudo completo de ePayco         |

Modelo: [`EpaycoTransaccion`](../app/Models/EpaycoTransaccion.php).
Relación: `PrecompraServicio::transaccionesEpayco()`.

### 2.1 Flujo de escritura

1. Frontend → `validar-pago-epayco` o `guardar-venta`.
2. `ApiEpayco::validarReferencia()` normaliza campos + adjunta `payload_raw`.
3. `actualizarPrecompraDesdePago()`:
   - inserta en `epayco_transacciones` (siempre, incluso si la precompra ya
     está `PA`);
   - actualiza `precompras_servicios` solo si la precompra es actualizable
     (por `ref_payco`, o por id en `PE`/`AB`) y aún no está pagada.

---

## 3. Consulta típica de auditoría

```sql
SELECT t.*
FROM epayco_transacciones t
WHERE t.precompra_id = :id
ORDER BY t.created_at ASC;
```

O por referencia:

```sql
SELECT *
FROM epayco_transacciones
WHERE ref_payco = :ref
ORDER BY created_at DESC;
```

---

## 4. Qué falta (fuera de este cambio)

| # | Acción                                                         | Estado      |
| - | -------------------------------------------------------------- | ----------- |
| 1 | Mostrar `transaction_id` / `approval_code` en Admservicios     | Pendiente   |
| 2 | Incluir campos en reporte de compras                           | Pendiente   |
| 3 | Webhook `confirmation` + validar `x_signature`                 | Pendiente   |
| 4 | Quitar `withoutVerifying()` en la validación TLS               | Pendiente   |
