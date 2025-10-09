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
                'detalle' => 'AFILIACION TRABAJADOR',
                'dias' => 7,
            ],
            [
                'tipopc' => '10',
                'detalle' => 'AFILIACION FACULTATIVOS',
                'dias' => 3,
            ],
            [
                'tipopc' => '11',
                'detalle' => 'AFILIACION MADRES COMUNITARIAS',
                'dias' => 3,
            ],
            [
                'tipopc' => '12',
                'detalle' => 'AFILIACION SERVICIO DOMESTICO',
                'dias' => 3,
            ],
            [
                'tipopc' => '13',
                'detalle' => 'AFILIACION INDEPENDIENTES',
                'dias' => 3,
            ],
            [
                'tipopc' => '14',
                'detalle' => 'DATOS BASICOS DE TRABAJADOR',
                'dias' => 7,
            ],
            [
                'tipopc' => '2',
                'detalle' => 'AFILIACION EMPRESA',
                'dias' => 3,
            ],
            [
                'tipopc' => '3',
                'detalle' => 'AFILIACION CONYUGE',
                'dias' => 7,
            ],
            [
                'tipopc' => '4',
                'detalle' => 'AFILIACION BENEFICIARIO',
                'dias' => 7,
            ],
            [
                'tipopc' => '5',
                'detalle' => 'DATOS BASICOS DE EMPRESAS',
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
                'detalle' => 'AFILIACION PENSIONADOS',
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

    /* INSERT INTO `mercurio09` VALUES ('1','AFILIACION TRABAJADOR',7),('10','AFILIACION FACULTATIVOS',3),('11','AFILIACION MADRES COMUNITARIAS',3),('12','AFILIACION SERVICIO DOMESTICO',3),('13','AFILIACION INDEPENDIENTES',3),('14','DATOS BASICOS DE TRABAJADOR',7),('2','AFILIACION EMPRESA',3),('3','AFILIACION CONYUGE',7),('4','AFILIACION BENEFICIARIO',7),('5','DATOS BASICOS DE EMPRESAS',5),('7','RETIRO TRABAJADOR',5),('8','CERTIFICADOS',5),('9','AFILIACION PENSIONADOS',3); */
}
