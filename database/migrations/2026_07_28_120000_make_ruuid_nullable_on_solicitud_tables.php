<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tablas de solicitud: ruuid deja de ser NOT NULL / UNIQUE.
     * Se conserva el índice no único para búsquedas.
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
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'ruuid')) {
                continue;
            }

            $this->dropUniqueIfExists($table, 'unique_'.$table.'_ruuid');
            DB::statement("ALTER TABLE `{$table}` MODIFY `ruuid` VARCHAR(20) NULL");
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'ruuid')) {
                continue;
            }

            // Puede fallar si existen NULL o duplicados.
            DB::statement("ALTER TABLE `{$table}` MODIFY `ruuid` VARCHAR(20) NOT NULL");

            $unique = 'unique_'.$table.'_ruuid';
            if (! $this->indexExists($table, $unique)) {
                DB::statement("ALTER TABLE `{$table}` ADD UNIQUE `{$unique}` (`ruuid`)");
            }
        }
    }

    private function dropUniqueIfExists(string $table, string $indexName): void
    {
        if (! $this->indexExists($table, $indexName)) {
            return;
        }

        DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
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
