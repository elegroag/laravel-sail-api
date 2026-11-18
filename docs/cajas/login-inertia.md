# Login Cajas (Inertia + React)

> **Última revisión:** 2026-11-18

## Objetivo

Autenticación administrativa de Cajas con UI Inertia/React, conservando el
**captcha de imagen** propio (no Google reCAPTCHA).

## Rutas

| Método | URL | Nombre | Acción |
| ------ | --- | ------ | ------ |
| GET | `/cajas/login` | `cajas.login` | Página Inertia `Cajas/Auth/Login` |
| GET | `/cajas/captcha` | `cajas.captcha.image` | PNG del captcha (sesión) |
| POST | `/cajas/autenticar` | `cajas.autenticar` | Valida captcha + `AuthCajas` |

## Captcha

- Generación: `CaptchaService::generate()` al servir la imagen.
- Verificación: `CaptchaService::verify()` (one-shot, TTL configurable).
- En el cliente: recarga con `?v=timestamp` tras error o botón refresh.

## UI

`resources/js/pages/Cajas/Auth/Login.tsx`
