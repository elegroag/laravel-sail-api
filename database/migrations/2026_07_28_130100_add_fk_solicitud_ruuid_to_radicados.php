<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Precondición: `radicados:backfill-from-solicitudes` OK y
     * migración unique_radicados_radicado aplicada.
     *
     * @var array<int, string>
     */
    private array $tables = [
        'mercurio30',
        'mercurio31',
        'mercurio32',
        'mercurio34',
        'mercurio36',
        'mercurio38',
        'mercurio39',
        'mercurio41',
        'mercurio47',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('radicados') || ! $this->indexExists('radicados', 'unique_radicados_radicado')) {
            throw new RuntimeException(
                'Falta UNIQUE en radicados.radicado. Ejecuta primero la migración make_radicados_radicado_unique_not_null y el backfill Console.'
            );
        }

        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'ruuid')) {
                continue;
            }

            $orphans = (int) DB::table($table)
                ->whereNotNull('ruuid')
                ->where('ruuid', '!=', '')
                ->whereNotExists(function ($q) use ($table) {
                    $q->select(DB::raw(1))
                        ->from('radicados')
                        ->whereColumn('radicados.radicado', $table.'.ruuid');
                })
                ->count();

            if ($orphans > 0) {
                throw new RuntimeException(
                    "Hay {$orphans} ruuid huérfanos en {$table}. Corre: php artisan radicados:backfill-from-solicitudes"
                );
            }

            $fk = 'fk_'.$table.'_radicado';
            if ($this->foreignKeyExists($table, $fk)) {
                continue;
            }

            // Alinear tipo con el padre.
            DB::statement("ALTER TABLE `{$table}` MODIFY `ruuid` VARCHAR(20) NULL");

            DB::statement(
                "ALTER TABLE `{$table}`
                 ADD CONSTRAINT `{$fk}`
                 FOREIGN KEY (`ruuid`) REFERENCES `radicados` (`radicado`)
                 ON UPDATE CASCADE
                 ON DELETE RESTRICT"
            );
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $fk = 'fk_'.$table.'_radicado';
            if ($this->foreignKeyExists($table, $fk)) {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk}`");
            }
        }
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

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $row = DB::selectOne(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = ?
             LIMIT 1',
            [$table, $constraintName, 'FOREIGN KEY']
        );

        return $row !== null;
    }
};
