<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ComandoEstructuras;

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
                'procesador' => 'p7',
                'estructura' => '/var/www/html/clisisu/artisan server:send {{servicio}} {{metodo}} {{params}} {{user}} {{sistema}} {{env}} {{comando}}',
                'variables' => 'servicio|metodo|params|user|sistema|env',
                'tipo' => 'PHP',
                'sistema' => 'Mercurio',
                'env' => '1',
                'descripcion' => 'Estructura procesador syncrono lineas de interface de comandos para servicios dispuestos',
                'asyncro' => 0,
            ],
            [
                'id' => 2,
                'procesador' => 'p7',
                'estructura' => '/var/www/html/clisisu/artisan {{servicio}}:send {{params}}',
                'variables' => 'servicio|params',
                'tipo' => 'PHP',
                'sistema' => 'Mercurio',
                'env' => '1',
                'descripcion' => 'Estructura procesador syncrono lineas de interface de comandos para ejecutar un comando artisan',
                'asyncro' => 0,
            ],
            [
                'id' => 3,
                'procesador' => 'p7',
                'estructura' => '/var/www/html/clisisu/artisan server:send {{servicio}} {{metodo}} {{params}} {{user}} {{sistema}} {{env}} {{comando}}',
                'variables' => 'servicio|metodo|params|user|sistema|env',
                'tipo' => 'PHP',
                'sistema' => 'Mercurio',
                'env' => '1',
                'descripcion' => 'Estructura procesador asyncrono lineas de interface de comandos para servicios dispuestos',
                'asyncro' => 1,
            ],
        ];

        foreach ($estructuras as $estructura) {
            ComandoEstructuras::updateOrCreate(
                ['id' => $estructura['id']],
                $estructura
            );
        }
    }

    public function sql()
    {
        $sql = "INSERT INTO `comando_estructuras` VALUES
        (1,'p7','/var/www/html/clisisu/artisan server:send {{servicio}} {{metodo}} {{params}} {{user}} {{sistema}} {{env}} {{comando}}','servicio|metodo|params|user|sistema|env','PHP','Mercurio','1','Estructura procesador syncrono lineas de interface de comandos para servicios dispuestos ',0),
        (2,'p7','/var/www/html/clisisu/artisan {{servicio}}:send {{params}}','servicio|params','PHP','Mercurio','1','Estructura procesador syncrono lineas de interface de comandos para ejecutar un comando artisan',0),
        (3,'p7','/var/www/html/clisisu/artisan server:send {{servicio}} {{metodo}} {{params}} {{user}} {{sistema}} {{env}} {{comando}}','servicio|metodo|params|user|sistema|env','PHP','Mercurio','1','Estructura procesador asyncrono lineas de interface de comandos para servicios dispuestos ',1);
        ";
    }
}
