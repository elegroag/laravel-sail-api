<?php

namespace Database\Seeders;

use App\Models\Gener21;
use App\Models\MenuItem;
use App\Models\MenuPermission;
use App\Models\MenuTipo;
use Illuminate\Database\Seeder;

class MenuBannersSeeder extends Seeder
{
    public function run(): void
    {
        $menuItem = MenuItem::updateOrCreate(
            [
                'controller' => 'BannerController',
                'action' => 'index',
            ],
            [
                'title' => 'Banners login',
                'default_url' => '/cajas/banners/index',
                'icon' => 'ni ni-album-2',
                'color' => 'primary',
                'nota' => 'Banners promocionales (Dialog) del login de Mercurio',
                'parent_id' => null,
                'codapl' => 'CA',
            ]
        );

        $opciones = json_encode([
            'index' => true,
            'galeria' => true,
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
