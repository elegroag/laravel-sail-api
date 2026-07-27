<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatesAuditoriaSolicitudTable
{
    public function up(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $audit = 'auditoria_'.$table;

        if (! Schema::hasTable($audit)) {
            DB::statement("CREATE TABLE `{$audit}` LIKE `{$table}`");
        }

        $this->prepareAuditTable($table, $audit);
        $this->createDeleteTrigger($table, $audit);
    }

    public function down(string $table): void
    {
        $audit = 'auditoria_'.$table;
        $trigger = 'trg_'.$table.'_ad';

        DB::statement("DROP TRIGGER IF EXISTS `{$trigger}`");

        if (Schema::hasTable($audit)) {
            Schema::drop($audit);
        }
    }

    private function prepareAuditTable(string $source, string $audit): void
    {
        // Quitar UNIQUE (p. ej. ruuid) para permitir historial.
        $uniques = DB::select(
            'SELECT DISTINCT INDEX_NAME
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND NON_UNIQUE = 0
               AND INDEX_NAME != ?',
            [$audit, 'PRIMARY']
        );

        foreach ($uniques as $unique) {
            $name = $unique->INDEX_NAME;
            DB::statement("ALTER TABLE `{$audit}` DROP INDEX `{$name}`");
        }

        // id deja de ser PK/AI.
        $idCol = DB::selectOne(
            'SELECT COLUMN_TYPE, IS_NULLABLE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?',
            [$audit, 'id']
        );

        if ($idCol) {
            $nullSql = $idCol->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';
            DB::statement("ALTER TABLE `{$audit}` MODIFY `id` {$idCol->COLUMN_TYPE} {$nullSql}");
        }

        $hasPrimary = DB::selectOne(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_TYPE = ?',
            [$audit, 'PRIMARY KEY']
        );

        if ($hasPrimary && ! Schema::hasColumn($audit, 'audit_id')) {
            DB::statement("ALTER TABLE `{$audit}` DROP PRIMARY KEY");
        }

        if (! Schema::hasColumn($audit, 'audit_id')) {
            DB::statement("ALTER TABLE `{$audit}` ADD `audit_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
        }

        if (! Schema::hasColumn($audit, 'deleted_at')) {
            DB::statement("ALTER TABLE `{$audit}` ADD `deleted_at` DATETIME NOT NULL");
        }

        $indexExists = DB::selectOne(
            'SELECT INDEX_NAME
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND INDEX_NAME = ?
             LIMIT 1',
            [$audit, 'auditoria_'.$source.'_id_deleted_at_idx']
        );

        if (! $indexExists) {
            DB::statement(
                "ALTER TABLE `{$audit}` ADD INDEX `auditoria_{$source}_id_deleted_at_idx` (`id`, `deleted_at`)"
            );
        }
    }

    private function createDeleteTrigger(string $source, string $audit): void
    {
        $trigger = 'trg_'.$source.'_ad';
        DB::statement("DROP TRIGGER IF EXISTS `{$trigger}`");

        $columns = DB::select(
            "SELECT COLUMN_NAME
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME NOT IN (?, ?)
               AND EXTRA NOT LIKE '%GENERATED%' COLLATE utf8mb3_general_ci
             ORDER BY ORDINAL_POSITION",
            [$source, 'audit_id', 'deleted_at']
        );

        $names = array_map(fn ($c) => $c->COLUMN_NAME, $columns);
        if ($names === []) {
            return;
        }

        $colList = implode(', ', array_map(fn ($n) => "`{$n}`", $names));
        $oldList = implode(', ', array_map(fn ($n) => "OLD.`{$n}`", $names));

        DB::unprepared("
            CREATE TRIGGER `{$trigger}`
            AFTER DELETE ON `{$source}`
            FOR EACH ROW
            INSERT INTO `{$audit}` ({$colList}, `deleted_at`)
            VALUES ({$oldList}, NOW())
        ");
    }
}
