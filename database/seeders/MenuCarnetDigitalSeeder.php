<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\MenuTipo;
use Illuminate\Database\Seeder;

class MenuCarnetDigitalSeeder extends Seeder
{
    public function run(): void
    {
        $menuItem = MenuItem::updateOrCreate(
            [
                'controller' => 'CarnetController',
                'action' => 'index',
            ],
            [
                'title' => 'Carnet digital',
                'default_url' => 'mercurio/carnet',
                'icon' => 'fas fa-id-card',
                'color' => 'text-success',
                'nota' => 'Carnet digital del trabajador afiliado con QR de verificación',
                'parent_id' => null,
                'codapl' => 'ME',
            ]
        );

        MenuTipo::updateOrCreate(
            [
                'menu_item' => $menuItem->id,
                'tipo' => 'T',
            ],
            [
                'is_visible' => true,
                'position' => 90,
            ]
        );
    }
}
