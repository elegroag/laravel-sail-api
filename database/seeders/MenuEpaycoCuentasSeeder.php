<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\MenuPermission;
use Illuminate\Database\Seeder;

class MenuEpaycoCuentasSeeder extends Seeder
{
    public function run(): void
    {
        $menuItem = MenuItem::updateOrCreate(
            [
                'controller' => 'EpaycoCuentaController',
                'action' => 'index',
            ],
            [
                'title' => 'Cuentas ePayco',
                'default_url' => '/cajas/epayco-cuentas/index',
                'icon' => 'ni ni-credit-card',
                'color' => 'primary',
                'nota' => 'Administración de llaves y comercios ePayco por ambiente',
                'parent_id' => null,
                'codapl' => 'CA',
            ]
        );

        $opciones = json_encode([
            'index' => true,
            'buscar-cuenta' => true,
            'editar' => true,
            'guardar' => true,
            'borrar' => true,
        ]);

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
    }
}
