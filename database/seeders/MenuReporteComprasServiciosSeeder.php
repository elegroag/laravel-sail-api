<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\MenuPermission;
use App\Models\MenuTipo;
use Illuminate\Database\Seeder;

class MenuReporteComprasServiciosSeeder extends Seeder
{
    public function run(): void
    {
        $menuItem = MenuItem::updateOrCreate(
            [
                'controller' => 'ReporteComprasServiciosController',
                'action' => 'index',
            ],
            [
                'title' => 'Ventas en línea',
                'default_url' => '/cajas/reporte-compras-servicios/index',
                'icon' => 'ni ni-cart',
                'color' => 'primary',
                'nota' => 'Reporte de preventas y ventas en línea de servicios del portal ecommerce',
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
                    'position' => 92 + $index,
                ]
            );
        }
    }
}
