# Control sobre la modal de ePayco — Diagnóstico

Documento complementario a [pasarela-pago-epayco.md](./pasarela-pago-epayco.md).
Evalúa **qué tanto se controla la modal de ePayco** en el flujo del catálogo
de servicios de Mercurio.

> **Conclusión rápida:** El control es **bajo**. Solo se controla el payload
> que se le envía a `epaycoHandler.open()`. El aspecto visual, los medios de
> pago disponibles, los textos, los callbacks y los webhooks **no se
> configuran** en el código actual.

---

## 1. Lo que SÍ se controla

Solo dos cosas: la **configuración del handler** y el **payload de la
transacción**.

### 1.1 Configuración del handler

[resources/views/mercurio/ecommerce/index.blade.php:206-209](../resources/views/mercurio/ecommerce/index.blade.php#L206-L209)

```js
epaycoHandler = ePayco.checkout.configure({
    key: EPAYCO_PUBLIC_KEY,
    test: EPAYCO_TEST
});
```

Únicamente la llave pública y el flag `test`. Sin opciones adicionales.

### 1.2 Payload enviado a `epaycoHandler.open()`

[public/src/Mercurio/Ecommerce/main.js:942-961](../public/src/Mercurio/Ecommerce/main.js#L942-L961)

```js
var data = {
    name: servicioNombre,
    description: servicioNombre,
    invoice: invoice,                   // 'ORD' + Date.now()
    currency: 'cop',
    amount: valor,
    tax_base: '0',
    tax: '0',
    country: 'co',
    lang: 'es',
    external: 'false',
    extra1: documento,                  // cedtra
    extra2: $('#hid_codser').val(),
    extra3: $('#hid_numero').val(),
    response: window.location.href,
    name_billing: nombre,
    type_doc_billing: 'cc',
    number_doc_billing: documento,
    email_billing: email
};
```

Campos enviados:

| Campo                | Función                                                     |
| -------------------- | ----------------------------------------------------------- |
| `name`               | Título del producto en el checkout                          |
| `description`        | Descripción visible                                         |
| `invoice`            | Identificador de la orden (`ORD{timestamp}`)                |
| `currency`           | `cop` (peso colombiano)                                     |
| `amount`             | Monto a pagar                                               |
| `tax_base` / `tax`   | Impuestos (`0` en este flujo)                               |
| `country` / `lang`   | Localización (`co` / `es`)                                  |
| `external`           | `false` → checkout embebido (no redirige a ePayco externo)  |
| `extra1/2/3`         | Datos auxiliares (cedtra, codser, numero)                   |
| `response`           | URL de retorno client-side                                  |
| `name_billing`       | Nombre del pagador                                          |
| `type_doc_billing`   | Tipo de documento (`cc` = cédula)                           |
| `number_doc_billing` | Número de documento del pagador                             |
| `email_billing`      | Email del pagador                                           |

---

## 2. Lo que NO se controla

| Aspecto                            | Por qué no se controla                                                       |
| ---------------------------------- | ---------------------------------------------------------------------------- |
| **Aspecto visual**                 | Colores, logos, fuentes, layout → lo gobierna el CSS interno de ePayco.      |
| **Botones de pago disponibles**    | No se filtran con `methods` ni `methodsDisable`.                             |
| **Textos del modal**               | `title`, `titleButtonPay`, etc. → no se setean.                              |
| **Idioma real del checkout**       | `lang: 'es'` se envía pero el copy final lo sirve ePayco.                    |
| **Campos opcionales del pagador**  | `address`, `phone_contact`, `city`, `duedays` → no se mandan.                |
| **Webhook server-side**            | No se configura `confirmation`; solo se usa `response` (client-side).        |
| **Cierre de la modal**             | Sin `onClose`, sin `onError`, sin polling. Abandonos silenciosos.            |

### 2.1 Implicación crítica: abandonos silenciosos

Si el usuario abre la modal de ePayco y la cierra sin completar el pago:

1. **ePayco no redirige** a `response` (porque no hubo transacción).
2. `verificarRespuestaEpayco()` no se dispara (no hay `ref_payco` en la URL).
3. **El backend nunca se entera** del cierre.
4. La precompra queda en estado `PE` para siempre.

El único flujo de recuperación hoy es que el usuario entre manualmente a
`/mercurio/servicios/compras-pendientes` y la desestima con un motivo del
catálogo. No existe ningún job automático ni notificación que detecte
precompras abandonadas.

---

## 3. Lo que ePayco SOPORTA y este proyecto NO usa

| Parámetro / hook    | Para qué sirve                                                | Impacto potencial en este proyecto                          |
| ------------------- | ------------------------------------------------------------- | ------------------------------------------------------------ |
| `onOpen`            | Callback al abrir la modal                                    | Deshabilitar botones, mostrar loader, analytics.            |
| `onClose`           | Callback al cerrar la modal (con o sin pago)                  | **Detectar abandonos** y marcar la precompra como tal.       |
| `onCreateToken`     | Tokenización previa al cobro                                  | No aplica (se cobra inmediato).                              |
| `methods`           | Whitelist de medios (`card`, `pse`, `nequi`, `daviplata`, ...) | Restringir a tarjeta + PSE por ejemplo.                      |
| `methodsDisable`    | Blacklist de medios                                           | Bloquear métodos que no se quieran ofrecer.                  |
| `confirmation`      | URL server-side para webhook                                  | Confirmar pagos aunque el usuario cierre el navegador.       |
| `x_signature`       | Firma de la transacción                                       | Validar integridad de la respuesta en el backend.            |
| `duedays`           | Plazos de cuota                                               | Ofrecer financiación a N cuotas.                            |
| `ico`               | Impuestos al consumo                                          | Aplicar impuestos reales.                                    |
| `discount`          | Descuento global                                              | Mostrar precio con descuento.                                |
| `titleButtonPay`    | Texto del botón de pagar                                      | "Pagar ahora" vs "Confirmar compra".                         |
| `expiration`        | Fecha de expiración del link de pago (si `external: true`)    | Caducar links no usados.                                     |
| `ico_tax_base`      | Base de impuesto al consumo                                   | Igual que `tax_base` pero para ICO.                          |

Referencia: documentación oficial de ePayco Standard Checkout.

---

## 4. Matriz de control por capa

```
┌─────────────────────────────────────────────────────────────────────┐
│ Capa                            │ Control │ Notas                   │
├─────────────────────────────────┼─────────┼─────────────────────────┤
│ Llave pública / modo test       │   ●     │ vía .env → config       │
│ Datos del producto (monto, etc.)│   ●     │ payload de open()       │
│ Datos de facturación            │   ●     │ payload de open()       │
│ URL de retorno (response)       │   ●     │ window.location.href    │
│ Campos opcionales (address, …)  │   ○     │ no enviados             │
│ Impuestos (tax, ico)            │   ○     │ hardcodeados en 0       │
│ Medios de pago visibles         │   ○     │ ePayco muestra todos    │
│ Apariencia / branding           │   ○     │ CSS interno de ePayco   │
│ Textos del modal                │   ○     │ copy de ePayco          │
│ Callbacks (onOpen, onClose)     │   ○     │ no se pasan             │
│ Webhook server-side             │   ○     │ no hay `confirmation`   │
│ Detección de abandonos          │   ○     │ solo manual             │
└─────────────────────────────────────────────────────────────────────┘

● = controlado por el código   ○ = NO controlado
```

---

## 5. Recomendaciones ordenadas por impacto

### 5.1 Corto plazo — agregar `onClose` (mínimo invasivo)

Pasar un callback `onClose` al `epaycoHandler.open(data)` para detectar
cuando el usuario cierra la modal sin completar el pago.

```js
var data = { ... };
data.onClose = function() {
    Swal.fire({
        title: 'Pago no completado',
        text: 'Tu compra quedó en estado pendiente. Puedes retomarla desde "Compras pendientes".',
        icon: 'info',
        confirmButtonText: 'Entendido'
    });
};
```

**Beneficio:** UX inmediata, sin tocar backend.

**Limitación:** sigue dependiendo de que el usuario tenga la pestaña abierta.

### 5.2 Mediano plazo — webhook `confirmation` (más robusto)

1. Crear un endpoint nuevo, p. ej. `POST /api/epayco/webhook` (o
   `/mercurio/servicios/webhook-epayco`), **fuera** del middleware
   `mercurio.auth` (ePayco no se autentica con JWT).
2. Registrar la URL en el panel de ePayco como `confirmation`.
3. Validar la `x_signature` que envía ePayco.
4. Actualizar la precompra consultando el endpoint `/reference/{ref_payco}`
   para confirmar el estado antes de cambiar nada.
5. Hacer el flujo **idempotente**: si el mismo `ref_payco` notifica dos
   veces, no romper.

**Beneficio:** confirmación aunque el usuario cierre el navegador.

**Riesgo:** exponer endpoint público → obligatorio validar firma.

### 5.3 Job de limpieza de precompras abandonadas

Comando Artisan + schedule diario (02:00):

```bash
# Simulación
php artisan precompras:marcar-abandonadas --dias=7 --dry-run

# Ejecución
php artisan precompras:marcar-abandonadas --dias=7
```

- Job: [`MarcarPrecomprasAbandonadas`](../app/Jobs/MarcarPrecomprasAbandonadas.php)
- Comando: `precompras:marcar-abandonadas`
- Schedule: [`routes/console.php`](../routes/console.php) — diario a las 02:00
- Criterio: `estado = PE` y `fecha_precompra` anterior a N días (default 7) → `AB`
- Motivo: `MOTIVO_ABANDONO_INACTIVIDAD`

Requiere que el scheduler del servidor esté activo. Guía operativa:
[precompras-job-abandonadas.md](./precompras-job-abandonadas.md).

**Beneficio:** limpia PE huérfanas (cierre de pestaña/navegador sin `onClose`).

---

## 6. Riesgos actuales

1. **Abandonos invisibles** — sin `onClose` ni `confirmation`, no se sabe
   cuántos usuarios cierran la modal sin pagar.
2. **Precompras huérfanas** — pueden acumularse y distorsionar métricas de
   "compras pendientes".
3. **Medios de pago no curados** — si ePayco habilita un método no deseado,
   este proyecto lo mostraría sin filtro.
4. **Branding inconsistente** — la modal rompe el look & feel del resto de
   la aplicación.
5. **Sin firma en respuestas** — si en el futuro se agrega un webhook sin
   validar `x_signature`, queda expuesto a manipulación.

---

## 7. Próximos pasos sugeridos

| # | Acción                                                                   | Esfuerzo | Valor |
| - | ------------------------------------------------------------------------ | -------- | ----- |
| 1 | Agregar `onClose` con SweetAlert informativo                              | Bajo     | Alto  |
| 2 | Whitelist de medios de pago con `methods`                                | Bajo     | Medio |
| 3 | Crear endpoint de webhook `confirmation` con validación de firma         | Medio    | Alto  |
| 4 | ~~Job programado que marque precompras abandonadas~~ — hecho (`precompras:marcar-abandonadas`) | — | — |
| 5 | Customizar textos (`titleButtonPay`, `title`)                            | Bajo     | Bajo  |
| 6 | Habilitar verificación TLS en `ApiEpayco::validarReferencia()` (sin `withoutVerifying()`) | Bajo | Seguridad |