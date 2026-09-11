# Checklist — deuda post-migración Mercurio/Cajas

Fuente: diagnóstico de operador-edwin (2026-09-11). Solo inventario; **no implementar** hasta definir alcance.

| Dato | Valor |
| --- | --- |
| Repo local | `/home/edwin-tics/proyectos/comfaca-enlinea/laravel` |
| GitHub | https://github.com/elegroag/laravel-sail-api.git |
| Rama / HEAD | `release/v01` @ `8dc181f4` |
| vs origin | +25 commits (ePayco / ecommerce / banners / certificados); **no cubren** esta deuda |
| Stack | Laravel 12; Mercurio (afiliados, JWT+cookie) y Cajas (backoffice, cookie) |
| UI | Blade + gulp/AMD (119 views Mercurio, 180 Cajas, `public/src`); Inertia+React parcial (~134 TSX, auth/landing/admin) |
| Tests | MySQL `mercurio_dev` @ `172.168.0.15` |

Migró bien (no es deuda): rutas/middlewares `mercurio.auth` / `cajas.auth`, Services, docs ePayco, `legacy/` acotado, gitignore `vendor`/`node_modules`, sin Phalcon. `.env.example` está en local (`1a340916`), no en origin.

---

## Crítica

- [ ] **CSRF desactivado por prefijo.** `bootstrap/app.php` (aprox. L33–38) exceptúa `web/*`, `mercurio/*`, `cajas/*`, `api/*`. Restaurar CSRF; dejar except solo webhook ePayco (y equivalentes que lo requieran).
- [ ] **CORS abierto.** `config/cors.php`: `allowed_origins` `*` y `supports_credentials` true. Cerrar orígenes y no combinar `*` con credentials.
- [x] **`dd()` / `dump()` en código de producto.** Quitados (2026-09-11), sin commit:
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
- [ ] Push de los +25 commits locales (ePayco/ecommerce/banners/certificados) cuando Ricardo lo pida. **No sustituye esta deuda.**

---

## ActiveRecord Kumbia → Laravel

Contexto (2026-09-11, HEAD `8dc181f4`): adaptación temporal en `app/Models/Adapter` y `app/Http/Controllers/Adapter`. **Retomar** hacia recursos Laravel: Eloquent (`where`, relaciones, `$fillable`), `Illuminate\Support\Facades\Cache`, `Illuminate\Http\Request` (y FormRequest). Código nuevo **no** debe extender `ModelBase` ni `ApplicationController`.

Estado medido:

| Pieza | Cantidad |
| --- | --- |
| Models `extends ModelBase` | 95 |
| Models Eloquent puro (`extends Model`, sin ModelBase) | 21 |
| `whereRaw` / `DB::select` en `app/` | 239 |
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

**Services — empezar por aquí** (67 de ~200 PHP en `app/Services`; cada uno con `findFirst` / `inQueryAssoc` / `rawConnect` / `whereRaw` / `new MercurioNN`). El resto de services no toca ActiveRecord en este inventario.

### Services/Aprueba (5 de 14, cerrado)

Patrón ya migrado (copiar, no reinventar): `ApruebaTrabajador` / `ApruebaBeneficiario` — `Mercurio01::first()`, `Model::where('id', $id)->first()`, `Mercurio07::where(...)->where(...)->first()`.

Alcance recomendado: **solo queries**. No tocar `procesar()` de negocio, ni `ApplicationController`, ni `ModelBase`. `ApruebaSolicitud`, `ValidacionControlChecklist`, Beneficiario/Conyuge/Facultativo/Independiente/Pensionado/Trabajador/DatosTrabajador ya no usan `findFirst`.

- [x] `ApruebaCertificado.php` — Eloquent (2026-09-11, sin commit): `Mercurio01::first()`, `Mercurio45::where('id', …)->first()`, `Mercurio07` where encadenados. `procesar()` intacto.
- [x] `ApruebaMadreComuni.php` — Eloquent (2026-09-11, sin commit): `Mercurio01::first()`, `Mercurio39::where('id')`, `Mercurio07` where encadenados (usuario E y solicitante). `procesar()` intacto.
- [x] `ApruebaServicioDomestico.php` — Eloquent (2026-09-11, sin commit): mismo patrón que MadreComuni con `Mercurio40`. `procesar()` intacto.
- [x] `ApruebaDatosEmpresa.php` — Eloquent (2026-09-11, sin commit): `where` bindings en Mercurio47/33/30/07; `procesar()` intacto.
- [x] `ApruebaEmpresa.php` — Eloquent (2026-09-11, sin commit): `findSolicitante` con where encadenados. `procesar()` intacto. **Services/Aprueba cerrado.**

### Services/Entidades (14 + concern)

- [ ] `ActualizaEmpresaService.php` — whereRaw, DbBase, inQueryAssoc, rawConnect
- [ ] `BeneficiarioService.php` — whereRaw, DbBase, inQueryAssoc, rawConnect
- [ ] `CertificadoService.php` — whereRaw, DbBase, inQueryAssoc, rawConnect
- [ ] `ConyugeService.php` — whereRaw, DbBase, rawConnect, DB::select
- [ ] `DatosTrabajadorService.php` — whereRaw, DbBase, inQueryAssoc, rawConnect
- [ ] `EmpresaService.php` — whereRaw, DbBase, rawConnect, DB::select
- [ ] `FacultativoService.php` — whereRaw, DbBase, inQueryAssoc, rawConnect
- [ ] `IndependienteService.php` — whereRaw, DbBase, inQueryAssoc, rawConnect
- [ ] `MadresComuniService.php` — whereRaw×4
- [ ] `ParticularService.php` — DbBase, rawConnect
- [ ] `PensionadoService.php` — whereRaw, DbBase, inQueryAssoc, rawConnect
- [ ] `RetiroService.php` — whereRaw, DbBase, inQueryAssoc, rawConnect
- [ ] `ServicioDomesticoService.php` — whereRaw×4
- [ ] `TrabajadorService.php` — whereRaw, DbBase, rawConnect, DB::select
- [ ] `Entidades/Concerns/PaginatesSolicitudQueries.php` — DbBase, inQueryAssoc, DB::select (compartido)

### Services/CajaServices (16)

- [ ] `BeneficiarioServices.php` — whereRaw
- [ ] `CertificadosServices.php` — whereRaw, findFirst
- [ ] `ConyugeServices.php` — whereRaw
- [ ] `EmpresaServices.php` — whereRaw
- [ ] `FacultativoServices.php` — whereRaw
- [ ] `IndependienteServices.php` — whereRaw
- [ ] `MadresComuniServices.php` — whereRaw
- [ ] `Mercurio13Services.php` — new Mercurio
- [ ] `Mercurio14Services.php` — new Mercurio
- [ ] `NotificacionService.php` — inQueryAssoc, DbBase, rawConnect
- [ ] `PensionadoServices.php` — whereRaw
- [ ] `ServicioDomesticoServices.php` — whereRaw
- [ ] `TrabajadorServices.php` — whereRaw
- [ ] `UpDatosEmpresaServices.php` — whereRaw
- [ ] `UpDatosTrabajadorService.php` — whereRaw
- [ ] `UsuarioServices.php` — whereRaw

### Services/Cajas (3)

- [ ] `Mercurio01Service.php` — whereRaw
- [ ] `Mercurio02Service.php` — whereRaw
- [ ] `Mercurio11Service.php` — whereRaw

### Services/Formularios (4)

- [ ] `Afiliacion/FormularioBeneficiario.php` — findFirst×4
- [ ] `Afiliacion/FormularioConyuge.php` — findFirst×3
- [ ] `Declaration/JuramentadaBeneficiario.php` — findFirst×6
- [ ] `Declaration/JuramentadaConyuge.php` — findFirst×2

### Services/FormulariosAdjuntos (3)

- [ ] `BeneficiarioAdjuntoService.php` — new MercurioNN
- [ ] `ConyugeAdjuntoService.php` — new MercurioNN
- [ ] `TrabajadorAdjuntoService.php` — new MercurioNN

### Services/PreparaFormularios (2)

- [ ] `GestionFirmas.php` — findFirst, new MercurioNN
- [ ] `TrabajadorFormulario.php` — findFirst, new MercurioNN

### Services/Autentications (2)

- [ ] `AutenticaGeneral.php` — findFirst
- [ ] `AutenticaService.php` — new MercurioNN

### Services/Signup (5)

- [ ] `SignupDomestico.php` — findFirst
- [ ] `SignupEmpresas.php` — new MercurioNN
- [ ] `SignupFacultativos.php` — new MercurioNN
- [ ] `SignupIndependientes.php` — new MercurioNN
- [ ] `SignupPensionados.php` — new MercurioNN

### Services/Utils (6)

- [ ] `ChangeCuentaService.php` — findFirst
- [ ] `CrearUsuario.php` — new MercurioNN
- [ ] `GeneralService.php` — findFirst, whereRaw (archivo grande)
- [ ] `GuardarArchivoService.php` — DbBase
- [ ] `RegistroSeguimiento.php` — new MercurioNN
- [ ] `SolicitaClaveService.php` — findFirst×9

### Services/Menu (2)

- [ ] `Menu.php` — DbBase, inQueryAssoc, rawConnect
- [ ] `MenuCajas.php` — DbBase, inQueryAssoc, rawConnect

### Services/SatApi (2)

- [ ] `SatConsultaServices.php` — findFirst×5 (archivo grande)
- [ ] `SatServices.php` — findFirst

### Services/Certificados (1)

- [ ] `EnviarCertificadoEmailService.php` — findFirst

### Services/Reportes (1)

- [ ] `ReporteSolicitudes.php` — whereRaw

Destino por pieza: queries → Eloquent; tablas lookup → Cache; input HTTP → `Request` / FormRequest. No mezclar `findFirst("col='x'")` nuevo. Orden sugerido para arrancar: `Entidades` (núcleo de solicitudes) o `CajaServices` (mismo dominio, más chicos).

---

## Cortes sugeridos (elegir alcance)

Orden propuesto por operador; no iniciado.

| Corte | Alcance | Ítems |
| --- | --- | --- |
| 1 | Seguridad mínima | CSRF por prefijo (salvo webhook ePayco), CORS cerrado, quitar `dd()`/`dump()` |
| 2 | Docs | Alinear README y `architecture.md` |
| 3 | Refactor | Extraer Services de `Aprueba*` / `Beneficiario` |
| 4 | Frontend | Congelar gulp; Inertia en módulos nuevos |
| 5 | Mail + Swagger | Unificar mail; instalar swagger o borrar del README |
| 6 | Tests | Smoke login Mercurio/Cajas + 1 Aprueba; DB test aislada |
| 7 | Git | Push de los +25 (no paga esta deuda) |
| 8 | ActiveRecord → Laravel | Empezar por **Services** (67 listados). Eloquent + Cache + Request. No borrar el adapter hasta vaciar consumidores |

---

## Fuera de alcance de este checklist

`dd()`/`dump()` ya se quitó (pendiente commit). El resto no se implementa hasta que Ricardo elija corte. Los +25 locales de ecommerce/ePayco son otra línea de trabajo.
