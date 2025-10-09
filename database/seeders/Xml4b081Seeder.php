<?php

namespace Database\Seeders;

use App\Models\Xml4b081;
use Illuminate\Database\Seeder;

class Xml4b081Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $tiposBeneficiario = [
            ['tipben' => 1, 'nombre' => 'NIÑO'],
            ['tipben' => 2, 'nombre' => 'NIÑA'],
            ['tipben' => 3, 'nombre' => 'MUJER GESTANTE'],
            ['tipben' => 4, 'nombre' => 'MADRE LACTANTE'],
            ['tipben' => 5, 'nombre' => 'ADOLESCENTE'],
        ];

        foreach ($tiposBeneficiario as $tipo) {
            Xml4b081::updateOrCreate(
                ['tipben' => $tipo['tipben']],
                $tipo
            );
        }
    }
}
