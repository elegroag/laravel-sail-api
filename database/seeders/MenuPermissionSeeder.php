<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class MenuPermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    private const TABLE = 'menu_permissions';

    /**
     * Ejecuta el seeder cargando el SQL externo.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->limpiarTabla();
            DB::unprepared($this->sql());
        });
    }

    /**
     * Obtiene el contenido del archivo SQL requerido.
     */
    protected function sql(): string
    {
        $sqlPath = database_path('seeders/dbsql/menu_permissions.sql');

        if (! File::exists($sqlPath)) {
            throw new RuntimeException('No se encontró el archivo SQL para el seeder menu_permissions.');
        }

        return File::get($sqlPath);
    }

    /**
     * Elimina los registros existentes para permitir re-ejecuciones idempotentes.
     */
    protected function limpiarTabla(): void
    {
        DB::statement(sprintf('DELETE FROM %s', self::TABLE));
    }
}
