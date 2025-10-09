<?php

namespace Database\Seeders;

use App\Models\Xml4b005;
use Illuminate\Database\Seeder;

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
