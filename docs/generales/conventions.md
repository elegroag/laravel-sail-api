# Convenciones de Código

## PHP (Laravel)

### Style Guide

- **Formatter**: Laravel Pint (`./vendor/bin/pint`)
- **Estándar**: PSR-12
- **No ejecutar manualmente**: Pint se corre vía CI/post-commit

### Naming Conventions

| Elemento | Convención | Ejemplo |
|----------|-----------|---------|
| Models | PascalCase singular | `Empresa`, `Mercurio35` |
| Tables | snake_case plural | `empresas`, `mercurio35` |
| Controllers | PascalCase + Controller | `EmpresaController` |
| Services | PascalCase | `ReportGenerator`, `ApruebaService` |
| Middleware | PascalCase | `EnsureCookieAuthenticated` |
| Routes | kebab-case archivos | `aprueba_empresa.php`, `config_basica.php` |
| Helpers | snake_case funciones | `formatear_fecha()`, `generar_uuid()` |
| Variables | camelCase | `$empresaData`, `$tokenList` |
| Constantes | UPPER_SNAKE | `MAX_REINTENTOS`, `TIPO_EMPLEADO` |
| Métodos | camelCase | `getById()`, `calcularAporte()` |

### Model Conventions

Los modelos Eloquent van en `app/Models/` y extienden `Illuminate\Database\Eloquent\Model`.

```php
// ✅ Correcto
class Empresa extends Model
{
    protected $table = 'empresas';
    protected $fillable = ['nit', 'razon_social'];
}

// ⚠️ Legacy - estos nombres requieren mejora
class Mercurio35 extends Model
class Gener02 extends Model
class Xml4b004 extends Model
```

### Controllers

```php
// ✅ Correcto - un controller por recurso
class EmpresaController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(Empresa $empresa) {}
}

// ⚠️ Legacy - controllers con múltiples métodos para múltiples tablas
// (estos vienen del sistema KumbiaPHP y deben refactorizarse)
```

### Services

Los servicios encapsulan lógica de negocio y se almacenan en `app/Services/`.

```php
// ✅ Correcto
namespace App\Services\Reportes;

class ReportGenerator
{
    public function generate(array $params): Report {}
}
```

### Routes

**Archivos de ruta**: kebab-case (`aprueba_empresa.php`, `config_basica.php`)

```php
// ✅ Correcto
Route::middleware(['cajas.auth'])->group(function () {
    Route::post('/empresa/aprobar', [ApruebaController::class, 'aprobarEmpresa']);
});

// ⚠️ Legacy - rutas en archivos sin namespace consistente
```

### Queries

```php
// ✅ Preferir Eloquent sobre Query Builder cuando sea posible
$empresas = Empresa::where('estado', 'activo')
    ->with('trabajadores')
    ->paginate(20);

// Para queries complejas, usar query scopes
class Empresa extends Model
{
    public function scopeActivas($query) { ... }
    public function scopeConTrabajadores($query) { ... }
}
```

---

## React / TypeScript

### Style Guide

- **Linter**: ESLint + `eslint-plugin-react` + `eslint-plugin-react-hooks`
- **Formatter**: Prettier
- **Config**: `.prettierrc` con printWidth 150, tabWidth 4, single quotes

### Running Lint/Format

```bash
npm run lint      # ESLint --fix
npm run format    # Prettier --write
npm run types     # tsc --noEmit
```

### Naming Conventions

| Elemento | Convención | Ejemplo |
|----------|-----------|---------|
| Components | PascalCase | `EmpresaForm.tsx`, `DataTable.tsx` |
| Hooks | camelCase con use | `useEmpresaData.ts`, `useAuth.ts` |
| Utils | camelCase | `formatters.ts`, `validators.ts` |
| Types/Interfaces | PascalCase | `EmpresaData`, `UserSession` |
| Constantes | UPPER_SNAKE | `API_ENDPOINTS`, `MAX_PAGINA` |
| Archivos genéricos | kebab-case | `empresa-card.tsx`, `login-form.tsx` |

### Component Structure

```tsx
// ✅ Componente bien estructurado
import { useState } from 'react';
import { clsx } from 'clsx';

interface Props {
  title: string;
  onSubmit: (data: FormData) => void;
}

export function EmpresaForm({ title, onSubmit }: Props) {
  const [loading, setLoading] = useState(false);

  return (
    <div className={clsx('rounded-lg border p-4')}>
      <h2 className="text-lg font-semibold">{title}</h2>
    </div>
  );
}
```

### Imports

```tsx
// ✅ Usar path aliases configurados en tsconfig.json
import { Button } from '@/components/ui/button';
import { useEmpresa } from '@/hooks/useEmpresa';
import { formatCurrency } from '@/utils/formatters';

// ⚠️ Evitar imports relativos profundos
// import { Button } from '../../../../components/ui/button';
```

---

## Archivos de Ruta (Backend)

Los archivos en `routes/cajas/` y `routes/mercurio/` siguen un patrón de **1 archivo = 1 recurso/funcionalidad**:

```
routes/cajas/
├── admin_productos.php     # Admin de productos
├── aprueba_beneficiario.php # Aprobación de beneficiarios
├── aprueba_empresa.php     # Aprobación de empresas
├── config_basica.php       # Configuración básica
├── movile_menu.php         # Menú móvil
└── ...

routes/mercurio/
├── empresa.php              # CRUD empresas
├── trabajador.php           # CRUD trabajadores
├── beneficiario.php         # CRUD beneficiarios
└── ...
```

---

## Migraciones

- **Nombre de archivo**: `YYYY_MM_DD_HHMMSS_create_<table>_table.php`
- **ID**: timestamps autoincrementales (`2025_09_22_151239`)
- **Up/Down**: Siempre incluir ambos métodos
- **No modificar migraciones existentes**: crear nueva migración para cambios

```php
// ✅ Correcto
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nit')->unique();
            $table->string('razon_social');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
```

---

## Git

- **Commits**: Conventional Commits (`feat:`, `fix:`, `docs:`, `refactor:`)
- **Ramas**: `feat/nombre`, `fix/nombre`, `hotfix/nombre`
- **No commits directos a main**: todo vía PR

---

## Testing

```bash
php artisan test                    # Todas las pruebas
php artisan test --filter=SomeTest  # Test específico
```

- Tests van en `tests/Feature/` y `tests/Unit/`
- Unit tests para lógica pura (services, helpers)
- Feature tests para HTTP endpoints y integraciones
- DB: MySQL `mercurio_dev` en `172.168.0.15` (no SQLite)

---

## Archivos Especiales

### Helpers Autoloaded

```php
// composer.json autoload.files
app/Helpers/helpers.php   // Helpers genéricos (formateo, fechas, etc.)
app/Helpers/files.php     // Utilidades de archivos
app/Helpers/hashes.php    // Hashing y encriptación
legacy/Thiagoprz/CompositeKey/HasCompositeKey.php  // Claves compuestas
legacy/Excel/UserReportExcel.php                   // Reportes Excel legacy
```

### Legacy Code

El código en `legacy/` es código migrado de KumbiaPHP:
- **No agregar nueva lógica** a archivos legacy
- **Migrar progresivamente** a Laravel moderno
- **Documentar** cualquier dependencia de código legacy

---

## Commit Messages

```
feat: add approval workflow for empresa registration
fix: resolve session timeout issue in cajas
docs: update architecture diagram
refactor: rename Mercurio35 to AfiliadoEmpresa
test: add coverage for ReportGenerator
```

## Entornos

| Entorno | Descripción | Características |
|---------|-------------|------------------|
| local | Desarrollo local | Vite dev server, debug=true |
| testing | Tests | MySQL mercurio_dev |
| production | Deploy | Docker, optimizado |
