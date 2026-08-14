# Puesta en marcha: Job de precompras abandonadas

Instrucciones operativas para activar el job que marca como **abandonadas (`AB`)**
las precompras pendientes (`PE`) antiguas.

> **Audiencia:** DevOps / mantenedores del entorno Mercurio.
> **Código:** [`MarcarPrecomprasAbandonadas`](../app/Jobs/MarcarPrecomprasAbandonadas.php),
> comando `precompras:marcar-abandonadas`, schedule en
> [`routes/console.php`](../routes/console.php).
> **Relacionado:** [epayco-modal-control.md](./epayco-modal-control.md),
> [pasarela-pago-epayco.md](./pasarela-pago-epayco.md).

---

## 1. Qué hace

Cada ejecución:

1. Busca filas en `precompras_servicios` con `estado = 'PE'`.
2. Filtra las que tienen `fecha_precompra` anterior a **N días** (default **7**).
3. Las actualiza a `estado = 'AB'` con:
   - `motivo_desestimacion` = `ABANDONO_INACTIVIDAD`
   - `detalle_desestimacion` = texto de inactividad
   - `fecha_desestimacion` = ahora

Así se limpian precompras huérfanas (usuario cerró el navegador sin pasar por
`onClose` de ePayco). Las marcadas por `onClose` ya quedan en `AB` al momento;
este job no las toca.

---

## 2. Prerrequisitos

1. Código desplegado con:
   - `app/Jobs/MarcarPrecomprasAbandonadas.php`
   - `app/Console/Commands/MarcarPrecomprasAbandonadasCommand.php`
   - entrada en `routes/console.php`
2. Base de datos con la tabla `precompras_servicios` y el estado `AB` en uso
   (no requiere migración extra para este job).
3. PHP / Laravel operativo en el servidor (`php artisan` funciona dentro del
   contenedor o host donde corre la app).

---

## 3. Verificar que el comando existe

Desde la raíz del proyecto Laravel:

```bash
php artisan precompras:marcar-abandonadas --help
```

Debe mostrar las opciones `--dias`, `--dry-run` y `--queue`.

Listar el schedule registrado:

```bash
php artisan schedule:list
```

Debe aparecer algo equivalente a:

```text
0 2 * * *  php artisan precompras:marcar-abandonadas --dias=7
```

(diario a las **02:00** hora del servidor).

---

## 4. Prueba manual (recomendada antes del cron)

### 4.1 Simulación (no escribe)

```bash
php artisan precompras:marcar-abandonadas --dias=7 --dry-run
```

Salida esperada: cantidad de **candidatas** a abandonar.

### 4.2 Ejecución real

```bash
php artisan precompras:marcar-abandonadas --dias=7
```

Salida esperada: cantidad de precompras **actualizadas** a `AB`.

### 4.3 Comprobar en BD

```sql
SELECT id, documento, estado, motivo_desestimacion, fecha_precompra, fecha_desestimacion
FROM precompras_servicios
WHERE motivo_desestimacion = 'ABANDONO_INACTIVIDAD'
ORDER BY fecha_desestimacion DESC
LIMIT 20;
```

### 4.4 Encolar en la cola (opcional)

Solo si el worker de colas está activo (`queue:work` / `composer dev`):

```bash
php artisan precompras:marcar-abandonadas --dias=7 --queue
```

El schedule diario **no** usa `--queue`: corre el job en sincronía dentro de
`schedule:run` (no depende de un worker).

---

## 5. Activar el scheduler en el servidor

Laravel **no** dispara el schedule solo. Debe existir un cron (o equivalente)
que ejecute **cada minuto**:

```cron
* * * * * cd /ruta/absoluta/al/laravel && php artisan schedule:run >> /dev/null 2>&1
```

### 5.1 Host (crontab del usuario de la app)

```bash
crontab -e
```

Agregar la línea anterior ajustando la ruta. Ejemplo:

```cron
* * * * * cd /home/admin/contenedores/desarrollo/mercurio && php artisan schedule:run >> /dev/null 2>&1
```

### 5.2 Docker / contenedor

Opciones habituales:

**A) Cron dentro del contenedor PHP** (si la imagen lo permite):

```cron
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

**B) Contenedor/sidecar dedicado** que solo corre:

```bash
php artisan schedule:work
```

(`schedule:work` deja un proceso en foreground que invoca el scheduler cada
minuto; útil en Compose como servicio `scheduler`.)

**C) Cron en el host** que entra al contenedor:

```cron
* * * * * docker exec <nombre_contenedor_php> php artisan schedule:run >> /dev/null 2>&1
```

Elige **una** de estas formas; no combines varias o el comando podría
ejecutarse más de una vez por minuto (el schedule interno igual solo dispara
la tarea a las 02:00, pero conviene un solo origen).

---

## 6. Zona horaria

La hora `02:00` usa `config('app.timezone')` (variable `APP_TIMEZONE` en `.env`). Confirma la zona antes de poner en marcha:

```bash
php artisan tinker --execute="echo config('app.timezone');"
```

Si necesitas otra hora, cambia en `routes/console.php`:

```php
Schedule::command('precompras:marcar-abandonadas --dias=7')
    ->dailyAt('02:00')
    ->withoutOverlapping();
```

Si necesitas otro TTL, cambia `--dias=7` en esa misma línea (y documenta el
cambio aquí).

---

## 7. Checklist de puesta en marcha

- [ ] Código desplegado en el entorno (dev / prod).
- [ ] `php artisan precompras:marcar-abandonadas --help` OK.
- [ ] `php artisan schedule:list` muestra la tarea diaria.
- [ ] `--dry-run` ejecutado y revisado el número de candidatas.
- [ ] (Opcional) Una ejecución real de prueba con `--dias` alto o en staging.
- [ ] Cron / `schedule:work` / `docker exec` configurado **una sola vez**.
- [ ] Zona horaria de la app verificada.
- [ ] Al día siguiente (o forzando la hora), confirmar en logs/BD que corrió.

---

## 8. Logs

El job escribe en el log de Laravel (`storage/logs/laravel.log` o el canal
configurado):

- `MarcarPrecomprasAbandonadas dry-run` — simulación
- `MarcarPrecomprasAbandonadas ejecutado` — con `actualizadas` y `limite`

```bash
grep MarcarPrecomprasAbandonadas storage/logs/laravel.log | tail -20
```

---

## 9. Troubleshooting

| Síntoma | Qué revisar |
| ------- | ----------- |
| El comando no aparece | Despliegue incompleto; `composer dump-autoload`; caché de config (`php artisan optimize:clear`). |
| `schedule:list` no muestra la tarea | Falta el bloque en `routes/console.php` o archivo no desplegado. |
| Nunca se ejecuta a las 02:00 | No hay cron / `schedule:work`; ruta del `cd` incorrecta; contenedor distinto. |
| Siempre 0 actualizadas | No hay `PE` con más de N días; TTL demasiado alto; `fecha_precompra` reciente. |
| Error de `cache_locks` / overlapping | Driver de cache inaccesible; el comando usa `withoutOverlapping()`. Revisar `CACHE_STORE` y conectividad a la DB/Redis del cache. |
| Se marca una PE que el usuario aún quería pagar | Bajar o subir `--dias` según política de negocio; el `onClose` ya marca `AB` inmediato al cerrar la modal. |

---

## 10. Resumen rápido

```bash
# 1. Probar
php artisan precompras:marcar-abandonadas --dias=7 --dry-run
php artisan precompras:marcar-abandonadas --dias=7

# 2. Confirmar schedule
php artisan schedule:list

# 3. Activar cron (una vez en el servidor)
# * * * * * cd /ruta/al/laravel && php artisan schedule:run >> /dev/null 2>&1
```
