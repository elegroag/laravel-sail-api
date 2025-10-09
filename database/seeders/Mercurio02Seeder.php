<?php

namespace Database\Seeders;

use App\Models\Mercurio02;
use Illuminate\Database\Seeder;

class Mercurio02Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $cajas = [
            ['codcaj' => 'CCF113', 'nit' => '891190047', 'razsoc' => 'CAJA DE COMPENSACION FAMILIAR DEL CAQUETA', 'sigla' => 'COMFACA', 'email' => 'enlinea@comfaca.com', 'direccion' => 'CRR 11 10-34 EDIFICIO COMFACA', 'telefono' => '4366300', 'codciu' => '18001', 'pagweb' => 'www.comfaca.com', 'pagfac' => 'https://www.facebook.com/comfaca.caqueta/', 'pagtwi' => null, 'pagyou' => null],
        ];

        foreach ($cajas as $caja) {
            Mercurio02::updateOrCreate(
                ['codcaj' => $caja['codcaj']],
                $caja
            );
        }
    }
}
