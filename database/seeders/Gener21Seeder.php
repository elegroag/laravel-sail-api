<?php

namespace Database\Seeders;

use App\Models\Gener21;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Gener21Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $registros = [
            ['tipfun' => 'ACTU', 'detalle' => 'ACTUALIZAR DATOS'],
            ['tipfun' => 'ADAD', 'detalle' => 'ADMINISTRADOR SUBSIDIO'],
            ['tipfun' => 'API', 'detalle' => 'API SISU'],
            ['tipfun' => 'AUDI', 'detalle' => 'AUDITORIA INTERNA'],
            ['tipfun' => 'CONS', 'detalle' => 'CONSULTAS AFILIADOS'],
            ['tipfun' => 'FONE', 'detalle' => 'USUARIO DE FONEDE'],
            ['tipfun' => 'FOSF', 'detalle' => 'USUARIO FOSFEC'],
            ['tipfun' => 'INVI', 'detalle' => 'USUARIO DE CONSULTA GRAL'],
            ['tipfun' => 'SAFI', 'detalle' => 'AFILIACION TRABAJADORES'],
            ['tipfun' => 'SAPO', 'detalle' => 'APORTES SUBSIDIO'],
            ['tipfun' => 'SAT', 'detalle' => 'SAT UNICAMENTE MINISTERIO'],
            ['tipfun' => 'SLIQ', 'detalle' => 'LIQUIDACION SUBSIDIO'],
            ['tipfun' => 'UIS', 'detalle' => 'AFI EMPRESAS CONSULTA TRABAJA'],
            ['tipfun' => 'UXML', 'detalle' => 'USUARIO XML'],
        ];

        foreach ($registros as $registro) {
            Gener21::updateOrCreate(
                ['tipfun' => $registro['tipfun']],
                $registro
            );
        }
    }
}
