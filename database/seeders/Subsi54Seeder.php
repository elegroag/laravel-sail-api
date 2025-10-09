<?php

namespace Database\Seeders;

use App\Models\Subsi54;
use Illuminate\Database\Seeder;

class Subsi54Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $tiposSociedad = [
            ['id' => 1, 'tipsoc' => '00', 'detalle' => 'SIN DEFINIR'],
            ['id' => 2, 'tipsoc' => '01', 'detalle' => 'SOCIEDAD LIMITADA'],
            ['id' => 3, 'tipsoc' => '02', 'detalle' => 'SOCIEDAD ANONIMA'],
            ['id' => 4, 'tipsoc' => '03', 'detalle' => 'SOCIEDAD COMANDITA SIMPLE (SCS)'],
            ['id' => 5, 'tipsoc' => '04', 'detalle' => 'SOCIEDAD COMANDITA POR ACCIONES'],
            ['id' => 6, 'tipsoc' => '05', 'detalle' => 'SOCIEDAD SIN ANIMO DE LUCRO'],
            ['id' => 7, 'tipsoc' => '06', 'detalle' => 'PERSONA NATURAL'],
            ['id' => 8, 'tipsoc' => '07', 'detalle' => 'UNIPERSONAL'],
            ['id' => 9, 'tipsoc' => '08', 'detalle' => 'INDEPENDIENTE'],
            ['id' => 10, 'tipsoc' => '09', 'detalle' => 'MIPYME'],
            ['id' => 11, 'tipsoc' => '10', 'detalle' => 'C.T.A'],
            ['id' => 12, 'tipsoc' => '11', 'detalle' => 'PERSONA JURIDICA'],
            ['id' => 13, 'tipsoc' => '12', 'detalle' => 'SOCIEDADES POR ACCIONES SIMPLIFICADA SAS'],
        ];

        foreach ($tiposSociedad as $tipo) {
            Subsi54::updateOrCreate(
                ['id' => $tipo['id']],
                $tipo
            );
        }
    }
}
