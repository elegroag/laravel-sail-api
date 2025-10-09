<?php

namespace Database\Seeders;

use App\Models\RecepcionSat;
use Illuminate\Database\Seeder;

class RecepcionSatSeeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $recepcion = [
            'id' => 73,
            'contenido' => '{"resultado":"6bbd1580-54c7-4f88-8747-61419c916241","mensaje":"El Numero de transacción no corresponde a ninguna solicitud de afiliación.","codigo":"GN35"}',
            'numero_transaccion' => '132CC03062022155300002',
            'fecha' => '2022-07-22 20:20:59',
        ];

        RecepcionSat::updateOrCreate(
            ['id' => $recepcion['id']],
            $recepcion
        );
    }
}
