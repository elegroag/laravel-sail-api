# Pasarela de Pago ePayco — Catálogo de Servicios (Mercurio)

Documentación del flujo completo de la pasarela de pago **ePayco** integrada en el
catálogo de servicios de Mercurio, accesible en
`http://comfaca.ecommerce.com.co:9043/mercurio/servicios/index` (ruta
`/mercurio/servicios/index`).

> **Audiencia:** desarrolladores y mantenedores del módulo de ecommerce Mercurio.
> **Última revisión:** 2026-08-14.

---

## 1. Resumen general

La compra de servicios para los usuarios finales (trabajadores, beneficiarios)
se paga electrónicamente con **ePayco** (pasarela colombiana). El proyecto usa
el **Standard Checkout** de ePayco (checkout embebido en modal vía
`https://checkout.epayco.co/checkout.js`) y un **endpoint de validación de
referencia** para confirmar el estado real de cada transacción contra los
servidores de ePayco.

El diseño completo se articula en torno a dos principios:

1. **Precompra antes de pagar** — Antes de abrir la pasarela, el sistema crea
   un registro de precompra en estado `PE` (pendiente) en la tabla
   `precompras_servicios`. Esto permite reintentar pagos y auditar incluso las
   compras abandonadas.
2. **Validación server-side del pago** — La confirmación del pago nunca se
   confía en los query params de retorno de ePayco (cliente). El backend
   siempre consulta el endpoint `reference` de ePayco usando el `ref_payco`
   para obtener el `x_cod_transaction_state` real y, solo si es `1` (Aceptada),
   registra la venta en el sistema de subsidio.

---

## 2. Stack y componentes

| Capa              | Tecnología / Archivo                                                                                  |
| ----------------- | ------------------------------------------------------------------------------------------------------ |
| Frontend catálogo | [resources/views/mercurio/ecommerce/index.blade.php](../resources/views/mercurio/ecommerce/index.blade.php) |
| JS catálogo       | [public/src/Mercurio/Ecommerce/main.js](../public/src/Mercurio/Ecommerce/main.js)                      |
| JS pendientes     | [public/src/Mercurio/ComprasPendientes/main.js](../public/src/Mercurio/ComprasPendientes/main.js)      |
| Backend routes    | [routes/mercurio/servicios.php](../routes/mercurio/servicios.php)                                       |
| Controller        | [app/Http/Controllers/Mercurio/EcommerceController.php](../app/Http/Controllers/Mercurio/EcommerceController.php) |
| Servicio ePayco   | [app/Services/Api/ApiEpayco.php](../app/Services/Api/ApiEpayco.php)                                    |
| Servicio Subsidio | [app/Services/Api/ApiSubsidio.php](../app/Services/Api/ApiSubsidio.php)                                |
| Estados           | [app/Services/Ecommerce/EstadoPrecompra.php](../app/Services/Ecommerce/EstadoPrecompra.php)            |
| Modelo precompra  | [app/Models/PrecompraServicio.php](../app/Models/PrecompraServicio.php)                                |
| Modelo auditoría  | [app/Models/EpaycoTransaccion.php](../app/Models/EpaycoTransaccion.php)                                |
| Config            | [config/app.php](../config/app.php) (sección `epayco`)                                                 |
| Migraciones       | [database/migrations/2026_07_08_120000_add_epayco_reference_api_endpoint.php](../database/migrations/2026_07_08_120000_add_epayco_reference_api_endpoint.php)<br>[database/migrations/2026_07_15_100000_create_precompras_servicios_table.php](../database/migrations/2026_07_15_100000_create_precompras_servicios_table.php)<br>[database/migrations/2026_07_15_170000_add_desestimacion_to_precompras_servicios_table.php](../database/migrations/2026_07_15_170000_add_desestimacion_to_precompras_servicios_table.php)<br>[database/migrations/2026_08_14_160000_create_epayco_transacciones_table.php](../database/migrations/2026_08_14_160000_create_epayco_transacciones_table.php) |

---

## 3. Configuración

### 3.1 Variables de entorno (`.env`)

```ini
EPAYCO_MODE="development"                              # development | production
EPAYCO_PUBLIC_KEY="5700c4372a4369500c22efede64aa3f3"  # llave pública (cliente JS + header Authorization)
EPAYCO_PRIVATE_KEY=                                    # llave privada (Basic Auth backend)
```

`config/app.php` mapea estas variables al bloque `epayco`:

```php
'epayco' => [
    'mode'        => env('EPAYCO_MODE'),
    'public_key'  => env('EPAYCO_PUBLIC_KEY'),
    'private_key' => env('EPAYCO_PRIVATE_KEY'),
],
```

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
  [`EcommerceModule.init()`](../public/src/Mercurio/Ecommerce/main.js#L1064-L1076)
  vía `$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': ... }})`).

---

## 5. Modelo de datos: `precompras_servicios`

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
| `estado`             | `string(2)`      | `PE` Pendiente · `PA` Pagado · `DE` Desestimado · `RE` Rechazado |
| `ref_payco`          | `string(80)`     | Referencia única devuelta por ePayco                     |
| `cod_estado_epayco`  | `string(2)`      | `x_cod_transaction_state` (1–12)                         |
| `motivo_epayco`      | `string(255)`    | `x_response_reason_text` / `x_response`                  |
| `motivo_desestimacion` | `string(50)`   | Código de motivo (`YA_NO_INTERESA`, `VALOR_ALTO`, etc.)  |
| `detalle_desestimacion` | `string(255)` | Texto libre cuando motivo = `OTRO`                       |
| `fecha_precompra`    | `timestamp`      | Creación del registro                                    |
| `fecha_pago`         | `timestamp`      | Se setea cuando `estado` pasa a `PA`                     |
| `fecha_desestimacion`| `timestamp`      | Se setea cuando el usuario desestima la compra           |
| `created_at` / `updated_at` | timestamps | Eloquent                                                  |

Índices: `(documento, estado)` y `ref_payco`. La columna
`motivo_desestimacion` y amigas se añadieron en
[migración 2026_07_15_170000](../database/migrations/2026_07_15_170000_add_desestimacion_to_precompras_servicios_table.php).

### 5.1 Estados y mapeo ePayco → precompra

Definidos en
[`EstadoPrecompra`](../app/Services/Ecommerce/EstadoPrecompra.php):

```php
const PENDIENTE   = 'PE';
const PAGADO      = 'PA';
const DESESTIMADO = 'DE';
const RECHAZADO   = 'RE';
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

> **Nota:** El estado `DE` (Desestimado) **nunca** se asigna automáticamente
> desde ePayco. Es exclusivo para cuando el usuario cancela manualmente su
> precompra en la pantalla de "Compras pendientes" con un motivo del catálogo.

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
│   5. ePayco.checkout.open(data)  ← pasarela abre modal                   │
│   6. Usuario paga con tarjeta / PSE / Nequi / etc.                       │
│   7. ePayco redirige a response URL con ?ref_payco=...                    │
│   8. verificarRespuestaEpayco() lee URL y valida con backend             │
│   9. Backend consulta ePayco /reference/{ref_payco}                       │
│  10. Si x_cod_transaction_state == 1 → guardarVenta → ApiSubsidio        │
└──────────────────────────────────────────────────────────────────────────┘
```

### 6.2 Paso a paso

#### ① Carga del catálogo

- `GET /mercurio/servicios/index` →
  [`EcommerceController@index`](../app/Http/Controllers/Mercurio/EcommerceController.php#L47-L72).
- Renderiza `mercurio/ecommerce/index.blade.php`. Inyecta a la vista:
  - `EPAYCO_PUBLIC_KEY`
  - `EPAYCO_TEST` (`true` cuando `config('app.epayco.mode') === 'development'`)
  - `documento` del usuario activo
  - `pendientesCount` (badge rojo con cantidad de precompras en `PE`)
- En el `<head>` del blade se carga el SDK de ePayco:
  `<script src="https://checkout.epayco.co/checkout.js"></script>` y se
  configura el handler:
  ```js
  epaycoHandler = ePayco.checkout.configure({
      key: EPAYCO_PUBLIC_KEY,
      test: EPAYCO_TEST
  });
  ```
- Se cargan las rutas generadas con `route()` para usarlas desde JS.

#### ② Identificación del trabajador

- `POST /mercurio/servicios/identificar-trabajador` →
  [`EcommerceController@identificarTrabajador`](../app/Http/Controllers/Mercurio/EcommerceController.php#L106-L153).
- Llama al servicio externo **CLIS/SISU** vía
  [`ApiSubsidio`](../app/Services/Api/ApiSubsidio.php) (servicio `Movil`,
  método `identifica-trabajador`) con `cedtra = hid_documento`.
- Devuelve `{ success, data: { trabajador, nucleo_familiar, ... } }`.
- El frontend pinta el grid de **beneficiarios** (trabajador, cónyuge,
  beneficiarios).

#### ③ Listado de servicios

- `POST /mercurio/servicios/listar-servicios` →
  [`EcommerceController@listarServicios`](../app/Http/Controllers/Mercurio/EcommerceController.php#L159-L191).
- Misma API: `Movil` / `listar-servicios`. Devuelve los servicios con sus
  cupos disponibles.
- El frontend renderiza el grid de **servicios** con buscador y filtro por
  código.

#### ④ Selección de beneficiario + servicio → validación de tarifa

- Al hacer click en un `servicio-card`, el frontend llama a
  `seleccionarServicio(srv)` →
  [`validarTarifa(codser, numero)`](../public/src/Mercurio/Ecommerce/main.js#L725-L819).
- `POST /mercurio/servicios/validar-tarifa` →
  [`EcommerceController@validarTarifa`](../app/Http/Controllers/Mercurio/EcommerceController.php#L197-L251).
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

[`procesarPago(event)`](../public/src/Mercurio/Ecommerce/main.js#L890-L1005):

1. **Valida cliente** que `valor > 0` y que `epaycoHandler` esté listo.
2. **Persiste contexto en `sessionStorage`** para sobrevivir el redirect de
   ePayco:
   ```
   epayco_cedtra, epayco_codser, epayco_numero,
   epayco_nota, epayco_codben, epayco_precompra_id
   ```
3. **Crea la precompra** vía
   [`POST /mercurio/servicios/crear-precompra`](../app/Http/Controllers/Mercurio/EcommerceController.php#L258-L305)
   → `PrecompraServicio::create([... 'estado' => PE])`.
4. **Guarda `epayco_precompra_id`** en `sessionStorage` y abre la pasarela:
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
       response: window.location.href,   // vuelve al catálogo
       name_billing, type_doc_billing: 'cc', number_doc_billing,
       email_billing
   });
   ```

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

[`verificarRespuestaEpayco()`](../public/src/Mercurio/Ecommerce/main.js#L314-L354):

- Lee `ref_payco` y `x_cod_transaction_state` del query string.
- Limpia la URL con `history.replaceState` (para que el refresh no re-trigger
  la validación).
- Si el código de URL ya viene `!= 1`, muestra alerta "Pago no completado" y
  termina.
- Si todo OK, abre un SweetAlert "Verificando pago…" y llama a
  `validarPagoEpayco(refPayco)`.

#### ⑧ Validación server-side contra ePayco

[`validarPagoEpayco(refPayco)`](../public/src/Mercurio/Ecommerce/main.js#L356-L409)
dispara `POST /mercurio/servicios/validar-pago-epayco` →

[`EcommerceController@validarPagoEpayco`](../app/Http/Controllers/Mercurio/EcommerceController.php#L316-L358):

```php
$resultado = $this->epayco->validarReferencia($ref_payco);
```

[`ApiEpayco::validarReferencia()`](../app/Services/Api/ApiEpayco.php#L55-L106)
hace `GET https://secure.epayco.co/validation/v1/reference/{refPayco}` (sin
verificar SSL por un quirk histórico — ver §10) y devuelve un payload
normalizado:

```php
return [
    'success' => true,
    'data' => [
        'aprobado'    => (int)$tx['x_cod_transaction_state'] === 1,
        'cod_estado'  => intval($tx['x_cod_transaction_state'] ?? 0),
        'respuesta'   => $tx['x_response'] ?? 'Sin respuesta',
        'motivo'      => $tx['x_response_reason_text'] ?? '',
        'monto'       => $tx['x_amount'] ?? '0',
        'ref_payco'   => $tx['x_ref_payco'] ?? $refPayco,
    ],
];
```

Si la respuesta es válida, el controller llama a
[`actualizarPrecompraDesdePago()`](../app/Http/Controllers/Mercurio/EcommerceController.php#L618-L664)
que:

1. Busca la precompra por `ref_payco`. Si no la encuentra, busca por
   `precompra_id` (pasado por el frontend) **solo si sigue en estado `PE`**.
2. Si ya está `PA`, no la degrada.
3. Mapea `cod_estado` → `EstadoPrecompra::desdeCodigoEpayco()`.
4. Persiste `ref_payco`, `cod_estado_epayco`, `motivo_epayco` y, si quedó
   `PA`, fija `fecha_pago`.

#### ⑨ Si `aprobado === true` → guardar venta

[`guardarVenta(refpago)`](../public/src/Mercurio/Ecommerce/main.js#L823-L886)
dispara `POST /mercurio/servicios/guardar-venta` con los datos originales
(`cedtra`, `codser`, `numero`, `codben`, `nota`, `refpago`, `precompra_id`).

[`EcommerceController@guardarVenta`](../app/Http/Controllers/Mercurio/EcommerceController.php#L364-L450):

1. Vuelve a llamar a `ApiEpayco::validarReferencia($refpago)` (doble check
   server-side).
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

#### ⑩ Limpieza

`limpiarSessionEpayco()` borra las claves en `sessionStorage` y
`limpiarSeleccionServicio()` resetea el panel lateral.

---

## 7. Compras pendientes y desestimación

Ruta `GET /mercurio/servicios/compras-pendientes` →
[`comprasPendientes()`](../app/Http/Controllers/Mercurio/EcommerceController.php#L78-L87) +
[`public/src/Mercurio/ComprasPendientes/main.js`](../public/src/Mercurio/ComprasPendientes/main.js).

### 7.1 Listado

- `POST /mercurio/servicios/listar-precompras` →
  [`listarPrecompras()`](../app/Http/Controllers/Mercurio/EcommerceController.php#L511-L545)
  trae todas las precompras en `PE` del `documento` activo.

### 7.2 Retomar pago

[`retomarPago(precompra)`](../public/src/Mercurio/ComprasPendientes/main.js#L239-L323):

1. Llama a `validarTarifa` para re-confirmar disponibilidad y precio
   vigente.
2. Si todo OK, llama a `abrirCheckout()` que reusa la misma precompra
   poniendo su `id` en `sessionStorage.epayco_precompra_id`.
3. Cuando ePayco redirige al catálogo, `verificarRespuestaEpayco()` +
   `validarPagoEpayco()` + `guardarVenta()` cierran el ciclo reutilizando la
   **misma fila de precompra**.

### 7.3 Desestimar compra

`POST /mercurio/servicios/desestimar-precompra` →
[`desestimarPrecompra()`](../app/Http/Controllers/Mercurio/EcommerceController.php#L552-L611):

- Valida `motivo ∈ {YA_NO_INTERESA, VALOR_ALTO, COMPRA_OTRO_MEDIO,
  PROBLEMA_PAGO, OTRO}`. Si es `OTRO`, exige `detalle` libre (≤ 255
  caracteres).
- Verifica que la precompra pertenezca al usuario y siga en `PE`.
- Marca `estado = DE`, persiste `motivo_desestimacion`,
  `detalle_desestimacion` y `fecha_desestimacion`.

---

## 8. Resumen de rutas

| Método | Ruta                                              | Controller                                |
| ------ | ------------------------------------------------- | ----------------------------------------- |
| GET    | `/mercurio/servicios/index`                       | `EcommerceController@index`               |
| GET    | `/mercurio/servicios/compras-pendientes`          | `EcommerceController@comprasPendientes`   |
| GET    | `/mercurio/servicios/ver-compras`                 | `EcommerceController@verCompras`          |
| POST   | `/mercurio/servicios/identificar-trabajador`      | `EcommerceController@identificarTrabajador`|
| POST   | `/mercurio/servicios/listar-servicios`            | `EcommerceController@listarServicios`     |
| POST   | `/mercurio/servicios/validar-tarifa`              | `EcommerceController@validarTarifa`       |
| POST   | `/mercurio/servicios/crear-precompra`             | `EcommerceController@crearPrecompra`      |
| POST   | `/mercurio/servicios/validar-pago-epayco`         | `EcommerceController@validarPagoEpayco`   |
| POST   | `/mercurio/servicios/guardar-venta`               | `EcommerceController@guardarVenta`        |
| POST   | `/mercurio/servicios/mis-compras`                 | `EcommerceController@misCompras`          |
| POST   | `/mercurio/servicios/listar-precompras`           | `EcommerceController@listarPrecompras`    |
| POST   | `/mercurio/servicios/desestimar-precompra`        | `EcommerceController@desestimarPrecompra` |

Todas viven bajo `middleware('mercurio.auth')`.

---

## 9. APIs externas consumidas

### 9.1 ePayco — validación de pagos

- **Método:** `GET`
- **URL:** `https://secure.epayco.co/validation/v1/reference/{ref_payco}`
- **Auth:** ninguno en este endpoint público (solo el path lleva la
  referencia). Las credenciales `EPAYCO_PUBLIC_KEY` / `EPAYCO_PRIVATE_KEY`
  se usan en `ApiEpayco::send()` para otros endpoints administrativos que el
  módulo no usa todavía.
- **Llamador:** [`ApiEpayco::validarReferencia()`](../app/Services/Api/ApiEpayco.php#L55-L106).
- **Campos consumidos de la respuesta:** `x_cod_transaction_state`,
  `x_response`, `x_response_reason_text`, `x_amount`, `x_ref_payco`.

### 9.2 CLIS/SISU — backend de subsidio

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

## 10. Consideraciones de seguridad y operación

1. **No confiar en el cliente.** Toda confirmación se valida con
   `validarReferencia()` antes de invocar `guardar-venta`. El query string
   que llega en el redirect solo se usa como UX.
2. **Doble validación del pago.** `validarPagoEpayco()` y `guardarVenta()`
   llaman ambos a `ApiEpayco::validarReferencia()`; esto es defensa en
   profundidad por si el usuario re-abre la URL de respuesta varias veces.
3. **CSRF.** Todas las llamadas AJAX envían el token CSRF vía
   `$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': ... } })`.
4. **TLS.** La consulta a ePayco usa `Http::withoutVerifying()` —
   **workaround histórico**; en producción debería habilitarse la
   verificación de certificado.
5. **Trazabilidad.** `setLogger()` se invoca en cada paso crítico
   (creación de precompra, pago validado, venta guardada, desestimación) y
   persiste `cod_estado_epayco` + `motivo_epayco` para auditoría.
6. **Idempotencia.** `actualizarPrecompraDesdePago()` no degrada una
   precompra que ya esté en `PA`, así que múltiples callbacks no la
   "rechazan" si el pago ya fue aprobado.
7. **Datos personales.** `sanitizarTexto()` en el frontend limpia acentos y
   caracteres no ASCII antes de enviar el `name` del checkout (ePayco es
   quisquilloso con tildes y emojis). El email se sanea con un fallback
   `sin@email.com` cuando el trabajador no tiene correo registrado.
8. **Modo development.** `EPAYCO_MODE=development` activa el modo test del
   checkout de ePayco (`test: true` en JS) **y** apunta
   `ApiEpayco::validarReferencia()` a `host_dev` (que en este caso es la
   misma URL de validación). El cambio de comportamiento real entre
   development/production se hace dentro del panel de ePayco.

---

## 11. Errores más comunes y diagnóstico

| Síntoma                                                           | Causa probable                                                                  | Dónde mirar                                                                                  |
| ----------------------------------------------------------------- | ------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- |
| El modal de ePayco no abre                                        | `epaycoHandler` es `null` (SDK no cargó o falla la configuración).              | Console log "Error inicializando ePayco" en [index.blade.php](../resources/views/mercurio/ecommerce/index.blade.php). |
| SweetAlert "No se pudo inicializar la pasarela"                   | Falta `window.epaycoHandler` por error JS.                                     | Recargar la página con el SDK cargado.                                                       |
| "Referencia de pago no proporcionada"                             | El usuario cerró la pasarela sin completar el redirect.                        | `verificarRespuestaEpayco()` no encontró `ref_payco` en la URL ni en `/checkout/{ref}/response`. |
| Pago aprobado pero la venta no se registra                        | `ApiSubsidio::guardar-venta` devuelve `flag = false`.                          | Revisar logs del backend en `ApiSubsidio`.                                                   |
| Precompra queda `PE` eternamente                                  | El usuario nunca volvió al sitio tras pagar (ePayco no redirigió).              | Revisar si `response` URL es accesible desde la red del usuario.                              |
| ePayco devuelve código distinto de 1 pero la precompra queda `PE`  | El usuario reabrió la URL de respuesta tras un pago válido anterior.           | `actualizarPrecompraDesdePago()` no degrada `PA` → `RE` ni `PE` → `PE` si ya estaba pagada.  |
| Cupos del mes en 0                                               | El beneficiario ya agotó la cuota mensual del servicio.                         | `validar-tarifa` retorna `cupos_mes = 0` y el frontend bloquea el botón "Procesar pago".    |

---

## 12. Anexo: callbacks y parámetros relevantes de ePayco

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

El frontend lee `ref_payco` desde varios nombres
(`ref_payco` / `refPayco` / `x_ref_payco`) y también analiza el path
`/checkout/{ref}/response` por compatibilidad.

---

## 13. Glosario rápido

- **Precompra** — registro en `precompras_servicios` que representa una
  intención de compra antes de que ePayco confirme el pago. Es el ancla que
  permite reintentar pagos y conciliar transacciones.
- **ref_payco** — referencia única de la transacción devuelta por ePayco;
  es la llave de cruce entre la precompra y el pago real.
- **Cod_estado_epayco** — copia local del `x_cod_transaction_state`; se
  persiste para auditoría y para mostrar el motivo del fallo.
- **Estado DE** — `Desestimado`. Solo el usuario lo asigna desde la pantalla
  de "Compras pendientes"; nunca viene de ePayco.

---

## 14. Próximos pasos / mejoras sugeridas

1. Habilitar verificación TLS en
   [`ApiEpayco::validarReferencia()`](../app/Services/Api/ApiEpayco.php)
   (quitar `withoutVerifying()`).
2. Mostrar `transaction_id` / `approval_code` desde `epayco_transacciones`
   en Admservicios y reportes.
3. Manejar la **firma** de ePayco en respuestas y webhooks
   (`x_signature`) cuando se implemente el endpoint de confirmación server-side.
4. ~~Auditar las precompras `PE` con más de N días~~ — comando
   `precompras:marcar-abandonadas` (schedule diario 02:00, TTL 7 días).