# Webhook ePayco confirmation + validación de firma

Guía para poner en marcha el endpoint server-side de confirmación de ePayco
con validación de `x_signature`.

> **Última revisión:** 2026-08-24  
> **URL del webhook:** `POST {APP_URL}/api/epayco/confirmation`  
> **Ruta Laravel:** `api.epayco.confirmation`  
> **Código:** [`EpaycoWebhookController`](../app/Http/Controllers/Api/EpaycoWebhookController.php),
> [`EpaycoSignatureValidator`](../app/Services/Ecommerce/EpaycoSignatureValidator.php),
> [`EpaycoConfirmationService`](../app/Services/Ecommerce/EpaycoConfirmationService.php)

> **Nota:** `EPAYCO_FORCE_APPROVED` **no** aplica a este endpoint. Solo altera
> `ApiEpayco::validarReferencia()` en entornos non-prod. El webhook exige
> firma válida y payload real de ePayco.

---

## 1. Qué hace

Cuando ePayco notifica un pago (aunque el usuario cierre el navegador):

1. Recibe el POST (form o JSON).
2. Valida `x_signature` con la fórmula oficial.
3. Inserta un snapshot en `epayco_transacciones` (`origen = webhook`).
4. Actualiza `precompras_servicios` (puede recuperar `AB` → `PA` si el pago llegó).
5. Si el pago es aceptado y la precompra **no** estaba ya `PA`, llama a
   `guardar-venta` en ApiSubsidio.
6. Responde `200 OK` (o `400` / `503` / `500` según el caso).

Fórmula:

```text
sha256( P_CUST_ID_CLIENTE ^ P_KEY ^ x_ref_payco ^ x_transaction_id ^ x_amount ^ x_currency_code )
```

---

## 2. Variables de entorno

En `.env` (valores del **panel ePayco** → configuración / llaves):

```ini
EPAYCO_CUSTOMER_ID="tu_p_cust_id_cliente"
EPAYCO_P_KEY="tu_p_key"
```

| Variable | Origen en panel ePayco | Uso |
| -------- | ---------------------- | --- |
| `EPAYCO_CUSTOMER_ID` | `P_CUST_ID_CLIENTE` | Primer segmento de la firma |
| `EPAYCO_P_CUST_ID_CLIENTE` | (alias) | Mismo valor; `config` acepta ambos |
| `EPAYCO_P_KEY` | `P_KEY` | Segundo segmento de la firma |

> **Nota:** No confundir con `EPAYCO_PUBLIC_KEY` / `EPAYCO_PRIVATE_KEY`
> (checkout JS y Basic Auth de la API). `P_KEY` es la llave de firma del
> webhook; suele verse aparte en el dashboard.

Tras editar `.env`:

```bash
php artisan config:clear
```

Mapeo en `config/app.php`:

```php
'epayco' => [
    // ...
    'customer_id' => env('EPAYCO_CUSTOMER_ID'),
    'p_key' => env('EPAYCO_P_KEY'),
],
```

---

## 3. Registrar la URL en ePayco

1. Calcula la URL pública (HTTPS en producción):

   ```text
   https://tu-dominio.com/api/epayco/confirmation
   ```

   En local (ejemplo Sail puerto 9043):

   ```text
   http://comfaca.ecommerce.com.co:9043/api/epayco/confirmation
   ```

2. En el panel ePayco, configura la **URL de confirmación** del comercio
   (o verifica que el checkout envíe `confirmation` en el payload).

3. El frontend ya envía `confirmation` y `extra4` (id de precompra) al abrir
   el checkout (`Ecommerce` y `ComprasPendientes`).

El endpoint es **público** (sin JWT). La seguridad es la firma.
CSRF ya excluye `api/*` en `bootstrap/app.php`.

---

## 4. Extras del checkout (correlación)

| Campo ePayco | Valor enviado |
| ------------ | ------------- |
| `extra1` | documento / cedtra |
| `extra2` | codser |
| `extra3` | numero |
| `extra4` | `precompra_id` |
| `confirmation` | URL del webhook |
| `response` | URL de retorno del usuario |

El servicio busca la precompra por: `ref_payco` → `extra4` →
`documento+codser+numero` en `PE`/`AB`.

---

## 5. Pruebas

### 5.1 Unit (firma)

```bash
php artisan test --filter=EpaycoSignatureValidatorTest
```

### 5.2 Endpoint vivo (firma válida de prueba)

Genera la firma con tus credenciales reales y un payload de sandbox:

```bash
php -r '
$c="CUSTOMER"; $k="PKEY";
$d=["x_ref_payco"=>"1","x_transaction_id"=>"1","x_amount"=>"1000","x_currency_code"=>"COP"];
echo hash("sha256", "$c^$k^{$d["x_ref_payco"]}^{$d["x_transaction_id"]}^{$d["x_amount"]}^{$d["x_currency_code"]}");
'
```

```bash
curl -X POST "$APP_URL/api/epayco/confirmation" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "x_ref_payco=1" \
  -d "x_transaction_id=1" \
  -d "x_amount=1000" \
  -d "x_currency_code=COP" \
  -d "x_cod_transaction_state=1" \
  -d "x_response=Aceptada" \
  -d "x_signature=FIRMA_CALCULADA" \
  -d "extra4=ID_PRECOMPRA"
```

- Firma mala → `400 Invalid signature`
- Sin `EPAYCO_CUSTOMER_ID` / `EPAYCO_P_KEY` → `503`
- OK → `200 OK` y fila en `epayco_transacciones` con `origen=webhook`

### 5.3 Logs

```bash
grep "ePayco confirmation" storage/logs/laravel.log | tail -30
```

---

## 6. Checklist de puesta en marcha

- [ ] `EPAYCO_CUSTOMER_ID` y `EPAYCO_P_KEY` en `.env` del entorno.
- [ ] `php artisan config:clear` (o `config:cache` en prod).
- [ ] URL pública HTTPS alcanzable desde internet (ePayco debe poder POSTear).
- [ ] URL registrada / enviada como `confirmation` en el checkout.
- [ ] Bundles JS regenerados tras cambios en `Ecommerce` / `ComprasPendientes`.
- [ ] Prueba con pago sandbox: precompra pasa a `PA` y aparece fila `webhook`.
- [ ] Confirmar que `guardar-venta` no se duplica si el usuario también vuelve
      por `response` (si ya estaba `PA`, el webhook no vuelve a registrar venta).

---

## 7. Troubleshooting

| Síntoma | Causa probable |
| ------- | -------------- |
| `400 Invalid signature` | `P_KEY` / `CUSTOMER_ID` incorrectos; `x_amount` distinto (decimales); campos vacíos. |
| `503` | Faltan env de firma. |
| `200` pero precompra no cambia | No se resolvió la precompra (`extra4` / ref / extras). |
| Venta no en subsidio | `guardar-venta` falló (ver log); o ya estaba `PA`. |
| ePayco reintenta el webhook | Respuestas no-200; revisa logs y responde rápido. |
