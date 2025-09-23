<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Xml4b005;

class Xml4b005Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $generos = [
            ['tipgen' => 1, 'nombre' => 'HOMBRE', 'codsex' => null],
            ['tipgen' => 2, 'nombre' => 'MUJER', 'codsex' => null],
            ['tipgen' => 3, 'nombre' => 'NO APLICA', 'codsex' => null],
            ['tipgen' => 4, 'nombre' => 'INDETERMINADO', 'codsex' => null],
        ];

        foreach ($generos as $genero) {
            Xml4b005::updateOrCreate(
                ['tipgen' => $genero['tipgen']],
                $genero
            );
        }
    }
}
