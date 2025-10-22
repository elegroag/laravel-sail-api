<?php

namespace Database\Seeders;

use App\Models\Mercurio11;
use Illuminate\Database\Seeder;

class Mercurio11Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estadosRechazo = [
            [
                'codest' => '01',
                'detalle' => 'Documentación Incompleta',
            ],
            [
                'codest' => '02',
                'detalle' => 'Falta de Firmas',
            ],
            [
                'codest' => '03',
                'detalle' => 'Datos Incompletos',
            ],
            [
                'codest' => '04',
                'detalle' => 'Datos Inconsistentes para la Afiliación',
            ],
            [
                'codest' => '05',
                'detalle' => 'Ya existe un registro activo en nuestra base',
            ],
            [
                'codest' => '06',
                'detalle' => 'Anulación de Afiliación',
            ],
        ];

        foreach ($estadosRechazo as $estado) {
            Mercurio11::updateOrCreate(
                ['codest' => $estado['codest']],
                $estado
            );
        }
    }
}
