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
| Cajas | 63 | 56 | 18 | 45 |
| Mercurio | 25 | 22 | 22 | 3 |

Fuera de este checklist: `Adapter/ApplicationController.php`, `Api/*` (6), `Web/WebController.php`, `Controller.php` base.

Orden sugerido (recomendación, no arrancar):
1. Cajas `MercurioNNController` clones (CRUD parecido, mucho `DbBase` de constructor).
2. Cajas Auth / Reportes con `findFirst`.
3. Cajas `Aprueba*` (gordos; Services/Aprueba ya Eloquent).
4. Mercurio portal (afiliación + Principal).

## Cajas — Auth / principal

- [ ] `AuthController.php` — `ApplicationController`, 290 LOC — `findFirst`×1, `DbBase`×4/`rawConnect`×2; `setParamToView`×7
- [x] `PrincipalController.php` — `Controller`, 321 LOC — **omitido**: Eloquent / `Controller` Laravel, sin APIs Kumbia de query
- [x] `CaptchaController.php` — `Controller`, 100 LOC — **omitido**: Eloquent / `Controller` Laravel, sin APIs Kumbia de query
- [x] `UsuarioController.php` — `Controller`, 492 LOC — **omitido**: Eloquent / `Controller` Laravel, sin APIs Kumbia de query

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

## Cajas — Reportes / consultas / auditoría

- [ ] `AuditoriaController.php` — `ApplicationController`, 352 LOC — `DbBase`×2/`rawConnect`×1
- [ ] `ConsultaController.php` — `ApplicationController`, 673 LOC — `DbBase`×2/`rawConnect`×1; omitir `whereRaw`×1, `new Model`×1
- [x] `ConsultaDocumentoSolicitudController.php` — `ApplicationController`, 32 LOC — **omitido queries**: sin `findFirst`/`inQueryAssoc`/`DbBase` (sigue `ApplicationController`)
- [x] `InformeSolicitudController.php` — `ApplicationController`, 80 LOC — **omitido queries**: sin `findFirst`/`inQueryAssoc`/`DbBase` (sigue `ApplicationController`)
- [ ] `ReasignaController.php` — `ApplicationController`, 234 LOC — `DbBase`×2/`rawConnect`×1; omitir `whereRaw`×1, `new Model`×6
- [ ] `ReportesController.php` — `ApplicationController`, 732 LOC — `findFirst`×18, `DbBase`×2/`rawConnect`×1; `setParamToView`×1
- [ ] `ReportesolController.php` — `ApplicationController`, 77 LOC — `DbBase`×3/`rawConnect`×1
- [ ] `ReporteOportunidadAfiliacionController.php` — `ApplicationController`, 147 LOC — `DbBase`×3/`rawConnect`×1
- [x] `ReporteComprasServiciosController.php` — `ApplicationController`, 32 LOC — **omitido queries**: sin `findFirst`/`inQueryAssoc`/`DbBase` (sigue `ApplicationController`)
- [x] `ReporteSolicitudesEmpresaController.php` — `ApplicationController`, 39 LOC — **omitido queries**: sin `findFirst`/`inQueryAssoc`/`DbBase` (sigue `ApplicationController`)

## Cajas — Admin / menú / otros

- [ ] `AdmproductosController.php` — `ApplicationController`, 533 LOC — `findFirst`×7, `DbBase`×2/`rawConnect`×1; `setParamToView`×3
- [ ] `AdmserviciosController.php` — `ApplicationController`, 213 LOC — `DbBase`×2/`rawConnect`×1; omitir `whereRaw`×1
- [ ] `BannerController.php` — `ApplicationController`, 262 LOC — `DbBase`×2/`rawConnect`×1
- [ ] `ComandoController.php` — `ApplicationController`, 113 LOC — `findFirst`×3, `inQueryAssoc`×1, `DbBase`×2/`rawConnect`×1
- [ ] `ComponenteDinamicoController.php` — `Controller`, 411 LOC — `DbBase`×2/`rawConnect`×1
- [ ] `EpaycoCuentaController.php` — `ApplicationController`, 228 LOC — `DbBase`×2/`rawConnect`×1
- [ ] `FormularioDinamicoController.php` — `Controller`, 290 LOC — `DbBase`×2/`rawConnect`×1
- [ ] `Gener42Controller.php` — `ApplicationController`, 117 LOC — `DbBase`×2/`rawConnect`×1; omitir `whereRaw`×3, `new Model`×1
- [ ] `MenuController.php` — `Controller`, 535 LOC — `DbBase`×3/`rawConnect`×1; omitir `whereRaw`×1, `new Model`×1
- [x] `MenuPermissionController.php` — `Controller`, 186 LOC — **omitido**: Eloquent / `Controller` Laravel, sin APIs Kumbia de query
- [ ] `NotificacionesController.php` — `ApplicationController`, 149 LOC — `findFirst`×1, `DbBase`×2/`rawConnect`×1; `setParamToView`×3

## Mercurio — Auth / principal / usuario / notificaciones

- [ ] `AuthController.php` — `Controller`, 1034 LOC — `DbBase`×3/`rawConnect`×1; omitir `whereRaw`×1
- [ ] `PrincipalController.php` — `ApplicationController`, 877 LOC — `findFirst`×6, `inQueryAssoc`×1, `DbBase`×3/`rawConnect`×1; omitir `whereRaw`×2, `new Model`×6
- [ ] `UsuarioController.php` — `ApplicationController`, 219 LOC — `DbBase`×2/`rawConnect`×1; omitir `new Model`×3
- [ ] `NotificacionesController.php` — `ApplicationController`, 217 LOC — `DbBase`×3/`rawConnect`×1

## Mercurio — Afiliación

- [ ] `EmpresaController.php` — `ApplicationController`, 661 LOC — `findFirst`×1, `DbBase`×3/`rawConnect`×1; omitir `new Model`×1
- [ ] `TrabajadorController.php` — `ApplicationController`, 748 LOC — `DbBase`×3/`rawConnect`×1; omitir `whereRaw`×1, `new Model`×4
- [ ] `ConyugeController.php` — `ApplicationController`, 908 LOC — `DbBase`×3/`rawConnect`×1; omitir `whereRaw`×1, `new Model`×3
- [ ] `BeneficiarioController.php` — `ApplicationController`, 1147 LOC — `DbBase`×3/`rawConnect`×1; omitir `whereRaw`×2, `new Model`×5
- [ ] `IndependienteController.php` — `ApplicationController`, 777 LOC — `DbBase`×3/`rawConnect`×1; omitir `whereRaw`×1, `new Model`×3
- [ ] `FacultativoController.php` — `ApplicationController`, 738 LOC — `DbBase`×2/`rawConnect`×1; omitir `whereRaw`×1, `new Model`×2
- [ ] `PensionadoController.php` — `ApplicationController`, 758 LOC — `findFirst`×2, `DbBase`×3/`rawConnect`×1; omitir `whereRaw`×1, `new Model`×4
- [ ] `DomesticoController.php` — `ApplicationController`, 526 LOC — `DbBase`×2/`rawConnect`×1; `renderText`×2; omitir `new Model`×6
- [ ] `ComunitariaController.php` — `ApplicationController`, 524 LOC — `DbBase`×2/`rawConnect`×1; `renderText`×1; omitir `new Model`×5
- [ ] `ParticularController.php` — `ApplicationController`, 57 LOC — `DbBase`×2/`rawConnect`×1

## Mercurio — Consultas / movimientos / certificados / firmas / productos / actualiza

- [ ] `ConsultasEmpresaController.php` — `ApplicationController`, 1029 LOC — `DbBase`×2/`rawConnect`×1; `renderText`×1; omitir `new Model`×6
- [ ] `ConsultasTrabajadorController.php` — `ApplicationController`, 507 LOC — `DbBase`×2/`rawConnect`×1
- [ ] `MovimientosController.php` — `ApplicationController`, 219 LOC — `DbBase`×2/`rawConnect`×1
- [ ] `CertificadosController.php` — `ApplicationController`, 287 LOC — `DbBase`×2/`rawConnect`×1; omitir `new Model`×2
- [ ] `FirmasController.php` — `ApplicationController`, 277 LOC — `findFirst`×1, `DbBase`×3/`rawConnect`×1; omitir `new Model`×1
- [ ] `ProductosController.php` — `ApplicationController`, 243 LOC — `DbBase`×3/`rawConnect`×1
- [ ] `ActualizaEmpresaController.php` — `ApplicationController`, 636 LOC — `DbBase`×2/`rawConnect`×1; `clp`×2; omitir `new Model`×4
- [ ] `ActualizaTrabajadorController.php` — `ApplicationController`, 908 LOC — `inQueryAssoc`×4, `DbBase`×3/`rawConnect`×1; omitir `new Model`×3
- [x] `DocumentosController.php` — `Controller`, 132 LOC — **omitido**: Eloquent / `Controller` Laravel, sin APIs Kumbia de query

## Mercurio — Ecommerce

- [x] `EcommerceController.php` — `ApplicationController`, 1335 LOC — **omitido queries**: sin `findFirst`/`inQueryAssoc`/`DbBase` (sigue `ApplicationController`)

## Mercurio — Concerns

- [x] `RendersSolicitudesGrid.php` — `—`, 68 LOC — **omitido**: Eloquent / `Controller` Laravel, sin APIs Kumbia de query; `renderText`×2

## Nota Adapter / Api / Web (fuera)

- `Adapter/ApplicationController.php` — no migrar primero; 78 controllers lo extienden.
- `Api/*` (6) y `Web/WebController.php` ya extienden `Controller` Laravel.

