<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\MenuPermission;
use App\Models\MenuTipo;
use Illuminate\Database\Seeder;

class MenuCertificadoTrabajadorSeeder extends Seeder
{
    public function run(): void
    {
        $menuItem = MenuItem::updateOrCreate(
            [
                'controller' => 'ConsultaController',
                'action' => 'certificadoTrabajadorView',
            ],
            [
                'title' => 'Certificado trabajador',
                'default_url' => '/cajas/consulta/certificado_trabajador',
                'icon' => 'ni ni-paper-diploma',
                'color' => 'primary',
                'nota' => 'Generación de certificados de trabajador por NIT de empresa',
                'parent_id' => null,
                'codapl' => 'CA',
            ]
        );

        $opciones = json_encode([
            'index' => true,
            'consultar' => true,
        ]);

        foreach (['01', '02'] as $index => $tipfun) {
            MenuPermission::updateOrCreate(
                [
                    'menu_item' => $menuItem->id,
                    'tipfun' => 'ADAD',
                ],
                [
                    'can_view' => 'S',
                    'opciones' => $opciones,
                ]
            );

            MenuTipo::updateOrCreate(
                [
                    'menu_item' => $menuItem->id,
                    'tipo' => $tipfun,
                ],
                [
                    'is_visible' => true,
                    'position' => 91 + $index,
                ]
            );
        }
    }
}
