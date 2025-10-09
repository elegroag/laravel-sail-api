<?php

namespace Database\Seeders;

use App\Models\Mercurio51;
use Illuminate\Database\Seeder;

class Mercurio51Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $categorias = [
            ['codcat' => 1, 'detalle' => 'SUBSIDIO', 'tipo' => 'T', 'estado' => 'A'],
            ['codcat' => 2, 'detalle' => 'FINANCIERA', 'tipo' => 'T', 'estado' => 'A'],
            ['codcat' => 3, 'detalle' => 'SERVICIOS', 'tipo' => 'T', 'estado' => 'A'],
            ['codcat' => 4, 'detalle' => 'ATENCION AL CLIENTE', 'tipo' => 'T', 'estado' => 'A'],
            ['codcat' => 5, 'detalle' => 'SERVICIOS', 'tipo' => 'T', 'estado' => 'A'],
            ['codcat' => 6, 'detalle' => 'SERVICIOS', 'tipo' => 'B', 'estado' => 'A'],
        ];

        foreach ($categorias as $categoria) {
            Mercurio51::updateOrCreate(
                ['codcat' => $categoria['codcat']],
                $categoria
            );
        }
    }
}
