<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mercurio57;

class Mercurio57Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $promociones = [
            ['numpro' => 1, 'archivo' => 'promo_movil_1.png', 'orden' => 1, 'url' => 'comfaca.com/redverde', 'estado' => 'A'],
            ['numpro' => 2, 'archivo' => 'promo_movil_2.jpg', 'orden' => 2, 'url' => 'https://comfaca.com/', 'estado' => 'A'],
            ['numpro' => 3, 'archivo' => 'promo_movil_3.jpg', 'orden' => 3, 'url' => 'comfaca.com', 'estado' => 'A'],
            ['numpro' => 4, 'archivo' => 'promo_movil_4.png', 'orden' => 4, 'url' => 'comfaca.com', 'estado' => 'A'],
        ];

        foreach ($promociones as $promocion) {
            Mercurio57::updateOrCreate(
                ['numpro' => $promocion['numpro']],
                $promocion
            );
        }
    }
}
