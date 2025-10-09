<?php

namespace Database\Seeders;

use App\Models\Xml4b064;
use Illuminate\Database\Seeder;

class Xml4b064Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $areas = [
            ['codare' => 1, 'nombre' => 'URBANA'],
            ['codare' => 2, 'nombre' => 'RURAL'],
        ];

        foreach ($areas as $area) {
            Xml4b064::updateOrCreate(
                ['codare' => $area['codare']],
                $area
            );
        }
    }
}
