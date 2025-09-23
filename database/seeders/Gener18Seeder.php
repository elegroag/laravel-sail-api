<?php

namespace Database\Seeders;

use App\Models\Gener18;
use Illuminate\Database\Seeder;

class Gener18Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentos = [
            ['coddoc' => '1', 'detdoc' => 'CEDULA DE CIUDADANIA', 'codrua' => 'CC'],
            ['coddoc' => '10', 'detdoc' => 'TARJETA DE MOVILIDAD FRONTERIZA', 'codrua' => 'TMF'],
            ['coddoc' => '11', 'detdoc' => 'CARNE DIPLOMATICO', 'codrua' => 'CD'],
            ['coddoc' => '12', 'detdoc' => 'IDENTIFICACION DADA POR LA SECRETARIA DE EDUCACION', 'codrua' => 'ISE'],
            ['coddoc' => '13', 'detdoc' => 'VISA', 'codrua' => 'V'],
            ['coddoc' => '14', 'detdoc' => 'PERMISO PROTECCION TEMPORAL', 'codrua' => 'PT'],
            ['coddoc' => '2', 'detdoc' => 'TARJETA IDENTIDAD', 'codrua' => 'TI'],
            ['coddoc' => '3', 'detdoc' => 'NIT', 'codrua' => 'NI'],
            ['coddoc' => '4', 'detdoc' => 'CEDULA EXTRANJERIA', 'codrua' => 'CE'],
            ['coddoc' => '5', 'detdoc' => 'NUIP', 'codrua' => 'NU'],
            ['coddoc' => '6', 'detdoc' => 'PASAPORTE', 'codrua' => 'PA'],
            ['coddoc' => '7', 'detdoc' => 'REGISTRO CIVIL', 'codrua' => 'RC'],
            ['coddoc' => '8', 'detdoc' => 'PERMISO ESPECIAL DE PERMANENCIA', 'codrua' => 'PEP'],
            ['coddoc' => '9', 'detdoc' => 'CERTIFICADO CABILDO', 'codrua' => 'CB'],
        ];

        foreach ($documentos as $documento) {
            // Upsert para mantener los códigos alineados con el SQL legado
            Gener18::updateOrCreate(
                ['coddoc' => $documento['coddoc']],
                $documento
            );
        }
    }
}
