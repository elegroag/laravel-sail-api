<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Precondición: correr `php artisan radicados:backfill-from-solicitudes`
     * hasta que no queden ruuid huérfanos en solicitudes.
     */
    public function up(): void
    {
        if (! Schema::hasTable('radicados') || ! Schema::hasColumn('radicados', 'radicado')) {
            return;
        }

        // Deduplicar por radicado (conservar MIN(id)).
        $dupes = DB::select(
            'SELECT radicado, MIN(id) AS keep_id, COUNT(*) AS cnt
             FROM radicados
             WHERE radicado IS NOT NULL AND radicado != \'\'
             GROUP BY radicado
             HAVING cnt > 1'
        );

        foreach ($dupes as $dupe) {
            DB::table('radicados')
                ->where('radicado', $dupe->radicado)
                ->where('id', '!=', $dupe->keep_id)
                ->delete();
        }

        // Eliminar filas sin radicado antes de NOT NULL.
        DB::table('radicados')
            ->whereNull('radicado')
            ->orWhere('radicado', '')
            ->delete();

        DB::statement('ALTER TABLE `radicados` MODIFY `radicado` VARCHAR(20) NOT NULL');

        if (! $this->indexExists('radicados', 'unique_radicados_radicado')) {
            DB::statement('ALTER TABLE `radicados` ADD UNIQUE `unique_radicados_radicado` (`radicado`)');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('radicados')) {
            return;
        }

        if ($this->indexExists('radicados', 'unique_radicados_radicado')) {
            DB::statement('ALTER TABLE `radicados` DROP INDEX `unique_radicados_radicado`');
        }

        DB::statement('ALTER TABLE `radicados` MODIFY `radicado` VARCHAR(20) NULL');
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $row = DB::selectOne(
            'SELECT INDEX_NAME
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND INDEX_NAME = ?
             LIMIT 1',
            [$table, $indexName]
        );

        return $row !== null;
    }
};
