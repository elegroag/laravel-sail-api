<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mercurio04;

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
