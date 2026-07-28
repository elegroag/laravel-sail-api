# Sistema de Afiliación Empresarial

Sistema completo de gestión empresarial desarrollado con Laravel 12, MySQL, Inertia.js y React para el manejo de empresas, trabajadores y núcleos familiares.

## 🚀 Características Principales

### Backend (Laravel 12)

- **API REST completa** con endpoints para todas las entidades
- **Resources JSON y Collections** para respuestas estructuradas
- **Modelos Eloquent** con relaciones y validaciones
- **Migraciones y Seeders** para estructura y datos de prueba
- **Middleware de Inertia.js** para SSR

### Frontend (React + Inertia.js)

- **Server-Side Rendering (SSR)** con Inertia.js
- **Componentes React** modernos y reutilizables
- **Tailwind CSS** para estilos responsivos
- **Navegación SPA** sin recarga de página
- **Dashboard interactivo** con estadísticas en tiempo real

### Base de Datos (MySQL)

- **Estructura normalizada** con relaciones bien definidas
- **Datos de prueba** incluidos para testing
- **Integridad referencial** garantizada

## 📊 Entidades del Sistema

### 1. Empresas

- Información corporativa completa
- Gestión de empleados por empresa
- Estadísticas de nómina y personal
- Estados activo/inactivo

### 2. Trabajadores

- Datos personales y laborales
- Cálculo automático de edad y antigüedad
- Gestión salarial con formateo
- Relación con empresa y núcleo familiar

### 3. Núcleo Familiar

- Familiares de trabajadores
- Dependencia económica
- Relaciones de parentesco
- Información de contacto

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 12, PHP 8.4, MySQL
- **Frontend**: React 18, Inertia.js, Tailwind CSS
- **Build Tools**: Vite, NPM
- **Base de Datos**: MySQL 8.0

```sh
rsync -avz /home/edwin-tics/proyectos/comfaca-enlinea/flask-api/ admin@172.168.0.15:/home/admin/contenedores/desarrollo/flask-api

rsync -avz /home/edwin-tics/proyectos/comfaca-enlinea/laravel/ admin@172.168.0.15:/home/admin/contenedores/desarrollo/mercurio
```

## Otros comandos

```bash

# Limpiar el log de laravel
truncate -s 0 storage/logs/laravel.log

# Limpiar cache
php artisan route:clear && php artisan config:clear && php artisan cache:clear && php artisan view:clear

php artisan optimize:clear

php artisan route:cache && php artisan view:cache && php artisan config:cache && php artisan event:cache && php artisan optimize


# Generar swagger
php artisan l5-swagger:generate

# Consultar  la lista de endpoints de API
php artisan route:list --path=api


# Que db se esta usando
php artisan tinker
> DB::connection()->getDriverName();
> DB::connection()->getDatabaseName();

# Tambien se puede usar
php artisan db:show


# Correr el seeder
php artisan db:seed --class=DatabaseSeeder


# Correr las migraciones y el seeder solo para desarrollo
php artisan migrate:fresh --seed

```

## Comandos para Docker Imagen

```bash

# Ejecutar el produccion Docker Compose
sed -i 's/^session.sid_length = 26/;session.sid_length = 26/' /usr/local/etc/php/php.ini
sed -i 's/^session.sid_bits_per_character = 5/;session.sid_bits_per_character = 5/' /usr/local/etc/php/php.ini

mkdir -p /tmp/tcpdf_temp
chmod 777 /tmp/tcpdf_temp

# resetear el apache de forma silent
docker exec -it <nombre_contenedor> apache2ctl graceful


# Aumentar límite de archivos abiertos temporalmente en el server principal
ulimit -n 65536

# Verificar que se aplicó
ulimit -n

# tambien al ejecutar en el server principal para hacerlo permanente
sudo bash -c 'cat >> /etc/security/limits.conf << EOF
* soft nofile 65536
* hard nofile 65536
EOF'

## Habilitar memoria
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 50M/' /usr/local/etc/php/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 55M/' /usr/local/etc/php/php.ini

# Entrar al contenedor y borrar la caché (ej. en Node.js, PHP, Symfony o Composer)
docker compose exec mercurio <comando_de_limpieza>

# Por ejemplo, para vaciar la caché de un proyecto Laravel/Composer en el servicio 'app':
docker compose exec mercurio php artisan cache:clear

# Por ejemplo, para limpiar npm/yarn en un servicio web:
docker compose exec mercurio npm cache clean --force
# Por ejemplo, para limpiar pnpm en un servicio web:
docker compose exec mercurio pnpm cache delete
docker compose exec mercurio pnpm store prune
```

### Mandos pnpm gulp

```bash
# Compilar el modulo con gulp (patron actual del proyecto)
APP=mercurio npx gulp Ecommerce

# O con sail:
vendor/bin/sail bash -c "cd public && APP=mercurio npx gulp Ecommerce"

# Debe generar public/mercurio/build/Ecommerce.js
ls -la public/mercurio/build/Ecommerce.js
```

### Cerrar evento Mercuro10

```bash
# Simular (recomendado primero)
php artisan mercurio10:cerrar-historicos --dry-run --cerrar-pendientes

# Aplicar: cierra A/X/D y los P asociados
php artisan mercurio10:cerrar-historicos --cerrar-pendientes

# Otros estados (ej. incluir R)
php artisan mercurio10:cerrar-historicos --estados=A,D,R --cerrar-pendientes
```

### Crear ruuid de Mercurio10 vacio

````bash
Comando listo: `mercurio10:backfill-ruuid`.

**Qué hace:** a eventos `P` sin `ruuid` les pone `{solicitud.ruuid}-{item:02d}` (igual que `SenderValidationCaja`), resolviendo la solicitud con `tipopc` + `numero`.

**Uso**

```bash
php artisan mercurio10:backfill-ruuid --dry-run
php artisan mercurio10:backfill-ruuid
php artisan mercurio10:backfill-ruuid --tipopc=1
````

**Dry-run actual:** ~128 243 a actualizar; 3 621 sin solicitud; 142 solicitud sin `ruuid`; 2 tipopc no mapeado.

```bash
php artisan mercurio10:backfill-feccie --dry-run
php artisan mercurio10:backfill-feccie
```

Detecta ruuid huérfanos en mercurio30/31/32/34/36/38/39/41/47, normaliza el prefijo `#` (solicitudes/`mercurio10` → canónico sin `#`, alineado con `radicados`), inserta faltantes (parse `TIPO-VIGENCIA-NUMERO`) y reporta no parseables / conflictos. Exit ≠ 0 si hay casos no reparables.

UUID legados u otros no parseables: `--regenerate-unparseable` (genera radicado nuevo por fila).

```bash
php artisan radicados:backfill-from-solicitudes --dry-run
php artisan radicados:backfill-from-solicitudes --dry-run --regenerate-unparseable
php artisan radicados:backfill-from-solicitudes --regenerate-unparseable
php artisan radicados:backfill-from-solicitudes

php artisan migrate --path=database/migrations/2026_07_28_130000_make_radicados_radicado_unique_not_null.php
php artisan migrate --path=database/migrations/2026_07_28_130100_add_fk_solicitud_ruuid_to_radicados.php
```
