# Pasarela de Pago ePayco — Catálogo de Servicios (Mercurio)

Documentación del flujo completo de la pasarela de pago **ePayco** integrada en el
catálogo de servicios de Mercurio, accesible en
`https://comfaca.ecommerce.com.co:9043/mercurio/servicios/index` (ruta
`/mercurio/servicios/index`; el contenedor publica `9043→443`, HTTPS obligatorio
para Smart Checkout v2 / `crypto.randomUUID`).

> **Última revisión:** 2026-11-04 (alineación con doc oficial Smart Checkout
> v2 / Apify; validación vía `validarReferenciaApify`; firma por cuenta DB).
> **Audiencia:** desarrolladores y mantenedores del módulo de ecommerce Mercurio.
> **Fuente oficial ePayco:** [repo-api-epayco.md](../../../repo-api-epayco.md)
> (captura de docs.epayco.com — General, Implementación, Respuesta y Confirmación).

Documentos relacionados:
- [epayco-modal-control.md](./epayco-modal-control.md) — control de la modal.
- [epayco-estado-transaccion-db.md](./epayco-estado-transaccion-db.md) —
  campos ePayco persistidos.
- [epayco-webhook-confirmation.md](./epayco-webhook-confirmation.md) —
  webhook + firma.
- [epayco-validacion-respuesta-cliente.md](./epayco-validacion-respuesta-cliente.md)
  — validación API, errores de conexión ePayco, SweetAlert por caso e
  excepciones si la precompra ya está `PA`.
- [pendientes-compra-en-linea.md](./pendientes-compra-en-linea.md) —
  backlog P0–P3 (ops, TLS, alertas, tests, cola, whitelist, UX reintento).
- [precompras-job-abandonadas.md](./precompras-job-abandonadas.md) — job TTL.
- [analisis-mejoras-epayco-precompras.md](./analisis-mejoras-epayco-precompras.md)
  — inventario aplicado vs pendientes.
- [conciliacion-subsidio.md](./conciliacion-subsidio.md) — cruce
  precompras `PA` vs ventas Subsidio (`ref_payco` = `refpago`).

---

## 1. Resumen general

La compra de servicios para los usuarios finales (trabajadores, beneficiarios)
se paga electrónicamente con **ePayco**. Según `EPAYCO_CHECKOUT_VERSION`:

- **`1`** — Standard Checkout (`checkout.js`, modal embebida) en **desktop**.
- **`2`** — Smart Checkout (`checkout-v2.js` + sesión Apify) en desktop (`onpage`).
- **Móvil / WebView** — siempre Smart Checkout v2 con `type: standard`
  (entorno seguro ePayco / redirect), aunque `EPAYCO_CHECKOUT_VERSION=1`.

Además hay **consulta de transacción vía Apify** (`POST /payment/transaction`)
y un **webhook `confirmation`** firmado para confirmar el estado real aunque
el cliente no vuelva. La página `response` solo sirve de UX (no es fuente de
verdad).

El diseño se articula en torno a estos principios:

1. **Precompra antes de pagar** — Registro `PE` en `precompras_servicios`
   antes de abrir la pasarela.
2. **Validación server-side del pago** — No se confía solo en query params;
   el backend consulta Apify con `referencePayco` (y/o recibe el webhook) y
   solo con código de estado `1` (Aceptada) registra la venta en subsidio.
3. **Auditoría append-only** — Cada validación / webhook inserta una fila en
   `epayco_transacciones` (`origen=validacion` | `webhook`).
4. **Detección de abandonos** — `onCloseModal` (v1) / hooks de cierre (v2) →
   `PE → AB` y job nocturno para PE huérfanas (> N días).
5. **Confirmación híbrida** — Cliente (`response`) + webhook firmado.
6. **Multi-cuenta ePayco** — Las llaves de comercio **no** salen del `.env`.
   Cada servicio del catálogo trae `epayco` (= `P_CUST_ID_CLIENTE`); se resuelve
   en `epayco_cuentas` (`EpaycoCuenta.p_id_customer`) y se guarda en la
   precompra para firmar el webhook.

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
| Servicio ePayco   | [app/Services/Api/ApiEpayco.php](../app/Services/Api/ApiEpayco.php) (`withCuenta`) |
| Resolver cuenta   | [EpaycoCuentaResolver](../app/Services/Ecommerce/EpaycoCuentaResolver.php) |
| Cuentas (Cajas)   | [EpaycoCuenta](../app/Models/EpaycoCuenta.php) / CRUD `/cajas/epayco-cuentas` |
| Confirmación      | [EpaycoConfirmationService](../app/Services/Ecommerce/EpaycoConfirmationService.php) / [EpaycoSignatureValidator](../app/Services/Ecommerce/EpaycoSignatureValidator.php) |
| Servicio Subsidio | [app/Services/Api/ApiSubsidio.php](../app/Services/Api/ApiSubsidio.php)                                |
| Estados           | [app/Services/Ecommerce/EstadoPrecompra.php](../app/Services/Ecommerce/EstadoPrecompra.php)            |
| Modelo precompra  | [app/Models/PrecompraServicio.php](../app/Models/PrecompraServicio.php)                                |
| Modelo auditoría  | [app/Models/EpaycoTransaccion.php](../app/Models/EpaycoTransaccion.php)                                |
| Job limpieza      | [app/Jobs/MarcarPrecomprasAbandonadas.php](../app/Jobs/MarcarPrecomprasAbandonadas.php)                |
| Comando consola   | [app/Console/Commands/MarcarPrecomprasAbandonadasCommand.php](../app/Console/Commands/MarcarPrecomprasAbandonadasCommand.php) |
| Config            | [config/app.php](../config/app.php) (sección `epayco`)                                                 |
| Scripts SDK       | [resources/views/mercurio/ecommerce/_epayco_scripts.blade.php](../resources/views/mercurio/ecommerce/_epayco_scripts.blade.php) (`checkout.js` / `checkout-v2.js`) |
| Migraciones       | [2026_07_15_100000_create_precompras_servicios_table](../database/migrations/2026_07_15_100000_create_precompras_servicios_table.php)<br>[2026_07_15_170000_add_desestimacion_to_precompras_servicios_table](../database/migrations/2026_07_15_170000_add_desestimacion_to_precompras_servicios_table.php)<br>[2026_08_14_160000_create_epayco_transacciones_table](../database/migrations/2026_08_14_160000_create_epayco_transacciones_table.php)<br>[2026_09_09_143000_create_epayco_cuentas_table](../database/migrations/2026_09_09_143000_create_epayco_cuentas_table.php)<br>[2026_09_09_163000_add_p_id_customer_to_precompras_servicios_table](../database/migrations/2026_09_09_163000_add_p_id_customer_to_precompras_servicios_table.php) |

---

## 3. Configuración

### 3.1 Variables de entorno (`.env`)

**Infra (siguen en `.env`):**

```ini
EPAYCO_CHECKOUT_VERSION="2"               # 1 = Standard, 2 = Smart Checkout
EPAYCO_APIFY_URL="https://apify.epayco.co" # base API Smart Checkout
EPAYCO_HTTP_VERIFY_SSL=true
EPAYCO_FORCE_APPROVED=false               # TEMPORAL QA: solo non-prod
```

**Comercio (desde DB `epayco_cuentas`, admin Cajas):**
`account`, `env_mode`, `public_key`, `private_key`, `p_key`, `p_id_customer`.

Lookup por servicio: campo **`epayco`** en `listar-servicios` =
`EpaycoCuenta.p_id_customer`. Se persiste en `precompras_servicios.p_id_customer`.

> Deprecadas para checkout/webhook: `EPAYCO_PUBLIC_KEY`, `EPAYCO_PRIVATE_KEY`,
> `EPAYCO_CUSTOMER_ID`, `EPAYCO_P_KEY`, `EPAYCO_MODE` (el modo viene de
> `cuenta.env_mode`).

### 3.2 Endpoints Apify (Smart Checkout + consulta de transacción)

La base URL es `config('app.epayco.apify_url')` → `EPAYCO_APIFY_URL`
(default `https://apify.epayco.co`). Todas las llamadas usan las llaves de la
cuenta resuelta con `ApiEpayco::withCuenta()` y respetan
`EPAYCO_HTTP_VERIFY_SSL`.

| Paso | Método | Path | Auth | Uso en Mercurio |
| ---- | ------ | ---- | ---- | --------------- |
| Login | `POST` | `/login` | Basic `PUBLIC_KEY:PRIVATE_KEY` (base64) | `ApiEpayco::obtenerTokenApify()` (token JWT cacheado) |
| Crear sesión | `POST` | `/payment/session/create` | Bearer token | `ApiEpayco::crearSesionCheckout()` → `sessionId` |
| Consultar pago | `POST` | `/payment/transaction` | Bearer token | `ApiEpayco::validarReferenciaApify()` body `{ referencePayco }` |

> **Histórico:** la migración
> [2026_07_08_120000_add_epayco_reference_api_endpoint](../database/migrations/2026_07_08_120000_add_epayco_reference_api_endpoint.php)
> insertó `Epayco-Reference` → `https://secure.epayco.co/validation/v1` en
> `api_endpoints`. Ese host **ya no lo consume** el código de checkout;
> la validación activa es Apify. La doc oficial aún documenta el GET
> `secure.epayco.co/validation/v1/reference/{ref_payco}` para enriquecer la
> página de `response` en el cliente; Mercurio no lo usa en runtime.

Detalle de propiedades de sesión y hooks: §16.

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
│   5. Checkout open:                                                      │
│        v1 → open(data) + onCloseModal                                    │
│        v2 → Apify session + configure({sessionId,type,test}) + open()    │
│        + confirmation + extras.extra4=precompra_id                       │
│        + cierre → POST /abandonar-precompra (PE → AB)                    │
│   6. Usuario paga con tarjeta / PSE / Nequi / etc.                       │
│   7a. ePayco redirige a response URL con ?ref_payco=...                  │
│       → verificarRespuestaEpayco → validarReferenciaApify → guardarVenta │
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
  - `EPAYCO_CHECKOUT_VERSION` (`1` o `2`)
  - `documento` del usuario activo
  - `pendientesCount` (badge rojo con cantidad de precompras en `PE`)
- El partial `_epayco_scripts` carga el SDK según versión (`checkout.js` o
  `checkout-v2.js`). Las llaves / `test` **no** se inyectan en el layout: se
  resuelven por cuenta al crear precompra (v1) o sesión Apify (v2).
- En v1, `configurarCheckoutV1(publicKey, test)` arma el handler con
  `ePayco.checkout.configure({ key, test })` al momento del pago.
- Se cargan las rutas generadas con `route()` para usarlas desde JS
  (incluye `crearSesionEpayco` y `epaycoConfirmation`).

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

1. Valida cupos, items, valor y campo `epayco` del servicio; confirma contacto.
2. **Persiste contexto en `sessionStorage`** para sobrevivir el redirect:
   ```
   epayco_cedtra, epayco_codser, epayco_numero,
   epayco_nota, epayco_codben, epayco_items, epayco_p_id_customer
   ```
3. Según versión / entorno:
   - **Smart Checkout v2** (`EPAYCO_CHECKOUT_VERSION=2` o móvil):
     [`abrirCheckoutV2()`](../public/src/Mercurio/Ecommerce/epayco.js) →
     `POST /mercurio/servicios/crear-sesion-epayco` (crea/reusa precompra +
     sesión Apify) →
     `ePayco.checkout.configure({ sessionId, type, test })` → `open()`.
     `type` = `onpage` (desktop) o `standard` (móvil/WebView).
   - **Standard v1** (desktop con versión `1`):
     `POST /crear-precompra` → `configurarCheckoutV1(public_key, test)` →
     `registrarOnCloseEpayco()` → `handler.open({...})` con `confirmation`
     y `extra4`.

Payload típico de apertura **v1**:
```js
epaycoHandler.open({
    name, description, invoice: 'ORD' + Date.now(),
    currency: 'cop', amount: valor,
    tax_base: '0', tax: '0',
    country: 'co', lang: 'es',
    external: 'false',
    extra1: cedtra, extra2: codser, extra3: numero,
    extra4: String(precompraId),
    response: window.location.href,
    confirmation: store.routes.epaycoConfirmation,
    name_billing, type_doc_billing: 'cc', number_doc_billing,
    email_billing
});
```

Sesión Apify **v2** (backend, resumido — ver §16):
```json
{
  "checkout_version": "2",
  "name": "<nombre servicio>",
  "description": "<nombre servicio>",
  "invoice": "ORD{precompra_id}-{timestamp}",
  "currency": "COP",
  "amount": 35000.0,
  "lang": "ES",
  "country": "CO",
  "response": "<route servicios.index>",
  "confirmation": "<route api.epayco.confirmation>",
  "extras": { "extra1": "cedtra", "extra2": "codser", "extra3": "numero",
              "extra4": "precompra_id", "extra5": "billing.name", "extra6": "billing.email" },
  "billing": { "email", "name", "typeDoc": "CC", "numberDoc", "callingCode": "+57", "mobilePhone", "address" }
}
```

> **`invoice`**: en v1 lo genera el front (`ORD` + `Date.now()`); en v2 el
> backend usa `ORD{precompra_id}-{time()}`. No se persiste en la precompra —
> la referencia canónica es `ref_payco`.

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
$apiEpayco = $this->apiEpaycoParaPrecompra($precompraAntes);
$resultado = $apiEpayco->validarReferenciaApify($ref_payco);
```

[`ApiEpayco::validarReferenciaApify()`](../app/Services/Api/ApiEpayco.php)
hace `POST {EPAYCO_APIFY_URL}/payment/transaction` con Bearer (login Apify
previo) y body `{ "referencePayco": "..." }` (TLS según
`EPAYCO_HTTP_VERIFY_SSL`) y:

1. Si Apify responde `success: false` → `success: false` + mensaje
   (`textResponse` / `titleResponse`).
2. Si hay transacción válida, normaliza `aprobado` / `cod_estado` /
   `payload_raw` desde campos Apify (`codTransactionState`, `refPayco`, …).
3. Detalle de casos y SweetAlert al cliente:
   [epayco-validacion-respuesta-cliente.md](./epayco-validacion-respuesta-cliente.md).

Si `validarReferenciaApify` falla pero la precompra ya está `PA` (webhook),
`validarPagoEpayco` responde como aprobado (`ya_pagada`) para no bloquear
el flujo.

Si la respuesta es válida, el controller llama a
[`actualizarPrecompraDesdePago()`](../app/Http/Controllers/Mercurio/EcommerceController.php)
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

1. Resuelve la precompra (ref / id) **antes** de interpretar ePayco.
2. Vuelve a llamar a `ApiEpayco::validarReferenciaApify($refpago)` (doble check).
3. Si la API falla o no aprueba **pero** la precompra ya está `PA` →
   responde éxito “Venta ya registrada previamente” (no rechazo falso).
4. Si aprueba y aún no estaba `PA`, llama a Subsidio `guardar-venta`.
5. Si ya estaba `PA` y la API aprueba → omite Subsidio (idempotencia).
6. Frontend: SweetAlert “Compra exitosa” (o “Error” solo si no hay `PA`).

Matriz completa de mensajes UI:
[epayco-validacion-respuesta-cliente.md](./epayco-validacion-respuesta-cliente.md).

#### ⑩ Si el usuario cierra el checkout sin pagar → abandonar

[`marcarPrecompraAbandonada(precompraId)`](../public/src/Mercurio/Ecommerce/epayco.js)
se dispara desde el callback de cierre (v1: `onCloseModal` vía
`registrarOnCloseEpayco`; v2 onpage: hooks de cierre — ver §16.4):

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

### 8.1 Cierre del checkout (frontend → backend)

Cuando el usuario cierra el checkout sin completar el pago, tras **2.5 s**
se llama a `POST /mercurio/servicios/abandonar-precompra` → estado `AB`
con motivo `ABANDONO_CHECKOUT` (si `__epaycoPagoEnValidacion === false`).

- **v1:** `epaycoHandler.onCloseModal`.
- **v2 `onpage`:** debería usarse `checkout.setHooks({ onClosed, ... })`
  (API oficial). Hoy el código aún intenta `onClosed(fn)` / `onErrors(fn)`
  — pendiente de alinear (§15 #16).
- **v2 `standard`:** no hay hooks de cierre en el dominio propio (redirect);
  el job nocturno cubre abandonos huérfanos.

Carrera controlada con `window.__epaycoPagoEnValidacion`:
- Si la response URL ya disparó la validación, la bandera queda `true` y
  el cierre **no** marca `AB`.
- Al terminar `validarPagoEpayco()` (aprobado o rechazado) se libera a
  `false`.

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
| POST   | `/mercurio/servicios/crear-sesion-epayco`          | `EcommerceController@crearSesionCheckout` |
| POST   | `/mercurio/servicios/abandonar-precompra`         | `EcommerceController@abandonarPrecompra`  |
| POST   | `/mercurio/servicios/validar-pago-epayco`         | `EcommerceController@validarPagoEpayco`   |
| POST   | `/mercurio/servicios/guardar-venta`               | `EcommerceController@guardarVenta`        |
| POST   | `/mercurio/servicios/mis-compras`                 | `EcommerceController@misCompras`          |
| POST   | `/mercurio/servicios/listar-precompras`           | `EcommerceController@listarPrecompras`    |
| POST   | `/mercurio/servicios/desestimar-precompra`        | `EcommerceController@desestimarPrecompra` |
| POST   | `/api/epayco/confirmation`                        | `EpaycoWebhookController@confirmation` (público) |

Las rutas `/mercurio/servicios/*` viven bajo `middleware('mercurio.auth')`.
El webhook es público (seguridad = firma `x_signature`).

---

## 10. APIs externas consumidas

### 10.1 ePayco Apify — Smart Checkout y validación

Base: `EPAYCO_APIFY_URL` (`https://apify.epayco.co`). Credenciales de
comercio: `public_key` / `private_key` de `epayco_cuentas` vía `withCuenta()`.

| Operación | Método / path | Auth | Llamador |
| --------- | ------------- | ---- | -------- |
| Token | `POST /login` | Basic | `obtenerTokenApify()` |
| Sesión checkout | `POST /payment/session/create` | Bearer | `crearSesionCheckout()` |
| Consultar pago | `POST /payment/transaction` `{ referencePayco }` | Bearer | `validarReferenciaApify()` |

- **Llamadores de validación:** `EcommerceController@validarPagoEpayco`,
  `guardarVenta`, `AdmserviciosController` (reconsulta soporte).
- **Campos normalizados desde Apify:** `codTransactionState` → `cod_estado`,
  `refPayco`, `status`/`response`, `responseReasonText`, `amount`,
  `invoice`, `transactionId`, `autorizacion`, `bank`, `franchise`,
  `cardNumber`, `quotas`, `currency`, `date`, `signature`.
- **QA:** con `EPAYCO_FORCE_APPROVED=true` (solo non-prod) se omite Apify y
  se simula aprobado.

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
   `validarReferenciaApify()` (o el webhook firmado) antes de invocar
   `guardar-venta`. El query string del redirect `response` solo se usa
   como UX (alineado con la doc oficial ePayco).
2. **Doble validación del pago.** `validarPagoEpayco()` y `guardarVenta()`
   llaman ambos a `ApiEpayco::validarReferenciaApify()`; defensa en
   profundidad si el usuario re-abre la URL de respuesta. Cada llamada
   deja su propia fila en `epayco_transacciones`.
3. **Carrera `onClose` vs response URL.** La bandera
   `window.__epaycoPagoEnValidacion` evita que un cierre marque como
   `AB` una precompra cuyo pago está siendo validado en paralelo.
4. **Carrera `actualizarPrecompraDesdePago()`** permite actualizar una
   precompra en `AB` si llega una validación con `ref_payco` (pago tardío
   que llegó después del cierre).
5. **CSRF.** Todas las llamadas AJAX autenticadas envían el token CSRF vía
   `$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': ... } })`. El webhook
   `api/*` está excluido de CSRF.
6. **TLS.** `ApiEpayco` verifica el certificado SSL por defecto
   (`EPAYCO_HTTP_VERIFY_SSL=true`) en login, sesión y consulta Apify.
   Solo desactivar en entornos con CA/proxy problemáticos (`false`).
7. **Trazabilidad.** `setLogger()` / `Log::info` en pasos críticos y
   `epayco_transacciones` guarda el payload crudo de cada validación /
   webhook.
8. **Idempotencia.** Webhook / actualización no degradan una precompra
   `PA`. Auditoría *append-only*. `guardarVenta` y el webhook omiten
   Subsidio si la precompra **ya estaba** `PA` (evita doble registro).
9. **Datos personales.** `sanitizarTexto()` limpia acentos/no-ASCII en el
   nombre del checkout. Email con fallback `sin@email.com`. Billing v2
   se arma en `resolverBillingCheckout()`.
10. **Modo test.** Viene de `epayco_cuentas.env_mode === 'development'` →
    `test: true` en `configure()`. `EPAYCO_MODE` del `.env` está
    **deprecado**.
11. **Webhook firmado.** `POST /api/epayco/confirmation` valida
    `x_signature` con `p_id_customer` + `p_key` de la cuenta asociada a
    la precompra (no vars de `.env`). Fórmula:
    `sha256(p_cust_id_cliente^p_key^x_ref_payco^x_transaction_id^x_amount^x_currency_code)`.
    Guía: [epayco-webhook-confirmation.md](./epayco-webhook-confirmation.md).
12. **`EPAYCO_FORCE_APPROVED` (solo QA).** Si es `true` y el entorno no es
    production (`APP_ENV` / `APP_MODE`), `validarReferenciaApify` fuerza
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
| No aparecen filas en `epayco_transacciones`                       | `validarReferenciaApify()` no devolvió éxito o no llegó al insert.             | Logs `ApiEpayco.validarReferenciaApify` / error registrando auditoría.                      |
| Cliente ve “Error” / “pago no aprobado” pero cobro y webhook OK   | Reconsulta Apify falló; sin idempotencia PA.                                   | Ver [epayco-validacion-respuesta-cliente.md](./epayco-validacion-respuesta-cliente.md). Si precompra `PA` → “Compra exitosa”. |
| Sesión v2 no se crea / “No se pudo autenticar con ePayco”         | Llaves de cuenta vacías o login Apify fallido.                                 | Cajas → cuentas ePayco; logs `obtenerTokenApify` / `crearSesionCheckout`.                   |
| Abandono `AB` no ocurre al cerrar checkout v2 onpage              | Hooks no registrados con `setHooks()` (API oficial v2).                        | Ver §16.4 y pendiente #16 en §15.                                                           |
| `precompras:marcar-abandonadas` no se ejecuta                     | Scheduler no configurado o `withoutOverlapping()` trabado.                      | `php artisan schedule:list` y `schedule:run`.                                                |
| Cupos del mes en 0                                               | El beneficiario ya agotó la cuota mensual del servicio.                         | `validar-tarifa` retorna `cupos_mes = 0` y el frontend bloquea el botón "Procesar pago".    |

---

## 13. Anexo: callbacks y parámetros relevantes de ePayco

### 13.1 Página de respuesta (`response`)

Cuando ePayco redirige al `response` URL, incluye al menos `ref_payco` en
query (y a menudo más params). **No es confiable** para cerrar la venta:
el usuario puede cerrar el navegador o manipular la URL. Mercurio usa el
redirect solo como disparador UX → luego consulta Apify / espera webhook.

| Param                       | Significado                                             |
| --------------------------- | ------------------------------------------------------- |
| `ref_payco` / `x_ref_payco` | Referencia única de la transacción en ePayco            |
| `x_cod_transaction_state`   | Código de estado (1=Aceptada, 2=Rechazada, etc.)        |
| `x_response`                | Respuesta corta (`Aceptada`, `Rechazada`, `Pendiente`, `Fallida`) |
| `x_response_reason_text`    | Motivo detallado (texto legible)                        |
| `x_amount`                  | Monto cobrado                                           |
| `x_id_invoice`              | Identificador enviado como `invoice` (`ORD…`)           |

El frontend lee `ref_payco` desde `ref_payco` / `refPayco` / `x_ref_payco`
y también el path `/checkout/{ref}/response` por compatibilidad.

La doc oficial permite enriquecer el resultado con
`GET https://secure.epayco.co/validation/v1/reference/{ref_payco}`; Mercurio
**no** usa ese GET en runtime (usa Apify, §10.1).

### 13.2 Webhook `confirmation` (parámetros principales)

ePayco puede notificar por GET o POST según el `method` de la sesión
(Mercurio hoy solo registra `POST` y no envía `method` en la sesión).
Parámetros relevantes (lista completa en §16.5 / doc oficial):

| Parámetro | Uso en Mercurio |
| --------- | --------------- |
| `x_ref_payco` / `ref_payco` | Correlacionar / persistir |
| `x_cod_transaction_state` | Mapear a `PA` / `RE` / `PE` |
| `x_response` | Auditoría (`Aceptada`, `Rechazada`, `Pendiente`, `Fallida`) |
| `x_signature` | Validar autenticidad |
| `x_amount` / `x_currency_code` / `x_transaction_id` | Firma + auditoría |
| `x_extra1`…`x_extra4` | documento, codser, numero, precompra_id |
| `x_extra5` / `x_extra6` | billing name / email (v2) |

Persistidos en `epayco_transacciones`: `x_transaction_id`, `x_approval_code`,
`x_bank_name`, `x_franchise`, `x_card_number`, `x_quotas`, `x_currency_code`,
`x_date` / `x_transaction_date`, `x_signature`.

---

## 14. Glosario rápido

- **Precompra** — registro en `precompras_servicios` que representa una
  intención de compra antes de que ePayco confirme el pago. Es el ancla que
  permite reintentar pagos y conciliar transacciones.
- **sessionId** — ID de sesión Smart Checkout v2 retornado por Apify
  (`/payment/session/create`); se pasa a `ePayco.checkout.configure`.
- **ref_payco** — referencia única de la transacción devuelta por ePayco;
  es la llave de cruce entre la precompra y el pago real.
- **Cod_estado_epayco** — copia local del código de estado (1–12); se
  persiste para auditoría y para mostrar el motivo del fallo.
- **Estado DE** — `Desestimado`. Solo el usuario lo asigna desde la pantalla
  de "Compras pendientes" con un motivo del catálogo; nunca viene de
  ePayco.
- **Estado AB** — `Abandonada`. Asignado por cierre del checkout (frontend)
  o por el job nocturno `precompras:marcar-abandonadas`. Una precompra en
  `AB` **no es retomable** y no aparece en "Compras pendientes".
- **EpaycoTransaccion** — fila en `epayco_transacciones` con un snapshot
  completo de cada validación (`origen=validacion`) o webhook
  (`origen=webhook`). Es append-only y se usa para auditoría y soporte
  (tabla/CSV Admservicios + modal `detalle/{id}`).
- **onpage / standard** — tipos de Smart Checkout: embebido en el sitio vs
  redirect al entorno seguro ePayco (mejor en móvil/WebView).

---

## 15. Próximos pasos / mejoras sugeridas

| # | Acción | Estado |
| - | ------ | ------ |
| 1 | TLS `EPAYCO_HTTP_VERIFY_SSL` | Hecho |
| 2 | `transaction_id` / `approval_code` en Admservicios (tabla/CSV) | Hecho |
| 3 | Modal detalle Admservicios con historial completo | Hecho |
| 4 | Webhook `confirmation` + firma | Hecho |
| 5 | Job `precompras:marcar-abandonadas` | Hecho |
| 6 | Cierre checkout + abandono `AB` (v1 `onCloseModal`; v2 parcial) | Hecho* |
| 7 | Auditoría `epayco_transacciones` | Hecho |
| 8 | Checkout modular + `EPAYCO_CHECKOUT_VERSION` | Hecho |
| 9 | Móvil/WebView fuerza Checkout v2 `type: standard` | Hecho |
| 10 | `EPAYCO_FORCE_APPROVED` (QA non-prod) | Hecho |
| 11 | `transaction_id` / `approval_code` en `ReporteComprasServicios` | Hecho |
| 12 | Whitelist de medios (`methodsDisable` string[]) | Pendiente |
| 13 | TLS configurable en `APIClient` (Subsidio) | Pendiente |
| 14 | Tests Feature del webhook | Pendiente |
| 15 | Alertas operativas firma `400` / credenciales `503` | Pendiente |
| 16 | Migrar hooks v2 a `checkout.setHooks({ onCreated, onResponse, onClosed, onErrors })` | Pendiente |
| 17 | Enviar `method: "POST"` (y valorar `noRedirectOnClose` / `forceResponse`) en sesión Apify | Pendiente |
| 18 | Desglose impuestos sesión (`taxBase` / `tax` / `taxIco`) si facturación lo exige | Pendiente |

\*El abandono en v1 está operativo. En v2 onpage el código aún registra
`checkout.onClosed(fn)` / `onErrors(fn)`; la API oficial exige `setHooks`
(§16.4). En `type: standard` los hooks no aplican (redirect).

Detalle de backlog: [analisis-mejoras-epayco-precompras.md](./analisis-mejoras-epayco-precompras.md).  
Especificaciones a implementar: [pendientes-compra-en-linea.md](./pendientes-compra-en-linea.md).

---

## 16. Referencia oficial Smart Checkout v2 → Mercurio

Resumen del contrato ePayco (fuente:
[repo-api-epayco.md](../../../repo-api-epayco.md) / docs.epayco.com) mapeado
a la implementación actual. No aplica a Mercurio: `splitPayment`,
`PSEMultiCredit`, `PSESubMerchant`.

### 16.1 Ciclo de vida (oficial)

1. **Inicialización** — backend crea sesión Apify.
2. **Renderizado** — front monta Smart Checkout con `sessionId`.
3. **Procesamiento** — usuario paga en PCI DSS de ePayco.
4. **Confirmación** — webhook `confirmation` + redirect a `response`.

### 16.2 Autenticación y sesión (Apify)

```bash
# 1) Token
POST https://apify.epayco.co/login
Authorization: Basic base64(PUBLIC_KEY:PRIVATE_KEY)
→ { "token": "<JWT>" }

# 2) Sesión
POST https://apify.epayco.co/payment/session/create
Authorization: Bearer <JWT>
Content-Type: application/json
→ { "success": true, "data": { "sessionId": "...", "token": "..." } }
```

Mercurio: `ApiEpayco::obtenerTokenApify()` (cache por JWT `exp`) +
`crearSesionCheckout()` desde `EcommerceController@crearSesionCheckout`.

### 16.3 Propiedades de sesión

**Requeridas (oficial):** `checkout_version` (`"2"`), `name`, `currency`,
`amount`.

**Opcionales relevantes para Mercurio:**

| Propiedad | Tipo | En Mercurio hoy |
| --------- | ---- | --------------- |
| `description` | string | Sí (= nombre servicio) |
| `lang` | `ES` \| `EN` | Sí (`ES`) |
| `country` | string | Sí (`CO`) |
| `invoice` | string | Sí (`ORD{id}-{ts}`) |
| `taxBase` / `tax` / `taxIco` | number | No (pendiente #18) |
| `response` | URL | Sí (`servicios.index`) |
| `confirmation` | URL webhook | Sí (`api.epayco.confirmation`) |
| `methodsDisable` | string[] | No (pendiente #12) |
| `method` | `GET` \| `POST` | No (pendiente #17; webhook solo POST) |
| `dues` | number | No |
| `noRedirectOnClose` | boolean | No (útil con control de modal) |
| `forceResponse` | boolean | No (omite summary final) |
| `uniqueTransactionPerBill` | boolean | No |
| `extras.extra1`–`extra11` | string | Sí extra1–6 |
| `billing.*` | object | Sí (`email`, `name`, `typeDoc`, `numberDoc`, `callingCode`, `mobilePhone`, `address`) |

### 16.4 Frontend: script, configure y hooks

```html
<script src="https://checkout.epayco.co/checkout-v2.js"></script>
```

```js
// API oficial (docs ePayco)
const checkout = ePayco.checkout.configure({
  sessionId,
  type: 'onpage', // o 'standard'
  test: true      // false en producción
});

checkout.setHooks({
  onCreated: (data) => { /* checkout abierto */ },
  onResponse: (response) => { /* resultado pago (UX) */ },
  onErrors: (error) => { /* error de proceso */ },
  onClosed: (errors) => { /* cierre / cancelación */ },
});

checkout.open();
```

| Concepto oficial | Mercurio |
| ---------------- | -------- |
| Script `checkout-v2.js` | `_epayco_scripts.blade.php` + carga bajo demanda en móvil si versión=1 |
| `type: onpage` | Desktop con v2 |
| `type: standard` | Móvil / WebView (redirect entorno seguro) |
| `test` | `cuenta.env_mode === 'development'` |
| `setHooks` | **Pendiente** — hoy `onClosed(fn)` / `onErrors(fn)` (API legacy). Hooks oficiales solo en `onpage`. |
| `onClosed` → abandono | Tras 2.5 s → `POST abandonar-precompra` si no hay pago en validación |

### 16.5 Response vs confirmation (oficial)

| Mecanismo | Quién | Confiable para cerrar venta | Mercurio |
| --------- | ----- | --------------------------- | -------- |
| `response` | Browser redirect | **No** | `verificarRespuestaEpayco` → Apify → `guardarVenta` |
| `confirmation` | Server→server webhook | **Sí** (firma + reintentos) | `EpaycoConfirmationService` → auditoría + `PA` + Subsidio |

Buenas prácticas oficiales ya cubiertas o parciales:

1. Siempre validar en webhook / no confiar solo en `response` — sí.
2. Idempotencia ante reintentos — sí (`PA` no se degrada; no doble Subsidio).
3. Responder HTTP 200 rápido — sí (`OK`).
4. Validar `x_signature` — sí (por cuenta DB).
5. Registrar webhooks — sí (`epayco_transacciones`).
6. HTTPS en confirmation — ops / entorno.
7. No redirigir el endpoint webhook — sí (respuesta texto plana).

Fórmula de firma (oficial = Mercurio):

```text
SHA256(p_cust_id_cliente^p_key^x_ref_payco^x_transaction_id^x_amount^x_currency_code)
```

### 16.6 Mapa rápido oficial → archivos

| Paso oficial | Archivo Mercurio |
| ------------ | ---------------- |
| Login Apify | `ApiEpayco::obtenerTokenApify` |
| Create session | `ApiEpayco::crearSesionCheckout` / `EcommerceController@crearSesionCheckout` |
| Configure + open | `public/src/Mercurio/Ecommerce/epayco.js` (`abrirCheckoutV2`) |
| Response UX | `verificarRespuestaEpayco` |
| Consulta estado | `validarReferenciaApify` |
| Confirmation | `EpaycoWebhookController` + `EpaycoConfirmationService` + `EpaycoSignatureValidator` |