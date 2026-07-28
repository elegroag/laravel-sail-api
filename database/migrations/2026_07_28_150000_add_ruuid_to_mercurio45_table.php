<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Añade ruuid nullable + UNIQUE a mercurio45 (certificados) y a su tabla de auditoría si existe.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->addRuuidColumn('mercurio45', withUnique: true);

        if (Schema::hasTable('auditoria_mercurio45')) {
            $this->addRuuidColumn('auditoria_mercurio45', withUnique: false);
        }
    }

    public function down(): void
    {
        foreach (['mercurio45', 'auditoria_mercurio45'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'ruuid')) {
                continue;
            }

            $unique = 'unique_'.$table.'_ruuid';
            if ($table === 'mercurio45' && $this->indexExists($table, $unique)) {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$unique}`");
            }

            if ($this->indexExists($table, 'fk_'.$table.'_ruuid')) {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `fk_{$table}_ruuid`");
            }

            DB::statement("ALTER TABLE `{$table}` DROP COLUMN `ruuid`");
        }
    }

    private function addRuuidColumn(string $table, bool $withUnique): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        if (! Schema::hasColumn($table, 'ruuid')) {
            DB::statement("ALTER TABLE `{$table}` ADD COLUMN `ruuid` VARCHAR(20) NULL AFTER `fecsol`");
        }

        if ($withUnique) {
            DB::table($table)->where('ruuid', '')->update(['ruuid' => null]);

            $unique = 'unique_'.$table.'_ruuid';
            if (! $this->indexExists($table, $unique)) {
                DB::statement("ALTER TABLE `{$table}` ADD UNIQUE `{$unique}` (`ruuid`)");
            }

            return;
        }

        $index = 'fk_'.$table.'_ruuid';
        if (! $this->indexExists($table, $index)) {
            DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$index}` (`ruuid`)");
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
