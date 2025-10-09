<?php

namespace Database\Seeders;

use App\Models\Mercurio04;
use Illuminate\Database\Seeder;

class Mercurio04Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $oficinas = [
            ['codofi' => '01', 'detalle' => 'PRINCIPAL', 'principal' => 'S', 'estado' => 'A'],
        ];

        foreach ($oficinas as $oficina) {
            Mercurio04::updateOrCreate(
                ['codofi' => $oficina['codofi']],
                $oficina
            );
        }
    }
}
