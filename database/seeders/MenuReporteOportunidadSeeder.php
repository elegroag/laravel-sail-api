<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\MenuPermission;
use Illuminate\Database\Seeder;

class MenuReporteOportunidadSeeder extends Seeder
{
    public function run(): void
    {
        $menuItem = MenuItem::updateOrCreate(
            [
                'controller' => 'ReporteOportunidadAfiliacionController',
                'action' => 'index',
            ],
            [
                'title' => 'Reporte Oportunidad Afiliaciones',
                'default_url' => '/cajas/reporte-oportunidad',
                'icon' => 'ni ni-chart-bar-32',
                'color' => 'primary',
                'nota' => 'Control de oportunidad en afiliaciones',
                'parent_id' => null,
                'codapl' => 'CAJ',
            ]
        );

        $opciones = json_encode([
            'index' => true,
            'exportarpaporaportante' => true,
            'exportarportrabajador' => true,
        ]);

        foreach (['01', '02'] as $tipfun) {
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
        }
    }
}
