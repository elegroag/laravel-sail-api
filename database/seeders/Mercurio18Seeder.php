<?php

namespace Database\Seeders;

use App\Models\Mercurio18;
use Illuminate\Database\Seeder;

class Mercurio18Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preguntasSeguridad = [
            [
                'codigo' => '1',
                'detalle' => 'SU CIUDAD FAVORITA',
            ],
            [
                'codigo' => '2',
                'detalle' => 'NOMBRE DE MEJOR AMIGO DE INFANCIA',
            ],
        ];

        foreach ($preguntasSeguridad as $pregunta) {
            Mercurio18::updateOrCreate(
                ['codigo' => $pregunta['codigo']],
                $pregunta
            );
        }
    }

    /* INSERT INTO `mercurio18` VALUES ('1','SU CIUDAD FAVORITA'),('2','NOMBRE DE MEJOR AMIGO DE INFANCIA'); */
}
