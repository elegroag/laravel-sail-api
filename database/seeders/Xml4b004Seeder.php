<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Xml4b004;

class Xml4b004Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $tiposIdentificacion = [
            ['tipide' => 1, 'nombre' => 'CEDULA DE CIUDADANIA', 'coddoc' => 1],
            ['tipide' => 2, 'nombre' => 'TARJETA DE IDENTIDAD', 'coddoc' => 2],
            ['tipide' => 3, 'nombre' => 'REGISTRO CIVIL', 'coddoc' => 5],
            ['tipide' => 4, 'nombre' => 'CEDULA DE EXTRANJERIA', 'coddoc' => 4],
            ['tipide' => 5, 'nombre' => 'NUIP', 'coddoc' => 0],
            ['tipide' => 6, 'nombre' => 'PASAPORTE', 'coddoc' => 8],
            ['tipide' => 7, 'nombre' => 'NIT', 'coddoc' => 3],
            ['tipide' => 8, 'nombre' => 'CARNET DIPLOMATICO', 'coddoc' => 6],
            ['tipide' => 9, 'nombre' => 'PERMISO ESPECIAL DE PERMANENCIA (P.E.P)', 'coddoc' => 8],
            ['tipide' => 10, 'nombre' => 'CERTIFICADO CABILDO', 'coddoc' => 0],
            ['tipide' => 11, 'nombre' => 'IDENTIFICACIÓN DADA POR LA SECRETARÍA DE EDUCACIÓN', 'coddoc' => 0],
            ['tipide' => 12, 'nombre' => 'TARJETA DE MOVILIDAD FRONTERIZA (TMF)', 'coddoc' => 0],
            ['tipide' => 13, 'nombre' => 'VISA', 'coddoc' => 0],
        ];

        foreach ($tiposIdentificacion as $tipo) {
            Xml4b004::updateOrCreate(
                ['tipide' => $tipo['tipide']],
                $tipo
            );
        }
    }
}
