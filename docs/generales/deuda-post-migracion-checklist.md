# Checklist — deuda post-migración Mercurio/Cajas

Fuente: diagnóstico de operador-edwin (2026-09-11). Corte 8 Services en curso; CSRF/CORS y cortes 1–7 esperan decisión.

| Dato | Valor |
| --- | --- |
| Repo local | `/home/edwin-tics/proyectos/comfaca-enlinea/laravel` |
| GitHub | https://github.com/elegroag/laravel-sail-api.git |
| Rama / HEAD | `release/v01` |
| vs origin | +33 commits, **sin push**. Incluye Corte 8 Services (Aprueba, Entidades, CajaServices, Cajas, Formularios, PreparaFormularios) más ePayco/ecommerce |
| Stack | Laravel 12; Mercurio (afiliados, JWT+cookie) y Cajas (backoffice, cookie) |
| UI | Blade + gulp/AMD (119 views Mercurio, 180 Cajas, `public/src`); Inertia+React parcial (~134 TSX, auth/landing/admin) |
| Tests | MySQL `mercurio_dev` @ `172.168.0.15` |

Migró bien (no es deuda): rutas/middlewares `mercurio.auth` / `cajas.auth`, Services, docs ePayco, `legacy/` acotado, gitignore `vendor`/`node_modules`, sin Phalcon. `.env.example` está en local (`1a340916`), no en origin.

---

## Crítica

- [ ] **CSRF desactivado por prefijo.** `bootstrap/app.php` (aprox. L33–38) exceptúa `web/*`, `mercurio/*`, `cajas/*`, `api/*`. Restaurar CSRF; dejar except solo webhook ePayco (y equivalentes que lo requieran).
- [ ] **CORS abierto.** `config/cors.php`: `allowed_origins` `*` y `supports_credentials` true. Cerrar orígenes y no combinar `*` con credentials.
- [x] **`dd()` / `dump()` en código de producto.** Quitados (`e6870011`):
  - [x] `app/Services/Aprueba/ApruebaMadreComuni.php` — `dd($procesadorComando->getLineaComando())` (bloqueaba la aprobación)
  - [x] `app/Services/CajaServices/NotificacionService.php` — `dd` al fallar `save()`; ahora lanza `RuntimeException` con los mensajes
  - [x] `app/Services/Utils/GeneralService.php` — `dump('sss', …)`
  - [x] Comentarios: `TrabajadorService.php`, `BeneficiariosDocuments.php`
- [ ] **ActiveRecord Kumbia (adaptación temporal).** `ModelBase` extiende Eloquent pero reimplementa `findFirst` / `whereRaw` / `DB::select`. Retomar migración a Eloquent, Cache y Request de Laravel. Inventario de módulos abajo.

## Alta

- [ ] **Dual UI Vite + gulp** sin deprecación. Congelar gulp; Inertia/React solo en módulos nuevos. Documentar qué queda en Blade/AMD.
- [ ] **Controllers gordos.** 78 extienden `ApplicationController`; varios >1000 LOC (`Aprueba*`, `Beneficiario`, Auth Mercurio). Extraer lógica a Services.
- [ ] **Mail dual.** Laravel Mail + PHPMailer. Unificar en uno.
- [ ] **Acoplamiento runtime** a API Python/Flask. Documentar contrato y fallos; no ampliar el acoplamiento.
- [ ] **Swagger fantasma.** README pide `l5-swagger:generate`; el paquete no está en `composer.json`. Instalarlo o borrar la instrucción.
- [ ] **Tests delgados.** 61 ctrl Cajas → 3 Feature; 25 Mercurio → 1 Feature; PHPUnit contra MySQL compartido. Smoke login Mercurio/Cajas + 1 flujo Aprueba; DB de test aislada.

## Media

- [ ] `MercurioNN` / KumbiaPDF (legado PDF).
- [ ] Excel vía PEAR legado.
- [ ] `JwtManager` sin usos (retirar o usar).
- [ ] `ScriptLegacy` silencia warnings.
- [ ] API starter `empresas` / `trabajadores` convive con el dominio real.
- [ ] README / `docs/generales/architecture.md` venden React como frontend principal (desactualizado). Alinear con Blade+gulp + Inertia parcial.
- [ ] Push de `.env.example` (`1a340916`) si debe vivir en origin.
- [ ] Push de los +32 commits locales cuando Ricardo lo pida.

---

## ActiveRecord Kumbia → Laravel

Contexto (2026-09-11): adaptación temporal en `app/Models/Adapter` y `app/Http/Controllers/Adapter`. **Retomar** hacia recursos Laravel: Eloquent (`where`, relaciones, `$fillable`), `Illuminate\Support\Facades\Cache`, `Illuminate\Http\Request` (y FormRequest). Código nuevo **no** debe extender `ModelBase` ni `ApplicationController`.

Estado medido:

| Pieza | Cantidad |
| --- | --- |
| Models `extends ModelBase` | 95 |
| Models Eloquent puro (`extends Model`, sin ModelBase) | 21 |
| `whereRaw` / `DB::select` en `app/` | 239 (conteo bruto; `whereRaw` Eloquent **no** es Corte 8) |
| Controllers `extends ApplicationController` | 78 (56 Cajas + 22 Mercurio) |
| APIs Kumbia aún usadas | `findFirst`, `findAllBySql`, `inQueryAssoc`, `DbBase::rawConnect`, `getSource` |

Ya en Eloquent (no reabrir): `EpaycoCuenta`, `EpaycoTransaccion`, `PrecompraServicio`, `FormularioDinamico`, `ComponenteDinamico`, `Mercurio62/64/66/68–71`, `NucleoFamiliar`, `Radicado`, `Profile`, `Task`, `Trabajador`, `RefreshToken`, `ApiEndpoint`, `Xml4b091`, `Xml4b094`.

### Capa adapter (quitar al final, no primero)

- [ ] `app/Models/Adapter/ModelBase.php` — `findFirst` + `whereRaw`
- [ ] `app/Models/Adapter/ActiveRecordBase.php` — `inQueryAssoc`
- [ ] `app/Models/Adapter/DbBase.php` — `rawConnect`
- [ ] `app/Http/Controllers/Adapter/ApplicationController.php` — 78 controllers; `clp()`, `setParamToView`, `renderText` vs `Request`/`view()`
- [ ] `app/Helpers/helpers.php` — `get_params_destructures` y helpers Kumbia

### Models por dominio (95)

- [ ] **MercurioNN** (59): `Mercurio01`–`Mercurio85` que aún extienden `ModelBase` (afiliación, solicitudes, parámetros). Consumidores fuertes: Cajas `Mercurio*Controller`, Mercurio portal, `Services/Entidades`, `Services/Aprueba`.
- [ ] **Gener** (7): `Gener02`, `Gener08`, `Gener09`, `Gener18`, `Gener21`, `Gener40`, `Gener42` (usuarios/tablas Cajas).
- [ ] **SAT** (5): `Sat01`, `Sat14`, `Sat15`, `Mercusat02`, `RecepcionSat` + `Services/SatApi`.
- [ ] **Xml4b/4d** (10): `Xml4b001`, `004`, `005`, `064`, `070`, `081`, `085`, `086`, `087`, `Xml4d088`.
- [ ] **Menu** (3): `MenuItem`, `MenuPermission`, `MenuTipo` + `Services/Menu`.
- [ ] **Otros** (11): `AfiliadoHabil`, `AuditoriaSolicitudBase`, `Banner`, `ComandoEstructuras`, `Comandos`, `Empresa`, `Notificaciones`, `PinesAfiliado`, `ServiciosCupos`, `Subsi54`, `Tranoms`.

### Módulos consumidores (migrar por corte, no big-bang)

**Cajas (backoffice)** — 56 controllers con `ApplicationController` / `rawConnect`:

- [ ] Auth Cajas (`Cajas/AuthController`)
- [ ] Tablas Mercurio Cajas (`Mercurio01`–`14`, `18`, `26`, `50`–`59`, `67`, `72`–`74`)
- [ ] Aprueba* (`ApruebaEmpresa`, `Trabajador`, `Beneficiario`, `Conyuge`, `Independiente`, `Facultativo`, `Comunitaria`, `Pensionado`, `Certificado`, `Retiro`, `UpEmpresa`, `UpTrabajador`)
- [ ] Reportes (`ReportesController`, `ReportesolController`, `ReporteOportunidadAfiliacionController`, `ConsultaController`, `AuditoriaController`, `ReasignaController`)
- [ ] Admin (`Admproductos`, `Admservicios`, `Banner`, `Comando`, `Menu`, `Notificaciones`, `Gener42`, `EpaycoCuenta`, `FormularioDinamico`, `ComponenteDinamico`)

**Mercurio (portal afiliados)** — 22 controllers:

- [ ] Auth / Principal / Usuario / Notificaciones
- [ ] Afiliación: `Empresa`, `Trabajador`, `Conyuge`, `Beneficiario`, `Independiente`, `Facultativo`, `Pensionado`, `Domestico`, `Comunitaria`, `Particular`
- [ ] Consultas / movimientos / certificados / firmas / productos / actualiza empresa-trabajador

**Services — empezar por aquí** (inventario original 67). Deuda restante Corte 8: `findFirst` / `inQueryAssoc` / `rawConnect` / `DbBase`. El resto de services no toca ActiveRecord en este inventario.

**Omitir en Corte 8 (son Eloquent, no Kumbia):**
- `whereRaw` (query builder). Incluye `whereRaw($condi_extra)` del request. El interpolado `col='$x'` sí era deuda y ya se sustituyó en Entidades capa A (`5d3c2962`).
- `new Model` / `new MercurioNN` / `new GenerNN`. Instanciar un modelo y `save()`/`fill()` es Eloquent. No listar como deuda Kumbia.

### Services/Aprueba (5 de 14, cerrado)

Patrón ya migrado (copiar, no reinventar): `ApruebaTrabajador` / `ApruebaBeneficiario` — `Mercurio01::first()`, `Model::where('id', $id)->first()`, `Mercurio07::where(...)->where(...)->first()`.

Alcance recomendado: **solo queries**. No tocar `procesar()` de negocio, ni `ApplicationController`, ni `ModelBase`. `ApruebaSolicitud`, `ValidacionControlChecklist`, Beneficiario/Conyuge/Facultativo/Independiente/Pensionado/Trabajador/DatosTrabajador ya no usan `findFirst`.

- [x] `ApruebaCertificado.php` — Eloquent (`e6870011`): `Mercurio01::first()`, `Mercurio45::where('id')->first()`, `Mercurio07` where encadenados. `procesar()` intacto.
- [x] `ApruebaMadreComuni.php` — Eloquent (`e6870011`): `Mercurio01::first()`, `Mercurio39::where('id')`, `Mercurio07` where encadenados. `procesar()` intacto.
- [x] `ApruebaServicioDomestico.php` — Eloquent (`e6870011`): mismo patrón con `Mercurio40`. `procesar()` intacto.
- [x] `ApruebaDatosEmpresa.php` — Eloquent (`e6870011`): `where` bindings Mercurio47/33/30/07. `procesar()` intacto.
- [x] `ApruebaEmpresa.php` — Eloquent (`e6870011`): `findSolicitante` where encadenados. `procesar()` intacto. **Services/Aprueba cerrado.**

### Services/Entidades (14 + concern) — cerrado

Dos capas hechas: (A) `whereRaw` interpolado → Eloquent `where` (`5d3c2962`); (B) `DbBase`/`inQueryAssoc` → `DB::select` (`697d3937`). `whereRaw($condi_extra)` **omitido** (Eloquent). `ApiEndpointService` y `NotificacionService` ya eran Eloquent.

Orden recomendado:
1. `ParticularService` — constructor `DbBase` sin usarse; queries ya Eloquent.
2. `MadresComuniService` + `ServicioDomesticoService` (clones, 74 LOC) — `whereRaw` usuario/id.
3. Mismo patrón `consultaTipopc` en el resto.
4. Trait `PaginatesSolicitudQueries` + `inQueryAssoc` de listados (SQL crudo).

**Capa A (whereRaw interpolado) — hecha (`5d3c2962`).**

- [x] `ParticularService.php` — sin `DbBase`
- [x] `MadresComuniService.php` / `ServicioDomesticoService.php` — usuario/id
- [x] `consultaTipopc` interpolado: ActualizaEmpresa, Beneficiario, Certificado, Conyuge, DatosTrabajador, Facultativo, Independiente, Pensionado, Retiro, Trabajador (counts documento+coddoc)

**Capa B (DbBase / inQueryAssoc) — hecha (`697d3937`).** SQL crudo pasa por `DB::select`/`selectOne` (helpers del trait). No se reescribió a query builder.

- [x] `ActualizaEmpresaService.php` — `selectAssoc` / `selectOneAssoc`; sin DbBase
- [x] `BeneficiarioService.php` — `selectAssoc`; `getCount` a Eloquent count
- [x] `CertificadoService.php` — `selectAssoc`; `getCount` a Eloquent count
- [x] `ConyugeService.php` — constructor DbBase muerto retirado
- [x] `DatosTrabajadorService.php` — `selectAssoc`; sin DbBase
- [x] `EmpresaService.php` — constructor DbBase muerto retirado
- [x] `FacultativoService.php` — `selectAssoc`; sin DbBase
- [x] `IndependienteService.php` — `selectAssoc`; sin DbBase
- [x] `PensionadoService.php` — `selectAssoc`; sin DbBase
- [x] `RetiroService.php` — `selectAssoc`; `getCount` a Eloquent count
- [x] `TrabajadorService.php` — constructor DbBase muerto retirado
- [x] `Entidades/Concerns/PaginatesSolicitudQueries.php` — solo `DB::select`/`selectOne`; helpers `selectAssoc`/`selectOneAssoc`

### Services/CajaServices — cerrado

**Capa A (queries) — hecha (`d235d919`).** `whereRaw` Eloquent omitido.

- [x] `NotificacionService.php` — `DbBase`/`inQueryAssoc` → Eloquent `where`/`orderByDesc`
- [x] `CertificadosServices.php` — `findFirst` → `Mercurio01::first()`
- [x] `Mercurio13Services.php` — `find($query)` → `Mercurio13::whereRaw($query)->get()`
- [x] `Mercurio14Services.php` — `find($query)` → `Mercurio14::whereRaw($query)->get()`
- [x] `UsuarioServices.php` — **omitido**: solo `whereRaw` Eloquent

**Capa B (writes `Mercurio10::create` en rechazar/devolver) — hecha.** 12 archivos: setters + `save()` → `create()`. `campos_corregir` va en el insert de devolver. `max(item)` con `where` bindings.

- [x] `BeneficiarioServices.php`
- [x] `CertificadosServices.php`
- [x] `ConyugeServices.php`
- [x] `EmpresaServices.php`
- [x] `FacultativoServices.php`
- [x] `IndependienteServices.php`
- [x] `MadresComuniServices.php`
- [x] `PensionadoServices.php`
- [x] `ServicioDomesticoServices.php`
- [x] `TrabajadorServices.php`
- [x] `UpDatosEmpresaServices.php`
- [x] `UpDatosTrabajadorService.php`

### Services/Cajas — cerrado (`c991df53`)

- [x] `Mercurio01Service.php` — ya Eloquent (`first()` / `fill`); `whereRaw` omitido
- [x] `Mercurio02Service.php` — **omitido**: solo `whereRaw` Eloquent
- [x] `Mercurio11Service.php` — `updateOrCreate` / `firstOrNew`; `whereRaw` omitido

### Services/Formularios (4) — cerrado (`c991df53`)

- [x] `Afiliacion/FormularioBeneficiario.php` — `Gener18::where('coddoc')->first()`
- [x] `Afiliacion/FormularioConyuge.php` — `Gener18::where('coddoc')->first()`
- [x] `Declaration/JuramentadaBeneficiario.php` — `Gener18::where('coddoc')->first()`
- [x] `Declaration/JuramentadaConyuge.php` — `Gener18::where('coddoc')->first()`

### Services/FormulariosAdjuntos (3 omitidos)

- [x] `BeneficiarioAdjuntoService.php` — **omitido**: solo `new MercurioNN` Eloquent
- [x] `ConyugeAdjuntoService.php` — **omitido**: solo `new MercurioNN` Eloquent
- [x] `TrabajadorAdjuntoService.php` — **omitido**: solo `new MercurioNN` Eloquent

### Services/PreparaFormularios (2) — cerrado

- [x] `GestionFirmas.php` — `Mercurio16::where()->first()`; reload usa la misma instancia
- [x] `TrabajadorFormulario.php` — `findFirst`/`getFind` → `where`/`whereIn`/`first`/`get`

### Services/Autentications (1 pendiente + 1 omitido)

- [ ] `AutenticaGeneral.php` — `findFirst`×2 (`Mercurio01`)
- [x] `AutenticaService.php` — **omitido**: solo `new Mercurio19` Eloquent

### Services/Signup (1 pendiente + 4 omitidos)

- [ ] `SignupDomestico.php` — `findFirst`×2 (`Mercurio07`, `Mercurio01`)
- [x] `SignupEmpresas.php` — **omitido**: solo `new Mercurio30` Eloquent
- [x] `SignupFacultativos.php` — **omitido**: solo `new Mercurio36` Eloquent
- [x] `SignupIndependientes.php` — **omitido**: solo `new Mercurio41` Eloquent
- [x] `SignupPensionados.php` — **omitido**: solo `new Mercurio38` Eloquent

### Services/Utils (3 pendientes + 3 omitidos)

- [ ] `ChangeCuentaService.php` — `findFirst`×1 (`Mercurio07`)
- [x] `CrearUsuario.php` — **omitido**: solo `new MercurioNN` Eloquent
- [ ] `GeneralService.php` — `findFirst`×4 (archivo grande)
- [x] `GuardarArchivoService.php` — **omitido**: solo `use DbBase` sin usos
- [x] `RegistroSeguimiento.php` — **omitido**: solo `new Mercurio10` Eloquent
- [ ] `SolicitaClaveService.php` — `findFirst`×9

### Services/Menu (2)

- [ ] `Menu.php` — `DbBase`, `inQueryAssoc`, `rawConnect`
- [ ] `MenuCajas.php` — `DbBase`, `inQueryAssoc`, `rawConnect`

### Services/SatApi (2)

- [ ] `SatConsultaServices.php` — `findFirst`×5 (archivo grande)
- [ ] `SatServices.php` — `findFirst`×1 (`Mercusat02`)

### Services/Certificados (1)

- [ ] `EnviarCertificadoEmailService.php` — `findFirst`×1 (`Mercurio01`)

### Services/Reportes (1 omitido)

- [x] `ReporteSolicitudes.php` — **omitido**: solo `whereRaw` Eloquent

Destino por pieza: queries → Eloquent; tablas lookup → Cache; input HTTP → `Request` / FormRequest. No mezclar `findFirst("col='x'")` nuevo.

**Corte 8 Services — progreso.** Cerrados: Aprueba, Entidades, CajaServices, Cajas, Formularios, PreparaFormularios. Omitidos: `whereRaw`, `new Model`, FormulariosAdjuntos, Reportes, varios Signup/Utils. **Pendiente:** AutenticaGeneral, SignupDomestico, Utils (`ChangeCuenta`, `GeneralService`, `SolicitaClave`), Menu (2), SatApi (2), Certificados (1).

---

## Cortes sugeridos (elegir alcance)

Cortes 1–7 no iniciados (salvo `dd()`/`dump()` en Corte 1, hecho). Corte 8 Services en curso.

| Corte | Alcance | Ítems |
| --- | --- | --- |
| 1 | Seguridad mínima | CSRF por prefijo (salvo webhook ePayco), CORS cerrado, quitar `dd()`/`dump()` |
| 2 | Docs | Alinear README y `architecture.md` |
| 3 | Refactor | Extraer Services de `Aprueba*` / `Beneficiario` |
| 4 | Frontend | Congelar gulp; Inertia en módulos nuevos |
| 5 | Mail + Swagger | Unificar mail; instalar swagger o borrar del README |
| 6 | Tests | Smoke login Mercurio/Cajas + 1 Aprueba; DB test aislada |
| 7 | Git | Push de los +33 locales (espera ok de Ricardo) |
| 8 | ActiveRecord → Laravel | **En curso.** Services: `findFirst` / `inQueryAssoc` / `DbBase`. Omitir `whereRaw` y `new Model`. No borrar el adapter hasta vaciar consumidores |

---

## Fuera de alcance de este checklist

`dd()`/`dump()` ya se quitó (`e6870011`). Corte 8 Services sigue el inventario de abajo. CSRF/CORS y el resto de cortes 1–7 no se tocan hasta que Ricardo los elija. El push a origin espera su ok.
