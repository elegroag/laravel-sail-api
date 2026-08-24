# Arquitectura del Proyecto

## Overview

Sistema de gestión de afiliación empresarial migrado desde KumbiaPHP a Laravel 12. La aplicación sirve dos entornos completamente separados: **Cajas** (administrativo) y **Mercurio** (clientes/afiliados).

```
┌─────────────────────────────────────────────────────────────┐
│                      Browser / Client                        │
└──────────┬──────────────────────────────────┬───────────────┘
           │                                  │
           ▼                                  ▼
   ┌───────────────┐                 ┌───────────────┐
   │    Cajas      │                 │   Mercurio     │
   │  (Admin UI)   │                 │  (Client UI)   │
   │ React+Inertia │                 │ React+Inertia  │
   └───────┬───────┘                 └───────┬───────┘
           │                                  │
           ▼                                  ▼
   ┌───────────────┐                 ┌───────────────┐
   │  Middleware   │                 │  Middleware   │
   │ cajas.auth    │                 │ mercurio.auth │
   └───────┬───────┘                 └───────┬───────┘
           │                                  │
           ▼                                  ▼
   ┌─────────────────────────────────────────────────────┐
   │                  Laravel 12 Backend                   │
   │  Routes: routes/cajas/*    Routes: routes/mercurio/* │
   │  Controllers (Http/Controllers)                      │
   │  Services/*                                          │
   │  Models/* (Eloquent)                                │
   └──────────┬──────────────────────────┬───────────────┘
              │                          │
              ▼                          ▼
       ┌─────────────┐            ┌─────────────┐
       │    MySQL    │            │  JWT Auth   │
       │   (Mercurio │            │ (tymon/jwt) │
       │    _dev)    │            │             │
       └─────────────┘            └─────────────┘
```

## Capas de la Aplicación

### 1. Routes (`routes/`)

```
routes/
├── web.php          # Páginas SSR con Inertia (dashboard, login)
├── api.php          # API REST genérica
├── console.php      # Comandos Artisan programados
├── cajas/           # ~50 archivos — flujo administrativo
│   ├── admin_productos.php
│   ├── aprueba_*.php   # ~12 workflows de aprobación
│   ├── config_*.php    # Configuraciones
│   ├── movile_*.php    # Rutas móviles
│   └── ...
└── mercurio/        # ~16 archivos — API clientes/afiliados
    ├── empresa.php
    ├── trabajador.php
    ├── beneficiario.php
    └── ...
```

**Cajas** usa autenticación por sesión/cookie.
**Mercurio** usa autenticación JWT (`tymon/jwt-auth`).

### 2. Controllers (`app/Http/Controllers/`)

```
Http/Controllers/
├── Controllers/           # Controllers base Laravel
├── Cajas/                # Lógica administrativa
│   ├── ApruebaController.php
│   └── ...
├── Mercurio/             # Lógica de cliente (API)
│   └── ...
└── Api/                  # API resources
```

### 3. Models (`app/Models/`)

**119 modelos totales** en 5 categorías:

| Categoría | Patrón | Ejemplos | Descripción |
|-----------|--------|---------|-------------|
| Mercurio | `Mercurio{NN}` | Mercurio01–85 | Módulo principal de negocio |
| Gener | `Gener{NN}` | Gener02,09,18,21,40,42 | Lógica genérica heredada |
| XML/SAT | `Xml4b{NNN}` | Xml4b001,004,005… | Integración SAT/formularios |
| SAT | `Sat{NN}` | Sat01,14,15 | Módulo SAT |
| Negocio | Nombres claros | Empresa, Trabajador, User | Entidades de negocio claras |

> **Nota:** Los modelos `MercurioXX`, `GenerXX`, `Xml4bXXX` son legacy de KumbiaPHP y deberían renombrarse progresivamente a nombres descriptivos.

### 4. Services (`app/Services/`)

```
Services/
├── Api/           # Lógica de integración API
├── Aportes/       # Cálculo de aportes
├── Aprueba/       # Workflows de aprobación (Cajas)
├── Autentications/# Auth (JWT y Cookie)
├── CajaServices/  # Servicios de caja
├── Cajas/         # Lógica de cajas
├── Certificados/  # Generación de certificados (TCPDF)
├── Entidades/     # Lógica de entidades
├── FactoryReportes/# Generadores de reportes
├── Formularios/   # Formularios dinámicos
├── Menu/          # Sistema de menús
├── Reportes/      # Reportes (TCPDF + Phpspreadsheet)
├── SatApi/        # Integración SAT
├── SftpTools/     # Herramientas SFTP
└── Utils/         # Utilidades varias
```

### 5. Middleware (`app/Http/Middleware/`)

- `EnsureCookieAuthenticated.php` — Valida sesión cookie para Mercurio
- `CajasCookieAuthenticated.php` — Valida sesión cookie para Cajas

### 6. Helpers (`app/Helpers/`)

```php
// Autoloaded en composer.json
app/Helpers/helpers.php    // Helpers genéricos
app/Helpers/files.php      // Utilidades de archivos
app/Helpers/hashes.php     // Utilidades de hashing
```

### 7. Legacy (`legacy/`)

Código preservado de KumbiaPHP, cargado vía autoload de Composer:

```
legacy/
├── Thiagoprz/CompositeKey/HasCompositeKey.php  # Soporte clave compuesta
├── Excel/UserReportExcel.php                   # Reportes Excel legacy
├── JWT/                                        # Lógica JWT legacy
└── ScriptLegacy.php                            # Scripts de migración
```

## Frontend (`resources/js/`)

```
js/
├── app.tsx            # Entry point Inertia
├── ssr.tsx            # SSR entry point
├── pages/             # Páginas Inertia (autenticadas)
├── components/        # Componentes React reutilizables
├── layouts/           # Layouts (Dashboard, Auth)
├── services/          # Llamadas API
├── hooks/             # Custom React hooks
├── utils/             # Utilidades JS
├── types/             # TypeScript types
└── constants/         # Constantes
```

Stack: React 19 + Inertia.js + Tailwind CSS v4 + Radix UI + Lucide icons

## Base de Datos

### Conexiones

- **Default (`mysql`)**: MySQL `mercurio_dev` en `172.168.0.15` — base de datos principal
- **Testing**: MySQL `mercurio_dev` en `172.168.0.15` (no SQLite)

### Tablas Principales

Migraciones en `database/migrations/`. Las tablas映射 a los modelos con prefijo `mercurio` (ej. `mercurio01`, `mercurio35`).

### Estructura

- ~119 tablas de negocio (migración masiva `2025_09_22`)
- `sessions` — tabla de sesiones (migración a database driver en curso)
- `menu_items`, `menu_permissions`, `menu_tipos` — sistema de menús
- `api_endpoints` — gestión de endpoints API
- `formularios_dinamicos`, `componentes_dinamicos` — formularios builder

## Autenticación

### Cajas
- Middleware: `cajas.auth` → `CajasCookieAuthenticated`
- Driver: Cookie/Session
- Login: `routes/cajas/*.php`

### Mercurio
- Middleware: `mercurio.auth` → JWT via `tymon/jwt-auth`
- Login: `routes/mercurio/*.php`
- Tokens: Access + Refresh token storage en `refresh_tokens`

## Integraciones Externas

| Servicio | Librería | Uso |
|----------|----------|-----|
| PDF | `tecnickcom/tcpdf` | Certificados, reportes |
| Excel | `phpoffice/phpspreadsheet` | Exportación datos |
| Email | `phpmailer/phpmailer` | Notificaciones |
| SSH/SFTP | `phpseclib/phpseclib` | Transferencia archivos |
| UUID | `ramsey/uuid` | Identificadores únicos |

## Despliegue

- **Método**: rsync a servidor en `172.168.0.15`
- **Contenedores**: Docker `compose.yml` para producción
- **SSHDocker**: TCPDF requiere `/tmp/tcpdf_temp` con chmod 777
- **Límite archivos**: 65536 open files en producción
