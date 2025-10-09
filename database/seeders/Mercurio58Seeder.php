<?php

namespace Database\Seeders;

use App\Models\Mercurio58;
use Illuminate\Database\Seeder;

class Mercurio58Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $areas = [
            ['numero' => 1, 'archivo' => 'area_1_1.png', 'orden' => 1, 'codare' => 1],
            ['numero' => 2, 'archivo' => 'area_2_2.png', 'orden' => 2, 'codare' => 2],
            ['numero' => 3, 'archivo' => 'area_3_3.png', 'orden' => 3, 'codare' => 3],
            ['numero' => 4, 'archivo' => 'area_4_4.png', 'orden' => 4, 'codare' => 4],
            ['numero' => 5, 'archivo' => 'area_5_5.png', 'orden' => 5, 'codare' => 5],
            ['numero' => 6, 'archivo' => 'area_6_6.png', 'orden' => 6, 'codare' => 6],
            ['numero' => 7, 'archivo' => 'area_7_7.png', 'orden' => 7, 'codare' => 7],
            ['numero' => 8, 'archivo' => 'area_9_8.png', 'orden' => 8, 'codare' => 9],
            ['numero' => 9, 'archivo' => 'area_10_9.png', 'orden' => 9, 'codare' => 10],
            ['numero' => 10, 'archivo' => 'area_11_10.png', 'orden' => 10, 'codare' => 11],
            ['numero' => 11, 'archivo' => 'area_8_11.png', 'orden' => 11, 'codare' => 8],
        ];

        foreach ($areas as $area) {
            Mercurio58::updateOrCreate(
                ['numero' => $area['numero']],
                $area
            );
        }
    }
}
