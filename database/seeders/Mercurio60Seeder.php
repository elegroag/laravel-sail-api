<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mercurio60;

class Mercurio60Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $movimientos = [
            ['id' => 2, 'codinf' => 'CCF013-24-00001', 'codser' => '1', 'numero' => 2, 'tipo' => 'T', 'documento' => '7561396', 'coddoc' => '1', 'codcat' => 'C', 'valtot' => 94000, 'fecsis' => '2020-12-10', 'hora' => '15:54:41', 'tipmov' => 'B', 'online' => null, 'consumo' => 'S', 'feccon' => '2020-12-10', 'punuti' => 0, 'puntos' => 0, 'estado' => 'P'],
            ['id' => 3, 'codinf' => 'CCF013-24-00001', 'codser' => '110', 'numero' => 1, 'tipo' => 'T', 'documento' => '7561396', 'coddoc' => '1', 'codcat' => 'C', 'valtot' => 37000, 'fecsis' => '2020-12-10', 'hora' => '16:26:43', 'tipmov' => 'P', 'online' => null, 'consumo' => 'N', 'feccon' => null, 'punuti' => 37, 'puntos' => 0, 'estado' => 'P'],
        ];

        foreach ($movimientos as $movimiento) {
            Mercurio60::updateOrCreate(
                ['id' => $movimiento['id']],
                $movimiento
            );
        }
    }
}
