# Migración de Sesiones: Cookie a Database

## Resumen
Documento que detalla los cambios necesarios para migrar el sistema de sesiones de Laravel del driver `cookie` al driver `database` para resolver problemas de autenticación intermitentes en Mercurio y Cajas.

## Motivación
El driver actual `cookie` presenta problemas de estabilidad:
- Sesiones almacenadas en el cliente (cookies)
- Límite de tamaño (~4KB)
- Bloqueo por configuraciones del navegador
- Pérdida al limpiar cookies
- Inestabilidad con SameSite='none' + Secure=true

## Archivos de Configuración Afectados

### 1. `.env`
**Variable a modificar:**
```bash
SESSION_DRIVER=database
```

**Variables opcionales a considerar:**
```bash
SESSION_CONNECTION=mysql           # Conexión de BD para sesiones (usa default si está vacío)
SESSION_TABLE=sessions             # Nombre de la tabla (usa default si está vacío)
SESSION_LIFETIME=4320             # Lifetime en minutos (72 horas actual)
SESSION_ENCRYPT=true              # Mantener en true (encriptación de payload)
```

### 2. `config/session.php`
**No requiere cambios directos** - El archivo ya está configurado correctamente para usar el driver de database:
- Linea 21: `'driver' => env('SESSION_DRIVER', 'cookie'),`
- Linea 89: `'table' => env('SESSION_TABLE', 'sessions'),`
- Linea 76: `'connection' => env('SESSION_CONNECTION'),`

## Cambios Requeridos

### Paso 1: Crear Migración de Tabla de Sesiones
**Comando:**
```bash
./vendor/bin/sail artisan make:migration create_sessions_table
```

**Contenido esperado de la migración:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
```

### Paso 2: Ejecutar Migración
**Comando:**
```bash
./vendor/bin/sail artisan migrate
```

### Paso 3: Actualizar Variable de Entorno
**Modificar `.env`:**
```bash
SESSION_DRIVER=database
```

### Paso 4: Reiniciar Servicios (Opcional pero recomendado)
**Comando:**
```bash
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
```

## Servicios Afectados

### Laravel Sail (Docker)
**Contenedores afectados:**
- `laravel.test` - Contenedor principal de PHP
- `mysql` - Base de datos (donde se almacenarán las sesiones)

**Impacto:**
- No requiere cambios en `compose.yml`
- No requiere reinicio de contenedores
- La tabla de sesiones se crea en la base de datos existente

### Middleware de Autenticación
**Archivos NO afectados (código transparente):**
- `app/Http/Middleware/EnsureCookieAuthenticated.php` (Mercurio)
- `app/Http/Middleware/CajasCookieAuthenticated.php` (Cajas)
- `app/Library/Auth/SessionCookies.php`

**Razón:** Estos archivos usan `session()->has('user')` y `session()->put()` que funcionan idénticamente con cualquier driver de sesión.

### Rutas
**Archivos NO afectados:**
- `routes/mercurio/*.php` (todas usan middleware `mercurio.auth`)
- `routes/cajas/*.php` (todas usan middleware `cajas.auth`)

## Comandos de Mantenimiento

### Limpieza de Sesiones Expiradas
Laravel limpia automáticamente las sesiones expiradas usando el sistema de "lottery" (configurado en `config/session.php` línea 117):
```php
'lottery' => [2, 100],  // 2% de probabilidad en cada request
```

**Limpieza manual (si es necesario):**
```bash
./vendor/bin/sail artisan session:table
./vendor/bin/sail artisan migrate
```

### Verificación de Sesiones Activas
**Consulta SQL:**
```sql
SELECT * FROM sessions WHERE last_activity > UNIX_TIMESTAMP(NOW() - INTERVAL 72 HOUR);
```

## Consideraciones de Seguridad

### Encriptación de Sesión
**Mantener activo:**
```bash
SESSION_ENCRYPT=true
```
Esto encripta el payload de la sesión antes de almacenarlo en la base de datos.

### Cookies de Sesión
**Configuración actual (mantener):**
```php
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => env('SESSION_HTTP_ONLY', true),
'same_site' => env('SESSION_SAME_SITE', 'none'),
```

**Nota:** Con driver `database`, la cookie solo contiene el ID de sesión, no el payload completo, lo que reduce el riesgo de exposición de datos.

## Ventajas del Cambio

1. **Estabilidad:** Sesiones almacenadas en servidor, no dependen del cliente
2. **Persistencia:** No afectadas por limpieza de cookies del navegador
3. **Capacidad:** Sin límite de tamaño (~4KB en cookies)
4. **Escalabilidad:** Sesiones compartibles entre múltiples servidores
5. **Seguridad:** Payload en servidor, cookie solo contiene ID
6. **Debugging:** Sesiones visibles y consultables en base de datos

## Desventajas

1. **Latencia:** Consulta adicional a base de datos por request (mínima, ~1-2ms)
2. **Dependencia:** Requiere disponibilidad de base de datos
3. **Mantenimiento:** Tabla de sesiones requiere limpieza (automática con lottery)

## Rollback (Si es necesario)

**Revertir a cookie driver:**
```bash
# 1. Modificar .env
SESSION_DRIVER=cookie

# 2. Limpiar caché
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear

# 3. Opcional: Eliminar tabla de sesiones
./vendor/bin/sail artisan migrate:rollback
```

## Estrategia de Despliegue en Producción

### Pre-requisitos
1. **Backup de Base de Datos**
```bash
# Backup completo de la base de datos antes de cualquier cambio
./vendor/bin/sail artisan db:backup
# O usando mysqldump directamente
mysqldump -u usuario -p nombre_base_datos > backup_pre_migracion_$(date +%Y%m%d_%H%M%S).sql
```

2. **Pruebas en Staging**
- Ejecutar todos los pasos en ambiente de staging primero
- Validar que no hay errores ni impacto en funcionalidad
- Monitorear por al menos 24 horas en staging

3. **Horario de Mantenimiento**
- Programar el cambio en horario de baja actividad
- Notificar a usuarios si es necesario (maintenance window)
- Preparar comunicación de rollback si hay problemas

### Pasos para Producción

#### Fase 1: Preparación (Sin impacto en usuarios)
```bash
# 1. Crear migración de tabla de sesiones
./vendor/bin/sail artisan make:migration create_sessions_table

# 2. Revisar el archivo de migración generado
# Debe estar en: database/migrations/YYYY_MM_DD_HHMMSS_create_sessions_table.php

# 3. Ejecutar migración en producción (esto crea la tabla vacía)
./vendor/bin/sail artisan migrate --force
# Nota: --force evita la confirmación interactiva en producción
```

#### Fase 2: Cambio de Configuración (Impacto inmediato)
```bash
# 4. Modificar .env
SESSION_DRIVER=database

# 5. Limpiar caché de configuración
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear

# 6. Opcional: Reiniciar workers si hay queues
./vendor/bin/sail artisan queue:restart
```

#### Fase 3: Verificación Inmediata
```bash
# 7. Verificar que la tabla sessions existe y tiene estructura correcta
./vendor/bin/sail artisan db:table sessions

# 8. Verificar que se pueden crear sesiones
# Hacer login en Mercurio y Cajas
# Consultar la tabla sessions:
SELECT COUNT(*) FROM sessions;
```

### Impacto en Usuarios Activos

#### ¿Qué pasa con las sesiones existentes?
- **Sesiones cookie activas:** Los usuarios con sesiones cookie existentes serán deslogueados automáticamente cuando cambie el driver
- **Requerimiento de re-login:** Todos los usuarios necesitarán hacer login nuevamente
- **Sin pérdida de datos:** No se pierden datos de la base de datos, solo las sesiones activas

#### Estrategia para minimizar impacto
1. **Avisar con anticipación:** Notificar a usuarios sobre mantenimiento programado
2. **Ventana de mantenimiento:** Ejecutar cambio en horario de baja actividad
3. **Mensaje de mantenimiento:** Mostrar mensaje durante el cambio si es posible

### Monitoreo Post-Despliegue

#### Métricas a Monitorear
1. **Tasa de errores de autenticación**
```sql
-- Monitorear creación de sesiones
SELECT COUNT(*) as total_sesiones,
       MIN(last_activity) as primera_sesion,
       MAX(last_activity) as ultima_sesion
FROM sessions
WHERE last_activity > UNIX_TIMESTAMP(NOW() - INTERVAL 1 HOUR);
```

2. **Tamaño de tabla de sesiones**
```sql
-- Verificar crecimiento de la tabla
SELECT 
    COUNT(*) as total_registros,
    ROUND(SUM(LENGTH(payload))/1024/1024, 2) as tamaño_mb
FROM sessions;
```

3. **Sesiones por usuario (si user_id está poblado)**
```sql
-- Detectar sesiones múltiples por usuario
SELECT user_id, COUNT(*) as sesiones_activas
FROM sessions
WHERE user_id IS NOT NULL
GROUP BY user_id
HAVING COUNT(*) > 1;
```

#### Alertas Recomendadas
- Error rate > 1% en endpoints de autenticación
- Tiempo de respuesta > 500ms en endpoints que usan sesión
- Tamaño de tabla sessions > 1GB
- Fallos en conexión a base de datos

### Plan de Rollback Rápido

#### Si hay problemas críticos post-deploy
```bash
# 1. Revertir .env inmediatamente
SESSION_DRIVER=cookie

# 2. Limpiar caché
php artisan cache:clear
php artisan config:clear

# 3. Reiniciar servicios si es necesario
./vendor/bin/sail down --message="Mantenimiento temporal"
./vendor/bin/sail up
```

#### Tiempo estimado de rollback: < 5 minutos

### Consideraciones Adicionales para Producción

#### 1. Índices de la tabla sessions
La migración estándar de Laravel crea índices adecuados:
- `id` (PRIMARY)
- `user_id` (INDEX)
- `last_activity` (INDEX)

#### 2. Limpieza automática
El sistema de "lottery" [2, 100] limpia sesiones expiradas en 2% de los requests. Para producción con alto tráfico, considerar:
```php
// En config/session.php
'lottery' => [10, 100],  // Aumentar a 10% para limpieza más frecuente
```

#### 3. Job programado para limpieza (opcional)
```bash
# Crear job programado para limpieza nocturna
./vendor/bin/sail artisan make:command CleanExpiredSessions
```

Contenido del comando:
```php
public function handle()
{
    DB::table('sessions')
        ->where('last_activity', '<', now()->subMinutes(config('session.lifetime'))->timestamp)
        ->delete();
}
```

#### 4. Partitioning de tabla (para alto volumen)
Si el sistema tiene miles de usuarios concurrentes, considerar partitioning por fecha:
```sql
ALTER TABLE sessions PARTITION BY RANGE (TO_DAYS(FROM_UNIXTIME(last_activity))) (
    PARTITION p_2026_04 VALUES LESS THAN (TO_DAYS('2026-05-01')),
    PARTITION p_2026_05 VALUES LESS THAN (TO_DAYS('2026-06-01')),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

### Checklist Pre-Despliegue

- [ ] Backup de base de datos completado
- [ ] Migración probada en staging
- [ ] Migración creada y revisada
- [ ] Plan de comunicación a usuarios preparado
- [ ] Horario de mantenimiento definido
- [ ] Plan de rollback documentado
- [ ] Monitoreo configurado
- [ ] Equipo de soporte notificado

### Checklist Post-Despliegue

- [ ] Migración ejecutada exitosamente
- [ ] Configuración .env actualizada
- [ ] Caché limpiada
- [ ] Login Mercurio probado
- [ ] Login Cajas probado
- [ ] Sesiones verificadas en BD
- [ ] Monitoreo activo por 24 horas
- [ ] Documentación actualizada

## Pruebas Recomendadas (Staging)

### 1. Test de Login Mercurio
```bash
# Acceder a /web/login
# Iniciar sesión
# Verificar que se mantiene autenticado al navegar
```

### 2. Test de Login Cajas
```bash
# Acceder a /cajas/login
# Iniciar sesión
# Verificar que se mantiene autenticado al navegar
```

### 3. Test de Sesión Expirada
```bash
# Esperar 72 horas (SESSION_LIFETIME)
# Verificar que la sesión expira correctamente
```

### 4. Test de Múltiples Pestañas
```bash
# Abrir múltiples pestañas con la misma sesión
# Verificar que la sesión se mantiene sincronizada
```

## Referencias

- [Laravel Session Documentation](https://laravel.com/docs/12.x/session)
- [Configuración actual: config/session.php](../../config/session.php)
- [Configuración actual: config/auth.php](../../config/auth.php)
- [Middleware Mercurio: app/Http/Middleware/EnsureCookieAuthenticated.php](../../app/Http/Middleware/EnsureCookieAuthenticated.php)
- [Middleware Cajas: app/Http/Middleware/CajasCookieAuthenticated.php](../../app/Http/Middleware/CajasCookieAuthenticated.php)

## Fecha de Documentación
22 de Abril de 2026
