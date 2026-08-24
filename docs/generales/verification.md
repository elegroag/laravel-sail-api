# Verificación del Código

## Commands de Verificación Rápida

### Backend (PHP)

```bash
# Ver que Laravel funciona y la versión
php artisan --version

# Listar rutas registradas
php artisan route:list

# Ver estado de la DB
php artisan db:show

# Ver configuración cargada
php artisan config:show app

# Ver eventos registrados
php artisan event:list
```

### Frontend (Node/pnpm)

```bash
# Ver que las dependencias están instaladas
pnpm list --depth 0

# TypeScript check
pnpm run types

# ESLint check
pnpm run lint

# Prettier check
pnpm run format -- --check
```

### Build

```bash
# Build SPA
pnpm run build

# Build con SSR
pnpm run build:ssr
```

---

## Tests

### Requisitos Previos

- MySQL `mercurio_dev` corriendo en `172.168.0.15`
- Tabla `sessions` creada (migración `2026_04_22_222658_create_sessions_table.php`)
- Base de datos seeded: `php artisan migrate:fresh --seed`

> ⚠️ Los tests **NO** funcionan con SQLite en memoria. Requieren la conexión MySQL real.

### Ejecutar Tests

```bash
# Todos los tests
php artisan test

# Tests unitarios
php artisan test --testsuite=Unit

# Tests feature
php artisan test --testsuite=Feature

# Un test específico
php artisan test --filter=AuthJwtTest

# Con coverage (requiere xdebug/pcov)
php artisan test --coverage
```

### Tests Existentes

| Suite | Archivos | Qué prueban |
|-------|----------|-------------|
| Unit | `AuthJwtTest`, `ModelBaseTest`, `ActiveRecordBaseTest`, `ProcesadorComandosTest`, `ReportGeneratorTest`, `SignupServiceTest` | Lógica pura, modelos, JWT, comandos |
| Unit/Mercurio | Subcarpetas con tests de modelos | Modelos MercurioXX |
| Feature | `DashboardTest`, `DocumentGenerationManagerConyugeTest` | HTTP endpoints |
| Feature/Auth | Tests de autenticación | Login, logout, JWT |
| Feature/Mercurio | Tests de API Mercurio | Endpoints API |
| Feature/Services | Tests de servicios | Servicios específicos |
| Feature/Settings | Tests de configuraciones | Settings |

### Agregar Tests

```bash
# Crear test unitario
php artisan make:test NombreTest --unit

# Crear test feature
php artisan make:test NombreTest

# Crear test con PHPUnit directamente
./vendor/bin/phpunit --filter NombreTest
```

---

## Verificación de Funcionalidad Específica

### Autenticación JWT (Mercurio)

```bash
# Generar secret JWT si no existe
php artisan jwt:secret

# Verificar que tymon/jwt-auth está publishado
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

### PDF (TCPDF)

```bash
# Verificar que tcpdf_temp existe y tiene permisos
ls -la /tmp/tcpdf_temp/

# Si no existe:
mkdir -p /tmp/tcpdf_temp && chmod 777 /tmp/tcpdf_temp
```

### Excel Export

```php
// Verificar que PhpSpreadsheet está funcionando
use PhpOffice\PhpSpreadsheet\Spreadsheet;
$spreadsheet = new Spreadsheet();
```

### Queue/Jobs

```bash
# Ver jobs pendientes
php artisan queue:failed

# Trabajar un job manualmente
php artisan tinker
>>> dispatch(new \App\Jobs\SomeJob($data));
```

---

## Verificación de DB

### Estado de Migraciones

```bash
# Ver qué migraciones faltan correr
php artisan migrate:status
```

### Datos de Prueba

```bash
# Correr seeders
php artisan db:seed

# Seed específico
php artisan db:seed --class=DatabaseSeeder

# Fresh migrate + seed
php artisan migrate:fresh --seed
```

### Consultas Útiles

```sql
-- Ver tablas del sistema
SHOW TABLES LIKE 'mercurio%';

-- Ver roles/permisos
SELECT * FROM menu_tipos;
SELECT * FROM menu_items LIMIT 10;

-- Ver usuarios de prueba
SELECT id, email, name FROM users LIMIT 5;
```

---

## Verificación de Frontend

### Vite Dev Server

```bash
# Iniciar dev server
pnpm run dev

# Ver que responde en http://localhost:5173
curl -s -o /dev/null -w "%{http_code}" http://localhost:5173
```

### SSR

```bash
# Build SSR
pnpm run build:ssr

# Iniciar SSR server
php artisan inertia:start-ssr

# Verificar que responde
curl -s http://localhost:13714
```

---

## Verificación en Producción

### Docker

```bash
# Ver contenedores corriendo
docker ps

# Ver logs de un contenedor
docker logs -f <container_name>

# Ejecutar comando dentro del contenedor
docker exec -it <container_name> php artisan route:list
```

### Deploy

```bash
# Rsync a servidor
rsync -avz --dry-run /home/edwin-tics/proyectos/comfaca-enlinea/laravel/ admin@172.168.0.15:/home/admin/contenedores/desarrollo/mercurio

# Verificar que el rsync incluye los archivos correctos
# (remover --dry-run para ejecutar)
```

### Health Check

```bash
# En el servidor de producción
curl -s -o /dev/null -w "%{http_code}" http://localhost/api/health

# Ver logs
tail -f /home/admin/contenedores/desarrollo/mercurio/storage/logs/laravel.log
```

---

## Linting y Formatting

```bash
# PHP formatting
./vendor/bin/pint

# JS/TS formatting
pnpm run format

# Lint
pnpm run lint

# Type check
pnpm run types
```

---

## Checklist Pre-Deploy

- [ ] `php artisan test` pasa (o se conocen los fallos)
- [ ] `pnpm run build` completa sin errores
- [ ] Migraciones corriendo: `php artisan migrate:status`
- [ ] `.env` configurado correctamente (SESSION_DRIVER, DB, etc.)
- [ ] `/tmp/tcpdf_temp` existe con permisos 777
- [ ] `php artisan jwt:secret` ejecutado
- [ ] Secrets de producción configurados (no usar `.env` con secrets de dev)
- [ ] `php artisan config:cache` y `route:cache` ejecutados
- [ ] Base de datos seedada con datos de producción si aplica
