# Sistema de Gestión Empresarial

Sistema completo de gestión empresarial desarrollado con Laravel 10, MySQL, Inertia.js y React para el manejo de empresas, trabajadores y núcleos familiares.

## 🚀 Características Principales

### Backend (Laravel 10)

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

- **Backend**: Laravel 10, PHP 8.1, MySQL
- **Frontend**: React 18, Inertia.js, Tailwind CSS
- **Build Tools**: Vite, NPM
- **Base de Datos**: MySQL 8.0

## 📁 Estructura del Proyecto

```txt
empresa-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/           # Controladores API REST
│   │   │   └── WebController.php  # Controlador web Inertia
│   │   └── Resources/         # Resources JSON y Collections
│   └── Models/                # Modelos Eloquent
├── database/
│   ├── migrations/            # Migraciones de BD
│   └── seeders/              # Datos de prueba
├── resources/
│   ├── js/
│   │   ├── Components/       # Componentes React reutilizables
│   │   ├── Layouts/          # Layouts de página
│   │   └── Pages/            # Páginas React por entidad
│   └── views/                # Templates Blade
└── routes/
    ├── api.php              # Rutas API REST
    └── web.php              # Rutas web Inertia
```

## 🚀 Instalación y Configuración

### Prerrequisitos

- PHP 8.1+
- Composer
- Node.js 18+
- MySQL 8.0+

### Pasos de Instalación

1. **Instalar dependencias PHP**

    ```bash
    composer install
    ```

2. **Instalar dependencias Node.js**

    ```bash
    npm install
    ```

3. **Configurar base de datos**

    ```bash
    # Editar .env con credenciales de MySQL
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=empresa_db
    DB_USERNAME=empresa_user
    DB_PASSWORD=tu_password
    ```

4. **Ejecutar migraciones y seeders**

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

5. **Compilar assets frontend**

    ```bash
    npm run build
    ```

6. **Iniciar servidor**

    ```bash
    php artisan serve --host=0.0.0.0 --port=8000
    ```

## 📡 API Endpoints

### Empresas

- `GET /api/empresas` - Listar todas las empresas
- `POST /api/empresas` - Crear nueva empresa
- `GET /api/empresas/{id}` - Obtener empresa específica
- `PUT /api/empresas/{id}` - Actualizar empresa
- `DELETE /api/empresas/{id}` - Eliminar empresa

### Trabajadores

- `GET /api/trabajadores` - Listar todos los trabajadores
- `POST /api/trabajadores` - Crear nuevo trabajador
- `GET /api/trabajadores/{id}` - Obtener trabajador específico
- `PUT /api/trabajadores/{id}` - Actualizar trabajador
- `DELETE /api/trabajadores/{id}` - Eliminar trabajador

### Núcleos Familiares

- `GET /api/nucleos-familiares` - Listar todos los familiares
- `POST /api/nucleos-familiares` - Crear nuevo familiar
- `GET /api/nucleos-familiares/{id}` - Obtener familiar específico
- `PUT /api/nucleos-familiares/{id}` - Actualizar familiar
- `DELETE /api/nucleos-familiares/{id}` - Eliminar familiar

## 🌐 Rutas Web (Inertia.js)

- `/` - Dashboard principal
- `/empresas` - Gestión de empresas
- `/trabajadores` - Gestión de trabajadores
- `/nucleos-familiares` - Gestión de núcleos familiares

## 📊 Características de los Resources

### EmpresaResource

- Datos formateados de empresa
- Estadísticas de empleados
- Información de contacto estructurada

### TrabajadorResource

- Cálculo automático de edad y antigüedad
- Formateo de salario
- Relaciones con empresa y familiares

### NucleoFamiliarResource

- Información de parentesco formateada
- Estado de dependencia económica
- Relación con trabajador y empresa

## 📈 Collections con Metadatos

Cada Collection incluye metadatos estadísticos:

- **EmpresaCollection**: Total empresas, activas/inactivas, empleados
- **TrabajadorCollection**: Distribución salarial, género, estados
- **NucleoFamiliarCollection**: Dependencia económica, parentesco, edades

## 📝 Datos de Prueba

El sistema incluye datos de prueba:

- 2 empresas (TechCorp S.A., Constructora del Sur Ltda.)
- 3 trabajadores con diferentes cargos y salarios
- 5 familiares con diversas relaciones de parentesco

## 🔧 Comandos de Desarrollo

```bash
# Desarrollo frontend con hot reload
npm run dev

# Crear nueva migración
php artisan make:migration create_table_name

# Crear nuevo modelo con controlador y resource
php artisan make:model ModelName -mcr

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

- **Desarrollado con ❤️ usando Laravel, React e Inertia.js**
