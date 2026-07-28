<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ruuid nullable + UNIQUE por tabla de solicitud (sin FK a radicados).
 * Varios NULL permitidos; valores no nulos únicos dentro de cada tabla.
 */
return new class extends Migration
{
    /**
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
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'ruuid')) {
                continue;
            }

            // '' no es NULL: UNIQUE solo permitiría una fila vacía.
            DB::table($table)->where('ruuid', '')->update(['ruuid' => null]);

            $dupes = DB::select(
                "SELECT ruuid, COUNT(*) AS cnt
                 FROM `{$table}`
                 WHERE ruuid IS NOT NULL AND ruuid != ''
                 GROUP BY ruuid
                 HAVING cnt > 1
                 LIMIT 5"
            );

            if ($dupes !== []) {
                $sample = collect($dupes)
                    ->map(fn ($row) => "{$row->ruuid} (×{$row->cnt})")
                    ->implode(', ');

                throw new RuntimeException(
                    "No se puede crear UNIQUE en {$table}.ruuid: hay duplicados. Ejemplos: {$sample}"
                );
            }

            DB::statement("ALTER TABLE `{$table}` MODIFY `ruuid` VARCHAR(20) NULL");

            $unique = 'unique_'.$table.'_ruuid';
            if (! $this->indexExists($table, $unique)) {
                DB::statement("ALTER TABLE `{$table}` ADD UNIQUE `{$unique}` (`ruuid`)");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'ruuid')) {
                continue;
            }

            $unique = 'unique_'.$table.'_ruuid';
            if ($this->indexExists($table, $unique)) {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$unique}`");
            }

            // Se mantiene nullable (estado previo a este cambio / post-120000).
            DB::statement("ALTER TABLE `{$table}` MODIFY `ruuid` VARCHAR(20) NULL");
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
};
