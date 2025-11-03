<?php

namespace Database\Seeders;

use App\Models\ComandoEstructuras;
use Illuminate\Database\Seeder;

class ComandoEstructuraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estructuras = [
            [
                'id' => 1,
                'nombre' => 'clisisu_service_sync',
                'procesador' => 'p7',
                'estructura' => '/var/www/html/clisisu/artisan server:send {{servicio}} {{metodo}} {{params}} {{user}} {{sistema}} {{env}} {{comando}}',
                'variables' => 'servicio|metodo|params|user|sistema|env',
                'tipo' => 'PHP',
                'sistema' => 'Mercurio',
                'env' => 'production',
                'descripcion' => 'Estructura procesador syncrono lineas de interface de comandos para servicios dispuestos',
                'asyncro' => 0,
            ],
            [
                'id' => 2,
                'nombre' => 'clisisu_artisan_sync',
                'procesador' => 'p7',
                'estructura' => '/var/www/html/clisisu/artisan {{servicio}}:send {{params}}',
                'variables' => 'servicio|params',
                'tipo' => 'PHP',
                'sistema' => 'Mercurio',
                'env' => 'production',
                'descripcion' => 'Estructura procesador syncrono lineas de interface de comandos para ejecutar un comando artisan',
                'asyncro' => 0,
            ],
            [
                'id' => 3,
                'nombre' => 'clisisu_server_service_async',
                'procesador' => 'p7',
                'estructura' => '/var/www/html/clisisu/artisan server:send {{servicio}} {{metodo}} {{params}} {{user}} {{sistema}} {{env}} {{comando}}',
                'variables' => 'servicio|metodo|params|user|sistema|env',
                'tipo' => 'PHP',
                'sistema' => 'Mercurio',
                'env' => 'production',
                'descripcion' => 'Estructura procesador asyncrono lineas de interface de comandos para servicios dispuestos',
                'asyncro' => 1,
            ],
            [
                'id' => 4,
                'nombre' => 'py_generate_oficios_sync',
                'procesador' => 'uv',
                'estructura' => 'run python /var/www/html/nohup/python/generate-oficios/main.py {{template}} {{output}} {{context}}',
                'variables' => 'template|output|context',
                'tipo' => 'PYTHON',
                'sistema' => 'Mercurio',
                'env' => 'production',
                'descripcion' => 'Python sync procesador de oficios',
                'asyncro' => 0,
            ]
        ];

        foreach ($estructuras as $estructura) {
            ComandoEstructuras::create(
                $estructura
            );
        }
    }
}
