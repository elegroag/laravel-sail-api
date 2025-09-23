<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mercurio55;

class Mercurio55Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $areas = [
            ['codare' => 1, 'detalle' => 'SUBSIDIO', 'codcat' => 1, 'tipo' => 'T', 'estado' => 'A'],
            ['codare' => 2, 'detalle' => 'FOVIS', 'codcat' => 1, 'tipo' => 'T', 'estado' => 'A'],
            ['codare' => 3, 'detalle' => 'CREDITOS', 'codcat' => 2, 'tipo' => 'T', 'estado' => 'A'],
            ['codare' => 4, 'detalle' => 'TARJETAS', 'codcat' => 1, 'tipo' => 'T', 'estado' => 'A'],
            ['codare' => 5, 'detalle' => 'PQRS', 'codcat' => 4, 'tipo' => 'T', 'estado' => 'I'],
            ['codare' => 6, 'detalle' => 'SERVICIOS', 'codcat' => 5, 'tipo' => 'T', 'estado' => 'A'],
            ['codare' => 7, 'detalle' => 'COLEGIO', 'codcat' => 5, 'tipo' => 'T', 'estado' => 'A'],
            ['codare' => 8, 'detalle' => 'TECNICO', 'codcat' => 5, 'tipo' => 'T', 'estado' => 'I'],
            ['codare' => 9, 'detalle' => 'GIMNASIO', 'codcat' => 5, 'tipo' => 'T', 'estado' => 'A'],
            ['codare' => 10, 'detalle' => 'RADICACION', 'codcat' => 2, 'tipo' => 'T', 'estado' => 'I'],
            ['codare' => 11, 'detalle' => 'SERVICIOS', 'codcat' => 6, 'tipo' => 'B', 'estado' => 'A'],
        ];

        foreach ($areas as $area) {
            Mercurio55::updateOrCreate(
                ['codare' => $area['codare']],
                $area
            );
        }
    }
}
