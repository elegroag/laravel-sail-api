# Checklist — Models (deuda Kumbia / ActiveRecord)

Repo: `comfaca-enlinea/laravel`, rama `release/v01`. Fuente: hallazgo de operador-edwin (2026-09-11), **números recontados** en `app/Models`. **No se implementa** hasta que Ricardo elija alcance.

Eloquent por fuera, ActiveRecord Kumbia por dentro. No mezclar con Corte 8/9 (consumidores). No borrar `ModelBase` / `DbBase` / `ActiveRecordBase` hasta vaciar consumidores.

## Resumen

| Dato | Valor |
| --- | --- |
| PHP en `app/Models` | 132 (5 Adapter + 127 de dominio) |
| Extienden `ModelBase` | 95 |
| Eloquent limpio (`extends Model`, no adapter) | 21 |
| `MercurioNN` | 66 |
| `Xml*` | 12 |
| `Gener*` | 7 |
| `Sat*` / `Mercusat*` | 4 |
| `get*`/`set*` | 2037 en 88 models |

Nombres de tabla SISU (`mercurio31`, `gener02`), no de dominio. Renombrar duele: menú, ACL y SQL crudo cuelgan de esos nombres.

## Adapter (crítica)

- [ ] `ModelBase.php` — `findFirst`, `getFind`, `findBySql`, `findAllBySql`, `updateAll`, `deleteAll`, `maximum`, `minimum`, `getCount`, `getSource`. `findFirst` arma `whereRaw` sobre strings; si `conditions` es array, `implode(',', …)` no es AND válido. Cada `__construct` llama `DbBase::rawConnect()`.
- [ ] `ActiveRecordBase.php` — `insert` / `update` / `delete` / `fetchOne` / `inQueryAssoc` / `find` concatenan SQL **sin bindings**.
- [ ] `DbBase.php` — `rawConnect()` (conexión global estilo `$db`).
- [ ] `get_params_destructures()` (helpers) — firma Kumbia `find('conditions: …')`. Aún la usan `ModelBase`, `Tag`, `SignupDomestico`, `SenderEmail`, `CrearUsuario`.
- [x] `HasCustomUuid.php` — **omitido**: post-migración, no es Kumbia.
- [ ] `ValidateWithRules.php` — `rulesValiation` (typo) + Validator a mano; no FormRequest. 8 models lo usan.

## Eloquent limpio (omitir)

Nacieron Eloquent; no son deuda Kumbia:

- [x] `EpaycoCuenta.php`, `EpaycoTransaccion.php`, `PrecompraServicio.php`
- [x] `Task.php`, `Profile.php`, `RefreshToken.php`, `ApiEndpoint.php`
- [x] `ComponenteDinamico.php`, `FormularioDinamico.php`
- [x] `Mercurio62.php`, `Mercurio64.php`, `Mercurio66.php`, `Mercurio68.php`–`Mercurio71.php`
- [x] `NucleoFamiliar.php`, `Radicado.php`, `Trabajador.php`
- [x] `Xml4b091.php`, `Xml4b094.php` (el resto de `Xml*` sigue en `ModelBase`)

## ModelBase — gordos (get/set)

No reescribir getters en este inventario. Solo anotar. Código **nuevo** no agrega `getNit`/`setNit`.

| Model | LOC | get/set |
| --- | --- | --- |
| `Mercurio31` | 1334 | 141 |
| `Mercurio38` | 1290 | 122 |
| `Mercurio36` | 1208 | 117 |
| `Mercurio41` | 804 | 115 |
| `Mercurio30` | 769 | 114 |
| `Mercurio34` | 825 | 107 |
| `Mercurio32` | 1042 | 100 |
| `Mercurio39` | 884 | 83 |
| `Mercurio40` | 874 | 81 |

El resto de `MercurioNN` / `Gener*` / `Xml*` / `Sat*` sobre `ModelBase` queda en el mismo saco (95 archivos). No listar uno a uno hasta un corte de rename o de quitar get/set.

## Modelo mental (alta)

- `timestamps = false` + get/set vs `casts` / `$attributes`
- Validación en el model (`rulesValiation`), no FormRequest
- ACL y SQL crudo dependen del string `mercurioNN`

## Cortes sugeridos (elegir alcance, no arrancar)

1. **Bindings o matar writes de `ActiveRecordBase`** — `insert`/`update`/`delete`/`fetchOne`/`inQueryAssoc` sin bindings. Seguridad. No borrar la clase hasta vaciar callers.
2. **Congelar `ModelBase`** — código nuevo = Eloquent `where`/`first`. No más `findFirst("col='x'")` nuevo. Bajo costo, evita más deuda.
3. **No más get/set en models nuevos** — los 95 viejos se quedan; no crecer la lista de 2037.
4. **Rename `MercurioNN`** — solo donde no rompa menú/`codapl`/SQL interpolado. Alto, no primero.
5. **`get_params_destructures`** — dejar de usarlo fuera del adapter; `Tag`/Signup/Utils aún lo llaman.
6. **`ValidateWithRules` / typo `rulesValiation`** — no es Corte 8/9; esperar FormRequest.

## Fuera

Corte 8 Services y Corte 9 Controllers ya no deben llamar `findFirst`/`inQueryAssoc`/`DbBase` (cerrados). Este checklist es la **capa model/adapter**. Middleware es otro archivo (`deuda-middleware-checklist.md`).

