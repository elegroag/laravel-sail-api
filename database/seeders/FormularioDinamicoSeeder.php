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
                'name' => 'formulario_solicitud_credito',
                'title' => 'Solicitud de Crédito',
                'description' => 'Formulario para solicitud de crédito hipotecario',
                'module' => 'creditos',
                'endpoint' => '/api/creditos/solicitar',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 1,
                    'spacing' => 'lg',
                    'theme' => 'professional'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['cliente', 'asesor']
                ]
            ],
            [
                'name' => 'formulario_actualizacion_datos',
                'title' => 'Actualización de Datos Personales',
                'description' => 'Formulario para actualizar información personal del usuario',
                'module' => 'perfil',
                'endpoint' => '/api/perfil/actualizar',
                'method' => 'PUT',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 2,
                    'spacing' => 'md',
                    'theme' => 'clean'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['usuario']
                ]
            ],
            [
                'name' => 'formulario_contacto_soporte',
                'title' => 'Contacto con Soporte',
                'description' => 'Formulario para contactar al equipo de soporte técnico',
                'module' => 'soporte',
                'endpoint' => '/api/soporte/contactar',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 1,
                    'spacing' => 'sm',
                    'theme' => 'support'
                ],
                'permissions' => [
                    'public' => true,
                    'roles' => []
                ]
            ],
            [
                'name' => 'formulario_evaluacion_servicio',
                'title' => 'Evaluación de Servicio',
                'description' => 'Formulario para evaluar la calidad del servicio recibido',
                'module' => 'evaluaciones',
                'endpoint' => '/api/evaluaciones/servicio',
                'method' => 'POST',
                'is_active' => true,
                'layout_config' => [
                    'columns' => 1,
                    'spacing' => 'md',
                    'theme' => 'feedback'
                ],
                'permissions' => [
                    'public' => false,
                    'roles' => ['cliente']
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
