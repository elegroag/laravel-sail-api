<?php

namespace Database\Seeders;

use App\Models\Mercurio05;
use Illuminate\Database\Seeder;

class Mercurio05Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $oficinasCiudad = [
            ['codofi' => '01', 'codciu' => '18001'],
        ];

        foreach ($oficinasCiudad as $oficinaCiudad) {
            Mercurio05::updateOrCreate(
                ['codofi' => $oficinaCiudad['codofi'], 'codciu' => $oficinaCiudad['codciu']],
                $oficinaCiudad
            );
        }
    }
}
