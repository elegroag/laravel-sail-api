<?php

namespace Database\Seeders;

use App\Models\ComponenteDinamico;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FormularioDinamicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si existe la tabla formularios_dinamicos
        if (!Schema::hasTable('formularios_dinamicos')) {
            $this->command->info('Tabla formularios_dinamicos no existe, omitiendo seeder');
            return;
        }

        $formularios = [
            [
                'name' => 'formulario_registro_usuario',
                'title' => 'Registro de Usuario',
                'description' => 'Formulario para registro de nuevos usuarios en el sistema',
                'module' => 'auth',
                'endpoint' => '/api/auth/register',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 2,
                    'spacing' => 'md',
                    'theme' => 'default'
                ],
                'permissions' => [
                    'public' => true,
                    'roles' => []
                ]
            ],
            [
                'name' => 'mercurio30',
                'title' => 'Mercurio 30',
                'description' => 'Formulario para el modelo de datos Mercurio 30',
                'module' => 'mercurio',
                'endpoint' => '/api/mercurio/30',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 2,
                    'spacing' => 'md',
                    'theme' => 'default'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['admin', 'editor']
                ]
            ],
            [
                'name' => 'mercurio31',
                'title' => 'Mercurio 31',
                'description' => 'Formulario para el modelo de datos Mercurio 31',
                'module' => 'mercurio',
                'endpoint' => '/api/mercurio/31',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 2,
                    'spacing' => 'md',
                    'theme' => 'default'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['admin', 'editor']
                ]
            ],
            [
                'name' => 'mercurio32',
                'title' => 'Mercurio 32',
                'description' => 'Formulario para el modelo de datos Mercurio 32',
                'module' => 'mercurio',
                'endpoint' => '/api/mercurio/32',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 2,
                    'spacing' => 'md',
                    'theme' => 'default'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['admin', 'editor']
                ]
            ],
            [
                'name' => 'mercurio34',
                'title' => 'Mercurio 34',
                'description' => 'Formulario para el modelo de datos Mercurio 34',
                'module' => 'mercurio',
                'endpoint' => '/api/mercurio/34',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 2,
                    'spacing' => 'md',
                    'theme' => 'default'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['admin', 'editor']
                ]
            ],
            [
                'name' => 'mercurio36',
                'title' => 'Mercurio 36',
                'description' => 'Formulario para el modelo de datos Mercurio 36',
                'module' => 'mercurio',
                'endpoint' => '/api/mercurio/36',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 2,
                    'spacing' => 'md',
                    'theme' => 'default'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['admin', 'editor']
                ]
            ],
            [
                'name' => 'mercurio38',
                'title' => 'Mercurio 38',
                'description' => 'Formulario para el modelo de datos Mercurio 38',
                'module' => 'mercurio',
                'endpoint' => '/api/mercurio/38',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 2,
                    'spacing' => 'md',
                    'theme' => 'default'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['admin', 'editor']
                ]
            ],
            [
                'name' => 'mercurio41',
                'title' => 'Mercurio 41',
                'description' => 'Formulario para el modelo de datos Mercurio 41',
                'module' => 'mercurio',
                'endpoint' => '/api/mercurio/41',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 2,
                    'spacing' => 'md',
                    'theme' => 'default'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['admin', 'editor']
                ]
            ],
            [
                'name' => 'mercurio45',
                'title' => 'Mercurio 45',
                'description' => 'Formulario para el modelo de datos Mercurio 45',
                'module' => 'mercurio',
                'endpoint' => '/api/mercurio/45',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 2,
                    'spacing' => 'md',
                    'theme' => 'default'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['admin', 'editor']
                ]
            ],
            [
                'name' => 'mercurio47',
                'title' => 'Mercurio 47',
                'description' => 'Formulario para el modelo de datos Mercurio 47',
                'module' => 'mercurio',
                'endpoint' => '/api/mercurio/47',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 2,
                    'spacing' => 'md',
                    'theme' => 'default'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['admin', 'editor']
                ]
            ]
        ];

        foreach ($formularios as $formulario) {
            DB::table('formularios_dinamicos')->insert([
                'name' => $formulario['name'],
                'title' => $formulario['title'],
                'description' => $formulario['description'],
                'module' => $formulario['module'],
                'endpoint' => $formulario['endpoint'],
                'method' => $formulario['method'],
                'is_active' => $formulario['is_active'],
                'layout_config' => json_encode($formulario['layout_config']),
                'permissions' => json_encode($formulario['permissions']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Formularios dinámicos creados exitosamente');

        // Crear asociaciones entre formularios y componentes
        $this->asociarComponentesFormularios();
    }

    /**
     * Asociar componentes a formularios específicos
     */
    private function asociarComponentesFormularios(): void
    {
        // Verificar si existe la tabla de asociación
        if (!Schema::hasTable('formulario_componentes')) {
            $this->command->info('Tabla formulario_componentes no existe, omitiendo asociaciones');
            return;
        }

        $asociaciones = [
            'formulario_registro_usuario' => [
                'nombre_completo',
                'email',
                'tipo_documento',
                'numero_documento',
                'fecha_nacimiento',
                'telefono'
            ],
            'formulario_solicitud_credito' => [
                'nombre_completo',
                'email',
                'tipo_documento',
                'numero_documento',
                'telefono',
                'salario'
            ],
            'formulario_actualizacion_datos' => [
                'nombre_completo',
                'email',
                'telefono',
                'fecha_nacimiento'
            ],
            'formulario_contacto_soporte' => [
                'nombre_completo',
                'email',
                'tipo_documento',
                'numero_documento',
                'comentarios'
            ],
            'formulario_evaluacion_servicio' => [
                'email',
                'comentarios'
            ]
        ];

        foreach ($asociaciones as $formularioName => $componentes) {
            $formulario = DB::table('formularios_dinamicos')
                ->where('name', $formularioName)
                ->first();

            if (!$formulario) continue;

            foreach ($componentes as $index => $componenteName) {
                $componente = ComponenteDinamico::where('name', $componenteName)->first();

                if ($componente) {
                    DB::table('formulario_componentes')->insert([
                        'formulario_id' => $formulario->id,
                        'componente_id' => $componente->id,
                        'order' => $index + 1,
                        'group_id' => 1,
                        'is_required' => in_array($componenteName, ['nombre_completo', 'email']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('Asociaciones formulario-componentes creadas exitosamente');
    }
}
