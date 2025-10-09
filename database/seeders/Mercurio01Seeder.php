<?php

namespace Database\Seeders;

use App\Models\Mercurio01;
use Illuminate\Database\Seeder;

class Mercurio01Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $aplicaciones = [
            ['codapl' => 'ME', 'email' => 'enlinea@comfaca.com', 'clave' => 'lqoj eqrx cgiq ajec', 'path' => 'public/temp/', 'ftpserver' => 'dd', 'pathserver' => 'ddd', 'userserver' => 'dddd', 'passserver' => 'ddddd'],
        ];

        foreach ($aplicaciones as $aplicacion) {
            Mercurio01::updateOrCreate(
                ['codapl' => $aplicacion['codapl']],
                $aplicacion
            );
        }
    }
}
