<?php

namespace Database\Seeders;

use App\Models\MenuPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuPermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    private const TABLE = 'menu_permissions';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->limpiarTabla();
            // menu administrador
            $menu_admin = [
                ['tipfun' => 'ADAD', 'can_view' => 1, 'menu_item' => 3, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1, 'menu_item' => 4, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1, 'menu_item' => 5, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1, 'menu_item' => 6, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1, 'menu_item' => 7, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1, 'menu_item' => 8, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1, 'menu_item' => 9, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1, 'menu_item' => 10, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1, 'menu_item' => 11, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 1, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 2, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 12, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 13, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 14, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 15, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 16, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 17, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 18, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 19, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 20, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 21, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true, 'galeria' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 22, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 23, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 24, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 25, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 26, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 28, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 29, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 30, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 31, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 32, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 33, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 34, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 35, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 36, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 37, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 38, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 39, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 40, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 41, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 42, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 43, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 44, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 45, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 46, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 47, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 48, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 49, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 50, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 51, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 52, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 53, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 54, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 55, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ADAD', 'can_view' => 1,  "menu_item" => 56, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
            ];

            // usuario afiliacion de trabajadores
            $menu_safi = [
                ['tipfun' => 'SAFI', 'can_view' => 1, 'menu_item' => 1, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'SAFI', 'can_view' => 1, 'menu_item' => 6, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'SAFI', 'can_view' => 1, 'menu_item' => 10, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'SAFI', 'can_view' => 1, 'menu_item' => 11, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'SAFI', 'can_view' => 1, 'menu_item' => 29, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'SAFI', 'can_view' => 1, 'menu_item' => 30, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'SAFI', 'can_view' => 1, 'menu_item' => 31, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'SAFI', 'can_view' => 1, 'menu_item' => 32, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'SAFI', 'can_view' => 1, 'menu_item' => 33, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'SAFI', 'can_view' => 1, 'menu_item' => 53, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
            ];

            // usuario afiliacion de empresas
            $menu_uis = [
                ['tipfun' => 'UIS', 'can_view' => 1, 'menu_item' => 1, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'UIS', 'can_view' => 1, 'menu_item' => 5, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'UIS', 'can_view' => 1, 'menu_item' => 11, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'UIS', 'can_view' => 1, 'menu_item' => 22, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'UIS', 'can_view' => 1, 'menu_item' => 23, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'UIS', 'can_view' => 1, 'menu_item' => 24, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'UIS', 'can_view' => 1, 'menu_item' => 25, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'UIS', 'can_view' => 1, 'menu_item' => 26, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'UIS', 'can_view' => 1, 'menu_item' => 28, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'UIS', 'can_view' => 1, 'menu_item' => 53, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
            ];

            // usuario actualizacion de datos y certificados
            $menu_actu = [
                ['tipfun' => 'ACTU', 'can_view' => 1, 'menu_item' => 1, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ACTU', 'can_view' => 1, 'menu_item' => 5, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ACTU', 'can_view' => 1, 'menu_item' => 6, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ACTU', 'can_view' => 1, 'menu_item' => 11, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ACTU', 'can_view' => 1, 'menu_item' => 28, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ACTU', 'can_view' => 1, 'menu_item' => 33, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ACTU', 'can_view' => 1, 'menu_item' => 32, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
                ['tipfun' => 'ACTU', 'can_view' => 1, 'menu_item' => 53, 'opciones' => json_encode(['index' => true, 'filtro' => true, 'editar' => true, 'guardar' => true, 'borrar' => true])],
            ];

            foreach ($menu_admin as $item1) {
                MenuPermission::create($item1);
            }

            foreach ($menu_safi as $item1) {
                MenuPermission::create($item1);
            }

            foreach ($menu_uis as $item1) {
                MenuPermission::create($item1);
            }

            foreach ($menu_actu as $item1) {
                MenuPermission::create($item1);
            }
        });
    }

    /**
     * Elimina los registros existentes para permitir re-ejecuciones idempotentes.
     */
    protected function limpiarTabla(): void
    {
        DB::statement(sprintf('DELETE FROM %s', self::TABLE));
    }
}
