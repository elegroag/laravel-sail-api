<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mercurio11;

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
                'detalle' => 'DOCUMENTACION INCOMPLETA',
            ],
            [
                'codest' => '02',
                'detalle' => 'FALTA DE FIRMAS',
            ],
            [
                'codest' => '03',
                'detalle' => 'DATOS INCOMPLETOS',
            ],
            [
                'codest' => '04',
                'detalle' => 'DATOS INCONSISTENTES PARA LA AFILIACION',
            ],
            [
                'codest' => '05',
                'detalle' => 'YA EXISTE UN REGISTRO ACTIVO EN NUESTRA BASE',
            ],
            [
                'codest' => '06',
                'detalle' => 'ANULACION DE AFILIACION',
            ],
        ];

        foreach ($estadosRechazo as $estado) {
            Mercurio11::updateOrCreate(
                ['codest' => $estado['codest']],
                $estado
            );
        }
    }

    /* INSERT INTO `mercurio11` VALUES 
    ('01','DOCUMENTACION INCOMPLETA'),('02','FALTA DE FIRMAS'),
    ('03','DATOS INCOMPLETOS'),
    ('04','DATOS INCONSISTENTES PARA LA AFILIACION'),
    ('05','YA EXISTE UN REGISTRO ACTIVO EN NUESTRA BASE '),
    ('06','ANULACION DE AFILIACION'); */
}
