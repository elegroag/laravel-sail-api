# Checklist — Middleware (deuda Kumbia / ACL)

Repo: `comfaca-enlinea/laravel`, rama `release/v01`. Fuente: hallazgo de operador-edwin (2026-09-11), verificado en las 7 clases. **No se implementa** hasta que Ricardo elija alcance.

No hay `Filter.php` ni `beforeFilter`/`afterFilter`. La carpeta es Laravel. La deuda es el ACL portado + sesión/flash Kumbia + CSRF except por prefijo.

Colaterales (no son clases de `app/Http/Middleware` pero los middleware las usan): `SessionCookies`, `app/Helpers/flash.php`, `bootstrap/app.php`.

## Resumen

| Archivo | LOC | Alias / wire | Deuda Kumbia/ACL | Nota |
| --- | --- | --- | --- | --- |
| `MercurioAuthenticated.php` | 142 | `mercurio.auth` | sí | ACL menú; 401 filtra request/sesión |
| `CajasAuthenticated.php` | 167 | `cajas.auth` | sí | ACL menú + `validOption`; flash sin `__LINE__` |
| `ApiAuthMiddleware.php` | 68 | `api.auth` | no | Bearer + `AuthJwt` |
| `ApiDocumentationAuth.php` | 32 | `api.docs.auth` | no | stub `Auth::check()` en prod |
| `EnsureEndUserAvailable.php` | 54 | grupo `web` | no | mantenimiento end-users |
| `HandleInertiaRequests.php` | 101 | grupo `web` | no | Inertia share |
| `HandleAppearance.php` | 23 | grupo `web` | no | cookie appearance |

## Por archivo

- [ ] `MercurioAuthenticated.php` — `SessionCookies::check()` (no Auth/guards). `autorization()` parsea FQCN (`Mercurio\\FooController` → `FooController`) y mira `menu_items` + `menu_tipos` (`codapl=ME`, `tipo` de sesión). **No mira `actionMethod`** (salvo whitelist de comprobantes en `DocumentosController`). 401 JSON de sesión incluye `request->all()`, `session('user')`, `has`. `set_flashdata` en redirects. Typo `autorization`. Setea `user`/`tipo`/`tipfun` en attributes.
- [ ] `CajasAuthenticated.php` — mismo `SessionCookies` + parseo FQCN. ACL contra `menu_items` + `menu_permissions` por `tipfun`. **Sí mira action** (`validOption` sobre `opciones` JSON). Flash de no-autorizado ya no incluye `__LINE__` (queda controller/action). Bloquea si hay `session('tipo')` (usuario Mercurio). Setea `user`/`tipfun`/`opciones`.
- [x] `ApiAuthMiddleware.php` — **omitido**: Bearer + `AuthJwt::CheckSimpleToken`, sin ACL Kumbia.
- [ ] `ApiDocumentationAuth.php` — stub: en `production` usa `Auth::check()` (guard Laravel, no sesión Mercurio/Cajas) y `route('login')`. No es Kumbia; queda como deuda de wiring.
- [x] `EnsureEndUserAvailable.php` — **omitido**: mantenimiento `app.maintenance_endusers`; bypass si hay `tipfun`.
- [x] `HandleInertiaRequests.php` — **omitido**: Inertia Laravel.
- [x] `HandleAppearance.php` — **omitido**: cookie appearance.

## Transversal (no son middleware, los consumen)

- [ ] `SessionCookies` (`app/Library/Auth/SessionCookies.php`) — `check()` = `session()->has('user')`. Equivale a `Session::check()` de Kumbia. No hay guard Laravel.
- [ ] `set_flashdata` / `PERSISTE` / `FLASH` (`app/Helpers/flash.php`) — API Kumbia/CI. Los dos auth middleware la usan en redirects.
- [ ] CSRF except por prefijo — `bootstrap/app.php` exceptúa `web/*`, `mercurio/*`, `cajas/*`, `api/*`. Ya está en el checklist post-migración (Corte 1). No duplicar el trabajo; aquí solo se anota.
- [ ] Grupo `web` re-appendea `EncryptCookies`, `StartSession`, `VerifyCsrfToken`, `SubstituteBindings` (ya van en el stack web de Laravel).
- [ ] Vocabulario portado: `autorization`, `tipfun`, `tipo`, `codapl`, `application`/`controller`/`action`.

## Cortes sugeridos (elegir alcance, no arrancar)

1. **401 leak Mercurio** — no devolver `request->all()` / `session('user')` / `has` en JSON. Bajo, seguro.
2. **Flash Cajas** — `__LINE__` quitado. Queda el nombre de controller/action (Ricardo no pidió quitarlo).
3. **CSRF** — mismo ítem que Corte 1 del checklist post-migración. No empezar aquí si ya está decidido allá.
4. **ACL** — Policies/Gates por `tipfun`/`tipo` (o dejar menú y alinear Mercurio para que también mire `action`). Medio/alto.
5. **Auth guard** — sustituir `SessionCookies` por guard propio. Alto; toca login Mercurio/Cajas.
6. **Flash nativo** — `session()->flash()` en vez de `set_flashdata`. Alto; muchos consumidores fuera de middleware.
7. **ApiDocumentationAuth** — o cablear sesión Cajas, o documentar que el stub no cubre prod.
8. **Grupo web** — dejar de re-appendear middlewares que Laravel ya pone.

## Fuera

`Filter.php` / `beforeFilter` / `afterFilter`: no existen. No mezclar con Corte 8/9 de queries. No borrar `SessionCookies` ni `flash.php` hasta vaciar consumidores.

Models: `docs/generales/deuda-models-checklist.md`.
