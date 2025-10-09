<?php

namespace Database\Seeders;

use App\Models\ServiciosCupos;
use Illuminate\Database\Seeder;

class ServiciosCuposSeeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $servicios = [
            ['id' => 1, 'codser' => 'F', 'cupos' => '27', 'servicio' => 'Complemento Nutricional', 'estado' => 1, 'url' => 'https://portalpagos.davivienda.com/#/comercio/5910/'],
            ['id' => 2, 'codser' => 'A', 'cupos' => '40', 'servicio' => 'Reserva boletas celebración 50 años de COMFACA', 'estado' => 1000, 'url' => 'https://comfacaenlinea.com.co/recervar.php'],
        ];

        foreach ($servicios as $servicio) {
            ServiciosCupos::updateOrCreate(
                ['id' => $servicio['id']],
                $servicio
            );
        }
    }
}
