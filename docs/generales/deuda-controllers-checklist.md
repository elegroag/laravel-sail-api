# Checklist — Controllers Cajas / Mercurio (Corte 9)

Repo: `comfaca-enlinea/laravel`, rama `release/v01`. Corte 8 Services cerrado (`beaac54b`). Este inventario es **nuevo** y no mezcla con `deuda-post-migracion-checklist.md`.

Alcance: listar cada controller de `app/Http/Controllers/Cajas` y `.../Mercurio`. Adapter, Api y Web quedan fuera (salvo nota).

Misma política que Corte 8:
- Deuda de query: `findFirst`, `inQueryAssoc`, `DbBase`/`rawConnect`.
- **Omitir** `whereRaw` (Eloquent) y `new Model` / `new MercurioNN`.
- **No borrar primero** `ApplicationController` / `ModelBase`. `setParamToView` / `renderText` / `clp` se anotan, no se reescriben en este corte de queries.
- En `$request->input()` quitar filtros Kumbia: `addslaches`, `alpha`, `extraspaces`, `striptags` (Laravel `input($key)`).
- No se implementa nada hasta que Ricardo elija el primer grupo.

## Resumen

| App | Archivos | `ApplicationController` | Deuda query | Sin deuda query |
| --- | --- | --- | --- | --- |
| Cajas | 63 | 56 | 12 | 51 |
| Mercurio | 25 | 22 | 22 | 3 |

Fuera de este checklist: `Adapter/ApplicationController.php`, `Api/*` (6), `Web/WebController.php`, `Controller.php` base.

Orden sugerido (recomendación, no arrancar):
1. Cajas `MercurioNNController` clones (CRUD parecido, mucho `DbBase` de constructor).
2. Cajas Auth / Reportes con `findFirst`.
3. Cajas `Aprueba*` (gordos; Services/Aprueba ya Eloquent).
4. Mercurio portal (afiliación + Principal).

## Cajas — Auth / principal — cerrado

- [x] `AuthController.php` — `fetchOne`/`findFirst`/`updateAll`/`DbBase` → `Gener02` Eloquent; `setParamToView` intacto
- [x] `PrincipalController.php` — **omitido**: Eloquent / `Controller` Laravel
- [x] `CaptchaController.php` — **omitido**: Eloquent / `Controller` Laravel
- [x] `UsuarioController.php` — **omitido**: Eloquent / `Controller` Laravel

## Cajas — Aprueba — cerrado

- [x] `ApruebaBeneficiarioController.php` — `DbBase` txs → `DB::beginTransaction`; `setParamToView` intacto; omitir `whereRaw`/`new Model`
- [x] `ApruebaCertificadoController.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `ApruebaComunitariaController.php` — txs → `DB::*`; `setParamToView` intacto; omitir `whereRaw`/`new Model`
- [x] `ApruebaConyugeController.php` — txs + `fetchOne` → `DB::select`; `setParamToView` intacto; omitir `whereRaw`/`new Model`
- [x] `ApruebaEmpresaController.php` — txs → `DB::*`; `setParamToView` intacto; omitir `whereRaw`/`new Model`
- [x] `ApruebaFacultativoController.php` — `Mercurio36::where('id')->first()`; txs → `DB::*`; `setParamToView` intacto; omitir `whereRaw`/`new Model`
- [x] `ApruebaIndependienteController.php` — `Mercurio41::where()->first()` ×3; txs → `DB::*`; `setParamToView` intacto; omitir `whereRaw`/`new Model`
- [x] `ApruebaPensionadoController.php` — `Mercurio38::where()->first()` ×5; txs → `DB::*`; `setParamToView` intacto; omitir `whereRaw`/`new Model`
- [x] `ApruebaRetiroController.php` — txs → `DB::*`; `renderText` intacto; omitir `whereRaw`/`new Model`
- [x] `ApruebaTrabajadorController.php` — txs + `fetchOne` → `DB::select`; `setParamToView` intacto; omitir `whereRaw`/`new Model`
- [x] `ApruebaUpEmpresaController.php` — `inQueryAssoc`×4 → `DB::select`; txs → `DB::*`; `setParamToView` intacto; omitir `whereRaw`/`new Model`
- [x] `ApruebaUpTrabajadorController.php` — txs → `DB::*`; `setParamToView` intacto; omitir `whereRaw`/`new Model`

## Cajas — Tablas MercurioNN — cerrado

`DbBase` txs → `DB::beginTransaction`/`commit`/`rollBack`. Sin `findFirst`/`inQueryAssoc`. Intactos: `whereRaw`, `new Model`, `ApplicationController`.

- [x] `Mercurio01Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio02Controller.php` — txs → `DB::*`; `setParamToView` intacto; omitir `whereRaw`/`new Model`
- [x] `Mercurio03Controller.php` — `DbBase` constructor sin usos, retirado; omitir `whereRaw`/`new Model`
- [x] `Mercurio04Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio06Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio09Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio11Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio12Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio13Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio14Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio18Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio26Controller.php` — txs → `DB::*`; omitir `new Model`
- [x] `Mercurio50Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio51Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio52Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio53Controller.php` — txs → `DB::*`; omitir `new Model`
- [x] `Mercurio55Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio56Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio57Controller.php` — txs → `DB::*`; omitir `new Model`
- [x] `Mercurio58Controller.php` — txs → `DB::*`; omitir `new Model`
- [x] `Mercurio59Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio65Controller.php` — txs `$this->db` nulo → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio67Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `Mercurio72Controller.php` — txs → `DB::*`; omitir `new Model`
- [x] `Mercurio73Controller.php` — txs → `DB::*`; omitir `new Model`
- [x] `Mercurio74Controller.php` — txs → `DB::*`; omitir `new Model`

## Cajas — Reportes / consultas / auditoría — cerrado

- [x] `AuditoriaController.php` — `DbBase` constructor sin usos, retirado
- [x] `ConsultaController.php` — `DbBase` constructor sin usos, retirado; omitir `whereRaw`/`new Model`
- [x] `ConsultaDocumentoSolicitudController.php` — **omitido queries**
- [x] `InformeSolicitudController.php` — **omitido queries**
- [x] `ReasignaController.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `ReportesController.php` — `novedadesSubsidio`: SAT `find`/`findFirst` → `DB::table` (`sat02`–`sat13`, `empresa.sat20`); getters → columnas; `Gener02` Eloquent intacto. Sin modelos SAT nuevos.
- [x] `ReportesolController.php` — `DbBase` constructor sin usos, retirado
- [x] `ReporteOportunidadAfiliacionController.php` — `DbBase` constructor sin usos, retirado
- [x] `ReporteComprasServiciosController.php` — **omitido queries**
- [x] `ReporteSolicitudesEmpresaController.php` — **omitido queries**

## Cajas — Admin / menú / otros — cerrado

- [x] `AdmproductosController.php` — `findFirst`/`fetchOne` → `ServiciosCupos`/`PinesAfiliado` Eloquent; `DbBase` retirado; `setParamToView` intacto
- [x] `AdmserviciosController.php` — `DbBase` constructor sin usos, retirado; omitir `whereRaw`
- [x] `BannerController.php` — txs → `DB::*`
- [x] `ComandoController.php` — `$this->Comandos->findFirst`/`inQueryAssoc` → `Comandos` Eloquent; `DbBase` retirado
- [x] `ComponenteDinamicoController.php` — `DbBase` constructor sin usos, retirado
- [x] `EpaycoCuentaController.php` — txs → `DB::*`
- [x] `FormularioDinamicoController.php` — `DbBase` constructor sin usos, retirado
- [x] `Gener42Controller.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `MenuController.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `MenuPermissionController.php` — **omitido**: Eloquent / `Controller` Laravel
- [x] `NotificacionesController.php` — `findFirst`/`count` → `Notificaciones` Eloquent; `DbBase` retirado; `setParamToView` intacto

## Mercurio — Auth / principal / usuario / notificaciones — cerrado

- [x] `AuthController.php` — txs → `DB::*`; `DbBase` retirado; omitir `whereRaw`
- [x] `PrincipalController.php` — `findFirst` → Eloquent; `inQueryAssoc` → `Mercurio15::all()`; txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `UsuarioController.php` — `DbBase` constructor sin usos, retirado; omitir `new Model`
- [x] `NotificacionesController.php` — `DbBase` constructor sin usos, retirado

## Mercurio — Afiliación — cerrado

- [x] `EmpresaController.php` — `Mercurio30::where()->first()`; txs → `DB::*`; omitir `new Model`
- [x] `TrabajadorController.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `ConyugeController.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `BeneficiarioController.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `IndependienteController.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `FacultativoController.php` — txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `PensionadoController.php` — `Mercurio38`/`Mercurio37` Eloquent; txs → `DB::*`; omitir `whereRaw`/`new Model`
- [x] `DomesticoController.php` — `DbBase` constructor sin usos, retirado; `renderText` intacto; omitir `new Model`
- [x] `ComunitariaController.php` — `DbBase` constructor sin usos, retirado; `renderText` intacto; omitir `new Model`
- [x] `ParticularController.php` — `DbBase` constructor sin usos, retirado

## Mercurio — Consultas / movimientos / certificados / firmas / productos / actualiza — cerrado

- [x] `ConsultasEmpresaController.php` — `DbBase` constructor sin usos, retirado; `input('cedtra')` sin filtros Kumbia; `renderText` intacto; omitir `new Model`
- [x] `ConsultasTrabajadorController.php` — `DbBase` constructor sin usos, retirado
- [x] `MovimientosController.php` — `DbBase` constructor sin usos, retirado
- [x] `CertificadosController.php` — txs → `DB::*`; omitir `new Model`
- [x] `FirmasController.php` — `Mercurio01::first()`; txs → `DB::*`; omitir `new Model`
- [x] `ProductosController.php` — `DbBase` constructor sin usos, retirado
- [x] `ActualizaEmpresaController.php` — `fetchOne` → `Mercurio10` Eloquent; txs → `DB::*`; `clp` intacto; omitir `new Model`
- [x] `ActualizaTrabajadorController.php` — `inQueryAssoc`/`fetchOne` → `Mercurio47`/`10`/`28`/`33` Eloquent; txs → `DB::*`; omitir `new Model`
- [x] `DocumentosController.php` — **omitido**: Eloquent / `Controller` Laravel

## Mercurio — Ecommerce

- [x] `EcommerceController.php` — `ApplicationController`, 1335 LOC — **omitido queries**: sin `findFirst`/`inQueryAssoc`/`DbBase` (sigue `ApplicationController`)

## Mercurio — Concerns

- [x] `RendersSolicitudesGrid.php` — `—`, 68 LOC — **omitido**: Eloquent / `Controller` Laravel, sin APIs Kumbia de query; `renderText`×2

## Nota Adapter / Api / Web (fuera)

- `Adapter/ApplicationController.php` — no migrar primero; 78 controllers lo extienden.
- `Api/*` (6) y `Web/WebController.php` ya extienden `Controller` Laravel.

## Middleware

Inventario aparte: `docs/generales/deuda-middleware-checklist.md` (ACL/sesión/flash/CSRF). No mezclar con este corte de queries.
