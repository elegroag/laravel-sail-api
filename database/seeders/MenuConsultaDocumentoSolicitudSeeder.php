<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\MenuPermission;
use App\Models\MenuTipo;
use Illuminate\Database\Seeder;

class MenuConsultaDocumentoSolicitudSeeder extends Seeder
{
    public function run(): void
    {
        $menuItem = MenuItem::updateOrCreate(
            [
                'controller' => 'ConsultaDocumentoSolicitudController',
                'action' => 'index',
            ],
            [
                'title' => 'Consulta por documento',
                'default_url' => '/cajas/consulta-documento-solicitud/index',
                'icon' => 'ni ni-single-02',
                'color' => 'primary',
                'nota' => 'Consulta solicitudes de afiliación por documento de identificación',
                'parent_id' => null,
                'codapl' => 'CAJ',
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
                    'tipfun' => $tipfun,
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
                    'position' => 90 + $index,
                ]
            );
        }
    }
}
