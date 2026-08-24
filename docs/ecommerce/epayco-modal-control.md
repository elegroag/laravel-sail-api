# Control sobre la modal de ePayco — Estado actual

Documento complementario a [pasarela-pago-epayco.md](./pasarela-pago-epayco.md).
Evalúa **qué tanto se controla la modal de ePayco** en el flujo del catálogo
de servicios de Mercurio.

> **Última revisión:** 2026-08-24  
> **Conclusión rápida:** El control del **ciclo de pago** es **alto** (payload,
> `onClose` / `onClosed`, `confirmation` + firma, abandono `AB`, job TTL).
> El control del **UI del checkout** (medios, branding, textos) sigue siendo
> **bajo**: ePayco gobierna apariencia y métodos visibles.

---

## 1. Lo que SÍ se controla

### 1.1 Configuración del handler

[resources/views/mercurio/ecommerce/index.blade.php](../resources/views/mercurio/ecommerce/index.blade.php)
(y el blade de pendientes):

```js
epaycoHandler = ePayco.checkout.configure({
    key: EPAYCO_PUBLIC_KEY,
    test: EPAYCO_TEST
});
```

Además:

| Bandera / env | Efecto |
| ------------- | ------ |
| `EPAYCO_MODE` → `EPAYCO_TEST` | Modo test del checkout |
| `EPAYCO_CHECKOUT_VERSION` | `1` = Standard (`checkout.js`); `2` = Smart Checkout (`checkout-v2.js` + sesión Apify) |

### 1.2 Payload enviado al abrir el checkout

Código: [`public/src/Mercurio/Ecommerce/pago.js`](../public/src/Mercurio/Ecommerce/pago.js)
(catálogo) y [`ComprasPendientes/pago.js`](../public/src/Mercurio/ComprasPendientes/pago.js)
(retoma). Lógica compartida de cierre/validación:
[`Ecommerce/epayco.js`](../public/src/Mercurio/Ecommerce/epayco.js).

Campos relevantes del Standard Checkout (v1):

| Campo | Función |
| ----- | ------- |
| `name` / `description` | Producto en el checkout |
| `invoice` | Orden (`ORD{timestamp}`) |
| `currency` / `amount` / `tax*` | Monto e impuestos (`0` en este flujo) |
| `country` / `lang` | `co` / `es` |
| `external` | `false` → modal embebida |
| `extra1/2/3` | cedtra, codser, numero |
| `extra4` | **id de precompra** (resolución en webhook) |
| `response` | URL de retorno client-side |
| `confirmation` | URL del webhook `POST /api/epayco/confirmation` |
| `name_billing` / docs / email | Datos del pagador |

En Smart Checkout (v2) la sesión se crea en backend
(`EcommerceController` + `ApiEpayco`) e incluye `confirmation` / extras
equivalentes; el frontend abre con `sessionId`.

### 1.3 Callbacks de cierre

| Checkout | Hook | Comportamiento |
| -------- | ---- | -------------- |
| v1 | `epaycoHandler.onCloseModal` | Tras delay 2.5 s → `POST abandonar-precompra` si no hay pago en validación |
| v2 | `checkout.onClosed` (+ `onErrors` log) | Misma lógica de abandono |

Salvaguarda: `window.__epaycoPagoEnValidacion` evita marcar `AB` mientras se
valida un pago en curso.

### 1.4 Confirmación server-side

- Ruta pública: `POST /api/epayco/confirmation`
- Firma `x_signature` + auditoría `origen=webhook`
- Guía: [epayco-webhook-confirmation.md](./epayco-webhook-confirmation.md)

### 1.5 Limpieza de PE huérfanas

Job diario `precompras:marcar-abandonadas` (TTL 7 días).
Guía: [precompras-job-abandonadas.md](./precompras-job-abandonadas.md)

---

## 2. Lo que NO se controla (UI / producto)

| Aspecto | Por qué |
| ------- | ------- |
| **Aspecto visual** | CSS interno de ePayco |
| **Botones de pago** | No se usa `methods` / `methodsDisable` |
| **Textos del modal** | `title`, `titleButtonPay`, etc. no se setean |
| **Idioma real del copy** | `lang: 'es'` se envía; el copy final lo sirve ePayco |
| **Campos opcionales del pagador** | `address`, `phone_contact`, `city`, `duedays` no se mandan |

### 2.1 Abandonos (ya no son “silenciosos”)

Si el usuario cierra la modal sin pagar **con la pestaña abierta**:

1. Corre `onCloseModal` / `onClosed` → precompra `PE` → `AB`.
2. SweetAlert informa que no es retomable.

Si cierra el navegador / WebView / pierde red **sin** callback:

1. La precompra puede quedar `PE` hasta el job nocturno (o hasta que el
   webhook confirme un pago real → puede pasar a `PA` incluso desde `AB`).

---

## 3. Lo que ePayco soporta y aún no usamos

| Parámetro | Para qué | Estado en Mercurio |
| --------- | -------- | ------------------ |
| `onOpen` | Loader / analytics al abrir | No usado |
| `methods` / `methodsDisable` | Curar medios de pago | **Pendiente** |
| `title` / `titleButtonPay` | Copy del modal | No prioritario |
| `duedays` / `discount` / `ico*` | Cuotas, descuentos, ICO | No aplica al flujo actual |
| `expiration` | Caducar links (`external: true`) | No aplica (checkout embebido) |

Hooks ya en uso: `onCloseModal` / `onClosed`, `confirmation`, validación
`x_signature` en webhook.

---

## 4. Matriz de control por capa

```
┌─────────────────────────────────────────────────────────────────────┐
│ Capa                            │ Control │ Notas                   │
├─────────────────────────────────┼─────────┼─────────────────────────┤
│ Llave pública / modo test       │   ●     │ vía .env → config       │
│ Versión checkout (v1 / v2)      │   ●     │ EPAYCO_CHECKOUT_VERSION │
│ Datos del producto (monto, etc.)│   ●     │ payload / sesión Apify  │
│ Datos de facturación            │   ●     │ payload de open()       │
│ URL de retorno (response)       │   ●     │ window.location.href    │
│ Webhook (confirmation + extra4) │   ●     │ firma + auditoría       │
│ Callbacks de cierre             │   ●     │ onCloseModal / onClosed │
│ Detección de abandonos          │   ●     │ AB inmediato + job TTL  │
│ Campos opcionales (address, …)  │   ○     │ no enviados             │
│ Impuestos (tax, ico)            │   ○     │ hardcodeados en 0       │
│ Medios de pago visibles         │   ○     │ ePayco muestra todos    │
│ Apariencia / branding           │   ○     │ CSS interno de ePayco   │
│ Textos del modal                │   ○     │ copy de ePayco          │
└─────────────────────────────────────────────────────────────────────┘

● = controlado por el código   ○ = NO controlado
```

---

## 5. Riesgos residuales (UI / operación)

1. **Medios de pago no curados** — si ePayco habilita un método no deseado,
   el checkout lo mostraría sin filtro.
2. **Branding inconsistente** — la modal rompe el look & feel de Mercurio
   (limitación del producto ePayco).
3. **WebView Android** — iframes / storage / `onClose` frágiles; el webhook
   mitiga, pero conviene probar en dispositivo real.
4. **Firma solo en webhook** — `validarReferencia` confía en la API de ePayco
   (origen de confianza en ese path).

---

## 6. Próximos pasos sugeridos

| # | Acción | Esfuerzo | Valor | Estado |
| - | ------ | -------- | ----- | ------ |
| 1 | `onClose` / `onClosed` + SweetAlert + abandono `AB` | — | — | Hecho |
| 2 | Webhook `confirmation` + firma | — | — | Hecho |
| 3 | Job `precompras:marcar-abandonadas` | — | — | Hecho |
| 4 | TLS `EPAYCO_HTTP_VERIFY_SSL` | — | — | Hecho |
| 5 | Whitelist de medios (`methods` / `methodsDisable`) | Bajo | Medio | **Pendiente** |
| 6 | Customizar textos (`titleButtonPay`, `title`) | Bajo | Bajo | Opcional |
