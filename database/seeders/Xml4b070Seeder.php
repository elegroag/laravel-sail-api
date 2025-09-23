<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Xml4b070;

class Xml4b070Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $jornadas = [
            ['tipjor' => 1, 'nombre' => 'MAÑANA'],
            ['tipjor' => 2, 'nombre' => 'TARDE'],
            ['tipjor' => 3, 'nombre' => 'NOCTURNA'],
            ['tipjor' => 4, 'nombre' => 'JORNADA UNICA'],
        ];

        foreach ($jornadas as $jornada) {
            Xml4b070::updateOrCreate(
                ['tipjor' => $jornada['tipjor']],
                $jornada
            );
        }
    }
}
