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
                'name' => 'mercurio30',
                'title' => 'Mercurio 30',
                'description' => 'Formulario para el modelo de datos Mercurio 30',
                'module' => 'mercurio',
                'endpoint' => '/mercurio/mercurio30/guardar',
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
                'endpoint' => '/mercurio/mercurio31/guardar',
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
                'endpoint' => '/mercurio/mercurio32/guardar',
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
                'endpoint' => '/mercurio/mercurio34/guardar',
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
                'endpoint' => '/mercurio/mercurio36/guardar',
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
                'endpoint' => '/mercurio/mercurio38/guardar',
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
                'name' => 'mercurio39',
                'title' => 'Mercurio 39',
                'description' => 'Formulario para el modelo de datos Mercurio 39',
                'module' => 'mercurio',
                'endpoint' => '/mercurio/mercurio39/guardar',
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
                'name' => 'mercurio40',
                'title' => 'Mercurio 40',
                'description' => 'Formulario para el modelo de datos Mercurio 40',
                'module' => 'mercurio',
                'endpoint' => '/mercurio/mercurio40/guardar',
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
                'endpoint' => '/mercurio/mercurio41/guardar',
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
                'endpoint' => '/mercurio/mercurio45/guardar',
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
                'endpoint' => '/mercurio/mercurio47/guardar',
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
    }
}
