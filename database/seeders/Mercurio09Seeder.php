<?php

namespace Database\Seeders;

use App\Models\Mercurio09;
use Illuminate\Database\Seeder;

class Mercurio09Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposOperacion = [
            [
                'tipopc' => '1',
                'detalle' => 'AFILIACIÓN TRABAJADOR',
                'dias' => 7,
            ],
            [
                'tipopc' => '10',
                'detalle' => 'AFILIACIÓN FACULTATIVOS',
                'dias' => 3,
            ],
            [
                'tipopc' => '11',
                'detalle' => 'AFILIACIÓN MADRES COMUNITARIAS',
                'dias' => 3,
            ],
            [
                'tipopc' => '12',
                'detalle' => 'AFILIACIÓN SERVICIO DOMESTICO',
                'dias' => 3,
            ],
            [
                'tipopc' => '13',
                'detalle' => 'AFILIACIÓN INDEPENDIENTES',
                'dias' => 3,
            ],
            [
                'tipopc' => '14',
                'detalle' => 'DATOS BÁSICOS DE TRABAJADOR',
                'dias' => 7,
            ],
            [
                'tipopc' => '2',
                'detalle' => 'AFILIACIÓN EMPRESA',
                'dias' => 3,
            ],
            [
                'tipopc' => '3',
                'detalle' => 'AFILIACIÓN CÓNYUGE',
                'dias' => 7,
            ],
            [
                'tipopc' => '4',
                'detalle' => 'AFILIACIÓN BENEFICIARIO',
                'dias' => 7,
            ],
            [
                'tipopc' => '5',
                'detalle' => 'DATOS BÁSICOS DE EMPRESAS',
                'dias' => 5,
            ],
            [
                'tipopc' => '7',
                'detalle' => 'RETIRO TRABAJADOR',
                'dias' => 5,
            ],
            [
                'tipopc' => '8',
                'detalle' => 'CERTIFICADOS',
                'dias' => 5,
            ],
            [
                'tipopc' => '9',
                'detalle' => 'AFILIACIÓN PENSIONADOS',
                'dias' => 3,
            ],
        ];

        foreach ($tiposOperacion as $tipo) {
            Mercurio09::updateOrCreate(
                ['tipopc' => $tipo['tipopc']],
                $tipo
            );
        }
    }
}
