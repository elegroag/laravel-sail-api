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

# Limpiar el log de laravel
truncate -s 0 storage/logs/laravel.log

# Limpiar cache
php artisan route:clear && php artisan config:clear && php artisan cache:clear

# Generar swagger
php artisan l5-swagger:generate

# Consultar  la lista de endpoints de API
php artisan route:list --path=api
```