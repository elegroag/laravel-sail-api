<?php

namespace Database\Seeders;

use App\Models\Mercurio06;
use Illuminate\Database\Seeder;

class Mercurio06Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $tipos = [
            ['tipo' => 'B', 'detalle' => 'BENEFICIARIO'],
            ['tipo' => 'C', 'detalle' => 'CONYUGE'],
            ['tipo' => 'E', 'detalle' => 'EMPRESA'],
            ['tipo' => 'F', 'detalle' => 'FACULTATIVO'],
            ['tipo' => 'I', 'detalle' => 'INDEPENDIENTE'],
            ['tipo' => 'N', 'detalle' => 'FONIÑEZ'],
            ['tipo' => 'O', 'detalle' => 'PENSIONADO'],
            ['tipo' => 'P', 'detalle' => 'PARTICULAR'],
            ['tipo' => 'S', 'detalle' => 'SERVICIO DOMESTICO'],
            ['tipo' => 'T', 'detalle' => 'TRABAJADOR'],
        ];

        foreach ($tipos as $tipo) {
            Mercurio06::updateOrCreate(
                ['tipo' => $tipo['tipo']],
                $tipo
            );
        }
    }
}
