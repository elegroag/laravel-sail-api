# Pasarela de Pago ePayco — Catálogo de Servicios (Mercurio)

Documentación del flujo completo de la pasarela de pago **ePayco** integrada en el
catálogo de servicios de Mercurio, accesible en
`http://comfaca.ecommerce.com.co:9043/mercurio/servicios/index` (ruta
`/mercurio/servicios/index`).

> **Audiencia:** desarrolladores y mantenedores del módulo de ecommerce Mercurio.
> **Última revisión:** 2026-08-24 (sync docs: webhook, Admservicios detalle,
> módulos JS, checkout v1/v2, `EPAYCO_FORCE_APPROVED`).

Documentos relacionados:
- [epayco-modal-control.md](./epayco-modal-control.md) — control de la modal.
- [epayco-estado-transaccion-db.md](./epayco-estado-transaccion-db.md) —
  campos ePayco persistidos.
- [epayco-webhook-confirmation.md](./epayco-webhook-confirmation.md) —
  webhook + firma.
- [precompras-job-abandonadas.md](./precompras-job-abandonadas.md) — job TTL.
- [analisis-mejoras-epayco-precompras.md](./analisis-mejoras-epayco-precompras.md)
  — inventario aplicado vs pendientes.

---

## 1. Resumen general

La compra de servicios para los usuarios finales (trabajadores, beneficiarios)
se paga electrónicamente con **ePayco**. Según `EPAYCO_CHECKOUT_VERSION`:

- **`1`** — Standard Checkout (`checkout.js`, modal embebida) en **desktop**.
- **`2`** — Smart Checkout (`checkout-v2.js` + sesión Apify) en desktop (`onpage`).
- **Móvil / WebView** — siempre Smart Checkout v2 con `type: standard`
  (entorno seguro ePayco / redirect), aunque `EPAYCO_CHECKOUT_VERSION=1`.

Además hay un **endpoint de validación de referencia** y un **webhook
`confirmation`** firmado para confirmar el estado real aunque el cliente no
vuelva.

El diseño se articula en torno a cinco principios:

1. **Precompra antes de pagar** — Registro `PE` en `precompras_servicios`
   antes de abrir la pasarela.
2. **Validación server-side del pago** — No se confía solo en query params;
   el backend consulta `reference` (y/o recibe el webhook) y solo con
   `x_cod_transaction_state = 1` registra la venta en subsidio.
3. **Auditoría append-only** — Cada validación / webhook inserta una fila en
   `epayco_transacciones` (`origen=validacion` | `webhook`).
4. **Detección de abandonos** — `onCloseModal` / `onClosed` → `PE → AB` y job
   nocturno para PE huérfanas (> N días).
5. **Confirmación híbrida** — Cliente (`response`) + webhook firmado.

---

## 2. Stack y componentes

| Capa              | Tecnología / Archivo                                                                                  |
| ----------------- | ------------------------------------------------------------------------------------------------------ |
| Frontend catálogo | [resources/views/mercurio/ecommerce/index.blade.php](../resources/views/mercurio/ecommerce/index.blade.php) |
| JS catálogo       | [public/src/Mercurio/Ecommerce/](../public/src/Mercurio/Ecommerce/) — `main.js` (orquestación), `pago.js`, `epayco.js`, `venta.js`, `tarifa.js`, … |
| JS pendientes     | [public/src/Mercurio/ComprasPendientes/](../public/src/Mercurio/ComprasPendientes/) — `main.js`, `pago.js`, `utils.js`, … |
| Backend routes    | [routes/mercurio/servicios.php](../routes/mercurio/servicios.php)                                       |
| Webhook           | `POST /api/epayco/confirmation` → `EpaycoWebhookController`                                            |
| Routes consola    | [routes/console.php](../routes/console.php)                                                            |
| Controller        | [app/Http/Controllers/Mercurio/EcommerceController.php](../app/Http/Controllers/Mercurio/EcommerceController.php) |
| Admin Cajas       | [AdmserviciosController](../app/Http/Controllers/Cajas/AdmserviciosController.php) (tabla, CSV, `detalle/{id}`) |
| Servicio ePayco   | [app/Services/Api/ApiEpayco.php](../app/Services/Api/ApiEpayco.php)                                    |
| Confirmación      | [EpaycoConfirmationService](../app/Services/Ecommerce/EpaycoConfirmationService.php) / [EpaycoSignatureValidator](../app/Services/Ecommerce/EpaycoSignatureValidator.php) |
| Servicio Subsidio | [app/Services/Api/ApiSubsidio.php](../app/Services/Api/ApiSubsidio.php)                                |
| Estados           | [app/Services/Ecommerce/EstadoPrecompra.php](../app/Services/Ecommerce/EstadoPrecompra.php)            |
| Modelo precompra  | [app/Models/PrecompraServicio.php](../app/Models/PrecompraServicio.php)                                |
| Modelo auditoría  | [app/Models/EpaycoTransaccion.php](../app/Models/EpaycoTransaccion.php)                                |
| Job limpieza      | [app/Jobs/MarcarPrecomprasAbandonadas.php](../app/Jobs/MarcarPrecomprasAbandonadas.php)                |
| Comando consola   | [app/Console/Commands/MarcarPrecomprasAbandonadasCommand.php](../app/Console/Commands/MarcarPrecomprasAbandonadasCommand.php) |
| Config            | [config/app.php](../config/app.php) (sección `epayco`)                                                 |
| Migraciones       | [2026_07_08_120000_add_epayco_reference_api_endpoint](../database/migrations/2026_07_08_120000_add_epayco_reference_api_endpoint.php)<br>[2026_07_15_100000_create_precompras_servicios_table](../database/migrations/2026_07_15_100000_create_precompras_servicios_table.php)<br>[2026_07_15_170000_add_desestimacion_to_precompras_servicios_table](../database/migrations/2026_07_15_170000_add_desestimacion_to_precompras_servicios_table.php)<br>[2026_08_14_160000_create_epayco_transacciones_table](../database/migrations/2026_08_14_160000_create_epayco_transacciones_table.php) |

---

## 3. Configuración

### 3.1 Variables de entorno (`.env`)

```ini
EPAYCO_MODE="development"                 # development | production
EPAYCO_PUBLIC_KEY="..."                   # llave pública (JS + Basic Auth Apify)
EPAYCO_PRIVATE_KEY="..."                  # llave privada (Basic Auth backend / Apify)
EPAYCO_CHECKOUT_VERSION="1"               # 1 = Standard, 2 = Smart Checkout
EPAYCO_APIFY_URL="https://apify.epayco.co" # base API Smart Checkout (opcional)
EPAYCO_CUSTOMER_ID=                       # P_CUST_ID_CLIENTE (firma webhook)
# alias aceptado: EPAYCO_P_CUST_ID_CLIENTE
EPAYCO_P_KEY=                             # P_KEY (firma webhook)
EPAYCO_HTTP_VERIFY_SSL=true               # false solo si el CA/proxy del entorno falla
EPAYCO_FORCE_APPROVED=false               # TEMPORAL QA: solo non-prod; nunca true en production
```

`config/app.php` mapea estas variables al bloque `epayco` (`mode`, `public_key`,
`private_key`, `checkout_version`, `apify_url`, `customer_id`, `p_key`,
`verify_ssl`, `force_approved`).

### 3.2 Endpoint dinámico en `api_endpoints`

La URL del servicio **Epayco-Reference** (validación de pagos) se guarda en la
tabla `api_endpoints` (ver
[migración](../database/migrations/2026_07_08_120000_add_epayco_reference_api_endpoint.php)):

| connection_name | service_name        | endpoint_name | host_dev / host_pro                      |
| --------------- | ------------------- | ------------- | ----------------------------------------- |
| `api-epayco`    | `Epayco-Reference`  | `reference`   | `https://secure.epayco.co/validation/v1`  |

`ApiEpayco::validarReferencia()` resuelve el host según
`config('app.epayco.mode')`: si es `development` usa `host_dev`, si no,
`host_pro`.

---

## 4. Roles, autenticación y middleware

- Toda la ruta está bajo el grupo `middleware('mercurio.auth')` en
  [routes/mercurio/servicios.php](../routes/mercurio/servicios.php).
- El usuario ya está autenticado por **JWT** (`tymon/jwt-auth`) — ver
  `CLAUDE.md`.
- `EcommerceController` lee `session('user')` y `session('tipo')` y obtiene el
  `documento` activo con `self::getActUser('documento')`. Ese documento se usa
  para filtrar beneficiarios, servicios y precompras pendientes.
- CSRF: cada petición AJAX lleva el token desde la meta tag
  `<meta name="csrf-token">` (configurado en
  [`EcommerceModule.init()`](../public/src/Mercurio/Ecommerce/main.js)
  vía `$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': ... }})`).

---

## 5. Modelo de datos

### 5.1 Tabla `precompras_servicios`

Tabla central del flujo (ver
[migración](../database/migrations/2026_07_15_100000_create_precompras_servicios_table.php)).

| Columna              | Tipo             | Descripción                                              |
| -------------------- | ---------------- | -------------------------------------------------------- |
| `id`                 | PK autoincrement | Identificador interno                                    |
| `documento`          | `string(20)`     | `cedtra` del usuario en sesión                           |
| `codser`             | `string(20)`     | Código del servicio                                      |
| `numero`             | `integer`        | Número / versión del servicio                            |
| `codben`             | `string(20)`     | Beneficiario (`codben` o `cedtra` por defecto)           |
| `nota`               | `text`           | Nota libre del usuario                                   |
| `valor`              | `decimal(12,2)`  | Monto al momento de la precompra                         |
| `estado`             | `string(2)`      | `PE` Pendiente · `PA` Pagado · `DE` Desestimado · `RE` Rechazado · `AB` Abandonada |
| `ref_payco`          | `string(80)`     | Referencia única devuelta por ePayco                     |
| `cod_estado_epayco`  | `string(2)`      | `x_cod_transaction_state` (1–12)                         |
| `motivo_epayco`      | `string(255)`    | `x_response_reason_text` / `x_response`                  |
| `motivo_desestimacion` | `string(50)`   | Código de motivo (`YA_NO_INTERESA`, `VALOR_ALTO`, `ABANDONO_CHECKOUT`, `ABANDONO_INACTIVIDAD`, …) |
| `detalle_desestimacion` | `string(255)` | Texto libre cuando motivo = `OTRO` o descripción automática |
| `fecha_precompra`    | `timestamp`      | Creación del registro                                    |
| `fecha_pago`         | `timestamp`      | Se setea cuando `estado` pasa a `PA`                     |
| `fecha_desestimacion`| `timestamp`      | Se setea cuando la precompra pasa a `DE` o `AB`          |
| `created_at` / `updated_at` | timestamps | Eloquent                                                  |

Índices: `(documento, estado)` y `ref_payco`. La columna
`motivo_desestimacion` y amigas se añadieron en
[migración 2026_07_15_170000](../database/migrations/2026_07_15_170000_add_desestimacion_to_precompras_servicios_table.php).

### 5.2 Tabla `epayco_transacciones`

Tabla de **auditoría** añadida en
[migración 2026_08_14_160000](../database/migrations/2026_08_14_160000_create_epayco_transacciones_table.php).
Una precompra puede tener varias filas (revalidaciones / reintentos); el
modelo [`EpaycoTransaccion`](../app/Models/EpaycoTransaccion.php) las persiste
con `registrarDesdeValidacion()` y expone la relación inversa
`PrecompraServicio::transaccionesEpayco()`.

| Columna          | Tipo              | Contenido                                       |
| ---------------- | ----------------- | ----------------------------------------------- |
| `id`             | PK                |                                                 |
| `precompra_id`   | FK unsigned int   | FK a `precompras_servicios.id`, **nullable**, `nullOnDelete` |
| `ref_payco`      | `string(80)`      | `x_ref_payco`                                   |
| `transaction_id` | `string(50)`      | `x_transaction_id`                              |
| `invoice`        | `string(60)`      | `x_id_invoice`                                  |
| `approval_code`  | `string(20)`      | `x_approval_code`                               |
| `cod_estado`     | `string(2)`       | `x_cod_transaction_state` (1–12)                |
| `respuesta`      | `string(100)`     | `x_response`                                    |
| `motivo`         | `string(255)`     | `x_response_reason_text`                        |
| `amount`         | `decimal(12,2)`   | `x_amount`                                      |
| `currency`       | `string(10)`      | `x_currency_code`                               |
| `bank_name`      | `string(80)`      | `x_bank_name`                                   |
| `franchise`      | `string(40)`      | `x_franchise`                                   |
| `card_mask`      | `string(30)`      | `x_card_number` (enmascarado)                   |
| `quotas`         | `string(10)`      | `x_quotas`                                      |
| `signature`      | `string(255)`     | `x_signature`                                   |
| `fecha_epayco`   | `string(40)`      | `x_date` (texto original)                       |
| `origen`         | `string(30)`      | `validacion` · `webhook` (posible futuro: `manual`) |
| `payload_json`   | `json`            | Objeto `data` crudo completo de ePayco          |
| `created_at` / `updated_at` | timestamps | Eloquent                                    |

Índices: `ref_payco`, `transaction_id`, `precompra_id` y
`(precompra_id, created_at)`.

### 5.3 Estados y mapeo ePayco → precompra

Definidos en
[`EstadoPrecompra`](../app/Services/Ecommerce/EstadoPrecompra.php):

```php
const PENDIENTE   = 'PE';
const PAGADO      = 'PA';
const DESESTIMADO = 'DE';
const RECHAZADO   = 'RE';
const ABANDONADA  = 'AB';
```

`EstadoPrecompra::desdeCodigoEpayco(int $codigo)` mapea los códigos
`x_cod_transaction_state` de ePayco:

| `x_cod_transaction_state` | Significado ePayco | Estado precompra |
| ------------------------- | ------------------ | ---------------- |
| 1                         | Aceptada           | `PA` Pagado      |
| 2                         | Rechazada          | `RE` Rechazado   |
| 4                         | Fallida            | `RE` Rechazado   |
| 6                         | Reversada          | `RE` Rechazado   |
| 7                         | Retenida           | `RE` Rechazado   |
| 12                        | Antifraude         | `RE` Rechazado   |
| 3, 8, 9, 10, 11           | Pendiente / Iniciada / Expirada / Abandonada / Cancelada | `PE` Pendiente |

> **Notas:**
> - `DE` (Desestimado) **nunca** se asigna automáticamente desde ePayco; es
>   exclusivo para cuando el usuario cancela manualmente la precompra con
>   un motivo del catálogo (`YA_NO_INTERESA`, `VALOR_ALTO`,
>   `COMPRA_OTRO_MEDIO`, `PROBLEMA_PAGO`, `OTRO`).
> - `AB` (Abandonada) se asigna cuando el usuario **cierra el checkout de
>   ePayco sin completar el pago** (callback `onClose`) o cuando el job
>   nocturno marca precombras `PE` antiguas por inactividad. Una vez en
>   `AB` la precompra **no es retomable**.

---

## 6. Flujo end-to-end

### 6.1 Diagrama general

```
┌──────────────────────────────────────────────────────────────────────────┐
│ USUARIO                                                                  │
│   1. /mercurio/servicios/index                                            │
│   2. Selecciona beneficiario y servicio                                  │
│   3. Click "Procesar pago"                                               │
│   4. (Backend crea precompra PE en DB)                                   │
│   5. Checkout open (v1 open(data) | v2 sessionId)                        │
│        + confirmation + extra4=precompra_id                              │
│        + onClose/onClosed → POST /abandonar-precompra (PE → AB)          │
│   6. Usuario paga con tarjeta / PSE / Nequi / etc.                       │
│   7a. ePayco redirige a response URL con ?ref_payco=...                  │
│       → verificarRespuestaEpayco → validarReferencia → guardarVenta      │
│   7b. (en paralelo / si cierra navegador)                                │
│       ePayco POST /api/epayco/confirmation (firma)                       │
│       → audita origen=webhook → PA + guardar-venta si aplica             │
└──────────────────────────────────────────────────────────────────────────┘
                          ↓ (cron diario 02:00)
        precompras:marcar-abandonadas → precompras PE con >7 días → AB
```

### 6.2 Paso a paso

#### ① Carga del catálogo

- `GET /mercurio/servicios/index` →
  [`EcommerceController@index`](../app/Http/Controllers/Mercurio/EcommerceController.php).
- Renderiza `mercurio/ecommerce/index.blade.php`. Inyecta a la vista:
  - `EPAYCO_PUBLIC_KEY`
  - `EPAYCO_TEST` (`true` cuando `config('app.epayco.mode') === 'development'`)
  - `EPAYCO_CHECKOUT_VERSION` (`1` o `2`)
  - `documento` del usuario activo
  - `pendientesCount` (badge rojo con cantidad de precompras en `PE`)
- En el `<head>` del blade se carga el SDK según versión (`checkout.js` o
  `checkout-v2.js`) y se configura el handler Standard cuando aplica:
  ```js
  epaycoHandler = ePayco.checkout.configure({
      key: EPAYCO_PUBLIC_KEY,
      test: EPAYCO_TEST
  });
  ```
- Se cargan las rutas generadas con `route()` para usarlas desde JS
  (incluye `epaycoConfirmation`).

#### ② Identificación del trabajador

- `POST /mercurio/servicios/identificar-trabajador` →
  [`EcommerceController@identificarTrabajador`](../app/Http/Controllers/Mercurio/EcommerceController.php).
- Llama al servicio externo **CLIS/SISU** vía
  [`ApiSubsidio`](../app/Services/Api/ApiSubsidio.php) (servicio `Movil`,
  método `identifica-trabajador`) con `cedtra = hid_documento`.
- Devuelve `{ success, data: { trabajador, nucleo_familiar, ... } }`.
- El frontend pinta el grid de **beneficiarios** (trabajador, cónyuge,
  beneficiarios).

#### ③ Listado de servicios

- `POST /mercurio/servicios/listar-servicios` →
  [`EcommerceController@listarServicios`](../app/Http/Controllers/Mercurio/EcommerceController.php).
- Misma API: `Movil` / `listar-servicios`. Devuelve los servicios con sus
  cupos disponibles.
- El frontend renderiza el grid de **servicios** con buscador y filtro por
  código.

#### ④ Selección de beneficiario + servicio → validación de tarifa

- Al hacer click en un `servicio-card`, el frontend llama a
  `seleccionarServicio(srv)` →
  [`validarTarifa(codser, numero)`](../public/src/Mercurio/Ecommerce/tarifa.js).
- `POST /mercurio/servicios/validar-tarifa` →
  [`EcommerceController@validarTarifa`](../app/Http/Controllers/Mercurio/EcommerceController.php).
- API externa `Movil` / `validar-tarifas` con
  `cedtra`, `codser`, `numero`, `codben`.
- Devuelve:
  ```json
  {
    "success": true,
    "data": {
      "valser": "35000.00",          // valor a pagar
      "categoria": "A",              // categoría del trabajador
      "temporada": "...",            // temporada aplicada
      "cupos_mes": 2,                // cupos disponibles este mes
      "cupos_disponibles": 5         // cupos globales
    }
  }
  ```
- Si `cupos_mes === 0`, se deshabilita el botón "Procesar pago" y se muestra
  alerta.

#### ⑤ Click en "Procesar pago"

[`procesarPago(event)`](../public/src/Mercurio/Ecommerce/pago.js):

1. **Valida cliente** que `valor > 0` y que `epaycoHandler` esté listo.
2. **Persiste contexto en `sessionStorage`** para sobrevivir el redirect de
   ePayco:
   ```
   epayco_cedtra, epayco_codser, epayco_numero,
   epayco_nota, epayco_codben, epayco_precompra_id
   ```
3. **Crea la precompra** vía
   [`POST /mercurio/servicios/crear-precompra`](../app/Http/Controllers/Mercurio/EcommerceController.php)
   → `PrecompraServicio::create([... 'estado' => PE])`.
4. **Registra el callback `onClose`** con
   [`registrarOnCloseEpayco()`](../public/src/Mercurio/Ecommerce/epayco.js):
   - Si el usuario cierra la modal **sin** completar el pago y no se está
     validando un pago (`__epaycoPagoEnValidacion === false`), el callback
     llama a `POST /mercurio/servicios/abandonar-precompra` con el id de la
     precompra después de **2.5 s** (espera por si ePayco redirige a la
     response URL tras un pago válido).
5. **Guarda `epayco_precompra_id`** en `sessionStorage` y abre la pasarela
   (Standard v1). Incluye `confirmation` y `extra4`:
   ```js
   epaycoHandler.open({
       name, description, invoice: 'ORD' + Date.now(),
       currency: 'cop', amount: valor,
       tax_base: '0', tax: '0',
       country: 'co', lang: 'es',
       external: 'false',
       extra1: cedtra,           // documento
       extra2: codser,
       extra3: numero,
       extra4: String(precompraId),
       response: window.location.href,
       confirmation: store.routes.epaycoConfirmation,
       name_billing, type_doc_billing: 'cc', number_doc_billing,
       email_billing
   });
   ```
   Con Smart Checkout (v2) el backend crea la sesión Apify (mismo
   `confirmation` / extras) y el frontend abre con `sessionId`.

> **`invoice`**: el frontend genera un id único local con
> `Date.now()` (`ORD1691234567890`). No se persiste en la precompra — la
> referencia canónica para reconciliar es la `ref_payco` que devuelve ePayco.

#### ⑥ Pago en la pasarela ePayco

- El usuario completa el pago dentro del modal embebido de ePayco. Puede usar
  tarjeta de crédito, PSE, Nequi, etc.
- Al finalizar, ePayco redirige al **`response`** configurado (la propia URL
  del catálogo, con los query params de retorno:
  `?ref_payco=…&x_cod_transaction_state=…&x_response_reason_text=…`).

#### ⑦ Validación en el cliente (UI)

[`verificarRespuestaEpayco()`](../public/src/Mercurio/Ecommerce/epayco.js):

- Lee `ref_payco` y `x_cod_transaction_state` del query string.
- Setea `window.__epaycoPagoEnValidacion = true` (señal para que el
  `onClose` no marque la precompra como abandonada).
- Limpia la URL con `history.replaceState` (para que el refresh no
  re-trigger la validación).
- Si el código de URL ya viene `!= 1`, muestra alerta "Pago no completado",
  libera la bandera y termina.
- Si todo OK, abre un SweetAlert "Verificando pago…" y llama a
  `validarPagoEpayco(refPayco)`.

#### ⑧ Validación server-side contra ePayco

[`validarPagoEpayco(refPayco)`](../public/src/Mercurio/Ecommerce/epayco.js)
dispara `POST /mercurio/servicios/validar-pago-epayco` →

[`EcommerceController@validarPagoEpayco`](../app/Http/Controllers/Mercurio/EcommerceController.php):

```php
$resultado = $this->epayco->validarReferencia($ref_payco);
```

[`ApiEpayco::validarReferencia()`](../app/Services/Api/ApiEpayco.php#L53-L115)
hace `GET https://secure.epayco.co/validation/v1/reference/{refPayco}` (sin
verificar SSL por un quirk histórico — ver §10) y devuelve un payload
normalizado **enriquecido**:

```php
return [
    'success' => true,
    'data' => [
        // snapshot operativo
        'aprobado'    => (int)$tx['x_cod_transaction_state'] === 1,
        'cod_estado'  => intval($tx['x_cod_transaction_state'] ?? 0),
        'respuesta'   => $tx['x_response'] ?? 'Sin respuesta',
        'motivo'      => $tx['x_response_reason_text'] ?? '',
        'monto'       => $tx['x_amount'] ?? '0',
        'ref_payco'   => $tx['x_ref_payco'] ?? $refPayco,

        // campos crudos extraídos (para auditoría)
        'x_id_invoice'     => $tx['x_id_invoice']      ?? null,
        'x_transaction_id' => $tx['x_transaction_id']  ?? null,
        'x_approval_code'  => $tx['x_approval_code']   ?? null,
        'x_bank_name'      => $tx['x_bank_name']       ?? null,
        'x_franchise'      => $tx['x_franchise']       ?? null,
        'x_card_number'    => $tx['x_card_number']     ?? null,
        'x_quotas'         => $tx['x_quotas']          ?? null,
        'x_currency_code'  => $tx['x_currency_code']   ?? null,
        'x_date'           => $tx['x_date']            ?? null,
        'x_signature'      => $tx['x_signature']       ?? null,

        // payload crudo completo (para persistir 1:1 en epayco_transacciones)
        'payload_raw'      => is_array($tx) ? $tx : null,
    ],
];
```

Si la respuesta es válida, el controller llama a
[`actualizarPrecompraDesdePago()`](../app/Http/Controllers/Mercurio/EcommerceController.php#L712-L769)
que ejecuta, en este orden:

1. **Localiza la precompra**:
   - Primero por `ref_payco`. Si no la encuentra, busca por
     `precompra_id` (pasado por el frontend) **solo si está en `PE` o `AB`**
     (carrera con `onClose`: si el usuario cerró el checkout al mismo
     tiempo que ePayco redirigía, la precompra ya pudo haber pasado a
     `AB`).
2. **Registra auditoría** llamando a
   [`registrarTransaccionEpayco()`](../app/Http/Controllers/Mercurio/EcommerceController.php#L776-L783)
   → `EpaycoTransaccion::registrarDesdeValidacion()`. Esto **siempre se
   ejecuta**, incluso si la precompra ya está pagada o no es actualizable.
3. **Si no hay precompra actualizable, retorna** (la auditoría queda).
4. **No degrada** una precompra que ya esté en `PA`.
5. **Mapea** `cod_estado` → `EstadoPrecompra::desdeCodigoEpayco()` y
   persiste `estado`, `ref_payco`, `cod_estado_epayco`, `motivo_epayco`.
6. Si quedó `PA` y aún no tenía `fecha_pago`, lo setea.

#### ⑨ Si `aprobado === true` → guardar venta

[`guardarVenta(refpago)`](../public/src/Mercurio/Ecommerce/venta.js)
dispara `POST /mercurio/servicios/guardar-venta` con los datos originales
(`cedtra`, `codser`, `numero`, `codben`, `nota`, `refpago`, `precompra_id`).

[`EcommerceController@guardarVenta`](../app/Http/Controllers/Mercurio/EcommerceController.php):

1. Vuelve a llamar a `ApiEpayco::validarReferencia($refpago)` (doble check
   server-side → también deja otra fila en `epayco_transacciones`).
2. Si `x_cod_transaction_state !== 1`, rechaza la venta y actualiza la
   precompra.
3. Si está aprobada, llama al servicio externo de subsidio:
   ```
   ApiSubsidio::send([
       'servicio' => 'Movil',
       'metodo'   => 'guardar-venta',
       'params'   => ['cedtra', 'codser', 'numero', 'refpago', 'nota', 'codben']
   ])
   ```
4. Devuelve el resultado al frontend, que muestra SweetAlert "Compra exitosa"
   y limpia el formulario después de 3 s.

#### ⑩ Si el usuario cierra el checkout sin pagar → abandonar

[`marcarPrecompraAbandonada(precompraId)`](../public/src/Mercurio/Ecommerce/epayco.js)
se dispara desde el callback `onCloseModal` (registrado en el paso ⑤):

1. Verifica que `window.__epaycoPagoEnValidacion === false` (si es `true`,
   significa que la response URL está en juego y la precompra puede estar
   pagándose en paralelo).
2. Hace `POST /mercurio/servicios/abandonar-precompra` con `precompra_id`.

[`EcommerceController@abandonarPrecompra`](../app/Http/Controllers/Mercurio/EcommerceController.php):

1. Valida `precompra_id` y que la precompra sea del usuario.
2. Si ya está `PA`/`DE`/`RE`, no la degrada → responde `success: true`
   con `abandonada: false`.
3. Si ya estaba `AB`, responde `abandonada: true` sin reescribir.
4. Si está `PE`, la marca como `AB` con motivo `ABANDONO_CHECKOUT` y
   detalle `Cierre del checkout de ePayco sin completar el pago`.
5. Loguea con `setLogger()` para trazabilidad.

#### ⑪ Limpieza

`limpiarSessionEpayco()` borra las claves en `sessionStorage` y
`limpiarSeleccionServicio()` resetea el panel lateral.

---

## 7. Compras pendientes y desestimación

Ruta `GET /mercurio/servicios/compras-pendientes` →
[`comprasPendientes()`](../app/Http/Controllers/Mercurio/EcommerceController.php) +
[`public/src/Mercurio/ComprasPendientes/`](../public/src/Mercurio/ComprasPendientes/)
(`main.js` + `pago.js`, …).

### 7.1 Listado

- `POST /mercurio/servicios/listar-precompras` →
  [`listarPrecompras()`](../app/Http/Controllers/Mercurio/EcommerceController.php)
  trae todas las precompras en estado `PE` del `documento` activo. Las
  precompras en `AB` ya **no** aparecen en este listado (quedan cerradas).

### 7.2 Retomar pago

[`retomarPago(precompra)`](../public/src/Mercurio/ComprasPendientes/pago.js):

1. Llama a `validarTarifa` para re-confirmar disponibilidad y precio
   vigente.
2. Si todo OK, llama a `abrirCheckout()` que reusa la misma precompra
   poniendo su `id` en `sessionStorage.epayco_precompra_id`.
3. Cuando ePayco redirige al catálogo, `verificarRespuestaEpayco()` +
   `validarPagoEpayco()` + `guardarVenta()` cierran el ciclo reutilizando la
   **misma fila de precompra**.

### 7.3 Desestimar compra

`POST /mercurio/servicios/desestimar-precompra` →
[`desestimarPrecompra()`](../app/Http/Controllers/Mercurio/EcommerceController.php):

- Valida `motivo ∈ {YA_NO_INTERESA, VALOR_ALTO, COMPRA_OTRO_MEDIO,
  PROBLEMA_PAGO, OTRO}`. Si es `OTRO`, exige `detalle` libre (≤ 255
  caracteres).
- Verifica que la precompra pertenezca al usuario y siga en `PE`.
- Marca `estado = DE`, persiste `motivo_desestimacion`,
  `detalle_desestimacion` y `fecha_desestimacion`.

---

## 8. Detección y limpieza de precompras abandonadas

Hay **dos mecanismos complementarios** para evitar precompras `PE` huérfanas.

### 8.1 `onClose` (frontend → backend)

Cuando el usuario cierra el checkout de ePayco sin completar el pago, el
callback `onCloseModal` configurado en el paso ⑤ dispara, después de
2.5 s, `POST /mercurio/servicios/abandonar-precompra` → estado `AB` con
motivo `ABANDONO_CHECKOUT`. Este mecanismo **no requiere que el usuario
haya vuelto al catálogo**.

Carrera controlada con `window.__epaycoPagoEnValidacion`:
- Si la response URL ya llegó y disparó la validación del pago, esa
  bandera queda `true` y `onClose` **no** marca la precompra como
  abandonada (evita pisar un pago legítimo que ya pasó la barrera del
  backend).
- Una vez que `validarPagoEpayco()` termina (aprobado o rechazado), la
  bandera se libera a `false`.

### 8.2 Job nocturno de inactividad

Comando
[`precompras:marcar-abandonadas`](../app/Console/Commands/MarcarPrecomprasAbandonadasCommand.php)
que delega en el job
[`MarcarPrecomprasAbandonadas`](../app/Jobs/MarcarPrecomprasAbandonadas.php).

```bash
php artisan precompras:marcar-abandonadas --dias=7          # ejecución directa
php artisan precompras:marcar-abandonadas --dry-run         # simulación (no escribe)
php artisan precompras:marcar-abandonadas --queue --dias=7  # encolar el job
```

Lógica:
- Selecciona precompras con `estado = PE` y `fecha_precompra < now() - N días`
  (N configurable vía `--dias`, por defecto 7).
- Las pasa a `estado = AB` con motivo `ABANDONO_INACTIVIDAD` y detalle
  `Abandonada automaticamente por inactividad (>N dias)`.
- Loguea la cantidad afectada vía `Log::info()`.

Está agendado en
[routes/console.php](../routes/console.php) para correr **todos los días a
las 02:00**:

```php
Schedule::command('precompras:marcar-abandonadas --dias=7')
    ->dailyAt('02:00')
    ->withoutOverlapping();
```

Sirve de red de seguridad para los casos donde el usuario cerró el
navegador y el callback `onClose` nunca tuvo oportunidad de ejecutarse.

---

## 9. Resumen de rutas

| Método | Ruta                                              | Controller                                |
| ------ | ------------------------------------------------- | ----------------------------------------- |
| GET    | `/mercurio/servicios/index`                       | `EcommerceController@index`               |
| GET    | `/mercurio/servicios/compras-pendientes`          | `EcommerceController@comprasPendientes`   |
| GET    | `/mercurio/servicios/ver-compras`                 | `EcommerceController@verCompras`          |
| POST   | `/mercurio/servicios/identificar-trabajador`      | `EcommerceController@identificarTrabajador`|
| POST   | `/mercurio/servicios/listar-servicios`            | `EcommerceController@listarServicios`     |
| POST   | `/mercurio/servicios/validar-tarifa`              | `EcommerceController@validarTarifa`       |
| POST   | `/mercurio/servicios/crear-precompra`             | `EcommerceController@crearPrecompra`      |
| POST   | `/mercurio/servicios/abandonar-precompra`         | `EcommerceController@abandonarPrecompra`  |
| POST   | `/mercurio/servicios/validar-pago-epayco`         | `EcommerceController@validarPagoEpayco`   |
| POST   | `/mercurio/servicios/guardar-venta`               | `EcommerceController@guardarVenta`        |
| POST   | `/mercurio/servicios/mis-compras`                 | `EcommerceController@misCompras`          |
| POST   | `/mercurio/servicios/listar-precompras`           | `EcommerceController@listarPrecompras`    |
| POST   | `/mercurio/servicios/desestimar-precompra`        | `EcommerceController@desestimarPrecompra` |

Todas viven bajo `middleware('mercurio.auth')`.

---

## 10. APIs externas consumidas

### 10.1 ePayco — validación de pagos

- **Método:** `GET`
- **URL:** `https://secure.epayco.co/validation/v1/reference/{ref_payco}`
- **Auth:** ninguno en este endpoint público (solo el path lleva la
  referencia). Las credenciales `EPAYCO_PUBLIC_KEY` / `EPAYCO_PRIVATE_KEY`
  se usan en `ApiEpayco::send()` para otros endpoints administrativos que el
  módulo no usa todavía.
- **Llamador:** [`ApiEpayco::validarReferencia()`](../app/Services/Api/ApiEpayco.php).
- **Campos consumidos de la respuesta:** `x_cod_transaction_state`,
  `x_response`, `x_response_reason_text`, `x_amount`, `x_ref_payco`,
  `x_id_invoice`, `x_transaction_id`, `x_approval_code`, `x_bank_name`,
  `x_franchise`, `x_card_number`, `x_quotas`, `x_currency_code`, `x_date`,
  `x_signature`.

### 10.2 CLIS/SISU — backend de subsidio

- **Cliente:** [`ApiSubsidio`](../app/Services/Api/ApiSubsidio.php).
- **Auth:** Basic Auth con `HOST_API_USER` y `HOST_API_PASSWORD`
  (configurados en `config/app.php`).
- **Conexión:** `api-clisisu` (resuelta por la tabla `api_endpoints`).
- **Métodos invocados por el módulo:**
  - `Movil / identifica-trabajador` → datos del trabajador y núcleo familiar
  - `Movil / listar-servicios` → catálogo y cupos
  - `Movil / validar-tarifas` → cálculo de valor y cupos del mes
  - `Movil / guardar-venta` → persistencia oficial de la venta
  - `Movil / mis-compras` → consulta de compras previas

---

## 11. Consideraciones de seguridad y operación

1. **No confiar en el cliente.** Toda confirmación se valida con
   `validarReferencia()` antes de invocar `guardar-venta`. El query string
   que llega en el redirect solo se usa como UX.
2. **Doble validación del pago.** `validarPagoEpayco()` y `guardarVenta()`
   llaman ambos a `ApiEpayco::validarReferencia()`; esto es defensa en
   profundidad por si el usuario re-abre la URL de respuesta varias veces.
   Cada llamada deja su propia fila en `epayco_transacciones`.
3. **Carrera `onClose` vs response URL.** La bandera
   `window.__epaycoPagoEnValidacion` evita que un `onClose` marque como
   `AB` una precompra cuyo pago está siendo validado en paralelo.
4. **Carrera `actualizarPrecompraDesdePago()`** permite actualizar una
   precompra en `AB` si llega una validación con `ref_payco` (pago tardío
   que llegó después del `onClose`).
5. **CSRF.** Todas las llamadas AJAX envían el token CSRF vía
   `$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': ... } })`.
6. **TLS.** `ApiEpayco` verifica el certificado SSL por defecto
   (`EPAYCO_HTTP_VERIFY_SSL=true`) en `validarReferencia` y llamadas Apify.
   Solo desactivar en entornos con CA/proxy problemáticos (`false`).
7. **Trazabilidad.** `setLogger()` se invoca en cada paso crítico
   (creación de precompra, pago validado, venta guardada, desestimación,
   abandono) y `epayco_transacciones` guarda el payload crudo de cada
   validación / webhook.
8. **Idempotencia.** `actualizarPrecompraDesdePago()` / webhook no degradan
   una precompra que ya esté en `PA`. La auditoría es siempre
   *append-only* — nunca se sobrescribe. Además,
   `EcommerceController::guardarVenta` y el webhook omiten la llamada a
   Subsidio si la precompra **ya estaba** `PA` antes de actualizar (evita
   doble `guardar-venta` cuando el webhook llega primero).
9. **Datos personales.** `sanitizarTexto()` en el frontend limpia acentos y
   caracteres no ASCII antes de enviar el `name` del checkout (ePayco es
   quisquilloso con tildes y emojis). El email se sanea con un fallback
   `sin@email.com` cuando el trabajador no tiene correo registrado.
10. **Modo development.** `EPAYCO_MODE=development` activa el modo test del
    checkout (`test: true` en JS). El cambio de comportamiento real entre
    development/production se hace dentro del panel de ePayco.
11. **Webhook firmado.** `POST /api/epayco/confirmation` valida
    `x_signature` con `EPAYCO_CUSTOMER_ID` / `EPAYCO_P_KEY`. Guía:
    [epayco-webhook-confirmation.md](./epayco-webhook-confirmation.md).
12. **`EPAYCO_FORCE_APPROVED` (solo QA).** Si es `true` y el entorno no es
    production (`APP_ENV` / `APP_MODE`), `validarReferencia` fuerza
    `aprobado=true`. **Nunca** activar en producción. No afecta el webhook.

---

## 12. Errores más comunes y diagnóstico

| Síntoma                                                           | Causa probable                                                                  | Dónde mirar                                                                                  |
| ----------------------------------------------------------------- | ------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- |
| El modal de ePayco no abre                                        | `epaycoHandler` es `null` (SDK no cargó o falla la configuración).              | Console log "Error inicializando ePayco" en [index.blade.php](../resources/views/mercurio/ecommerce/index.blade.php). |
| SweetAlert "No se pudo inicializar la pasarela"                   | Falta `window.epaycoHandler` por error JS.                                     | Recargar la página con el SDK cargado.                                                       |
| "Referencia de pago no proporcionada"                             | El usuario cerró la pasarela sin completar el redirect.                        | `verificarRespuestaEpayco()` no encontró `ref_payco` en la URL ni en `/checkout/{ref}/response`. |
| Pago aprobado pero la venta no se registra                        | `ApiSubsidio::guardar-venta` devuelve `flag = false`.                          | Revisar logs del backend en `ApiSubsidio`.                                                   |
| Precompra queda `PE` eternamente                                  | `onClose` nunca se disparó (cierre brusco del navegador) y el job nocturno aún no la procesó. | Esperar el siguiente run de `precompras:marcar-abandonadas` (02:00). |
| ePayco devuelve código distinto de 1 pero la precompra queda `PE`  | El usuario reabrió la URL de respuesta tras un pago válido anterior.           | `actualizarPrecompraDesdePago()` no degrada `PA` → `RE` ni `PE` → `PE` si ya estaba pagada.  |
| Precompra pasa a `AB` aunque el pago se completó                  | Carrera entre `onClose` y la response URL cuando la red es lenta.              | Revisar `epayco_transacciones` por `precompra_id`; si hay una fila con `cod_estado=1` y la precompra está `AB`, re-marcar manualmente como `PA`. |
| No aparecen filas en `epayco_transacciones`                       | `validarReferencia()` no devolvió HTTP 200 o no llegó al insert.               | Revisar `setLogger('Error registrando epayco_transacciones: ...')` en backend.              |
| `precompras:marcar-abandonadas` no se ejecuta                     | Scheduler no configurado o `withoutOverlapping()` trabado.                      | `php artisan schedule:list` y `schedule:run`.                                                |
| Cupos del mes en 0                                               | El beneficiario ya agotó la cuota mensual del servicio.                         | `validar-tarifa` retorna `cupos_mes = 0` y el frontend bloquea el botón "Procesar pago".    |

---

## 13. Anexo: callbacks y parámetros relevantes de ePayco

Cuando ePayco redirige al `response` URL, envía los siguientes query params
(los nombres pueden variar ligeramente según el método de pago):

| Param                       | Significado                                             |
| --------------------------- | ------------------------------------------------------- |
| `ref_payco` / `x_ref_payco` | Referencia única de la transacción en ePayco            |
| `x_cod_transaction_state`   | Código de estado (1=Aceptada, 2=Rechazada, etc.)        |
| `x_response`                | Respuesta corta (`Aceptada`, `Rechazada`, ...)          |
| `x_response_reason_text`    | Motivo detallado (texto legible)                        |
| `x_amount`                  | Monto cobrado                                           |
| `x_id_invoice`              | Identificador enviado como `invoice` (`ORD…`)           |

Y los que se persisten en `epayco_transacciones` desde el payload crudo:
`x_transaction_id`, `x_approval_code`, `x_bank_name`, `x_franchise`,
`x_card_number`, `x_quotas`, `x_currency_code`, `x_date`, `x_signature`.

El frontend lee `ref_payco` desde varios nombres
(`ref_payco` / `refPayco` / `x_ref_payco`) y también analiza el path
`/checkout/{ref}/response` por compatibilidad.

---

## 14. Glosario rápido

- **Precompra** — registro en `precompras_servicios` que representa una
  intención de compra antes de que ePayco confirme el pago. Es el ancla que
  permite reintentar pagos y conciliar transacciones.
- **ref_payco** — referencia única de la transacción devuelta por ePayco;
  es la llave de cruce entre la precompra y el pago real.
- **Cod_estado_epayco** — copia local del `x_cod_transaction_state`; se
  persiste para auditoría y para mostrar el motivo del fallo.
- **Estado DE** — `Desestimado`. Solo el usuario lo asigna desde la pantalla
  de "Compras pendientes" con un motivo del catálogo; nunca viene de
  ePayco.
- **Estado AB** — `Abandonada`. Asignado por `onClose` (frontend) o por el
  job nocturno `precompras:marcar-abandonadas`. Una precompra en `AB` **no
  es retomable** y no aparece en el listado de "Compras pendientes".
- **EpaycoTransaccion** — fila en `epayco_transacciones` con un snapshot
  completo de cada validación (`origen=validacion`) o webhook
  (`origen=webhook`). Es append-only y se usa para auditoría y soporte
  (tabla/CSV Admservicios + modal `detalle/{id}`).

---

## 15. Próximos pasos / mejoras sugeridas

| # | Acción | Estado |
| - | ------ | ------ |
| 1 | TLS `EPAYCO_HTTP_VERIFY_SSL` | Hecho |
| 2 | `transaction_id` / `approval_code` en Admservicios (tabla/CSV) | Hecho |
| 3 | Modal detalle Admservicios con historial completo | Hecho |
| 4 | Webhook `confirmation` + firma | Hecho |
| 5 | Job `precompras:marcar-abandonadas` | Hecho |
| 6 | `onClose` / `onClosed` + abandono `AB` | Hecho |
| 7 | Auditoría `epayco_transacciones` | Hecho |
| 8 | Checkout modular + `EPAYCO_CHECKOUT_VERSION` | Hecho |
| 9 | Móvil/WebView fuerza Checkout v2 `type: standard` | Hecho |
| 10 | `EPAYCO_FORCE_APPROVED` (QA non-prod) | Hecho |
| 11 | `transaction_id` / `approval_code` en `ReporteComprasServicios` | Hecho |
| 12 | Whitelist de medios (`methods` / `methodsDisable`) | Pendiente |
| 13 | TLS configurable en `APIClient` (Subsidio) | Pendiente |
| 14 | Tests Feature del webhook | Pendiente |
| 15 | Alertas operativas firma `400` / credenciales `503` | Pendiente |

Detalle de backlog: [analisis-mejoras-epayco-precompras.md](./analisis-mejoras-epayco-precompras.md).