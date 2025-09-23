<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Xml4b087;

class Xml4b087Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $poblaciones = [
            ['codpob' => 1, 'nombre' => 'VICTIMAS DEL CONFLICTO ARMADO'],
            ['codpob' => 2, 'nombre' => 'EN CONDICION DE DESPLAZAMIENTO'],
            ['codpob' => 3, 'nombre' => 'EN CONDICION DE DISCAPACIDAD FISICA'],
            ['codpob' => 4, 'nombre' => 'VICTIMAS DEL CONFLICTO ARMADO Y EN CONDICION DE DESPLAZAMIENTO'],
            ['codpob' => 5, 'nombre' => 'VICTIMAS DEL CONFLICTO ARMADO Y EN CONDICION DE DISCAPACIDAD FISICA'],
            ['codpob' => 6, 'nombre' => 'VICTIMAS DEL CONFLICTO ARMADO EN CONDICION DE DESPLAZAMIENTO Y EN CONDICION DE DISCAPACIDAD FISICA'],
            ['codpob' => 7, 'nombre' => 'EN CONDICION DE DESPLAZAMIENTO Y EN CONDICION DE DISCAPACIDAD FISICA'],
            ['codpob' => 8, 'nombre' => 'NO APLICA'],
        ];

        foreach ($poblaciones as $poblacion) {
            Xml4b087::updateOrCreate(
                ['codpob' => $poblacion['codpob']],
                $poblacion
            );
        }
    }
}
